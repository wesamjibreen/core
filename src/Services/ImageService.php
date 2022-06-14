<?php

namespace Core\Services;

use Core\Exceptions\DeleteException;
use Core\Exceptions\UploadingException;
use Core\Models\Image;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Exceptions\UploadErrorException;

class ImageService
{

    /**
     * @var string
     * @author WeSSaM
     */
    protected string $disk = "public";


    /**
     * @var string
     * @author WeSSaM
     */
    protected string $path = "uploads";


    /**
     * @var string
     * @author WeSSaM
     */
    protected string $folder = "images";


    /**
     * @param UploadedFile $image
     * @throws UploadingException
     * @author WeSSaM
     */
    public function upload($image)
    {
        if ($image instanceof UploadedFile)
            $data = $this->fileUpload($image);
        else if (is_base64($image))
            $data = $this->base64Upload($image);
        else return null;

        return $this->saveToModel($data);
    }

    public function base64Upload(): array
    {
        return [];
    }

    /**
     * @throws UploadingException
     */
    public function fileUpload($image): array
    {
        $extension = $image->getClientOriginalExtension();

        $filename = $this->createUniqueFilename($extension);

        $uploadSuccess1 = $this->original($image, $filename);
        $originalName = str_replace('.' . $extension, '', $image->getClientOriginalName());

        if (!$uploadSuccess1)
            throw  new UploadingException(__('Core::messages.uploading_error_exception'), 500);

        return [
            'file_name' => $filename,
            'display_name' => $originalName,
            'size' => $image->getSize(),
            'extension' => $extension
        ];
    }

    /**
     * Storing new record with data for uploaded image
     *
     * @param $data
     * @return mixed
     * @author WeSSaM
     */
    public function saveToModel($data): mixed
    {
        return Image::create($data);
    }

    /**
     * Optimize Original Image
     * @param $image
     * @param $filename
     * @return
     */
    public function original($image, $filename)
    {
        return $image->storeAs("public/$this->path/$this->folder", $filename);
    }

    /**
     * @param $extension
     * @return string
     */
    public function createUniqueFilename($extension)
    {
        return 'image_' . time() . mt_rand() . '.' . $extension;
    }

    /**
     * Remove from storage according to parsed filename
     *
     * @param $filename
     * @return bool
     * @throws UploadingException
     * @author Omar
     */
    public function removeFromStorage($filename): bool
    {
        $fullPath = "$this->path/$this->folder/$filename";
        if ( !$this->storage()->exists($fullPath))
            throw  new UploadingException(__('Core::messages.delete_storage_error_exception'), 500);
        return $this->storage()->delete($fullPath);
    }


    /**
     * Get storage's instance based on current disk value
     *
     * @return Filesystem
     * @author Omar
     */
    public function storage(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    /**
     * @param string $folder
     * @return ImageService
     */
    public function setFolder(string $folder): ImageService
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }
}
