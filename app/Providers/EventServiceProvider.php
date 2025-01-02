<?php

namespace App\Providers;

use Illuminate\Session\TokenMismatchException;
use App\Listeners\HandleTokenMismatch;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TokenMismatchException::class => [
            HandleTokenMismatch::class,
        ],
    ];
}