<?php

namespace tuanlq11\laravelhelper;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use \Validator as Validator;

class SecurimageServiceProvider extends ServiceProvider
{

    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
    }
}