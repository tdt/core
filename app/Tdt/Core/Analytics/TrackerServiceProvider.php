<?php

namespace Tdt\Core\Analytics;

use Illuminate\Support\ServiceProvider;

class TrackerServiceProvider extends ServiceProvider
{

    public function register()
    {
        \App::bind(
            'Tdt\Core\Analytics\TrackerInterface',
            'Tdt\Core\Analytics\GaTracker'
        );
    }
}
