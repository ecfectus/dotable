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
    public function set(string $path = '', $value, $unset = false) : DotableInterface;

    public function get(string $path = '', $default = null);

    public function has(string $path = '') : bool;

    public function prepend(string $path = '', $value) : DotableInterface;

    public function append(string $path = '', $value) : DotableInterface;

    public function merge(string $path = '', array $value = []) : DotableInterface;

    public function toArray() : array;
}