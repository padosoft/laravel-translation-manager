<?php namespace Barryvdh\TranslationManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class Manager{

    /** @var \Illuminate\Foundation\Application  */
    protected $app;
    /** @var \Illuminate\Filesystem\Filesystem  */
    protected $files;
    /** @var \Illuminate\Events\Dispatcher  */
    protected $events;

    protected $config;

    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
        $this->config = $app['config']['translation-manager'];
    }

    public function missingKey($namespace, $group, $key)
    {
        if(!in_array($group, $this->config['exclude_groups'])) {
            Translation::firstOrCreate(array(
                'locale' => $this->app['config']['app.locale'],
                'group' => $group,
                'key' => $key,
            ));
        }
    }

    public function importTranslations($replace = false,$base_path=null)
    {
        $counter = 0;
        $vendor = true;
        if (is_null($base_path)){
            $base_path=$this->app['path.lang'];
            $vendor = false;
        }
        echo "\r\nprocesso ".$base_path."\r\n";
       //dd(" dir da ecludere da file config ".get_var_dump_output($this->config['exclude_groups'])); exit;

        foreach($this->files->directories($base_path) as $langPath){
            $locale = basename($langPath);

            //echo ("dir trovata ".get_var_dump_output($locale));
            if(in_array($locale, $this->config['exclude_dir'])) {
                //echo ("dir eclusa ".get_var_dump_output($locale));
                continue;
            }
            //vendor is a special dir
            if ($locale=='vendor'){

                foreach($this->files->directories($langPath) as $vendorPath){

                    $this->importTranslations($replace,$vendorPath);
                }
                continue;
            }
            //dd("lingua trovata ".get_var_dump_output($locale)); exit;
            $vendor?$vendorName = $this->files->name($this->files->dirname($langPath)):$vendorName='';
            foreach($this->files->allfiles($langPath) as $file) {

                $info = pathinfo($file);

                $group = $info['filename'];

                if(in_array($group, $this->config['exclude_groups'])) {
                    continue;
                }

                $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, "", $info['dirname']);
                if ($subLangPath != $langPath) {
                    $group = $subLangPath . "/" . $group;
                }
                if ($vendor){
                    $translations = \Lang::getLoader()->load($locale, $group,$vendorName);
                }else{
                    $translations = \Lang::getLoader()->load($locale, $group);
                }

                if ($translations && is_array($translations)) {
                    foreach(array_dot($translations) as $key => $value){
                        // process only string values
                        if(is_array($value)){
                            continue;
                        }

                        $value = (string) mb_convert_encoding($value,'UTF-8',mb_detect_encoding($value));
                        $translation = Translation::firstOrNew(array(
                            'locale' => $locale,
                            'group' => $group,
                            'key' => $key,
                            'package'=>$vendorName
                        ));

                        // Check if the database is different then the files
                        $newStatus = $translation->value === $value ? Translation::STATUS_SAVED : Translation::STATUS_CHANGED;
                        if($newStatus !== (int) $translation->status){
                            $translation->status = $newStatus;
                        }

                        // Only replace when empty, or explicitly told so
                        if($replace || !$translation->value){
                            $translation->value = $value;
                        }

                        $translation->save();

                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }

    public function findTranslations($path = null)
    {
        $path = $path ?: base_path();
        $keys = array();
        $functions =  array('trans', 'trans_choice', 'Lang::get', 'Lang::choice', 'Lang::trans', 'Lang::transChoice', '@lang', '@choice', '__');
        $pattern =                              // See http://regexr.com/392hu
            "[^\w|>]".                          // Must not have an alphanum or _ or > before real method
            "(".implode('|', $functions) .")".  // Must start with one of the functions
            "\(".                               // Match opening parenthese
            "[\'\"]".                           // Match " or '
            "(".                                // Start a new group to match:
                "[a-zA-Z0-9_-]+".               // Must start with group
                "([.][^\1)]+)+".                // Be followed by one or more items/keys
            ")".                                // Close group
            "[\'\"]".                           // Closing quote
            "[\),]";                            // Close parentheses or new parameter

        // Find all PHP + Twig files in the app folder, except for storage
        $finder = new Finder();
        $finder->in($path)->exclude('storage')->name('*.php')->name('*.twig')->files();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            // Search the current file for the pattern
            if(preg_match_all("/$pattern/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $key) {
                    $keys[] = $key;
                }
            }
        }
        // Remove duplicates
        $keys = array_unique($keys);

        // Add the translations to the database, if not existing.
        foreach($keys as $key){
            // Split the group and item
            list($group, $item) = explode('.', $key, 2);
            $this->missingKey('', $group, $item);
        }

        // Return the number of found translations
        return count($keys);
    }

    public function exportTranslations($group)
    {
        if(!in_array($group, $this->config['exclude_groups'])) {
            if($group == '*') {
                return $this->exportAllTranslations();
            }

            if ($this->config['sort_keys'])
                $tree = $this->makeTree(Translation::ofTranslatedGroup($group)->orderByGroupKeys('ltm_translations.key')->get());
            else
                $tree = $this->makeTree(Translation::ofTranslatedGroup($group)->get());
            $package='';
            if (strpos($group,'::')!==false){
                $package=substr($group,0,strpos($group,'::'));
                $group=substr($group,strpos($group,'::')+2);
            }

            foreach($tree as $locale => $groups){
                if ($locale=='vendor'){
                    continue;
                }
                if(isset($groups[$group])){
                    $translations = $groups[$group];
                    $path = $this->app['path.lang'].'/'.$locale.'/'.$group.'.php';
                    $output = "<?php\n\nreturn ".var_export($translations, true).";\n";
                    if (!file_exists($this->app['path.lang'].'/'.$locale)) {
                        mkdir($this->app['path.lang'].'/'.$locale, 0755, true);
                    }
                    $this->files->put($path, $output);
                }
            }
            if (isset($tree['vendor']) && $package!=''){
                foreach($tree['vendor'] as $pack => $locales){
                    if ($package!='' && $package!=$pack){
                        continue;
                    }
                    foreach ($locales as $locale=>$groups){
                        if($group!='*' && !isset($groups[$group])) {
                            continue;
                        }
                        $translations = $groups[$group];
                        $path = $this->app['path.lang'].'/vendor/'.$pack.'/'.$locale.'/'.$group.'.php';
                        $output = "<?php\n\nreturn ".var_export($translations, true).";\n";
                        if (!file_exists($this->app['path.lang'].'/vendor/'.$pack.'/'.$locale)) {
                            mkdir($this->app['path.lang'].'/vendor/'.$pack.'/'.$locale, 0755, true);
                        }
                        $this->files->put($path, $output);
                    }
                }
            }
            if ($package!=''){
                $group=$package.'::'.$group;
            }
            Translation::ofTranslatedGroup($group)->update(array('status' => Translation::STATUS_SAVED));
        }
    }

    public function exportAllTranslations()
    {
        $groups = Translation::whereNotNull('value')->distinct()->select('group','package')->get();

        foreach($groups as $group){
            //echo ("group: ".$group->group);
            $group->package!=''?$grp=$group->package.'::'.$group->group:$grp=$group->group;
            $this->exportTranslations($grp);
        }
    }

    public function cleanTranslations()
    {
        Translation::whereNull('value')->delete();
    }

    public function truncateTranslations()
    {
        Translation::truncate();
    }

    protected function makeTree($translations)
    {
        $array = array();
        foreach($translations as $translation){
            if ($translation->package!=''){
                array_set($array['vendor'][$translation->package][$translation->locale][$translation->group], $translation->key, $translation->value);
            }else{
                array_set($array[$translation->locale][$translation->group], $translation->key, $translation->value);

            }

        }
        return $array;
    }

    public function getConfig($key = null)
    {
        if($key == null) {
            return $this->config;
        }
        else {
            return $this->config[$key];
        }
    }

}
