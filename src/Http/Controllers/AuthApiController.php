<?php

namespace Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Exceptions\ModelNotSupportedException;
use Core\Http\Resources\AuthResource;
use Core\Models\AuthenticationModel;
use Core\Repositories\BaseRepository;
use Core\Traits\Base;
use Core\Traits\Controller\Auth\OtpSupport;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use ReflectionClass;

class AuthApiController extends Controller
{
    use Base, OtpSupport;

    /**
     * @var string
     * @author WeSSaM
     */
    protected string $model = AuthenticationModel::class;

    /**
     * @var string
     * @author WeSSaM
     */
    protected string $guard = "";

    /**
     * @var $resource
     * @author WeSSaM
     */
    protected $resource;


    /**
     * AuthApiController Constructor
     *
     * @author WeSSaM
     */
    public function __construct()
    {
//        if (!$this->model)
//            $this->model = $this->__predicateModel();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws AuthenticationException
     * @author WeSSaM
     */
    public function login(Request $request): mixed
    {
        $credentials = $request->only([$this->username(), 'password']);

        config()->set('jwt.user', $this->getModel()); // change jwt user @author WeSSaM
        config()->set('auth.providers.users.model', $this->getModel()); // load the user model @author WeSSaM

        if (!$token = $this->guard()->attempt($credentials))
            throw new AuthenticationException(__('Core::messages.invalid_credential'));

        $this->authenticated($token);

        return response()->success(__('Core::messages.successfully_logged_in'), [
            'access_token' => $token,
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'token_type' => 'Bearer',
            'auth' => new $this->resource($this->guard()->user()),
        ]);
    }


    /**
     * @return string
     * @author WeSSaM
     */
    public function username(): string
    {
        return "email";
    }


    /**
     * @return Guard|StatefulGuard|Application|Factory
     * @throws \ReflectionException
     * @author WeSSaM
     */
    public function guard(): Guard|StatefulGuard|Application|Factory
    {
        return auth($this->getGuardName());
    }

//
//    /**
//     * @return string
//     * @author WeSSaM
//     */
//    public function __GuardName(): string
//    {
//        return $this->guard ?? $this->getGuardName();
//    }
//
//
//    public function __defaultGuardName(): string
//    {
//        $reflect = (new ReflectionClass($this->getModel()))->getShortName();
//        return lcfirst($reflect) . "-api";
//    }

    /**
     * returns predicted guard name if there's no default value
     * @return string
     * @throws \ReflectionException
     */
    public function getGuardName(): string
    {
        if ($this->guard) return $this->guard;
        return implode("-", array(lcfirst($this->resourceName()), "api"));
    }

    public function model(): mixed
    {
        return $this->getModel()::query();
    }

    public function update()
    {
        $user = $this->guard()->user();
    }


    public function updateAuthenticatedModel()
    {
        $model = (new BaseRepository(User::class))->update($this->guard()->id());
        return response()->success(__('Core::messages.saved_successfully'), [
            'auth' => new AuthResource($model),
        ]);
    }

    /**
     * this method can be overwritten for customization
     * @param $token
     * @return Authenticatable
     * @author WeSSaM
     */
    public function authenticated($token): Authenticatable
    {
        return $this->guard()->user();
    }


    /**
     * return model class
     * check if property exists then return its value
     * otherwise prediction method going to be called
     * @return string
     * @author WeSSaM
     */
    public function getModel(): string
    {
        if ($this->model && $this->model != AuthenticationModel::class) return $this->model;
        return $this->__predicateModel();
    }

    /**
     * returns predicted model class for authentication
     * prediction will be based on current module name
     * default will be User::class
     *
     * @return string
     * @throws ModelNotSupportedException
     */
    public function __predicateModel(): string
    {
        $model = $this->setResourceName($this->getModuleName())
            ->buildClass("model");
        if (class_exists($model)) return $model;

        throw new ModelNotSupportedException;
    }

}
