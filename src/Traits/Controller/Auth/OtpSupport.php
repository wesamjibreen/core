<?php

namespace Core\Traits\Controller\Auth;


use Carbon\Carbon;
use Core\Http\Requests\Auth\Otp\SendRequest;
use Core\Http\Requests\Auth\Otp\VerifyRequest;
use Core\Http\Resources\AuthResource;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait OtpSupport
{

    /**
     * @var mixed|string
     * @author WeSSaM
     */
    protected mixed $sendingRequest = SendRequest::class;

    /**
     * @var mixed|string
     * @author WeSSaM
     */
    protected mixed $verifyingRequest = VerifyRequest::class;

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @author WeSSaM
     */
    public function sendOTP()
    {
        if (class_exists($this->sendingRequest))
            app()->make($this->sendingRequest);

        $user = $this->model()->updateOrCreate(array('mobile' => request()->get($this->username())));
        return response()->success(__('Core::messages.otp_verification_sent_successfully'), [
            'otp' => (int)$user->sendOtp()->otp
        ]);
    }


    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @author WeSSaM
     */
    public function verifyOTP()
    {
        if (class_exists($this->verifyingRequest))
            app()->make($this->verifyingRequest);

        $user = $this->model()->where($this->username(), request()->get($this->username()))->authorized()->first();
        if (!$user)
            return response()->error(__("Core::messages.invalid_mobile_number"), 401);


        if ($user->getOtpCode() != request()->get("otp"))
            return response()->error(__("Core::messages.invalid_otp"), 401);

        $user->otp->update(array('verified_at' => Carbon::now()));
        $token = $this->authenticated($this->guard()->login($user));

        return response()->success(__('Core::messages.successfully_logged_in'), [
            'access_token' => $token,
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'token_type' => 'Bearer',
            'auth' => new AuthResource($this->guard()->user()),
        ]);
    }
}
