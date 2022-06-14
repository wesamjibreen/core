<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 13/4/2022
 * Time: 12:38 م
 */

namespace Core\Interfaces;

interface RepositoryInterface
{
    /**
     * fetch all resources
     *
     * @author WeSSaM
     * @return mixed
     */
    public function index();

    /**
     * find specific resource
     *
     * @author WeSSaM
     * @param $id
     * @return mixed
     */
    public function show($id);


    /**
     * create resource's record
     *
     * @author WeSSaM
     * @return mixed
     */
    public function store();

    /**
     * update resource's data
     *
     * @author WeSSaM
     * @param $id
     * @return mixed
     */
    public function update($id);

    /**
     * delete resource's data
     *
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function destroy($id);
}
