<?php
/**
 * Created by PhpStorm.
 * User: michalgallovic
 * Date: 02/11/15
 * Time: 23:08
 */

namespace App\Extensions\Modules;


use Pingpong\Modules\ModulesServiceProvider;

class ExtendedModuleServiceProvider extends ModulesServiceProvider
{
    /**
     * Register the service provider.
     */
    protected function registerServices()
    {
        $this->app->bindShared('modules', function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new Repository($app, $path);
        });
    }
}