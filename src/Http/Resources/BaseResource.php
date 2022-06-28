<?php


namespace Core\Http\Resources;

use Illuminate\Contracts\Support\Arrayable as ArrayableAlias;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\isReadable;

class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * @return array
     * @author WeSSaM
     */
    public function index()
    {
        $data = array('id' => $this->id);
        collect($this->fillable())->each(function ($attribute) use (&$data) {
            // if (in_array($attribute,$this->translatable()))
            $data[$attribute] = $this->{$attribute};
            if (in_array($attribute, $this->imageable()) && $value = $this->{$attribute}) $data[$attribute . "_url"] = image_url($value);
        });
        return $data;
    }

    /**
     * @return array
     * @author WeSSaM
     */
    public function show()
    {
        return $this->index();
    }


    /**
     * @return array
     * @author WeSSaM
     */
    public function edit()
    {
        return parent::toArray(request());
    }

    /**
     * @return array
     * @author WeSSaM
     */
    public function update()
    {
        return $this->edit();
    }

    /**
     * @return array
     * @author WeSSaM
     */
    public function store(): array
    {
        return $this->edit();
    }

    /**
     * @return array
     * @author WeSSaM
     */
    public function destroy(): array
    {
        return $this->show();
    }

    /**
     * @return array
     * @author WeSSaM
     */
    public function imageable(): array
    {
        return $this->resource->getImageable() ?? [];
    }


    /**
     * @return array
     * @author WeSSaM
     */
    public function translatable(): array
    {
        return $this->resource->getTranslatable() ?? [];
    }


    /**
     * @return array
     * @author WeSSaM
     */
    public function fillable(): array
    {
        return $this->resource->getFillable() ?? [];
    }


    public function __call($method, $parameters)
    {
        return $this->toArray(request());
    }
}
