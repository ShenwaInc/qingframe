<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Blade::directive("serverView", function ($template=''){
            $realname = str_replace(array(":", ".","'", '"'), array("/views/", "/"), $template);
            $path = base_path("servers/" . $realname . ".blade.php");
            if (!file_exists($path)){
                $platform = defined("IN_SYS") ? "web" : "app";
                $path = str_replace("/views/", "/views/$platform/", $path);
                if (!file_exists($path)){
                    throw new \Exception("View [$template] not found.(Path: $path)");
                }
            }
            return "<?php echo Illuminate\Support\Facades\View::file('$path')->render() ?>";
        });
        Blade::directive("moduleView", function ($template=''){
            if (strexists($template, ":")){
                $view = str_replace(array(":", ".","'", '"'), array("/views/", "/"), $template);
            }else{
                global $_MODULE_VIEW;
                $view = "$_MODULE_VIEW/views/$template";
            }
            $path = public_path("addons/" . $view . ".blade.php");
            if (!file_exists($path)){
                $platform = defined("IN_SYS") ? "web" : "app";
                $path = str_replace("/views/", "/views/$platform/", $path);
                if (!file_exists($path)){
                    throw new \Exception("View [$template] not found.(Path: $path)");
                }
            }
            return "<?php echo Illuminate\Support\Facades\View::file('$path')->render() ?>";
        });
    }
}
