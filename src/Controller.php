<?php namespace Barryvdh\TranslationManager;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
class Controller extends BaseController
{
    /** @var \Barryvdh\TranslationManager\Manager  */
    protected $manager;


    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getIndex($group = null)
    {
        $separator = "***";
        $search = "";
        $locales = $this->loadLocales();
        $langSelectedArray = array();
        foreach($locales as $locale) {
            $langSelectedArray[] = $locale;
        }

        $groups = Translation::groupBy('package','group');

        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->select(
            DB::raw("IF(`package`<>'',CONCAT(`package`,'::',`group`),`group`) AS `group`")
            )->orderBy('package')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;
        $package='';
        $query=Translation::where('status', Translation::STATUS_CHANGED);
        if (strpos($group,'::')!==false){
            $package=substr($group,0,strpos($group,'::'));
            $query->where('package',substr($group,0,strpos($group,'::')));
            $group=substr($group,strpos($group,'::')+2);
        }else{
            $query->where('package','');
        }
        $numChanged = $query->where('group', $group)->count();
        $query=Translation::where('group',$group);
        if ($package!=''){
            $query->where('package',$package);
        }else{
            $query->where('package','');
        }
        //dd($query->toSql());
        $allTranslations = $query->orderBy('key', 'asc')->get();

        $numTranslations = count($allTranslations);
        $translations = [];
        foreach($allTranslations as $translation){
            $translationKey = $translation->key;
            $translationGroup = $translation->group;
            $translationPackage = $translation->package;
            foreach($locales as $locale){
                $translationNew = Translation::where('group', '=',  $translationGroup)
                    ->where('ltm_translations.package', '=',  $translationPackage)
                    ->where('ltm_translations.key', '=',  $translationKey)
                    ->where('locale', '=', $locale)->first();

                //$translations[$translationKey][$locale] = $translationNew;
                $translations[$translationPackage.$separator.$translationGroup.$separator.$translationKey.$separator.$locale] = $translationNew;

            }
        }
        if ($package!=''){
            $group=$package.'::'.$group;
        }

         return view('translation-manager::index')
            ->with('separator', $separator)
            ->with('search', $search)
            ->with('langSelectedArray', $langSelectedArray)
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('group', $group)
            ->with('groups', $groups)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', $group!==null?action('\Barryvdh\TranslationManager\Controller@postEdit', [$group]):'')
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    public function getView($group)
    {
        //$groups = func_get_args();
        //$group = implode('/', $groups);
        return $this->getIndex($group);
    }

    protected function loadLocales()
    {
        //Set the default locale as the first one. 
        $locales = Translation::groupBy('locale')
            ->select('locale')
            ->get()
            ->pluck('locale');

        if ($locales instanceof Collection) {
            $locales = $locales->all();
        }
        $locales = array_merge([config('app.locale')], $locales);
        return array_unique($locales);
    }

    public function postAdd(Request $request)
    {
        $keys = explode("\n", $request->get('keys'));

        $groups = func_get_args();
        array_shift($groups); // remove the $request
        $group = implode('/', $groups);

        foreach($keys as $key){
            $key = trim($key);
            if($group && $key){
                $this->manager->missingKey('*', $group, $key);
            }
        }
        return redirect()->back();
    }

    public function postEdit(Request $request, $group)
    {
        if(!in_array($group, $this->manager->getConfig('exclude_groups'))) {
            $groups = func_get_args();
            array_shift($groups); // remove the $request
            $group = implode('/', $groups);
            $name = $request->get('name');
            $value = $request->get('value');

            list($locale, $key) = explode('|', $name, 2);
            $package='';
            if (strpos($group,'::')){
                $package=substr($group,0,strpos($group,'::'));
                $group=substr($group,strpos($group,'::')+2);
            }
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
                'package' => $package,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return array('status' => 'ok');
        }
    }

    public function postDelete()
    {
        $groups = func_get_args();
        $key = array_pop($groups); // the last arg is the key
        $group = implode('/', $groups);
        if(!in_array($group, $this->manager->getConfig('exclude_groups')) && $this->manager->getConfig('delete_enabled')) {
            Translation::where('group', $group)->where('key', $key)->delete();
            return ['status' => 'ok'];
        }
    }

    public function postImport(Request $request)
    {
        $replace = $request->get('replace', false);
        $counter = $this->manager->importTranslations($replace);

        return ['status' => 'ok', 'counter' => $counter];
    }

    public function postFind()
    {
        $numFound = $this->manager->findTranslations();

        return ['status' => 'ok', 'counter' => (int) $numFound];
    }

    public function postPublish($group)
    {

        //impostando $group = * forzo a fare il publish di tutti i gruppi
        //$group = "*";

        $this->manager->exportTranslations($group);

        return ['status' => 'ok'];
    }

    public function postFindOnDb(Request $request)
    {
        $separator = "***";
        $search = $request->get('search');
        $langSelectedArray = $request->get('lang');
        if (is_null($langSelectedArray))
            $langSelectedArray = array();

        $editUrl = "";
        $group = "";
        $groups = array();

        $locales = $this->loadLocales();
        $search_html=htmlentities($search);
        //dd(htmlentities($search) );

        //se scrivo sidebar.specializzazione vuol dire che voglio cercare nel gruppo sidebar co chiave specializzazione
        $pos = strrpos($search, ".");

        $numTranslations = 0;

        if ($pos !== false) {
            $search_arr = explode(".", $search);
            $allTranslations = Translation::where('ltm_translations.group',   $search_arr[0])
                ->where('ltm_translations.key',   $search_arr[1])
                ->get();

            $numTranslations = count($allTranslations);
        }

        //se scrivo sidebar*specializzazione vuol dire che voglio cercare nei value solo del gruppo sidebar
        $pos = strrpos($search, "*");

        //$numTranslations = 0;

        if ($pos !== false) {
            $search_arr = explode("*", $search);
            $allTranslations = Translation::where('ltm_translations.group',   $search_arr[0])
                ->where('ltm_translations.value',   'like',  "%$search_arr[1]%")
                ->get();
            $numTranslations = count($allTranslations);
        }

        //faccio una ricerca avendo convertito eventuali caratteri accentati nelle corrispondente entita html
        if ($numTranslations == 0) {
            $allTranslations = Translation::where('ltm_translations.value', 'like',  "%$search_html%")->get();
        }

        $numTranslations = count($allTranslations);

        //ricerco la parola senza convertire i caratteri accentati
        if ($numTranslations == 0) {
            $allTranslations = Translation::where('ltm_translations.value', 'like',  "%$search%")->get();
        }

        $numTranslations = count($allTranslations);
        //dd($allTranslations);



        //echo "<br>num parole trovate: ".$numTranslations;
        $translations = [];
        foreach($allTranslations as $translation){
            $translationKey = $translation->key;
            $translationGroup = $translation->group;
            $translationPackage = $translation->package;
            foreach($locales as $locale){
                $translationNew = Translation::where('group', '=',  $translationGroup)
                    ->where('ltm_translations.key', '=',  $translationKey)
                    ->where('ltm_translations.package', '=',  $translationPackage)
                    ->where('locale', '=', $locale)->first();

                //$translations[$translationKey][$locale] = $translationNew;
                $translations[$translationPackage.$separator.$translationGroup.$separator.$translationKey.$separator.$locale] = $translationNew;

            }
        }
        //dd($translations);

        $groups = Translation::groupBy('package','group');

        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }
         $groups = $groups->select(
             DB::raw("IF(`package`<>'',CONCAT(`package`,'::',`group`),`group`) AS `group`")
         )->orderBy('package')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;

        return view('translation-manager::index')
            ->with('separator', $separator)
            ->with('search', $search)
            ->with('langSelectedArray', $langSelectedArray)
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('group', $group)
            ->with('groups', $groups)
            ->with('numTranslations', $numTranslations)
            // ->with('numChanged', $numChanged)
            ->with('editUrl', action('\Barryvdh\TranslationManager\Controller@postFind', [$search]))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }
}
