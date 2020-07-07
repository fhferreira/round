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
    /** Round number using brazilian normative ABNT NBR 5891
     *
     * @param $number
     *
     * @return mixed
     * @throws CommaException
     */
    public static function round($number)
    {
        $number = (string)$number;

        if (strpos($number, ',') !== false) {
            throw new CommaException();
        }

        $numberArr = explode('.', "" . $number);

        $intPart = $numberArr[0];
        $decimalsNumbers = isset($numberArr[1]) ? (string)$numberArr[1] : '00';

        $down = false;
        $up = false;

        if (strlen($decimalsNumbers) <= 2) {
            $decimalsNumbers = str_pad($decimalsNumbers, 2, '0', STR_PAD_RIGHT);
            return ((float)($intPart . '.' . $decimalsNumbers));
        }

        $decimalsStr = substr($decimalsNumbers, 0, 2);
        $restStr = substr($decimalsNumbers, 2);

        if (strlen($restStr) == 1) {
            $restStr = $restStr . '0';
        }

        $strlenRest = strlen($restStr);
        $finalRest = str_pad('5', $strlenRest, '0', STR_PAD_RIGHT);

        if ($restStr < $finalRest) {
            $down = true;
            $up = false;
        } else {
            if ($restStr > $finalRest) {
                $down = false;
                $up = true;
            } else {
                if ($restStr == $finalRest) {
                    if (((int)$decimalsStr[1]) % 2 == 1) {
                        $down = false;
                        $up = true;
                    } else {
                        $down = true;
                        $up = false;
                    }
                }
            }
        }

        $final = 0;
        if ($down) {
            $final = ((float)($intPart . '.' . $decimalsStr));
        } else {
            if ($up) {
                $decimals = $decimalsStr + 1;
                $sumInt = $decimals == 100 ? 1 : 0;
                if ($sumInt > 0) {
                    $decimals = '00';
                }

                if ($decimalsStr[0] == 0) {
                    $decimals = '0' . $decimals;
                }

                $final = ((float)(($intPart + $sumInt) . '.' . ($decimals)));
            }
        }

        return $final;
    }

    /**
     * @param      $value
     * @param int  $percent
     * @param bool $rounded
     *
     * @return float
     */
    public static function getPercentFromAmount($value, $percent = 100): float
    {
        return (float)($value / 100 * $percent);
    }
}
