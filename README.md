# Time duration class

Duration is a class to abstract time durations

## Installation

    composer require duration/duration-php

## Usage

    require __DIR__ . '/vendor/autoload.php';
    use Duration\Duration;
    $d = Duration::parse("1h59m59s999ms999us999ns");
    $d->add(Duration::parse("1ns"));
    echo $d; // prints "2h"

## Methods

- **Duration::parse()**: creates new instance by parsing string like [Go](https://pkg.go.dev/time#Duration)


    $d = Duration::parse("+1h59m59s999ms999us999ns")


- **new / constructor**: creates new instance


    $d = new Duration([nanoseconds=0])


- **add(Duration)**: adds duration to `$d`


    $q = Duration::parse("1m");
    $d->add($q) -> Duration


- **sub(Duration)**: subtracts duration from `$d`


    $q = Duration::parse("1m");
    $d->sub($q) -> Duration

- **truncate(Duration)**: rounds to nearest duration


    $q = Duration::parse("5m");
    $d->truncate($q) -> Duration


- **abs()**: returns absolute duration

    Note: same as [Go's time#Duration.Abs()](https://pkg.go.dev/time#Duration.Abs), abs of duration for `PHP_MIN_INT` returns duration for `PHP_MAX_INT` and viceversa.


    $d->abs() -> Duration


## Time duration properties

| method               | returns |
|----------------------|---------|
| `$d->hours()`        | float   |
| `$d->minutes()`      | float   |
| `$d->seconds()`      | float   |
| `$d->microseconds()` | float   |
| `$d->nanoseconds()`  | int     |

## Makefile

- `make test` runs unit tests
