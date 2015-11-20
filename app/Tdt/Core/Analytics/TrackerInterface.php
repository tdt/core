<?php

namespace Tdt\Core\Analytics;

interface TrackerInterface
{
    public function track($request, $tracker_id);
}
