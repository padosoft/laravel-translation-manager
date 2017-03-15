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
        $search = "";
        $locales = $this->loadLocales();
        $langSelectedArray = array();
        foreach($locales as $locale) {
            $langSelectedArray[] = $locale;
        }

        $groups = Translation::groupBy('group');
        $groupAndKeyArray = array();

        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->select('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();


        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach($allTranslations as $translation){
            $translations[$translation->key][$translation->locale] = $translation;
        }

         return view('translation-manager::index')
            ->with('search', $search)
            ->with('langSelectedArray', $langSelectedArray)
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('group', $group)
            ->with('groups', $groups)
            ->with('groupAndKeyArray', $groupAndKeyArray)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', action('\Barryvdh\TranslationManager\Controller@postEdit', [$group]))
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
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
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

    public function postPublish()
    {

        //impostando $group = * forzo a fare il publish di tutti i gruppi
        $group = "*";

        $this->manager->exportTranslations($group);

        return ['status' => 'ok'];
    }

    public function postFindOnDb(Request $request)
    {
        $search = $request->get('search');
        $langSelectedArray = $request->get('lang');
        if (is_null($langSelectedArray))
            $langSelectedArray = array();
        //dd($lang);
        $editUrl = "";
        $group = "";
        $groups = array();
        $groupAndKeyArray = array();

        $locales = $this->loadLocales();
        $allTranslations = Translation::where('ltm_translations.value', 'like',  "%$search%")->get();

        $numTranslations = count($allTranslations);

        //echo "<br>num parole trovate: ".$numTranslations;
        $translations = [];
        foreach($allTranslations as $translation){
            $translations[$translation->key][$translation->locale] = $translation;
            $groupAndKeyArray[$translation->key] = $translation->group;
            //echo "<br>parola: ".$translation->key;
        }

        $groups = Translation::groupBy('group');

        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }
        $groups = $groups->select('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;

        return view('translation-manager::index')
            ->with('search', $search)
            ->with('langSelectedArray', $langSelectedArray)
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('group', $group)
            ->with('groups', $groups)
            ->with('groupAndKeyArray', $groupAndKeyArray)
            ->with('numTranslations', $numTranslations)
            // ->with('numChanged', $numChanged)
            ->with('editUrl', action('\Barryvdh\TranslationManager\Controller@postFind', [$search]))
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }
}
