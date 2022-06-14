<?php

namespace Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Exceptions\ModelNotFoundException;
use Core\Exceptions\UploadingException;
use Core\Http\Requests\ImageRequest;
use Core\Models\Image;
use Core\Services\ImageService;

class BaseImageController extends Controller
{

    /**
     * @var ImageService
     */
    protected ImageService $imageService;

    /**
     * @param ImageService $imageService
     * @author WeSSaM
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {

    }

    /**
     * @param ImageRequest $request
     * @return mixed
     * @throws UploadingException
     */
    public function store(ImageRequest $request): mixed
    {
        if (!$request->hasFile("image"))
            throw new UploadingException;

        $uploaded = $this->imageService->upload($request->image);
        return response()->success(__("Core::messages.image_uploaded_successfully"), $uploaded);
    }

    public function show($id)
    {

    }

    public function destroy($id)
    {

        $image = Image::where('id', $id)->orWhere('file_name', $id)->first();
        if (!$image)
            throw new ModelNotFoundException(__("Core::messages.model_not_found"), 500);

        $this->imageService->removeFromStorage($image->file_name);
        $image->delete();
        return response()->success(__("Core::messages.image_deleted_successfully"), []);


    }
}
