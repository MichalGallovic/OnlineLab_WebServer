<?php namespace App\Extensions\Modules;

use Pingpong\Modules\Json;

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
}