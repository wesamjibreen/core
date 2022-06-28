<?php

namespace Core\Traits\Controller;

use Core\Repositories\BaseRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

trait Repository
{

    /**
     * @var $repository
     * @author WeSSaM
     */
    protected $repository;


    /**
     * @return Repository
     * @author WeSSaM
     */
    function initRepository(): static
    {
        $this->setRepository(
            (new $this->repository)
                ->setModel($this->model)
                ->init($this->repositoryConfig));

        return $this;
    }

    /**
     * @param mixed $repository
     * @return Repository
     * @author WeSSaM
     */
    public function setRepository(mixed $repository): static
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return mixed
     * @author WeSSaM
     */
    public function getRepository(): mixed
    {
        return $this->repository;
    }
}
