<?php

namespace Core\Traits\Provider;

use Illuminate\Support\Facades\Route;

trait Routing
{
    /**
     * injecting resource macro function to generate custom restfull CRUD
     * @author WeSSaM
     */
    public function resourceRoutes()
    {
        Route::macro('dashboardResource', function ($resource, $controller, $function = null) {
            /**
             * Generate resource default rest-full routes
             *
             * @param $resource
             * @param string $controller
             * @return string
             * @author WeSSaM
             */
            Route::resource($resource, $controller);
            Route::patch($resource . '/delete/group', $controller . '@deleteGroup');
            Route::match(["put", "patch"], "$resource/{id}/status", "$controller@updateStatus");
            Route::match(["put", "post"], "$resource/ordering", "$controller@ordering");
            if (is_callable($function))
                Route::group(['prefix' => $resource], function () use ($function, $controller, $resource) {
                    call_user_func($function, $controller, $resource);
                });
            return $this;
        });
    }

    /**
     * injecting generated auth routes according to particular module
     * @author WeSSaM
     */
    public function authApiRoutes()
    {
        Route::macro('authApiRoutes', function ($controller = "AuthController", $withOtp = false) {
            /**
             * Generate module authentication routes
             * Default controller AuthController
             *
             * @param string $controller
             * @return string
             * @author WeSSaM
             */
            Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () use ($controller, $withOtp) {
                if ($withOtp) {
                    Route::post('otp/send', ['as' => 'otp.send', 'uses' => "$controller@sendOTP"]);
                    Route::post('otp/verify', ['as' => 'otp.verify', 'uses' => "$controller@verifyOTP"]);
                    Route::match(['patch', 'put'], 'update', ['as' => 'update', 'uses' => "$controller@updateAuthenticatedModel"]);
//                        ->middleware("auth");
                } else {
                    Route::post('login', ['as' => 'login', 'uses' => "$controller@login"]);
                    Route::get('logout', ['as' => 'logout', 'uses' => "$controller@logout"]);
                    Route::get('refresh', ['as' => 'refresh', 'uses' => "$controller@refresh"]);
                }

            });
            return $this;
        });
    }


    /**
     * injecting uploading routes's function
     *
     * @author WeSSaM
     */
    public function uploadingRoutes()
    {
        Route::macro('uploadingRoutes', function ($controller = 'BaseImageController', $middleware = "") {
            /**
             * Generate module uploading routes
             * Default controller AttachmentController
             *
             * @param string $controller
             * @return string
             * @author WeSSaM
             */
//            Route::group([ 'middleware' => $middleware], function () use ($controller) {
//                dd($controller);
            Route::resource('image', $controller);
//                Route::post('imageUploadBase64', ['as' => 'image', 'uses' => "$controller@imageUploadBase64"]);
//                Route::post('file', ['as' => 'file', 'uses' => "$controller@fileUpload"]);
//            });
        });
    }

}
