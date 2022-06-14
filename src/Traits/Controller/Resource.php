<?php

namespace Core\Traits\Controller;

use Core\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

trait Resource
{
    /**
     * CRUD's json resource class
     * @author WeSSaM
     */
    protected $resource;

    /**
     * @return mixed
     * @author WeSSaM
     */
    public function getResource(): mixed
    {
        $defaultClass = $this->buildClass("resource");
        if (class_exists($defaultClass)) return $defaultClass;
        return BaseResource::class;
    }

    /**
     * @param $data
     * @return mixed
     * @author WeSSaM
     */
    public function sanitizeToResource($data): mixed
    {
        $resource = $this->getResource();

        $callbackName = $this->getActionMethod();

        if ($data instanceof Model)
            return (new $resource($data))->{$callbackName}();

        $resourceResult = $data->map(function ($item) use ($resource, $callbackName) {
            return (new $resource($item))->{$callbackName}();
        });

        if (!method_exists($data, 'getCollection'))
            return $resourceResult;

        $data->setCollection($resourceResult);
        return $data;
    }
}
