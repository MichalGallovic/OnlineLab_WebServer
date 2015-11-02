<?php
/**
 * Created by PhpStorm.
 * User: michalgallovic
 * Date: 03/11/15
 * Time: 00:04
 */

namespace App\Extensions\Modules;


class ModuleRouteNotFoundException extends \Exception
{
    public function __construct($route) {
        parent::__construct("Module route: " ."\"" .$route ."\"". " not found. \nPossible typo in: " ."\"". $route ."\"". "\n\n");
    }
}