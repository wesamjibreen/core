<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\UrlGenerator;

class Image extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * model's attributes
     * @var string[]
     * @author WeSSaM
     */
    protected $fillable = [
        'display_name',
        'file_name',
        'extension',
        'size'
    ];

    /**
     * return image's full url
     *
     * @return UrlGenerator
     * @author WeSSaM
     */
    public function getImageUrlAttribute(): UrlGenerator
    {
        return image_url($this->file_name);
    }
    public function imageable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('model');
    }
}
