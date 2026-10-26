<?php
//condiguracion de las vistas que sirven para la aplicacion, en este caso se esta utilizando blade como motor de plantillas
return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),
];
