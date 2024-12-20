<?php

declare(strict_types=1);

namespace Oct8pus\Benchmark;

use DivisionByZeroError;
use MathPHP\Statistics\Average;
use MathPHP\Statistics\Descriptive;

class Helper
{
    public static int $pad1 = 19;
    public static int $pad2 = 14;

    /**
     * Analyze test results
     *
     * @param Report $report
     *
     * @return ?array
     */
    public static function analyzeTest(Report $report) : ?array
    {
        $data = $report->data();

        $quartiles = Descriptive::quartiles($data);

        return [
            'mean' => Average::mean($data),
            'median' => Average::median($data),
            'mode' => Average::mode($data)[0],
            'minimum' => min($data),
            'maximum' => max($data),
            'quartile 1' => $quartiles['Q1'],
            'quartile 3' => $quartiles['Q3'],
            'IQ range' => Descriptive::interquartileRange($data),
            'std deviation' => Descriptive::standardDeviation($data),
            'normality' => Stats::testNormal($data),
        ];
    }

    /**
     * Show comparison
     *
     * @param Reports $bReports
     * @param Reports $uReports
     *
     * @return void
     */
    public static function showCompare(Reports $bReports, Reports $uReports) : void
    {
        $line = str_pad('', self::$pad1 + 3 * self::$pad2 + 3, '-');

        echo "{$line}\n";

        // compare tests
        foreach ($bReports as $i => $bReport) {
            $uReport = $uReports[$i];

            $bResult = self::analyzeTest($bReport);
            $uResult = self::analyzeTest($uReport);

            if ($bResult === null || $uResult === null) {
                echo str_pad($bReport->name(), self::$pad1) . ' : ' . str_pad('FAILED', self::$pad2, ' ', STR_PAD_LEFT) . "\n";
                echo "{$line}\n";
                continue;
            }

            // show test results
            echo str_pad((string) $i, self::$pad1) . ' : ' . str_pad($bReport->name(), self::$pad2, ' ', STR_PAD_LEFT) . str_pad($uReport->name(), self::$pad2, ' ', STR_PAD_LEFT) . "\n";

            foreach ($bResult as $key => $bValue) {
                $uValue = $uResult[$key];

                if ($key === 'normality') {
                    echo str_pad($key, self::$pad1) . ' : ' . self::formatPercentage($bValue, false, self::$pad2) . self::formatPercentage($bValue, false, self::$pad2) . "\n";
                } else {
                    try {
                        $delta = Stats::relativeDifference($bValue, $uValue);

                        echo str_pad($key, self::$pad1) . ' : ' . self::formatNumber($bValue, self::$pad2) . self::formatNumber($uValue, self::$pad2) . self::formatPercentage($delta, true, self::$pad2) . "\n";
                    } catch (DivisionByZeroError $exception) {
                        echo str_pad($key, self::$pad1) . ' : ' . self::formatNumber($bValue, self::$pad2) . self::formatNumber($uValue, self::$pad2) . str_pad('nan', self::$pad2, ' ', STR_PAD_LEFT) . "\n";
                    }
                }
            }

            echo "{$line}\n";
        }
    }

    /**
     * Format number
     *
     * @param float $number
     * @param int   $padding
     *
     * @return string
     */
    public static function formatNumber(float $number, int $padding) : string
    {
        return str_pad(number_format($number, 0, '.', ''), $padding, ' ', STR_PAD_LEFT);
    }

    /**
     * Format percentage
     *
     * @param float $number
     * @param bool  $sign
     * @param int   $padding
     *
     * @return string
     */
    public static function formatPercentage(float $number, bool $sign, int $padding) : string
    {
        $str = '';

        if ($sign) {
            $str = ($number > 0) ? '+' : '';
        }

        $str .= number_format(100 * $number, 1, '.', '') . '%';

        $str = str_pad($str, $padding, ' ', STR_PAD_LEFT);

        if ($sign) {
            // add color
            if ($number > 0) {
                $str = "\033[01;32m{$str}\033[0m";
            } elseif ($number < 0) {
                $str = "\033[01;31m{$str}\033[0m";
            }
        }

        return $str;
    }

    /**
     * Format bytes
     *
     * @param float $size
     * @param int   $precision
     *
     * @return string
     *
     * @note https://stackoverflow.com/a/2510540/10126479
     */
    public static function formatBytes(float $size, int $precision = 2) : string
    {
        $base = log($size, 1024.0);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(1024 ** ($base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Get all array values as string
     *
     * @param array $cells
     *
     * @return string
     */
    public static function allMeasurements(array $cells) : string
    {
        $str = "\n\n";

        foreach ($cells as $key => $value) {
            $str .= self::formatNumber($value, 0) . ' ';

            if (!(($key + 1) % 32)) {
                $str .= "\n";
            }
        }

        return "{$str}\n";
    }

    /**
     * Get outliers as string
     *
     * @param array $cells
     *
     * @return string
     */
    public static function outliers(array $cells) : string
    {
        $outliers = Stats::outliers($cells);

        $str = "\n\n";

        foreach ($outliers as $key => $outlier) {
            $str .= self::formatNumber($outlier, 0) . ' ';

            if (!(($key + 1) % 32)) {
                $str .= "\n";
            }
        }

        return "{$str}\n";
    }

    /**
     * Clean not existing functions
     *
     * @param array $functions
     *
     * @return array
     */
    public static function cleanFunctions(array $functions) : array
    {
        // remove functions that don't exist
        foreach ($functions as $key => $function) {
            if (!function_exists($function)) {
                echo "Removed {$function} as it does not exist";
                unset($functions[$key]);
            }
        }

        return $functions;
    }

    /**
     * Create not random bytes string
     *
     * @param int $length
     *
     * @return string
     */
    public static function notRandomBytes(int $length) : string
    {
        $str = '';

        for ($i = 0; $i < $length; ++$i) {
            $str .= chr(rand(0, 255));
        }

        return $str;
    }
}
