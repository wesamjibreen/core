<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\UrlGenerator;

if (!function_exists('image_url')) {

    /**
     * returns image full url to preview
     * if size exists => full url will become with size
     * if not => default image returns
     * you may need to determine particular folder
     *
     * @param string $fileName
     * @param string|null $size
     * @param string|null $folder
     * @return string|UrlGenerator
     */
    function image_url(string $fileName, string $size = null, string $folder = null): string|UrlGenerator
    {
        return url("/storage/uploads/images/$fileName");
//        return url("image/$fileName");
    }
}


if (!function_exists('model_class')) {
    /**
     * return model class according to configured model's namespace
     *
     * @param $name
     * @return string
     * @author WeSSaM
     */
    function model_class($name): string
    {
        return config("core.namespaces.app.model") . '\\' . $name;
    }
}


function load_resource_pagination(\Illuminate\Pagination\LengthAwarePaginator $paginator, $resourceClass = null): array
{
    $data = $paginator->getCollection();
    if ($resourceClass != null) {
        $data = $resourceClass::collection($paginator->getCollection());
    }
    $result['payload'] = $data;
    $temp = $paginator->toArray();
    unset($temp['data']);
    $result['paginator'] = $temp;

    return $result;
}


if (!function_exists('sanitize_validation_errors')) {
    /**
     * prepared validation errors with single message
     * to be returned in response
     *
     * @author WeSSaM
     * @params $errors
     */
    function sanitize_validation_errors($errors)
    {
        $array = [];
        collect($errors)->each(function ($error, $key) use (&$array) {
            $array[$key] = $error[0];
        });
        return $array;
    }
}

if (!function_exists('is_base64')) {

    /**
     * @param $s
     * @return bool
     */
    function is_base64($s)
    {
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($s, true);
        if (false === $decoded) return false;

        // if string returned contains not printable chars
        if (0 < preg_match('/((?![[:graph:]])(?!\s)(?!\p{L}))./', $decoded, $matched)) return false;

        // Encode the string again
        if (base64_encode($decoded) != $s) return false;

        return true;
    }
}


if (!function_exists('get_model_relations')) {

    /**
     * Identify all relationships for a given model
     *
     * @param Model $model
     * @return  array
     */
    function get_model_relations(Model $model, $heritage = 'all')
    {
        $modelName = get_class($model);
        $types = ['children' => 'Has', 'parents' => 'Belongs', 'all' => ''];
        $heritage = in_array($heritage, array_keys($types)) ? $heritage : 'all';

        $reflectionClass = new \ReflectionClass($model);
        $traits = $reflectionClass->getTraits();    // Use this to omit trait methods

        $traitMethodNames = [];
        foreach ($traits as $name => $trait) {
            $traitMethods = $trait->getMethods();
            foreach ($traitMethods as $traitMethod) {
                $traitMethodNames[] = $traitMethod->getName();
            }
        }
        // Checking the return value actually requires executing the method.  So use this to avoid infinite recursion.
        $currentMethod = collect(explode('::', __METHOD__))->last();
        $filter = $types[$heritage];
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);  // The method must be public
        $methods = collect($methods)->filter(function ($method) use ($modelName, $traitMethodNames, $currentMethod) {
            $methodName = $method->getName();
            if (!in_array($methodName, $traitMethodNames)   //The method must not originate in a trait
                && strpos($methodName, '__') !== 0  //It must not be a magic method
                && $method->class === $modelName    //It must be in the self scope and not inherited
                && !$method->isStatic() //It must be in the this scope and not static
                && $methodName != $currentMethod    //It must not be an override of this one
            ) {
                $parameters = (new \ReflectionMethod($modelName, $methodName))->getParameters();
                return collect($parameters)->filter(function ($parameter) {
                    return !$parameter->isOptional();   // The method must have no required parameters
                })->isEmpty();  // If required parameters exist, this will be false and omit this method
            }
            return false;
        })->mapWithKeys(function ($method) use ($model, $filter) {
            $methodName = $method->getName();
            $relation = $model->$methodName();  //Must return a Relation child. This is why we only want to do this once
            if (is_subclass_of($relation, \Illuminate\Database\Eloquent\Relations\Relation::class)) {
                $type = (new \ReflectionClass($relation))->getShortName();  //If relation is of the desired heritage
                if (!$filter || strpos($type, $filter) === 0) {
                    return [$methodName => $type];
                }
            }
            return false;   // Remove elements reflecting methods that do not have the desired return type
        })->toArray();


        return $methods;
    }

}
