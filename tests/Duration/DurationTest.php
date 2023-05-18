<?php


namespace Duration;

require __DIR__ . '/../../vendor/autoload.php';

use InvalidArgumentException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    static protected array $cases = [
        " +1m6s833ms500us33ns" => 66833500033,
        "     +333ms500us33ns" => 333500033,
        "       +1h1m1ms999ns" => 3660001000999,
        "           +2h6m25ns" => 7560000000025,
        "              +2h30m" => 9000000000000,
        "            +58m33ns" => 3480000000033,
        "            +5us33ns" => 5033,
        "                  0s" => 0,
        "            -5us33ns" => -5033,
        "       -1h1m1ms999ns" => -3660001000999,
        " -1m6s833ms500us33ns" => -66833500033,
    ];

    public function test_constructor()
    {
        $d = new Duration();
        $this->assertEquals(0, $d->nanoseconds());

        $d = new Duration(100);
        $this->assertEquals(100, $d->nanoseconds());

        $cases = [
            '1' => [
                Duration::Nanosecond => 1,
                Duration::Microsecond => 1000,
                Duration::Millisecond => 1000000,
                Duration::Second => 1000000000,
                Duration::Minute => 60000000000,
                Duration::Hour => 3600000000000,
            ],
            '2.5' => [
                Duration::Nanosecond => 2,
                Duration::Microsecond => 2500,
                Duration::Millisecond => 2500000,
                Duration::Second => 2500000000,
                Duration::Minute => 150000000000,
                Duration::Hour => 9000000000000,
            ],
            '533' => [
                Duration::Nanosecond => 533,
                Duration::Microsecond => 533000,
                Duration::Millisecond => 533000000,
                Duration::Second => 533000000000,
                Duration::Minute => 31980000000000,
                Duration::Hour => 1918800000000000,
            ],
            '1001' => [
                Duration::Nanosecond => 1001,
                Duration::Microsecond => 1001000,
                Duration::Millisecond => 1001000000,
                Duration::Second => 1001000000000,
                Duration::Minute => 60060000000000,
                Duration::Hour => 3603600000000000,
            ],
        ];

        foreach ($cases as $n => $values) {
            foreach ($values as $multiplier => $expected) {
                $d = new Duration(floatval($n), $multiplier);
                $this->assertEquals($expected, $d->nanoseconds(), "expected {$expected} but got {$d->nanoseconds()}");
            }
        }
    }

    public function testParse()
    {
        foreach (self::$cases as $case => $expected) {
            $d = Duration::parse($case);
            $actual = $d->nanoseconds();
            $this->assertEquals($expected, $actual, "$actual <> $expected");
        }
    }

    public function testInvalidParse1()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("bad duration format");
        Duration::parse("");
    }

    public function testInvalidParse2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("bad duration format");
        Duration::parse("xyz");
    }

    public function testInvalidParse3()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("bad duration format");
        Duration::parse("10unknown");
    }

    public function testInvalidParse4()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("bad duration format");
        Duration::parse("10");
    }

    public function test__toString()
    {
        foreach (self::$cases as $expected => $case) {
            $d = new Duration($case);
            $actual = "{$d}";
            $this->assertEquals(trim($expected), $actual, "$actual <> {trim($expected)}");
            $d2 = Duration::parse($actual);
            $this->assertEquals($d->nanoseconds(), $d2->nanoseconds());
        }
    }

    public function test_overflow()
    {
        $cases = [
            "+61m61s1001ms1001us1001ns" => 3722002002001,
            "         +61m" => 3660000000000,
            "        +1h1m" => 3660000000000,
        ];
        $ds = [];
        foreach ($cases as $str => $n) {
            if (!isset($ds[$n])) {
                $ds[$n] = [];
            }
            $d = Duration::parse(trim($str));
            $this->assertEquals($n, $d->nanoseconds());
            $ds[$n][] = $d;
        }

        foreach ($ds as $n => $dd) {
            $expected = sprintf("%s", new Duration($n));
            foreach ($dd as $d) {
                $this->assertEquals($expected, "{$d}");
            }
        }
    }

    public function test_abs()
    {
        $cases = [
            "-1h" => -3600000000000,
            "+1h" => 3600000000000,
        ];
        foreach ($cases as $str => $expected) {
            $d = Duration::parse($str);
            $this->assertEquals($expected, $d->nanoseconds(), "{$expected} <> {$d->nanoseconds()}");
            $this->assertEquals(abs($expected), $d->abs()->nanoseconds(), "{$expected} <> {$d->abs()->nanoseconds()}");
        }
    }

    public function test_abs_maxmin()
    {
        $dmin = new Duration(PHP_INT_MIN);
        $dmax = new Duration(PHP_INT_MAX);
        $this->assertEquals($dmin->abs()->nanoseconds(), $dmax->nanoseconds(), "{$dmin->abs()->nanoseconds()} <> {$dmax->nanoseconds()}");
        $this->assertEquals($dmax->abs()->nanoseconds(), $dmin->nanoseconds(), "{$dmax->abs()->nanoseconds()} <> {$dmin->nanoseconds()}");
    }

    public function test_add()
    {
        $cases = [
            "1m1s" => [Duration::parse("1m"), Duration::parse("1s")],
        ];
        foreach ($cases as $total => $ds) {
            $expected = Duration::parse($total);
            $actual = new Duration(0);
            foreach ($ds as $d) {
                $actual->add($d);
            }
            $this->assertEquals($expected->nanoseconds(), $actual->nanoseconds(), "{$expected->nanoseconds()} <> {$actual->nanoseconds()}");
        }
    }

    public function test_sub()
    {
        $cases = [
            "56s500ms" => [Duration::parse("1m"), Duration::parse("1s"), Duration::parse("2s500ms")],
        ];
        foreach ($cases as $total => $ds) {
            $expected = Duration::parse($total);
            $actual = null;
            foreach ($ds as $d) {
                if ($actual === null) {
                    $actual = $d;
                } else {
                    $actual->sub($d);
                }
            }
            $this->assertEquals($expected->nanoseconds(), $actual->nanoseconds(), "{$expected->nanoseconds()} <> {$actual->nanoseconds()}");
        }
    }

    public function test_truncate()
    {
        $cases = [
            "+1m10s" => ["1m15s", "14s"],
            "+59m" => ["+59m59s", "1m"],
            "-59m" => ["-59m59s", "1m"]
        ];
        foreach ($cases as $exp => $case) {
            $expected = Duration::parse($exp);
            $d = Duration::parse($case[0]);
            $actual = $d->truncate(Duration::parse($case[1]));
            $this->assertEquals($expected, "{$actual}", "{$expected} <> {$actual}");
        }
    }
}
