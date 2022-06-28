<?php

namespace Core\Traits\Repository;

use Core\Exceptions\UploadingException;
use Illuminate\Support\Facades\Log;

trait CRUD
{
    /**
     * show resource by id
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function show($id): mixed
    {
        return $this->find($id);
    }

    /**
     * handling storing process for model
     * @return mixed
     * @throws UploadingException
     * @author WeSSaM
     */
    public function store(): mixed
    {
        Log::info($this->sanitizeAttributes());
        return $this->created($this->model::create($this->sanitizeAttributes()));
    }

    /**
     * handling updating process for specific model
     * @param $id
     * @return mixed
     * @throws UploadingException
     * @author WeSSaM
     */
    public function update($id): mixed
    {
        $model = $this->find($id);
        $model->update($this->sanitizeAttributes());
        $this->updated($model);
        return $model->refresh();
    }

    /**
     * delete resource's data
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function destroy($id): mixed
    {
        $model = $this->find($id);
        if ($model) $model->delete();
        return $model;
    }


}
