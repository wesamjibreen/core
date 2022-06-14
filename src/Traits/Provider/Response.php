<?php

namespace Core\Traits\Provider;

use Core\Http\Collections\BaseCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use  Illuminate\Support\Facades\Response as ResponseFacades;

trait Response
{


    /**
     * Add macro function to Response class
     * @author WeSSaM
     */
    function responseMacro()
    {
        /**
         * global success response
         *
         * @author WeSSaM
         */
        ResponseFacades::macro('success', function ($message = '', $data = [], $with = []) {
            $payload = array('status' => true, 'code' => 200, 'message' => $message);

            if ($data instanceof LengthAwarePaginator) $payload = array_merge($payload, load_resource_pagination($data));
            else  $payload = array_merge($payload, array('payload' => $data));

            return ResponseFacades::json(array_merge($payload, array('with' => $with)))
                ->header('Content-Type', 'application/json')->setStatusCode(200, $message);
        });

        /**
         * global error response
         *
         * @author WeSSaM
         */
        ResponseFacades::macro('error', function ($message = '', $code = null, $errors = []) {
            return ResponseFacades::json([
                'status' => false,
                'code' => $code,
                'message' => $message,
                'errors' => $errors,
            ])->header('Content-Type', 'application/json');
        });

    }

}
