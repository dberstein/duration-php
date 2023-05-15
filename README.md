# Time duration class

Duration is a class to abstract time durations

## Installation

    composer require duration/duration-php

## Usage

    use Duration;
    $d = Duration::parse("1h59m59s999ms999us999ns");
    $d->add(Duration::parse("1ns"));
    echo $d; // prints "2h"

## Methods

- $d = Duration::parse(`string`)

    Creates new instance by parsing string like [Go](https://pkg.go.dev/time#Duration)


- $d = new Duration(`nanoseconds`)

    Creates new instance


- $d->add(`duration`) -> Duration

  Adds `duration` to `$d`


- $d->sub(`duration`) -> Duration:

    Subtracts `duration` from `$d`


- $d->truncate(`duration`) -> Duration:

    Rounds to nearest `duration`


- $d->abs() -> Duration:

    Absolute duration


## Time duration properties
- $d->nanoseconds() -> int
- $d->microseconds() -> float
- $d->seconds() -> float
- $d->minutes() -> float
- $d->hours() -> float

## Makefile

- `make test` runs unit tests
