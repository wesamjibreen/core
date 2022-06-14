<?php

namespace Core\Models;


use Carbon\Carbon;

class Otp extends BaseModel
{
    /**
     * model's attributes
     * @var string[]
     * @author WeSSaM
     */
    protected $fillable = [
        'username',
        'model_type',
        'model_id',
        'otp',
        'verified_at',
        'expired_at',
    ];


    /**
     * @param $q
     * @return mixed
     */
    public function scopeActive($q)
    {
        return $q->whereNull("verified_at")
            ->orderBy("id", "DESC");
//            ->where("expired_at", "<=", Carbon::now()->subMinutes(15));
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo("model");
    }
}
