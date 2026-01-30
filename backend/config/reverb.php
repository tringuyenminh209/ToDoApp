<?php

return [

    'apps' => [
        [
            'id' => env('REVERB_APP_ID', 'todokizamu'),
            'name' => env('APP_NAME', 'ToDoKizamu'),
            'key' => env('REVERB_APP_KEY', 'todokizamu-key'),
            'secret' => env('REVERB_APP_SECRET', 'todokizamu-secret'),
            'path' => env('REVERB_APP_PATH', ''),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
        ],
    ],

    'supervisor' => [
        'processes' => 1,
        'max_restarts' => 3,
        'restart_interval' => 0,
    ],

];
