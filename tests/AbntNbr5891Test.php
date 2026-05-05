<?php

declare(strict_types=1);

namespace Fhferreira\Round\Tests;

use Fhferreira\Round\AbntNbr5891;
use Fhferreira\Round\Exceptions\CommaException;

class AbntNbr5891Test extends \PHPUnit\Framework\TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAmounts()
    {
        $amounts = array(
            //value, expected
            [1.099, 1.10],
            [1.097, 1.10],
            [1.098, 1.10],
            [1.096, 1.10],
            [1.095, 1.10],
            [1.094, 1.09],
            [1.093, 1.09],
            [1.089, 1.09],
            [1.085, 1.08],
            [1.084, 1.08],
            [12.6529, 12.65],
            [12.86512, 12.87],
            [12.744623, 12.74],
            [12.8752, 12.88],
            [12.8150, 12.82],
            [12.8050, 12.80],
            [12.5, 12.50],
            [12.3, 12.30],
            [12.33, 12.33],
            [12, 12],
            [0.025, 0.02],
            [0.036, 0.04],
            [0.046, 0.05],
            [0.026, 0.03],
            [0.2345645, 0.23],
            [-0.775, -0.78]
        );

        foreach ($amounts as $index => $amountInfo) {
            $rounded = AbntNbr5891::round($amountInfo[0]);
            $this->assertEquals($amountInfo[1], $rounded);
        }
    }

    /**
     * Worked table from the published ABNT NBR 5891 rounding guide.
     */
    public function testNbr5891PublishedTable()
    {
        $amounts = array(
            //value, expected
            ['0.342', 0.34],
            ['0.346', 0.35],
            ['0.3452', 0.35],
            ['0.3450', 0.34],
            ['0.332', 0.33],
            ['0.336', 0.34],
            ['0.3352', 0.34],
            ['0.3350', 0.34],
            ['0.3050', 0.30],
            ['0.3150', 0.32],
        );

        foreach ($amounts as $amountInfo) {
            $rounded = AbntNbr5891::round($amountInfo[0]);
            $this->assertEquals($amountInfo[1], $rounded, "Rounding {$amountInfo[0]}");
        }
    }

    /**
     * Regression coverage for negative values whose rounding carries into the
     * integer part. Previously, the carry incremented toward zero instead of
     * away from zero, producing values like -1.999 → 0.
     */
    public function testNegativeCarryOver()
    {
        $amounts = array(
            //value, expected
            [-1.999, -2.00],
            [-2.999, -3.00],
            [-2.995, -3.00],
            [-0.999, -1.00],
            [-0.995, -1.00],
            [-1.236, -1.24],
            [-1.234, -1.23],
            [-12.8150, -12.82],
            [-12.8050, -12.80],
        );

        foreach ($amounts as $amountInfo) {
            $rounded = AbntNbr5891::round($amountInfo[0]);
            $this->assertEquals($amountInfo[1], $rounded, "Rounding {$amountInfo[0]}");
        }
    }

    public function testPercentFromABNT()
    {
        $amounts = array(
            //value, percent, expected
           [100, 12, 12],
           [100, 13.45, 13.45],
           [149.57, 60, 89.74],
           [149.57, 40, 59.83],
           [4.54, 40, 1.82],
           [4.54, 60, 2.72],
           [159.38, 40, 63.75],
           [159.38, 60, 95.63],
           [159.38, 35, 55.78],
           [159.38, 65, 103.60],
        );

        foreach ($amounts as $index => $amountInfo) {
            $percent = AbntNbr5891::getPercentFromAmount($amountInfo[0], $amountInfo[1]);
            $rounded = AbntNbr5891::round($percent);
            $this->assertEquals($amountInfo[2], $rounded);
        }
    }

    public function testPercentFromABNTPlus100()
    {
        $amounts = array(
            //value, percent, expected
           [100, 12, 12, 1200],
           [100, 13.45, 13.45, 1345],
           [149.57, 60, 89.74, 8974],
           [149.57, 40, 59.83, 5983],
           [4.54, 40, 1.82, 182],
           [4.54, 60, 2.72, 272],
           [159.38, 40, 63.75, 6375],
           [159.38, 60, 95.63, 9563],
           [159.38, 35, 55.78, 5578],
           [159.38, 65, 103.60, 10360],
        );

        foreach ($amounts as $index => $amountInfo) {
            $percent = AbntNbr5891::getPercentFromAmount($amountInfo[0], $amountInfo[1]);
            $rounded = AbntNbr5891::round($percent);
            $this->assertEquals((int) $amountInfo[3], $rounded * 100);
        }
    }

    /**
     * Regression coverage for floating-point residues that PHP serializes in
     * scientific notation. Expressions like `6.9 - 0.34 - 6.56` evaluate to
     * ~8.88e-16 instead of exactly zero; before the fix the string cast
     * "8.8817841970013E-16" tricked the parser into returning 8.88.
     */
    public function testFloatingPointResidueRoundsToZero()
    {
        $this->assertSame(0.0, AbntNbr5891::round(6.9 - 0.34 - 0 - 6.56));
        $this->assertSame(0.0, AbntNbr5891::round(0.1 + 0.2 - 0.3));
        $this->assertSame(0.0, AbntNbr5891::round(-(6.9 - 0.34 - 6.56)));
    }

    public function testCommaException()
    {
        $this->expectException(CommaException::class);
        $this->expectExceptionMessage('Comma-formatted numbers are not allowed.');

        AbntNbr5891::round('100,50');
    }
}
