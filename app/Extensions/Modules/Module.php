<?php namespace App\Extensions\Modules;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class Module extends \Pingpong\Modules\Module
{
    public function routes()
    {
        return $this->get('routes');
    }

    public function mainRoute()
    {
        $routes = $this->routes();

        if(!isset($routes['main'])) return "";

        $mainRoute = $routes['main'];

        if(!Route::has($mainRoute))
            throw new ModuleRouteNotFoundException($mainRoute);

        return route($mainRoute);
    }

    public function iconPath()
    {
        $path = "modules/" . $this->getLowerName() . "/images/icon/" . $this->iconName();

        if(!File::exists($path))
            return "pictures/icon/default.png";

        return $path;
    }

    public function iconName()
    {
        //@TODO
        // Toto sa pravdepodobne bude uchovavat v databaze
        // takze zatial tu iba vraciam default nazov
        return "default.png";
    }



}