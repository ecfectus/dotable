# Dotable

[![Build Status](https://travis-ci.org/ecfectus/dotable.svg?branch=master)](https://travis-ci.org/ecfectus/dotable)

A simple dot notation accessible array class and trait, implementing DotableInterface, ArrayAccess, IteratorAggregate, Countable, JsonSerializable.

This class can turn normal arrays into super arrays with dot notation, you can use the helper methods `set`, `get`, `has`, `forget`, `prepend`, `append`, `merge`, and `count` to modify the underlying array.
Or You can access it just like any other array using `foreach`, `isset`, ``unset`, `count`, and `$array['dot.notation.key']`.

## Usage

```php
$d = new Dotable([]);

//these are both the same.
$d['one'] = ['two' => ['three' => 1], 'four' => ['val']];
===
$d->set('one', ['two' => ['three' => 1], 'four' => ['val']);

//and these
$var = $d['one.two.three'];
===
$var = $d->get('one.two.three', 1);//optional default value

//and these
$d['one.two.three'] = 2;
===
$d->set('one.two.three', 2);

//existence is the same
isset($d['one.two.three']);//true
isset($d['one.two.five']);//false
===
$d->has('one.two.three');//true
$d->has('one.two.five');//false

//and removal
unset($d['one.two.three']);
===
$d->forget('one.two.three');

//prepend
$v = $d['one.four'];
array_unshift($v, 'val2');
$d['one.four'] = $v
===
$d->prepend('one.four', 'val2');

//append
$v = $d['one.four'];
$v[] = 'val2';
$d['one.four'] = $v
===
$d->append('one.four', 'val2');

//merge
@todo

//count
count($d);
===
$d->count();

//get a plain array
$d->toArray();

//loop over the array
foreach($d as $key => $value){}
===
foreach($d->toArray() as $key => $value){}
```

## Using the trait

The dotable class simply uses the trait provided and implements all the interfaces: DotableInterface, ArrayAccess, IteratorAggregate, Countable, JsonSerializable.

The full functionality can be used in a different class not extended from the Dotable class.

```php
class MyClass extends Dotable implements DotableInterface, ArrayAccess, IteratorAggregate, Countable, JsonSerializable
{}

//or

class MyClass implements DotableInterface, ArrayAccess, IteratorAggregate, Countable, JsonSerializable
{
    use DotableTrait;

    public function __construct(array $items = [])
    {
        $this->set('', $items);//set the items using the traits method on the root index.
    }
}
```

Now `MyClass` has all the features of dotable without having to extends it.
Bear in mind if you provide the same methods in your class that overwrite the trait methods (including the implemented interface methods) you will loose functionality.
To ensure its maintained you will need to rewrite the method signatures when using the trait as explained in the php docs: http://php.net/manual/en/language.oop5.traits.php #example 6.
