<?php
/**
 * Created by PhpStorm.
 * User: michalgallovic
 * Date: 08/11/15
 * Time: 21:05
 */

namespace App\Providers;


use Illuminate\Support\Facades\Request;
use URL;
use Illuminate\Support\ServiceProvider;
use Module;

class ViewComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->composeLayout();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.

    }

    /**
     * Umozni pouzivat premennu $module v kazdom view,
     * ktore dedi od users.layouts.default
     */
    protected function composeLayout()
    {
        view()->composer('user.layouts.default', function($view) {
            $view->with(['module' => Module::current()]);
        });
    }
}