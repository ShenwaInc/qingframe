<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeAppLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:appLink 
                            {module : 模块名称，例如 my_app}
                            {--force : 强制覆盖已存在的链接}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '在 public/apps 目录下为指定模块创建软链接，指向 apps/{module}/public';

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $module = $this->argument('module');
        $force = $this->option('force');

        // 目标源目录（模块的 public 目录）
        $target = base_path("apps/{$module}/public");
        // 链接路径（public/apps/{module}）
        $link = public_path("apps/{$module}");

        // 1. 检查源目录是否存在
        if (! $this->files->isDirectory($target)) {
            $this->error("源目录不存在：{$target}");
            $this->line("请确保模块 {$module} 的 public 目录已创建。");
            return 1;
        }

        // 2. 确保 public/apps 父目录存在
        $appsDir = public_path('apps');
        if (! $this->files->isDirectory($appsDir)) {
            if (! $this->files->makeDirectory($appsDir, 0755, true)) {
                $this->error("无法创建目录：{$appsDir}");
                $this->showPermissionSolution($appsDir);
                return 1;
            }
            $this->info("已创建目录：{$appsDir}");
        }

        // 3. 处理已存在的链接或文件
        if ($this->files->exists($link) || is_link($link)) {
            if (! $force) {
                $this->error("链接已存在：{$link}");
                $this->line("如需覆盖，请使用 --force 选项。");
                return 1;
            }

            // 强制删除现有链接（或普通文件/目录）
            if (! $this->deleteExisting($link)) {
                $this->error("无法删除现有项：{$link}");
                $this->showPermissionSolution($link);
                return 1;
            }
            $this->info("已删除现有项：{$link}");
        }

        // 4. 创建软链接
        try {
            if ($this->createSymlink($target, $link)) {
                $this->info("软链接创建成功：{$link} -> {$target}");
                return 0;
            } else {
                $this->error("创建软链接失败：{$link} -> {$target}");
                $this->showSymlinkSolution($target, $link);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("创建软链接时发生异常：" . $e->getMessage());
            $this->showSymlinkSolution($target, $link);
            return 1;
        }
    }

    /**
     * 删除已存在的文件、目录或符号链接。
     *
     * @param  string  $path
     * @return bool
     */
    protected function deleteExisting($path)
    {
        if (is_link($path)) {
            // 符号链接直接删除链接本身（不影响目标）
            return unlink($path);
        } elseif ($this->files->isDirectory($path)) {
            // 普通目录递归删除（谨慎，一般只删除链接，但这里做安全兜底）
            return $this->files->deleteDirectory($path);
        } elseif ($this->files->exists($path)) {
            return $this->files->delete($path);
        }
        return true;
    }

    /**
     * 跨平台创建符号链接。
     *
     * @param  string  $target
     * @param  string  $link
     * @return bool
     */
    protected function createSymlink($target, $link)
    {
        // Windows 环境推荐使用相对路径，但绝对路径也可工作
        // 使用 PHP 内置 symlink() 函数
        if (PHP_OS_FAMILY === 'Windows') {
            // 对 Windows，确保以管理员身份运行或开启开发者模式
            // 尝试使用相对路径（有时更容易成功）
            $relativeTarget = $this->getRelativePath($link, $target);
            return @symlink($relativeTarget, $link);
        } else {
            // Linux / macOS 直接使用绝对路径
            return @symlink($target, $link);
        }
    }

    /**
     * 计算从 $from 到 $to 的相对路径。
     *
     * @param  string  $from  链接文件路径
     * @param  string  $to    目标路径
     * @return string
     */
    protected function getRelativePath($from, $to)
    {
        $from = is_file($from) ? dirname($from) : $from;
        $from = rtrim(str_replace('\\', '/', $from), '/');
        $to   = rtrim(str_replace('\\', '/', $to), '/');

        $fromParts = explode('/', $from);
        $toParts   = explode('/', $to);

        // 移除共同前缀
        $i = 0;
        $len = min(count($fromParts), count($toParts));
        while ($i < $len && $fromParts[$i] === $toParts[$i]) {
            $i++;
        }

        // 向上级目录的层级
        $up = count($fromParts) - $i;
        $relative = str_repeat('../', $up) . implode('/', array_slice($toParts, $i));
        return $relative ?: '.';
    }

    /**
     * 显示创建目录或文件时的权限解决方案。
     *
     * @param  string  $path
     * @return void
     */
    protected function showPermissionSolution($path)
    {
        $this->line('可能原因：权限不足。');
        if (PHP_OS_FAMILY === 'Windows') {
            $this->line('解决方案：');
            $this->line('  1. 以管理员身份重新运行命令（例如右键“命令提示符” -> 以管理员身份运行）。');
            $this->line('  2. 开启 Windows 开发者模式（设置 -> 更新和安全 -> 开发者选项）。');
            $this->line("  3. 手动创建目录并设置权限：mkdir \"{$path}\"");
        } else {
            $this->line('解决方案：');
            $this->line("  1. 检查父目录权限：ls -ld " . dirname($path));
            $this->line("  2. 修改所有者或权限，例如：sudo chown -R \$USER:\$USER " . dirname($path));
            $this->line("  3. 若需立即创建，可使用 sudo 运行命令：sudo php artisan make:appLink {$this->argument('module')}");
        }
    }

    /**
     * 显示创建符号链接失败的详细解决方案。
     *
     * @param  string  $target
     * @param  string  $link
     * @return void
     */
    protected function showSymlinkSolution($target, $link)
    {
        $this->line('创建软链接失败，请尝试以下方法：');
        if (PHP_OS_FAMILY === 'Windows') {
            $this->line('1. 以管理员身份重新运行此命令。');
            $this->line('2. 开启 Windows 开发者模式后重试。');
            $this->line('3. 手动使用 mklink 命令（需管理员权限）：');
            $this->line("   mklink /D \"{$link}\" \"{$target}\"");
        } else {
            $this->line('1. 检查您是否有权在 public/apps 目录下创建链接：');
            $this->line("   ls -ld " . dirname($link));
            $this->line('2. 如无权限，可使用 sudo 运行命令：');
            $this->line("   sudo php artisan make:appLink {$this->argument('module')}");
            $this->line('3. 或手动创建链接：');
            $this->line("   ln -s \"{$target}\" \"{$link}\"");
        }
    }
}