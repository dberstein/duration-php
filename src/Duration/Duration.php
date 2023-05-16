<?php

namespace Duration;

use InvalidArgumentException;

class Duration
{
    const Nanosecond = 1;
    const Microsecond = 1000;
    const Millisecond = 1000000;
    const Second = 1000000000;
    const Minute = 60 * 1000000000;
    const Hour = 3600 * 1000000000;

    /**
     * @var array
     */
    static protected array $unitMap = [
        "ns" => self::Nanosecond,
        "us" => self::Microsecond,
        "ms" => self::Millisecond,
        "s" => self::Second,
        "m" => self::Minute,
        "h" => self::Hour,
    ];

    /**
     * @var int
     */
    protected int $n;

    /**
     * @param int $n
     */
    public function __construct(int $n = 0)
    {
        $this->n = $n;
    }

    /**
     * @return int
     */
    public function nanoseconds(): int
    {
        return intval($this->n);
    }

    /**
     * @return float
     */
    public function microseconds(): float
    {
        return $this->nanoseconds() / self::Microsecond;
    }

    /**
     * @return float
     */
    public function milliseconds(): float
    {
        return $this->nanoseconds() / self::Millisecond;
    }

    /**
     * @return float
     */
    public function seconds(): float
    {
        return $this->nanoseconds() / self::Second;
    }

    /**
     * @return float
     */
    public function minutes(): float
    {
        return $this->nanoseconds() / self::Minute;
    }

    /**
     * @return float
     */
    public function hours(): float
    {
        return $this->nanoseconds() / self::Hour;
    }

    /**
     * @param string $s
     * @return Duration
     */
    static public function parse(string $s): Duration
    {
        $n = 0;
        if (preg_match_all("(([+\-])?(\d+(\.\d+)?)(ns|us|ms|s|m|h))", trim($s), $matches)) {
            $neg = $matches[1][0] == "-";
            foreach ($matches[2] as $key => $value) {
                $unit = $matches[4][$key];
                $n += $value * self::$unitMap[$unit];
            }
            return new Duration($n * ($neg ? -1 : 1));
        }
        throw new InvalidArgumentException("bad duration format");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $n = $this->n;
        $buf = [];
        if ($n == 0) {
            $buf[] = "0s";
        } else {
            $buf[] = ($n > 0) ? "+" : "-";
        }
        foreach (array_reverse(self::$unitMap, true) as $key => $value) {
            if (abs($n) < $value) {
                continue;
            }
            $q = intdiv($n, $value);
            $n -= $q * $value;
            $buf[] = sprintf("%d%s", abs($q), $key);
        }
        return implode("", $buf);
    }

    /**
     * @return Duration
     */
    public function abs(): Duration
    {
        if ($this->n == PHP_INT_MIN) {
            return new Duration(PHP_INT_MAX);
        }
        if ($this->n == PHP_INT_MAX) {
            return new Duration(PHP_INT_MIN);
        }
        return new Duration(abs($this->n));
    }

    /**
     * @param Duration $d
     * @return Duration
     */
    public function add(Duration $d): Duration
    {
        $this->n += $d->n;
        return $this;
    }

    /**
     * @param Duration $d
     * @return Duration
     */
    public function sub(Duration $d): Duration
    {
        $this->n -= $d->n;
        return $this;
    }

    /**
     * @param Duration $d
     * @return Duration
     */
    public function truncate(Duration $d): Duration
    {
        $q = intdiv($this->nanoseconds(), $d->nanoseconds());
        $n = $q * $d->nanoseconds();
        return new Duration($n);
    }
}


