<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:15 PM
 */

namespace AKK\ModelBundle\Repository;


use AKK\ModelBundle\Repository\Detail\RepositoryImpl;

class UserRepository
{
    /** @var  RepositoryImpl */
    private $repoImpl;

    public function __construct(RepositoryImpl $repositoryImpl)
    {
        $this->repoImpl = $repositoryImpl;
    }
}