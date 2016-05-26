<?php namespace App\Extensions\Modules;

use Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Pingpong\Modules\Facades\Module as ModuleFacade;

class Module extends \Pingpong\Modules\Module
{
    /**
     * Vrati vsetky routes modulu definovane v subore module.json
     *
     * @return mixed
     */
    public function routes()
    {
        return $this->get('routes');
    }

    public function settings($key = "")
    {
        if(!empty($key)) {
            return Arr::get($this->get('settings'), $key, null);
        }
        return $this->get('settings');
    }

    /**
     * Ak je v subore module.json deklarovana "main" route (hlavna cesta modulu)
     * a je zaroven definovana v routes.php subore modulu,
     * vrati link na nu
     *
     * - za main route sa berie url, na ktoru je odkaz v sidebar-e dashboardu
     *
     * @return Route|string
     * @throws ModuleRouteNotFoundException
     */
    public function mainRoute()
    {
        $routes = $this->routes();

        if(!isset($routes['main'])) return "";

        $mainRoute = $routes['main'];

        if(!Route::has($mainRoute))
            throw new ModuleRouteNotFoundException($mainRoute);

        return route($mainRoute);
    }

    /**
     * Ak modul obsahuje obrazok ikony ako subor Assets/images/icon/default.png
     * vrati cestu k tomuto suboru (mozme vyuzitie neskor vo funkcii url() alebo asset() )
     *
     * V opacnom pripade, vrati defaultnu ikonu definovanu zatial ako
     * public/pictures/icon/default.png
     *
     * @return string
     */
    public function iconPath()
    {
        $path = "modules/" . $this->getLowerName() . "/images/icon/" . $this->iconName();

        if(!File::exists($path)) {
            return "pictures/icon/default.png";
        }

        return $path;
    }

    /**
     * Vracia nazov ikony, pre dany modul
     *
     * @return string
     */
    public function iconName()
    {
        //@TODO
        // Toto sa pravdepodobne bude uchovavat v databaze
        // takze zatial tu iba vraciam default nazov
        return "default.png";
    }

    /**
     * Ak modul obsahuje pre dany jazyk v Resources/lang/{language}/default.php
     * prelozeny svoj nazov (kluc "name"), tak ho tato metoda vrati
     *
     * V opacnom pripade vracia defaultny nazov, priradeny v subore module.json
     *
     * @return string
     */
    public function localizedName()
    {
        $langKey = $this->alias . "::default.name";
        $translatedName = trans($langKey);
        return $translatedName == $langKey ? $this->getStudlyName() : $translatedName;
    }

    /**
     *
     *
     * @return mixed
     */
    public function isVisible()
    {
        $visible = $this->get('visible');

        // Defaultne je visibility true, aj ked nie je nastavena
        // v module.json
        return isset($visible) ? $visible : true;
    }

    /**
     * Ak sa url segment nachadza v url stringu
     * predpokladame, ze sme nasli modul,
     * ktory sme hladali
     *
     * @param $segment
     * @return bool
     * @throws ModuleRouteNotFoundException
     */
    public function hasUrlSegment($segment)
    {
        $url = $this->mainRoute();
        $segments = array_filter(explode('/',parse_url($url, PHP_URL_PATH)));

        if(count($segments) > 0) {
            return (new Collection($segments))->first() == $segment;
        } else {
            return false;
        }
    }

    public function isActive($activeClass = "selected", $inactiveClass = "")
    {
        $segment = Request::segment(1);

        if($this->hasUrlSegment($segment)) {
            return $activeClass;
        }

        return $inactiveClass;
    }

}