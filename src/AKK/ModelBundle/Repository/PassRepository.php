<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:15 PM
 */

namespace AKK\ModelBundle\Repository;


use AKK\ModelBundle\Data\Pass;
use AKK\ModelBundle\Data\PassType;
use AKK\ModelBundle\Repository\Detail\RepositoryImpl;

class PassRepository
{
    /** @var  RepositoryImpl */
    private $repoImpl;

    public function __construct(RepositoryImpl $repositoryImpl)
    {
        $this->repoImpl = $repositoryImpl;
    }

    public function objectToArray(Pass $pass)
    {
        $result = [
            'id' => $pass->id
        ];
        if($pass->type !== null) $result['pass_type_id'] = $pass->type->id;
        if($pass->user !== null) $result['user_id'] = $pass->user->id;
        if($pass->obtainDate !== null) $result['obtain_date'] = date('Y-m-d H:i:s', $pass->obtainDate);
        if($pass->validityStartDate !== null) $result['validity_start_date'] = date('Y-m-d H:i:s', $pass->validityStartDate);

        return $result;
    }

    public function arrayToObject(array $row): Pass
    {
        $result = new Pass();
        $result->id = $row['id'];
        if(array_key_exists('pass_type_id', $row)) {
            $result->type = new PassType();
            $result->type->id = $row['pass_type_id'];
        }
    }
}