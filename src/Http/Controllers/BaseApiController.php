<?php

namespace Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Traits\CrudHooks as Hooks;
use Core\Traits\Base;
use Core\Traits\Controller\Repository;
use Core\Traits\Controller\Request;
use Core\Traits\Controller\Resource;
use Core\Traits\Model;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;


class BaseApiController extends Controller
{
    use Model,
        Resource,
        Request,
        Repository,
        Base,
        Hooks;

    /**
     * BaseApiController Constructor
     * @author WeSSaM
     */
    public function __construct()
    {
        $this->settingDependencies();
        $this->initRepository();;
    }

    /**
     * resolving controller's dependencies classes ( model , resource , repository , request )
     * @author WeSSaM
     */
    public function settingDependencies()
    {
        collect($this->classes)->each(function ($item) {
            $this->conditionalSet(lcfirst($item), $this->buildClass(lcfirst($item)));
        });
    }

    /**
     * use get class magic method
     * @author WeSSaM
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name))
            return $this->$name;
    }


    /**
     * storing new record in resource's table
     * request validations automatically will be invoked
     *
     * @return JsonResponse
     * @throws BindingResolutionException
     * @author WeSSaM
     */
    public function store(): JsonResponse
    {
        $this->callValidationRequest();
        $this->creating();
        $model = $this->created($this->repository->store());
        return $this->getResponse($model);
    }

    /**
     * return resource data for edit
     *
     * @param $id
     * @return JsonResponse
     * @author WeSSaM
     */
    public function edit($id): JsonResponse
    {
        $model = $this->repository->find($id);
        return $this->getResponse($model);
    }

    /**
     * updating record in resource's table by id
     * request validations automatically will be invoked
     *
     * @param $id
     * @return JsonResponse
     * @throws BindingResolutionException
     * @author WeSSaM
     */
    public function update($id): JsonResponse
    {
        $this->callValidationRequest();
        $this->updating($id);
        $model = $this->updated($this->repository->update($id));
        return $this->getResponse($model);
    }


    /**
     * Handle calls to missing methods on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (!method_exists($this->repository, $method))
            parent::__call($method, $parameters);

        $payload = $this->repository->{$method}(...$parameters);
        return $this->getResponse($payload);
    }
}
