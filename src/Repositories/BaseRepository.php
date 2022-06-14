<?php

namespace Core\Repositories;


use Core\Exceptions\UploadingException;
use Core\Interfaces\RepositoryInterface;
use Core\Services\ImageService as ImageManager;
use Core\Traits\Base;
use Core\Traits\CrudHooks as Hooks;
use Core\Traits\Model;
use Core\Traits\Repository\CRUD;
use Core\Traits\Repository\Query;
use Core\Traits\Repository\Imageable;
use Core\Traits\Repository\Relations;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BaseRepository implements RepositoryInterface
{
    use Base, CRUD, Hooks, Query, Model, Relations,Imageable;


    /**
     * BaseRepository Constructor
     * @throws \Exception
     * @author WeSSaM
     */
    public function __construct()
    {

    }

    public function init(): static
    {
        $this->imageManager = new ImageManager;
        return $this;
    }

    /**
     * retrieve query without pagination by calling get method
     * you may need to parse custom columns for selection
     *
     * @param array $cols
     * @return mixed
     * @author WeSSaM
     */
    public function get(array $cols = ["*"]): mixed
    {
        return $this->query()->get($cols);
    }

    /**
     * retrieve query with pagination
     *
     * @param integer $perPage
     * @return mixed
     * @author WeSSaM
     */
    public function paginate(int $perPage = 10): mixed
    {
        return $this->query()->paginate($perPage);
    }

    /**
     * retrieve lists data
     * if request has no pagination then data will be without paginator
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @author WeSSaM
     */
    public function index(): mixed
    {
        if (request()->get("no_pagination", false))
            return $this->get();
        else
            return $this->paginate(10);
    }

    /**
     * find resource by id
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function find($id): mixed
    {
        return $this->query()->findOrFail($id);
    }


    /**
     * prepare row's data for saving
     * @return mixed
     * @throws UploadingException
     * @author WeSSaM
     */
    public function sanitizeAttributes(): array
    {
        return array_merge(
            request()->only($this->getFillable()),
            $this->handleImageableAttributes()
        );
    }
}
