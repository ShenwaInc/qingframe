<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleCustomRouteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:custom-route {module} {--server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create custom routes file for module or server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $moduleName = $this->argument('module');
        $isServer = $this->option('server');
        
        if ($isServer) {
            return $this->createServerCustomRoutes($moduleName);
        } else {
            return $this->createModuleCustomRoutes($moduleName);
        }
    }

    /**
     * 为模块创建自定义路由文件
     */
    private function createModuleCustomRoutes($moduleName)
    {
        $modulePath = public_path("addons/{$moduleName}");
        
        if (!is_dir($modulePath)) {
            $this->error("Module '{$moduleName}' not found in public/addons/");
            return 1;
        }
        
        $customRoutesFile = $modulePath . '/custom_routes.php';
        
        if (file_exists($customRoutesFile)) {
            if (!$this->confirm("Custom routes file already exists. Overwrite?")) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }
        
        $template = file_get_contents(resource_path('stub/module.custom_routes.stub'));
        $template = str_replace('{moduleName}', $moduleName, $template);
        
        if (file_put_contents($customRoutesFile, $template)) {
            $this->info("Custom routes file created successfully!");
            $this->info("File: {$customRoutesFile}");
            $this->info("You can now edit this file to add your custom routes.");
            return 0;
        } else {
            $this->error("Failed to create custom routes file.");
            return 1;
        }
    }

    /**
     * 为微服务创建自定义路由文件
     */
    private function createServerCustomRoutes($serverName)
    {
        $serverPath = base_path("servers/{$serverName}");
        
        if (!is_dir($serverPath)) {
            $this->error("Server '{$serverName}' not found in servers/");
            return 1;
        }
        
        $customRoutesFile = $serverPath . '/custom_routes.php';
        
        if (file_exists($customRoutesFile)) {
            if (!$this->confirm("Custom routes file already exists. Overwrite?")) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }
        
        $template = file_get_contents(resource_path('stub/module.custom_routes.stub'));
        $template = str_replace('{moduleName}', $serverName, $template);
        
        if (file_put_contents($customRoutesFile, $template)) {
            $this->info("Custom routes file created successfully!");
            $this->info("File: {$customRoutesFile}");
            $this->info("You can now edit this file to add your custom routes.");
            return 0;
        } else {
            $this->error("Failed to create custom routes file.");
            return 1;
        }
    }
} 