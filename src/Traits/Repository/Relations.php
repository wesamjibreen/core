<?php

namespace Core\Traits\Repository;

trait Relations
{
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
     * @author WeSSaM
     */
    public function syncRelations($model): mixed
    {
        $relations = collect($this->getRelations())->filter(function ($relation, $key) {
            if (!request()->has($key))
                return false;
            return $relation !== "BelongsTo";
        });
        foreach ($relations as $key => $relationClass) {
            $data = request()->get($key) ??  request()->get();
            $relation = $model->$key()->{lcfirst($relationClass) . "Sync"}($data, true);
//            dd($relation);
//            if (method_exists($relation, "dynamicSync")) {
//                dd($data);
//                $relation->dynamicSync($data, true);
//            }
//            $this->{"sync$relation"}($model, $relation, $data);
//            $model->$key()->delete();
//            if ($relation === "HasMany") {
//                dd($data, $relation);

//            }
        }

//        dd($relations);

        return $model;
//        dd('getRelations', $relations);

//        $this->modelInstance();
    }
}
