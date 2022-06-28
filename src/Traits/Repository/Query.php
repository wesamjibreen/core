<?php

namespace Core\Traits\Repository;

trait Query
{
    /**
     * the that used to order records by
     *
     * @author WeSSaM
     * @var string
     */
    protected string $orderedColumn = 'updated_at';

    /**
     * the way records should be ordered by
     *
     * @author WeSSaM
     * @var string
     */
    protected string $orderBy = 'desc';

    /**
     * Column to store ordering index on db
     *
     * @author WeSSaM
     * @var string
     */
    protected string $orderingColumn = "position";

    /**
     * @return string
     */
    public function getOrderedColumn(): string
    {
        return $this->orderedColumn;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderedColumn
     * @return Query
     */
    public function setOrderedColumn(string $orderedColumn): static
    {
        $this->orderedColumn = $orderedColumn;
        return $this;
    }

    /**
     * @param string $orderBy
     * @return static
     */
    public function setOrderBy(string $orderBy): static
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * get teh native query form model
     *
     * @return mixed
     * @author WeSSaM
     */
    protected function __getModelBaseQuery(): mixed
    {
        return $this->modelInstance()->query();
    }


    /**
     * the final query to be performed
     *
     * @author WeSSaM
     */
    protected function query()
    {
        return $this->__loadQuery()->with($this->__loadRelation())->orderBy($this->orderedColumn, $this->orderBy);
    }

    /**
     * get the single model according
     * to the given id
     *
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    protected function findModel($id): mixed
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * merge the custom queries with the
     * model's native query
     *
     * @return mixed
     * @author WeSSaM
     */
    private function __loadQuery(): mixed
    {
        return $this->__getModelBaseQuery()->where(function ($query) {
            $this->baseQuery($query);
            $methodName = $this->getActionMethod() . 'Query';
            if (method_exists($this, $methodName))
                $this->{$methodName}($query);

            $query->search(request());
        });
    }


    /**
     * merge the relation of base relation with
     * method's custom relation
     *
     * @return mixed
     * @author WeSSaM
     */
    private function __loadRelation(): mixed
    {
        $methodName = $this->getActionMethod() . 'With';
        $customWith = [];
        if (method_exists($this, $methodName))
            $customWith = $this->{$methodName}();
        return array_merge($this->with(), $customWith);
    }

    /**
     * function appended with each query
     *
     * @param $query
     * @author WeSSaM
     */
    protected function baseQuery($query): void
    {
    }

    /**
     * function appended with store query
     *
     * @param $query
     * @author WeSSaM
     */
    protected function storeQuery($query): void
    {
    }

    /**
     * function appended with find query
     *
     * @param $query
     * @author WeSSaM
     */
    protected function findQuery($query): void
    {

    }

    /**
     * function appended with update query
     *
     * @param $query
     * @author WeSSaM
     */
    protected function updateQuery($query): void
    {
    }

    /**
     * function appended with delete query
     *
     * @param $query
     * @author WeSSaM
     */
    protected function deleteQuery($query): void
    {
    }

    /**
     * function appended with show query
     *
     * @param $query
     * @author WeSSaM
     */
    protected function showQuery($query): void
    {
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function with(): array
    {
        return [];
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function findWith(): array
    {
        return [];
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function showWith(): array
    {
        return [];
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function storeWith(): array
    {
        return [];
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function updateWith(): array
    {
        return [];
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function deleteWith(): array
    {
        return [];
    }

    /**
     * @return array
     * @author WeSSaM
     */
    protected function indexWith(): array
    {
        return [];
    }

}
