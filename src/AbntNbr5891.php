<?php

declare(strict_types=1);

namespace Fhferreira\Round;

use Fhferreira\Round\Exceptions\CommaException;

/**
 * Class Rounder
 * @package Fhferreira\Round
 *
 * The rounding rules, following the ABNT NBR 5891 Standard, apply to the decimal digits located in the position
 * following the number of decimal digits to be transformed, that is, if we have a number of 4, 5, 6, n decimal digits
 * and if we want to round to 2, these rounding rules will apply:
 *
 * If the following decimal digits are less than 50, 500, 5000 ..., the previous one does not change.
 * If the following decimal digits are greater than 50, 500, 5000 ..., the previous one is incremented by one.
 * If the following decimal digits are equal to 50, 500, 5000 ..., the former is verified; if it is even, the former
 * does not change; if it is odd, the former is increased by one.
 *
 * Examples Edit
 * Rounding to 2 decimal digits, we must pay attention to the third and fourth decimal. Thus, according to the previous
 * rules: The number 12.6529 would be rounded up to 12.65 (here it is 12.65, since 29 is less than 50, so it does not
 * change) The number 12.86512 would be rounded to 12.87 (here it is 12.87, since 512 is greater than 500, then a unit
 * is increased) The number 12.744623 would be rounded to 12.74 (here it is 12.74, since 4623 is less than 5000, so it
 * does not change) The number 12.8752 would be rounded to 12.88 (here it is 12.88, since 52 is greater than 50, then a
 * unit is increased) The number 12.8150 would be rounded to 12.82 (here it is 12.82, since the next digits are equal
 * to 50 and the previous one is odd, in this case 1, then a unit is increased) The number 12.8050 would be rounded to
 * 12.80 (here it is 12.80, since the following digits are equal to 50 and the previous one is even, in this case 0,
 * then the previous one does not change)
 */
class AbntNbr5891
{
    /**
     * Round a number to two decimal places following ABNT NBR 5891.
     *
     * Rules, given the digits immediately after the last one to keep:
     *   - strictly less than 5           → keep
     *   - strictly greater than 5        → increment
     *   - equal to 5 followed by any
     *     non-zero digit                 → increment
     *   - equal to 5 followed only by
     *     zeros                          → round half to even
     *
     * @param int|float|string $number
     *
     * @throws CommaException
     */
    public static function round($number): float
    {
        $number = (string) $number;

        if (strpos($number, ',') !== false) {
            throw new CommaException();
        }

        $negative = isset($number[0]) && $number[0] === '-';
        if ($negative) {
            $number = substr($number, 1);
        }

        [$intPart, $decimals] = self::splitNumber($number);

        // Fewer than three decimal digits: nothing to round, just normalize.
        if (strlen($decimals) <= 2) {
            return self::toFloat($intPart, str_pad($decimals, 2, '0', STR_PAD_RIGHT), $negative);
        }

        $kept = substr($decimals, 0, 2);
        $tail = substr($decimals, 2);

        if (!self::shouldRoundUp($kept, $tail)) {
            return self::toFloat($intPart, $kept, $negative);
        }

        $incremented = ((int) $kept) + 1;
        if ($incremented === 100) {
            $intPart = (string) (((int) $intPart) + 1);
            $kept = '00';
        } else {
            $kept = str_pad((string) $incremented, 2, '0', STR_PAD_LEFT);
        }

        return self::toFloat($intPart, $kept, $negative);
    }

    /**
     * Decide whether to increment the last kept digit according to NBR 5891.
     */
    private static function shouldRoundUp(string $kept, string $tail): bool
    {
        $half = str_pad('5', strlen($tail), '0', STR_PAD_RIGHT);

        if ($tail > $half) {
            return true;
        }

        if ($tail === $half) {
            // Tie: round half to even — increment only when the last kept digit is odd.
            return ((int) $kept[1]) % 2 === 1;
        }

        return false;
    }

    /**
     * Split an unsigned numeric string into its integer and decimal parts.
     *
     * @return array{0: string, 1: string}
     */
    private static function splitNumber(string $number): array
    {
        $parts = explode('.', $number, 2);

        return [$parts[0], $parts[1] ?? ''];
    }

    private static function toFloat(string $intPart, string $decimals, bool $negative): float
    {
        $result = (float) ($intPart . '.' . $decimals);

        return $negative ? -$result : $result;
    }

    /**
     * Return the given percentage of a value.
     *
     * @param int|float        $value
     * @param int|float        $percent
     */
    public static function getPercentFromAmount($value, $percent = 100): float
    {
        return (float) ($value / 100 * $percent);
    }
}
