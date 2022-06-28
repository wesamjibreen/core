<?php

namespace Core\Traits\Repository;

use Core\Models\Image;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait Relations
{
    /**
     * @var array
     * @author WeSSaM
     */
    protected array $relationKeys = [];

    /**
     * @var array
     * @author WeSSaM
     */
    protected array $ignoredRelations = [];


    /**
     * get relations array from model's class
     *
     * @return array
     * @author WeSSaM
     */
    public function getRelations(): array
    {
        return get_model_relations($this->modelInstance());
    }

    /**
     * save relations from request
     *
     * @param $model
     * @return mixed $model
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @author WeSSaM
     */
    public function syncRelations($model): mixed
    {
        if (in_array("*", $this->ignoredRelations))
            return $model;

        $relations = collect($this->getRelations())->filter(function ($relation, $key) {
            if (!request()->has($key) || in_array($key, $this->ignoredRelations))
                return false;
            return $relation !== "BelongsTo";
        });

        foreach ($relations as $key => $relationClass) {
            $data = $this->getRelationData($key);
            $model->$key()->{lcfirst($relationClass) . "Sync"}($data, true);
        }

        if (request()->has('images'))
            $this->syncImages(request()->get('images'), $model);

        return $model;
    }

    /**
     * @param $data
     * @param $model
     * @return void
     */
    public function syncImages($data, $model): void
    {
        if (!empty($data)) {
            is_array($data[0]) ? $ids = collect($data)->pluck('id') : $ids = $data;
            Image::whereIn('id', $ids)->update([
                'model_id' => $model->id,
                'model_type' => $model::class
            ]);
        }
    }

    /**
     * @param $key
     * @return array|mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @author Omar
     */
    public function getRelationData($key): mixed
    {

        if (array_search($key, $this->relationKeys))
            return request()->get($this->relationKeys[$key]);

        $data = request()->get($key);
        if ($data)
            return $data;
        return request()->get($this->camelCase2UnderScore($key)) ?? [];
    }

    /**
     * @param $str
     * @param string $separator
     * @return mixed|string
     * @author Omar
     */
    function camelCase2UnderScore($str, string $separator = "_"): mixed
    {
        if (empty($str)) {
            return $str;
        }
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", $separator . "$0", $str);
        return strtolower($str);
    }
}
