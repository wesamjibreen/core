<?php

namespace Core\Traits\Repository;

use Core\Exceptions\UploadingException;
use Core\Services\ImageService as ImageManager;

trait Imageable
{

    /**
     * image service's instance used to handle uploading images
     * @var ImageManager
     * @author WeSSaM
     */
    protected ImageManager $imageManager;


    /**
     * uploading imageable attributes from request if images exists
     * else => previous images doesn't effected
     * @return array
     * @throws UploadingException
     * @author WeSSaM
     */
    public function handleImageableAttributes(): array
    {
        $imageable = [];
        collect($this->getImageable())->each(function ($attribute) use (&$imageable) {
            if (request()->hasFile($attribute)) {
                $image = $this->imageManager->upload(request()->$attribute);
                $imageable[$attribute] = $image->file_name;
            } elseif (request()->$attribute) $imageable[$attribute] = request()->$attribute;
        });
        return $imageable;
    }
}
