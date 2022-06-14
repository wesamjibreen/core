<?php

namespace Core\Traits;

trait Model
{

    /**
     * this function tries to predicate model according
     * to the structure of packages
     * @return mixed
     * @author WeSSaM
     */
    private function __predicateModel(): mixed
    {
        return $this->buildClass('model');
    }

    /**
     * get fillable array from model's instance
     * @return array
     * @author WeSSaM
     */
    public function getFillable(): array
    {
        return $this->modelInstance()->getFillable();
    }

    /**
     * get imagelable array from model's instance
     * @return array
     * @author WeSSaM
     */
    public function getImageable(): array
    {
        return $this->modelInstance()->getImageable();
    }

    /**
     * return new instance of model's class
     * @return mixed
     * @author WeSSaM
     */
    public function modelInstance(): mixed
    {
        return new $this->model;
    }

    /**
     * set new model class to repository
     * @param $model
     * @return $this
     * @author WeSSaM
     */
    public function setModel($model): static
    {
        $this->model = $model;
        return $this;
    }

    /**
     * get repo's  model
     * @return mixed|string
     * @author WeSSaM
     */
    public function getModel(): mixed
    {
        if ($this->model) return $this->model;
        return $this->__predicateModel();
    }



}
