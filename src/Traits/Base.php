<?php

namespace Core\Traits;

use Illuminate\Support\Facades\Route;

trait Base
{
    /**
     * classes types array
     *
     * @var array|string[]
     * @author WeSSaM
     */
    protected array $classes = [
        "Controller",
        "Resource",
        "Request",
        "Repository",
        "Model"
    ];

    protected string $resourceName = "";

    /**
     * set new value if property exists
     *
     * @param $attribute
     * @param $value
     * @return Base
     * @author WeSSaM
     */
    public function conditionalSet($attribute, $value): static
    {
        if (!$this->{$attribute}) $this->{$attribute} = $value;
        return $this;
    }

    /**
     * get current route method
     *
     * @return string|null
     * @author WeSSaM
     */
    public function getActionMethod(): ?string
    {
        return Route::current()?->getActionMethod();
    }

    /**
     * get response message based on current action method
     *
     * @return string|null
     * @author WeSSaM
     */
    public function getActionMessage(): ?string
    {
        $method = $this->getActionMethod();
        return __("Core::messages." . $method . "_successfully", ['resource' => trans($this->resourceName())]);
    }

    /**
     * making new instance of reflection class
     *
     * @return \ReflectionClass
     * @return mixed
     * @author WeSSaM
     */
    public function reflectionClass(): \ReflectionClass
    {
        return new \ReflectionClass($this);
    }

    /**
     * return short class name
     *
     * @return string
     * @author WeSSaM
     */
    private function classShortName(): string
    {
        return $this->reflectionClass()->getShortName();
    }

    /**
     * @return string
     * @author WeSSaM
     */
    public function getQueryMethodName(): string
    {
        return $this->getActionMethod() . "Query";
    }

    /**
     * build predicted class path based on current module name and parsed type
     *
     * @param $type
     * @param null $resourceName
     * @return mixed
     * @author WeSSaM
     */
    public function buildClass($type, $resourceName = null): mixed
    {
        $resourceName = $resourceName ?? $this->resourceName();
        $resourceClass = $type != "model" ? $resourceName . ucfirst($type) : $this->resourceName();

        $moduleNamespace = config("core.namespaces.module.$type");
        if ($moduleNamespace)
            $moduleNamespace = str_replace('$MODULE$', $this->getModuleName(), $moduleNamespace);


        if ($type == "request") {
            $fullClass = $this->actionRequestClass($type, $moduleNamespace);
            if (class_exists($fullClass))
                return $fullClass;
        }


        $fullClass = $moduleNamespace . '\\' . $resourceClass;
        if (class_exists($fullClass))
            return $fullClass;


        $appNamespace = config("core.namespaces.app.$type");
        $fullClass = $appNamespace . '\\' . $resourceClass;
        if (class_exists($fullClass))
            return $fullClass;

        return config("core.default.$type");
    }

    /**
     * predicate request class according to action method (store or update_
     *
     * @param $type
     * @param $namespace
     * @return string
     * @author WeSSaM
     */
    public function actionRequestClass($type, $namespace): string
    {
        return $namespace . '\\' . $this->resourceName() . '\\' . ucfirst($this->getActionMethod()) . ucfirst($type);
    }

    /**
     * get resource name according to class's model
     * @return string
     * @author WeSSaM
     */
    public function resourceName(): string
    {
        if ($this->resourceName) return $this->resourceName;
        return $this->resourceName = str_replace($this->classes, "", $this->classShortName());
    }


    /**
     * get current module name from class's namespace
     *
     * @return string
     * @author WeSSaM
     */
    public function getModuleName(): string
    {
        $namespacePieces = explode("\\", $this->reflectionClass()->getNamespaceName());
        return in_array("Modules", $namespacePieces) ? $namespacePieces[1] : "app";
    }


    /**
     * return json response if there's no exceptions
     * parsing $payload to suitable resource
     *
     * @param $payload
     * @return mixed
     * @author WeSSaM
     */
    public function getResponse($payload): mixed
    {
        return response()->success(
            $this->getActionMessage(),
            $this->sanitizeToResource($payload)
        );
    }

    /**
     * @param string $resourceName
     */
    public function setResourceName(string $resourceName)
    {
        $this->resourceName = $resourceName;
        return $this;
    }
}
