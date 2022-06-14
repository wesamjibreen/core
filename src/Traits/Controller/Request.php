<?php

namespace Core\Traits\Controller;


use Illuminate\Contracts\Container\BindingResolutionException;

trait Request
{
    /**
     * CRUD's json request class
     * @author WeSSaM
     */
    protected $request;


    /**
     * making new instance of request's class using app functions to validate requests rules
     *
     * @return Request
     * @throws BindingResolutionException
     * @author WeSSaM
     */
    public function callValidationRequest()
    {
        app()->make($this->getRequest());
    }


    /**
     * get request class
     *
     * @return mixed
     * @author WeSSaM
     */
    public function getRequest(): mixed
    {
        if ($this->request) return $this->request;
        return $this->buildClass("request");
    }

    /**
     * set new request class
     *
     * @param $request
     * @return Request
     * @author WeSSaM
     */
    public function setRequest($request): static
    {
        $this->request = $request;
        return $this;
    }




}
