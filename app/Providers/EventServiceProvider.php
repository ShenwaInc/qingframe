<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\StatementPrepared;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {

        parent::boot();
        //
        Event::listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        });
        Event::listen("global.*", function ($eventName,$data){
            $data['event'] = $eventName;
            file_put_contents(storage_path("logs/".TIMESTAMP.".log"), json_encode($data));
            return $eventName.":".TIMESTAMP;
        });
    }

    /**
     * 确定是否应自动发现事件和侦听器。
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
