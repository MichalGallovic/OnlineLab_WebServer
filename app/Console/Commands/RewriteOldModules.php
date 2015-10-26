<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RewriteOldModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rewrite:old {old_app_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rewriting old modules to new format.';

    /**
     * Cesta k priecinku starej aplikacie
     *
     * @var string
     */
    protected $old_app_path;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // Set default path of Old App - old_app
        $this->old_app_path = base_path()."/old_app/";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if($path = $this->argument('old_app_path')) {
            $this->stripSlashes($path);
        }

        if(!File::exists($this->old_app_path)) {
            $this->error("Folder: " . $this->old_app_path . " does not exists!");
            $this->info("Help: php artisan rewrite:old [old_app_path]");
            return;
        }

        $this->info("Rewriting modules: ");

        //Start rewriting modules
        $this->rewriteModules();
    }


    /**
     * Rewrite Old app modules
     * to New Sky Pingpong Labs Modules
     *
     */
    private function rewriteModules() {
        // Old app modules, that could be automatically rewritten
        $modules = ["controller","equipments","experiment","experimentinterface","livechart","profile","report","reservation","users"];
        // Path to new modules
        $newModulesBase = base_path() . "/modules";

        foreach($modules as $moduleName) {
            $this->info("Module: ". $moduleName);
            // Path to concrete module
            $modulePath = $this->old_app_path . "includes/modules/".$moduleName;
            $newModulePath = $newModulesBase."/".ucfirst($moduleName);
            // copy js assets
            File::copyDirectory($modulePath."/js", $newModulePath.'/Assets/js');
            // copy css assets
            File::copyDirectory($modulePath."/css", $newModulePath.'/Assets/css');
            //copy image assets
            File::copyDirectory($modulePath."/images", $newModulePath.'/Assets/images');
            // load htm or html file
            // replace {} with {!! trans('LANG_KEY') !!}
            // save it to module Resources/Views
            $files = File::files($modulePath."/");
            foreach($files as $filePath) {
                if(strpos($filePath,".htm") !== false || strpos($filePath,".html") !== false) {
                    $newFileName = $newModulePath . "/Resources/views/" . File::name($filePath) . ".blade.php";
                    $newFileData = $this->replaceHTMLTemplate(File::get($filePath));
                    File::put($newFileName,$newFileData);
                }
            }
            // load language files
            // rewrite them to laravel "language syntax (associative array)"
            // create corresponding folders and files and save them
            $langFiles = File::files($modulePath."/languages");
            // create language folders for sk,en languages
            File::makeDirectory($newModulePath."/Resources/lang/en", null, null, true);
            File::makeDirectory($newModulePath."/Resources/lang/sk",null, null, true);
            foreach($langFiles as $filePath) {
                $fileName = File::name($filePath);
                $newFileName = $newModulePath . "/Resources/lang/" . $fileName ."/" . $fileName . ".php";
                $newFileData = $this->transformLangFile(File::get($filePath));
                File::put($newFileName, $newFileData);
            }
        }

        $this->info("Rewriting finished!");

    }

    /**
     * Rewrite Old language file
     * to associatve array
     *
     * @param $file_data
     * @return string
     */
    private function transformLangFile($file_data) {
        // Regex matching strings inside quotes
        preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is",$file_data,$match);
        // Creating language files
        $outputfile = "<?php\n\n";
        $translations = [];
        foreach(array_chunk($match[0], 2) as $values)  {
         $translations[]=$values[0]."    =>  ".$values[1];
        }
        $outputfile .= "return [\n    ";
        $outputfile .= implode(",\n    ",str_replace("'","\"",$translations));
        $outputfile .= "\n];";
        return $outputfile;
    }

    /**
     * Rewrite FastTemplate {"LANG"} syntax
     * to laravel {!! trans('LANG') !!}
     *
     * @param $string
     * @return mixed
     */
    private function replaceHTMLTemplate($string) {
        $file_data = $string;
        $patterns = [];
        $patterns[0] = "/{/";
        $patterns[1] = "/}/";
        $replacements = [];
        $replacements[0] = "{!! trans('";
        $replacements[1] = " ') !!}";

        return preg_replace($patterns, $replacements, $file_data);
    }

    /**
     * Strip leading and trailing slashes
     *
     * @param $path
     */
    private function stripSlashes($path)
    {
        $path = $path[0] == "/" ? str_replace("/", "", $path) : $path;
        $path = $path[count($path) - 1] == "/" ? str_replace("/", "", $path) : $path;
        $this->old_app_path = base_path() . "/" . $path . "/";
    }
}
