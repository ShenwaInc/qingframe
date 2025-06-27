<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    public const HOME = '/console';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAppRoutes();

        // 加载模块自定义路由
        $this->mapModuleCustomRoutes();
    }

    /**
     * 加载模块自定义路由
     */
    protected function mapModuleCustomRoutes()
    {
        try {
            // 加载模块自定义路由
            $this->loadModuleCustomRoutes();
            
            // 加载微服务自定义路由
            $this->loadServerCustomRoutes();
        } catch (\Exception $e) {
            // 记录错误但不中断应用启动
            \Log::error('Failed to load custom routes: ' . $e->getMessage());
        }
    }

    /**
     * 加载模块自定义路由
     */
    private function loadModuleCustomRoutes()
    {
        $modulesPath = public_path('addons');
        if (!is_dir($modulesPath)) {
            return;
        }

        $modules = glob($modulesPath . '/*', GLOB_ONLYDIR);
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $customRoutesFile = $modulePath . '/custom_routes.php';
            
            if (file_exists($customRoutesFile)) {
                // 加载模块自定义路由文件
                require $customRoutesFile;
            }
        }
    }

    /**
     * 加载微服务自定义路由
     */
    private function loadServerCustomRoutes()
    {
        $serversPath = base_path('servers');
        if (!is_dir($serversPath)) {
            return;
        }

        $servers = glob($serversPath . '/*', GLOB_ONLYDIR);
        foreach ($servers as $serverPath) {
            $serverName = basename($serverPath);
            $customRoutesFile = $serverPath . '/custom_routes.php';
            
            if (file_exists($customRoutesFile)) {
                // 加载微服务自定义路由文件
                require $customRoutesFile;
            }
        }
    }

    protected function mapAppRoutes(){
        Route::prefix('app')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/app.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
