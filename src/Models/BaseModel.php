<?php

namespace Core\Models;

use Core\Traits\Model\Imageable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Translatable\HasTranslations;

class BaseModel extends Model
{
    use HasTranslations, Imageable;

    /**
     * @var array
     */
    public array $translatable = [];


    /**
     * @param $q
     * @param Request $request
     * @return void
     */
    public function scopeSearch($q, Request $request): void
    {

    }

}
