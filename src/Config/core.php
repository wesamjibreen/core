<?php

use Core\Http\Requests\BaseRequest;
use Core\Http\Resources\BaseResource;
use Core\Models\BaseModel;
use Core\Repositories\BaseRepository;

return [
    'namespaces' => [
        'module' => [
            'model' => 'Modules\$MODULE$\Entities',
            'resource' => 'Modules\$MODULE$\Transformers',
            'request' => 'Modules\$MODULE$\Http\Requests',
            'repository' => 'Modules\$MODULE$\Repositories',
        ],
        'app' => [
            'model' => 'App\Models',
            'resource' => 'App\Http\Resources',
            'request' => 'App\Http\Requests',
            'repository' => 'App\Http\Repositories',
        ]
    ],
    'default' =>  [
        'resource' => BaseResource::class,
        'request' => BaseRequest::class,
        'repository' => BaseRepository::class,
        'model' => BaseModel::class
    ]
];
