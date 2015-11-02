<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Pingpong\Modules\Facades\Module;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() {
        // Vsetky classy dedia od Controller a ked zavolaju parent::__construct()
        // tak lokalizacia funguje vo vsetkych route, na ktore su namapovane

        $localization = Module::get('Localization');

        if($localization->enabled()) {
            $this->middleware(\Modules\Localization\Http\Middleware\Language::class);
        }
    }

}
