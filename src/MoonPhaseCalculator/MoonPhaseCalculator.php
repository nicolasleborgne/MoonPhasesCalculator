<?php

namespace MoonPhaseCalculator;

use MoonPhaseCalculator\MoonPhases;
use \DateTime;
use \DateTimeZone;


class MoonPhaseCalculator
{
    /**
    * [$newMoonDateTime description]
    * @var [type]
    */
    private $newMoonDateTime = null;

    /**
    * [$firstQuarterDateTime description]
    * @var [type]
    */
    private $firstQuarterDateTime = null;

    /**
    * [$fullMoonDateTime description]
    * @var [type]
    */
    private $fullMoonDateTime = null;

    /**
    * [$lastQuarterDateTime description]
    * @var [type]
    */
    private $lastQuarterDateTime = null;

    /**
    * [NB_SECOND_PER_YEAR description]
    * @var integer
    */
    const NB_SECOND_PER_YEAR = 31557600;


    const MOON_SYNODIC_PERIOD = 29.53058886;

    /**
    * Constructor
    * @method __construct
    * @param  DateTime    $dateTime DateTime from wich compute moon phases
    */
    function __construct(DateTime $dateTime)
    {
        $decimalYear = $this->convertDateTimeToFloat($dateTime);

        $this->setNewMoonDateTime($this->calculateMoonPhase(MoonPhases::NEW_MOON, $decimalYear));
        $this->setFirstQuarterDateTime($this->calculateMoonPhase(MoonPhases::FIRST_QUARTER, $decimalYear)) ;
        $this->setFullMoonDateTime($this->calculateMoonPhase(MoonPhases::FULL_MOON, $decimalYear));
        $this->setLastQuarterDateTime($this->calculateMoonPhase(MoonPhases::LAST_QUARTER, $decimalYear));
    }

    /**
    * Compute a moon phase
    * @method calculateMoonPhase
    * @param  integer           $moonPhase   Moon phase to compute
    * @param  Float           $decimalYear Year in decimal
    * @return DateTime                        Moon phase computed
    */
    private function calculateMoonPhase($moonPhase, $decimalYear)
    {
        $k = round(($decimalYear - 2000) * 12.3685);

        switch ($moonPhase)
        {
            case MoonPhases::NEW_MOON:
            $t = $k / 1236.85;
            $e = 1 - 0.002516 * $t - 0.0000074 * pow($t, 2);

            $m = 2.5534 + (29.10535669 * $k) - (0.00000218 * pow($t, 2)) - (0.00000011 * pow($t, 3));
            $mp = 201.5643 + (385.81693528 * $k) + (0.0107438 * pow($t, 2)) + (0.00001239 * pow($t, 3)) - (0.000000058 * pow($t, 4));
            $f = 160.7108 + (390.67050274 * $k) - (0.0016341 * pow($t, 2)) - (0.00000227 * pow($t, 3)) + (0.000000011 * pow($t, 4));
            $ohm = 124.7746 - 1.56375580 * $k + 0.0020691 * pow($t, 2) + 0.00000215 * pow($t, 3);

            $m = $this->convertAngleOn360DegInterval($m);
            $mp = $this->convertAngleOn360DegInterval($mp);
            $f = $this->convertAngleOn360DegInterval($f);
            $ohm = $this->convertAngleOn360DegInterval($ohm);

            $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
            $s2 = $this->getSecondCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm);

            $jde = 2451550.09766 + (29.530588853 * $k) + (0.00015437 * pow($t, 2)) - (0.000000150 * pow($t, 3)) + (0.00000000073 * pow($t, 4));
            $jd = $jde + $s1 + $s2;
            echo PHP_EOL;
            echo 't: '.$t.PHP_EOL;
            echo 'e: '.$e.PHP_EOL;
            echo 'm: '.$m.PHP_EOL;
            echo 'mp: '.$mp.PHP_EOL;
            echo 'f: '.$f.PHP_EOL;
            echo 'ohm: '.$ohm.PHP_EOL;
            echo 's1: '.$s1.PHP_EOL;
            echo 's2: '.$s2.PHP_EOL;
            echo 'jde: '.$jde.PHP_EOL;
            echo 'jd: '.$jd.PHP_EOL;

            $dateTime = new DateTime(jdtogregorian(round($jd)), new DateTimeZone('Europe/Paris'));
            break;
            case MoonPhases::FIRST_QUARTER:
            $k += 0.25;
            $t = $k / 1236.85;
            $e = 1 - 0.002516 * $t - 0.0000074 * pow($t, 2);

            $m = 2.5534 + (29.10535669 * $k) - (0.00000218 * pow($t, 2)) - (0.00000011 * pow($t, 3));
            $mp = 201.5643 + (385.81693528 * $k) + (0.0107438 * pow($t, 2)) + (0.00001239 * pow($t, 3)) - (0.000000058 * pow($t, 4));
            $f = 160.7108 + (390.67050274 * $k) - (0.0016341 * pow($t, 2)) - (0.00000227 * pow($t, 3)) + (0.000000011 * pow($t, 4));
            $ohm = 124.7746 - 1.56375580 * $k + 0.0020691 * pow($t, 2) + 0.00000215 * pow($t, 3);

            $m = $this->convertAngleOn360DegInterval($m);
            $mp = $this->convertAngleOn360DegInterval($mp);
            $f = $this->convertAngleOn360DegInterval($f);
            $ohm = $this->convertAngleOn360DegInterval($ohm);

            $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
            $s4 = $this->getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm);
            $w = 0.00306 - (0.00038 * $e * cos(deg2rad($m))) + (0.00026 * cos(deg2rad($mp))) - (0.00002 * cos(deg2rad($mp - $m))) + (0.00002 * cos(deg2rad($mp + $m))) + (0.00002 * cos(deg2rad(2 * $f)));

            $jde = 2451550.09765 + (self::MOON_SYNODIC_PERIOD * $k) + (0.0001337 * pow($t, 2)) - (0.000000150 * pow($t, 3)) + (0.00000000073 * pow($t, 4));
            $jd = $jde + $s1 + $s4 + $w;

            $dateTime = new \DateTime(jdtogregorian(round($jd)), new \DateTimeZone('Europe/Paris'));
            break;
            case MoonPhases::FULL_MOON:
            $k += 0.50;
            $t = $k / 1236.85;
            $e = 1 - 0.002516 * $t - 0.0000074 * pow($t, 2);

            $m = 2.5534 + (29.10535669 * $k) - (0.00000218 * pow($t, 2)) - (0.00000011 * pow($t, 3));
            $mp = 201.5643 + (385.81693528 * $k) + (0.0107438 * pow($t, 2)) + (0.00001239 * pow($t, 3)) - (0.000000058 * pow($t, 4));
            $f = 160.7108 + (390.67050274 * $k) - (0.0016341 * pow($t, 2)) - (0.00000227 * pow($t, 3)) + (0.000000011 * pow($t, 4));
            $ohm = 124.7746 - 1.56375580 * $k + 0.0020691 * pow($t, 2) + 0.00000215 * pow($t, 3);

            $m = $this->convertAngleOn360DegInterval($m);
            $mp = $this->convertAngleOn360DegInterval($mp);
            $f = $this->convertAngleOn360DegInterval($f);
            $ohm = $this->convertAngleOn360DegInterval($ohm);

            $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
            $s3 = $this->getThirdCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm);

            $jde = 2451550.09765 + (self::MOON_SYNODIC_PERIOD * $k) + (0.0001337 * pow($t, 2)) - (0.000000150 * pow($t, 3)) + (0.00000000073 * pow($t, 4));
            $jd = $jde + $s1 + $s3;

            $dateTime = new DateTime(jdtogregorian(round($jd)), new DateTimeZone('Europe/Paris'));
            break;
            case MoonPhases::LAST_QUARTER:
            $k += 0.75;
            $t = $k / 1236.85;
            $e = 1 - 0.002516 * $t - 0.0000074 * pow($t, 2);

            $m = 2.5534 + (29.10535669 * $k) - (0.00000218 * pow($t, 2)) - (0.00000011 * pow($t, 3));
            $mp = 201.5643 + (385.81693528 * $k) + (0.0107438 * pow($t, 2)) + (0.00001239 * pow($t, 3)) - (0.000000058 * pow($t, 4));
            $f = 160.7108 + (390.67050274 * $k) - (0.0016341 * pow($t, 2)) - (0.00000227 * pow($t, 3)) + (0.000000011 * pow($t, 4));
            $ohm = 124.7746 - 1.56375580 * $k + 0.0020691 * pow($t, 2) + 0.00000215 * pow($t, 3);

            $m = $this->convertAngleOn360DegInterval($m);
            $mp = $this->convertAngleOn360DegInterval($mp);
            $f = $this->convertAngleOn360DegInterval($f);
            $ohm = $this->convertAngleOn360DegInterval($ohm);

            $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
            $s4 = $this->getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm);
            $w = 0.00306 - (0.00038 * $e * cos(deg2rad($m))) + (0.00026 * cos(deg2rad($mp))) - (0.00002 * cos(deg2rad($mp - $m))) + (0.00002 * cos(deg2rad($mp + $m))) + (0.00002 * cos(deg2rad(2 * $f)));

            $jde = 2451550.09765 + (self::MOON_SYNODIC_PERIOD * $k) + (0.0001337 * pow($t, 2)) - (0.000000150 * pow($t, 3)) + (0.00000000073 * pow($t, 4));
            $jd = $jde + $s1 + $s4 - $w;

            $dateTime = new DateTime(jdtogregorian(round($jd)), new DateTimeZone('Europe/Paris'));
            break;
            default:
            return;
            break;
        }

        return $dateTime;
    }

    private function convertAngleOn360DegInterval($angle)
    {
        return ($angle / 360 - floor($angle / 360)) * 360;
    }

    /**
    * Convert a DateTime php object into float
    * @method convertDateTimeToFloat
    * @param  DateTime           $dateTime Date to convert to float
    * @return Float                       Year in decimal
    */
    private function convertDateTimeToFloat(\DateTime $dateTime)
    {
        return $dateTime->format("Y") + $dateTime->format("z") * 24 * 3600 / self::NB_SECOND_PER_YEAR;
    }

    private function getPlanetaryArguments($k, $t)
    {
        $planetaryArguments[] = 299.77 + (0.107408 * $k) - (0.009173 * pow($t, 2));
        $planetaryArguments[] = 251.88 + (0.016321 *$k);
        $planetaryArguments[] = 251.83 + (26.651886 * $k);
        $planetaryArguments[] = 349.42 + (36.412478 * $k);
        $planetaryArguments[] = 84.66 + (18.206239 * $k);
        $planetaryArguments[] = 141.74 + (53.303771 * $k);
        $planetaryArguments[] = 207.14 + (2.453732 * $k);
        $planetaryArguments[] = 154.14 + (7.306860 * $k);
        $planetaryArguments[] = 34.52 + (27.261239 * $k);
        $planetaryArguments[] = 207.19 + (0.121824 * $k);
        $planetaryArguments[] = 291.34 + (1.844379 * $k);
        $planetaryArguments[] = 161.72 + (24.198154 * $k);
        $planetaryArguments[] = 239.56 + (25.513099 * $k);
        $planetaryArguments[] = 331.55 + (3.592518 * $k);

        return $planetaryArguments;
    }

    private function getFirstCorrectionsFactorsGroupFromPlanetaryArguments(Array $planetaryArguments)
    {
        $corrections[] = 0.000325 * sin(deg2rad($planetaryArguments[0]));
        $corrections[] = 0.000165 * sin(deg2rad($planetaryArguments[1]));
        $corrections[] = 0.000164 * sin(deg2rad($planetaryArguments[2]));
        $corrections[] = 0.000126 * sin(deg2rad($planetaryArguments[3]));
        $corrections[] = 0.000110 * sin(deg2rad($planetaryArguments[4]));
        $corrections[] = 0.000062 * sin(deg2rad($planetaryArguments[5]));
        $corrections[] = 0.000060 * sin(deg2rad($planetaryArguments[6]));
        $corrections[] = 0.000056 * sin(deg2rad($planetaryArguments[7]));
        $corrections[] = 0.000047 * sin(deg2rad($planetaryArguments[8]));
        $corrections[] = 0.000042 * sin(deg2rad($planetaryArguments[9]));
        $corrections[] = 0.000040 * sin(deg2rad($planetaryArguments[10]));
        $corrections[] = 0.000037 * sin(deg2rad($planetaryArguments[11]));
        $corrections[] = 0.000035 * sin(deg2rad($planetaryArguments[12]));
        $corrections[] = 0.000023 * sin(deg2rad($planetaryArguments[13]));

        return array_sum($corrections);
    }

    private function getSecondCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm)
    {
        $corrections[] = -0.40720 * sin(deg2rad($mp));
        $corrections[] = 0.17241 * $e * sin(deg2rad($m));
        $corrections[] = 0.01608 * sin(deg2rad(2 * $mp));
        $corrections[] = 0.01039 * sin(deg2rad(2 * $f));
        $corrections[] = 0.00739 * $e * sin(deg2rad($mp - $m));
        $corrections[] = -0.00514 * $e * sin(deg2rad($mp + $m));
        $corrections[] = 0.00208 * pow($e, 2) * sin(deg2rad(2 * $m));
        $corrections[] = -0.00111 * sin(deg2rad($mp - 2 * $f));
        $corrections[] = -0.00057 * sin(deg2rad($mp + 2 * $f));
        $corrections[] = 0.00056 * $e * sin(deg2rad(2 * $mp + $m));
        $corrections[] = -0.00042 * sin(deg2rad(3 * $mp));
        $corrections[] = 0.00042 * $e * sin(deg2rad($m + 2 * $f));
        $corrections[] = 0.00038 * $e * sin(deg2rad($m - 2 * $f));
        $corrections[] = -0.00024* $e * sin(deg2rad(2 * $mp - $m));
        $corrections[] = -0.00017 * sin(deg2rad($ohm));
        $corrections[] = -0.00007 * sin(deg2rad($mp + 2 * $m));
        $corrections[] = 0.00004 * sin(deg2rad(2 * $mp - 2 * $f));
        $corrections[] = 0.00004 * sin(deg2rad(3 * $m));
        $corrections[] = 0.00003 * sin(deg2rad($mp + $m - 2 * $f));
        $corrections[] = 0.00003 * sin(deg2rad(2 * $mp + 2 * $f));
        $corrections[] = -0.00003 * sin(deg2rad($mp + $m + 2 * $f));
        $corrections[] = 0.00003 * sin(deg2rad($mp - $m + 2 * $f));
        $corrections[] = -0.00002 * sin(deg2rad($mp - $m - 2 * $f));
        $corrections[] = -0.00002 * sin(deg2rad(3 * $mp + $m));
        $corrections[] = 0.00002 * sin(deg2rad(4 * $mp));

        return array_sum($corrections);
    }

    private function getThirdCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm)
    {
        $corrections[] = -0.40614 * sin(deg2rad($mp));
        $corrections[] = 0.17302 * $e * sin(deg2rad($m));
        $corrections[] = 0.01614 * sin(deg2rad(2 * $mp));
        $corrections[] = 0.01043 * sin(deg2rad(2 * $f));
        $corrections[] = 0.00734 * $e * sin(deg2rad($mp - $m));
        $corrections[] = -0.00515 * $e * sin(deg2rad($mp + $m));
        $corrections[] = 0.00209 * pow($e, 2) * sin(deg2rad(2 * $m));
        $corrections[] = -0.00111 * sin(deg2rad($mp - 2 * $f));
        $corrections[] = -0.00057 * sin(deg2rad($mp + 2 * $f));
        $corrections[] = 0.00056 * $e * sin(deg2rad(2 * $mp + $m));
        $corrections[] = -0.00042 * sin(deg2rad(3 * $mp));
        $corrections[] = 0.00042 * $e * sin(deg2rad($m + 2 * $f));
        $corrections[] = 0.00038 * $e * sin(deg2rad($m - 2 * $f));
        $corrections[] = -0.00024* $e * sin(deg2rad(2 * $mp - $m));
        $corrections[] = -0.00017 * sin(deg2rad($ohm));
        $corrections[] = -0.00007 * sin(deg2rad($mp + 2 * $m));
        $corrections[] = 0.00004 * sin(deg2rad(2 * $mp - 2 * $f));
        $corrections[] = 0.00004 * sin(deg2rad(3 * $m));
        $corrections[] = 0.00003 * sin(deg2rad($mp + $m - 2 * $f));
        $corrections[] = 0.00003 * sin(deg2rad(2 * $mp + 2 * $f));
        $corrections[] = -0.00003 * sin(deg2rad($mp + $m + 2 * $f));
        $corrections[] = 0.00003 * sin(deg2rad($mp - $m + 2 * $f));
        $corrections[] = -0.00002 * sin(deg2rad($mp - $m - 2 * $f));
        $corrections[] = -0.00002 * sin(deg2rad(3 * $mp + $m));
        $corrections[] = 0.00002 * sin(deg2rad(4 * $mp));

        return array_sum($corrections);
    }

    private function getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm)
    {
        $corrections[] = -0.62801 * sin(deg2rad( $mp));
        $corrections[] = 0.17172 * $e * sin(deg2rad( $m));
        $corrections[] = -0.01183 * $e * sin(deg2rad( $mp + $m));
        $corrections[] = 0.00862 * sin(deg2rad(2 * $mp));
        $corrections[] = 0.00804 * sin(deg2rad(2 * $f));
        $corrections[] = 0.00454 * $e * sin(deg2rad($mp - $m));
        $corrections[] = 0.00204 * pow($e, 2) * sin(deg2rad($m));
        $corrections[] = -0.00180 * sin(deg2rad($mp - 2 * $f));
        $corrections[] = -0.00070 * sin(deg2rad($mp + 2 * $f));
        $corrections[] = -0.00040 * sin(deg2rad(3 * $m));
        $corrections[] = -0.00034 * $e * sin(deg2rad(2 * $mp - $m));
        $corrections[] = 0.00032 * $e * sin(deg2rad($m + 2 * $f));
        $corrections[] = 0.00032 * $e * sin(deg2rad($m - 2 * $f));
        $corrections[] = -0.00028 * pow($e, 2) * sin(deg2rad($mp + 2 * $m));
        $corrections[] = 0.00027 * $e * sin(deg2rad(2 * $mp + $m));
        $corrections[] = -0.00017 * sin(deg2rad($ohm));
        $corrections[] = -0.00005 * sin(deg2rad($mp - $m - 2 * $f));
        $corrections[] = 0.00004 * sin(deg2rad(2 * $mp + 2 * $f));
        $corrections[] = -0.00004 * sin(deg2rad($mp + $m + 2 * $f));
        $corrections[] = 0.00004 * sin(deg2rad($mp - 2 * $m));
        $corrections[] = 0.00003 * sin(deg2rad($mp + $m - 2 * $f));
        $corrections[] = 0.00003 * sin(deg2rad(3 * $m));
        $corrections[] = 0.00002 * sin(deg2rad(2 * $mp - 2 * $f));
        $corrections[] = 0.00002 * sin(deg2rad($mp- $m +2 * $f));
        $corrections[] = -0.00002 * sin(deg2rad(3 * $mp + $m));

        return array_sum($corrections);
    }

    /**
    * Get the new moon phase date
    * @method getNewMoonDateTime
    * @return DateTime             New moon phase DateTime
    */
    public function getNewMoonDateTime()
    {
        return $this->newMoonDateTime;
    }

    /**
    * Get the first quarter moon phase date
    * @method getFirstQuarterDateTime
    * @return DateTime                  First quarter moon phase DateTime
    */
    public function getFirstQuarterDateTime()
    {
        return $this->firstQuarterDateTime;
    }

    /**
    * Get the full moon phase date
    * @method getFullMoonDateTime
    * @return DateTime              Full moon phase DateTime
    */
    public function getFullMoonDateTime()
    {
        return $this->fullMoonDateTime;
    }

    /**
    * Get the last quarter moon phase date
    * @method getLastQuarterDateTime
    * @return DateTime                 Last quarter moon phase DateTime
    */
    public function getLastQuarterDateTime()
    {
        return $this->lastQuarterDateTime;
    }

    /**
    * Set the new moon phase date
    * @method setNewMoonDateTime
    * @param  DateTime           $newMoonDateTime Value to set
    */
    private function setNewMoonDateTime(DateTime $newMoonDateTime)
    {
        $this->newMoonDateTime = $newMoonDateTime;
    }

    /**
    * Set the first quarter moon phase date
    * @method setFirstQuarterDateTime
    * @param  DateTime                $firstQuarterDateTime Value to set
    */
    private function setFirstQuarterDateTime(DateTime $firstQuarterDateTime)
    {
        $this->firstQuarterDateTime = $firstQuarterDateTime;
    }

    /**
    * Set the full moon phase date
    * @method setFullMoonDateTime
    * @param  DateTime            $fullMoonDateTime Value to set
    */
    private function setFullMoonDateTime(DateTime $fullMoonDateTime)
    {
        $this->fullMoonDateTime = $fullMoonDateTime;
    }

    /**
    * Set the last quarter moon phase date
    * @method setLastQuarterDateTime
    * @param  DateTime               $lastQuarterDateTime Value to set
    */
    private function setLastQuarterDateTime(DateTime $lastQuarterDateTime)
    {
        $this->lastQuarterDateTime = $lastQuarterDateTime;
    }
}
