<?php

namespace Core\Models;

use Core\Traits\Model\Imageable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AuthenticationModel extends Authenticatable implements JWTSubject
{
    use Imageable;

    /**
     * model's attributes
     * @var string[]
     * @author WeSSaM
     */
    protected $guarded = [];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * filter by active users only
     *
     * @param $q
     * @return mixed
     */
    public function scopeAuthorized($q): mixed
    {
        return $q;
    }
}
