<?php namespace App\Extensions\Modules;

use Pingpong\Modules\Json;
use Request;

class Repository extends \Pingpong\Modules\Repository
{
    /**
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $modules = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->app['files']->glob("{$path}/module.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $lowerName = strtolower($name);

                $modules[$name] = new Module($this->app, $lowerName, dirname($manifest));
            }
        }

        return $modules;
    }

    /**
     * Format the cached data as array of modules.
     *
     * @param array $cached
     *
     * @return array
     */
    protected function formatCached($cached)
    {
        $modules = [];

        foreach ($cached as $name => $module) {
            $path = $this->config('paths.modules').'/'.$name;

            $modules[] = new Module($this->app, $name, $path);
        }

        return $modules;
    }

    /**
     * Get all modules.
     *
     * @return array
     */
    public function all()
    {
        if (!$this->config('cache.enabled')) {
            return $this->scan();
        }

        return $this->formatCached($this->getCached());
    }

    /**
     * Vrati prave pouzivany modul
     *
     * @return mixed
     */
    public function current()
    {

        $segment = Request::segment(1);

        // Skusime vratit modul na zaklade 1.url segmentu
        // to je vo vacsine pripadov, ak sme neprepisali
        // v routes.php route::group url prefix
        // ktory je rovnaky ako nazov modulu
        $module = $this->get($segment);

        if(!$module) {
            $module = $this->moduleFromUrlSegment($segment);
        }


        return $module;
    }

    /**
     * Hladame modul na zaklade url segmentu
     *
     * @param $segment
     * @return Module|null
     */
    protected function moduleFromUrlSegment($segment)
    {
        $modules = $this->enabled();

        foreach($modules as $module) {
            if($module->hasUrlSegment($segment)) {
                return $module;
            }
        }

        return null;
    }
}