<?php
namespace Ecfectus\Dotable;

use ArrayAccess;
use IteratorAggregate;
use Countable;
use JsonSerializable;

/**
 * Class Dotable
 * @package Ecfectus
 */
class Dotable implements DotableInterface, ArrayAccess, IteratorAggregate, Countable, JsonSerializable
{
    use DotableTrait;
}
