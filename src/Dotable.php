<?php
namespace Ecfectus\Dotable;

use ArrayAccess;
use JsonSerializable;

/**
 * Class Dotable
 * @package Ecfectus
 */
class Dotable implements DotableInterface, ArrayAccess, JsonSerializable
{
    use DotableTrait;
}
