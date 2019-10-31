<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        /**
         * Pass controller and action name to all views
         */
        app('view')->composer('*', function ($view) {
            $data = ['action' => '', 'controller'=> ''];
            if(app('request')->route()) {
                $data['action'] = app('request')->route()->getAction();
                $data['controller'] = class_basename($data['action']['controller']);
                list($data['controller'], $data['action']) = explode('@', $data['controller']);
                $data['controller'] = snake_case($data['controller']);
                $data['controller'] = str_replace('_controller', '', $data['controller']);
                $data['action'] = snake_case($data['action']);
            }

            $view->with($data);
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
