<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:06 PM
 */

namespace AKK\ModelBundle\Data;


class Pass
{
    /** @var  int */
    public $id;

    /** @var  PassType|null */
    public $type;

    /** @var  User|null */
    public $user;

    /** @var  \DateTime|null */
    public $obtainDate;

    /** @var  \DateTime|null */
    public $validityStartDate;
}