<?php

namespace Core\Traits\Model;

trait Imageable
{
    /**
     * @var array
     * @author WeSSaM
     */
    protected array $imageable = [];

    /**
     * @return array
     */
    public function getImageable(): array
    {
        return $this->imageable;
    }

    /**
     * @param array $imageable
     * @return Imageable
     */
    public function setImageable(array $imageable): Imageable
    {
        $this->imageable = $imageable;
        return $this;
    }
}
