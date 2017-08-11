<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:04 PM
 */

namespace AKK\ModelBundle\Data;


class PassType
{
    /** @var  int */
    public $id;

    /** @var  string|null */
    public $name;

    /** @var  string|null */
    public $displayName;

    /** @var  int|null */
    public $priceHuf;

    /** @var  \DateInterval|null */
    public $validity;
}