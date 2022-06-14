<?php


namespace Core\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = collect($this->resource->toArray())->only($this->getFillable());
        collect($this->getImageable())->each(function ($attribute) use (&$data) {
            $data[$attribute . "_url"] = image_url($data[$attribute]);
        });
        return $data;
    }
}
