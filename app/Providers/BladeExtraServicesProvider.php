<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeExtraServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        Blade::if('hasrole', function ($expression) { // expression will be a string we pass from the view
            // check if any user logged in
            if (Auth::user()) {
                // checkif user hasAnyRole, then return true
                if (Auth::user()->hasAnyRole($expression)) {
                    return true;
                }
            }
            // else return false
            return false;
        });
    }
}
