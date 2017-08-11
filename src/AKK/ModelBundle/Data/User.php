<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/8/17
 * Time: 11:38 PM
 */

namespace AKK\ModelBundle\Data;


class User
{
    /** @var  int */
    public $id;

    /** @var  string|null */
    public $username;

    /** @var  string|null */
    public $password;

    /** @var  string|null */
    public $email;

    /** @var  string|null */
    public $firstName;

    /** @var  string|null */
    public $lastName;
}