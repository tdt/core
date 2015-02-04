<?php

use Monolog\Logger;

return array(
    'server' => 'mongodb://localhost:27017',
    'database' => 'database',
    'collection'=> 'logs',
    'log_level' => Logger::DEBUG,
    'time_zone' => 'GMT+1',
    'datetime_format' => 'Y-m-d H:i:s'
);