<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 07/10/16
 * Time: 16:50
 */

namespace Ecfectus\Dotable;


interface DotableInterface
{
    public function set($path, $value, $unset = false) : DotableInterface;

    public function get($path, $default = null);

    public function has($path) : bool;

    public function forget($path) : DotableInterface;

    public function prepend($path, $value) : DotableInterface;

    public function append($path, $value) : DotableInterface;

    public function merge($path, array $value = []) : DotableInterface;

    public function toArray() : array;
}