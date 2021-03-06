<?php
/**
 * This file is part of the MoonPhaseCalculator package.
 *
 * (c) Nicolas Le Borgne <le.borgne.nicolas44@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MoonPhaseCalculator;

use MoonPhaseCalculator\MoonPhases;
use \DateTime;
use \DateTimeZone;
use \DateInterval;

/**
 * Class MoonPhaseCalculator
 *
 * This class allowed to compute moon phases from a php DateTime.
 * It is based on the Jean Meeus Algorythm.
 *
 * @author Nicolas Le Borgne <le.borgne.nicolas44@gmail.com>
 * @package MoonPhaseCalculator
 * @version v1.0.0 First release of the library
 * @since v1.0.0 First release of the library
 * @see http://zpag.net/Calendrier/calculer_phases_lune.htm
 * @see http://zpag.net/Calendrier/phase_moon_Algorithms.pdf
 * @license MIT https://opensource.org/licenses/MIT
 */
class MoonPhaseCalculator
{
    /**
     * TimeZone to use for returning the DateTime
     *
     * @var DateTimeZone|null
     */
    private $timeZone = null;

    /**
     * DateTime used to compute moon phases
     *
     * @var DateTime|null
     */
    private $dateTime = null;

    /**
     * Float transcription of $dateTime
     *
     * @var Float|null
     */
    private $decimalYear = null;


    /**
     * Number of second per year
     */
    const NB_SECOND_PER_YEAR = 31557600;

    /**
     * Synodic period of the moon
     */
    const MOON_SYNODIC_PERIOD = 29.53058886;

    /**
     * Construct a MoonPhaseCalculator object that allowed to compute moon phases date
     *
     * @param DateTime $dateTime
     * @param DateTimeZone|null $timeZone
     */
    function __construct(DateTime $dateTime, DateTimeZone $timeZone = null)
    {
        $this->timeZone = $timeZone;
        $this->setDateTime($dateTime);
    }

    /**
     * Get the $dateTime property
     *
     * @return DateTime|null
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set the $dateTime property and transcript it in float inside $decimalYear property
     *
     * @param DateTime $dateTime
     */
    public function setDateTime(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        $this->decimalYear = $this->convertDateTimeToFloat($dateTime);
    }

    /**
     * Get a DateTime for new moon from $dateTime property
     *
     * @return DateTime
     */
    public function getNewMoon()
    {
        $k = $this->computeK(MoonPhases::NEW_MOON);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s2 = $this->getSecondCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s2;

        return $this->convertJDToDateTime($jd);
    }

    /**
     * Get a DateTime for waxing crescent from $dateTime property
     *
     * @return DateTime
     */
    public function getWaxingCrescent()
    {
        $k = $this->computeK(MoonPhases::NEW_MOON);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s2 = $this->getSecondCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s2 + self::MOON_SYNODIC_PERIOD / 8;

        return $this->convertJDToDateTime($jd);
    }

    /**
     * Get a DateTime for first quarter from $dateTime property
     *
     * @return DateTime
     */
    public function getFirstQuarter()
    {
        $k = $this->computeK(MoonPhases::FIRST_QUARTER);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s4 = $this->getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm);
        $w = $this->computeW($e, $m, $mp, $f);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s4 + $w;

        return $this->convertJDToDateTime($jd);
    }

    /**
     * Get a DateTime for waxing gibbous from $dateTime property
     *
     * @return DateTime
     */
    public function getWaxingGibbous()
    {
        $k = $this->computeK(MoonPhases::FIRST_QUARTER);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s4 = $this->getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm);
        $w = $this->computeW($e, $m, $mp, $f);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s4 + $w + self::MOON_SYNODIC_PERIOD / 8;

        return $this->convertJDToDateTime($jd);
    }


    /**
     * Get a DateTime for full moon from $dateTime property
     *
     * @return DateTime
     */
    public function getFullMoon()
    {
        $k = $this->computeK(MoonPhases::FULL_MOON);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s3 = $this->getThirdCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s3;

        return $this->convertJDToDateTime($jd);
    }

    /**
     * Get a DateTime for waning gibbous from $dateTime property
     *
     * @return DateTime
     */
    public function getWaningGibbous()
    {
        $k = $this->computeK(MoonPhases::FULL_MOON);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s3 = $this->getThirdCorrectionsFactorsGroup($e, $mp, $m, $f, $ohm);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s3 + self::MOON_SYNODIC_PERIOD / 8;

        return $this->convertJDToDateTime($jd);
    }

    /**
     * Get a DateTime for last quarter from $dateTime property
     *
     * @return DateTime
     */
    public function getLastQuarter()
    {
        $k = $this->computeK(MoonPhases::LAST_QUARTER);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s4 = $this->getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm);
        $w = $this->computeW($e, $m, $mp, $f);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s4 - $w;

        return $this->convertJDToDateTime($jd);
    }

    /**
     * Get a DateTime for waning crescent from $dateTime property
     *
     * @return DateTime
     */
    public function getWaningCrescent()
    {
        $k = $this->computeK(MoonPhases::LAST_QUARTER);
        $t = $this->computeT($k);
        $e = $this->computeE($t);

        $m = $this->computeM($k, $t);
        $mp = $this->computeMP($k, $t);
        $f = $this->computeF($k, $t);
        $ohm = $this->computeOhm($k, $t);

        $s1 = $this->getFirstCorrectionsFactorsGroupFromPlanetaryArguments($this->getPlanetaryArguments($k, $t));
        $s4 = $this->getFourthCorrectionsFactors($e, $mp, $m, $f, $ohm);
        $w = $this->computeW($e, $m, $mp, $f);

        $jde = $this->computeJDE($k, $t);
        $jd = $jde + $s1 + $s4 - $w + self::MOON_SYNODIC_PERIOD / 8;

        return $this->convertJDToDateTime($jd);
    }


    /**
     * Get the actual moon phase from the current date
     *
     * @return int|null|string
     */
    public function getCurrentMoonPhase()
    {
        $return = null;
        $this->setDateTime(new DateTime('now', $this->timeZone));

        if ($this->dateTimeDiffToSecond($this->dateTime, $this->getNewMoon()) > 0) {
            $this->setDateTime($this->dateTime->sub(new DateInterval('P15D')));
        }

        $currentDate = new DateTime('now', $this->timeZone);
        $moonPhases = $this->getAllMoonPhases();

        if ($this->dateTimeDiffToSecond($currentDate, $moonPhases[MoonPhases::NEW_MOON]) < 0) {
            foreach ($moonPhases as $moonPhase => $value) {
                if ($this->dateTimeDiffToSecond($currentDate, $value) > 0) {
                    $return = $moonPhase - 1;
                    break;
                }
            }
        } elseif ($this->dateTimeDiffToSecond($currentDate, $moonPhases[MoonPhases::NEW_MOON]) == 0) {
            $return =  MoonPhases::NEW_MOON;
        }

        return $return;
    }

    /**
     * Get the actual moon phase from a php DateTime object
     *
     * @return int|null|string
     */
    public function getMoonPhaseFromDateTime()
    {
        $return = null;
        $initialDateTime = clone $this->dateTime;

        if ($this->dateTimeDiffToSecond($this->dateTime, $this->getNewMoon()) > 0) {
            $this->setDateTime($this->dateTime->sub(new DateInterval('P15D')));
        }

        $moonPhases = $this->getAllMoonPhases();

        if ($this->dateTimeDiffToSecond($initialDateTime, $moonPhases[MoonPhases::NEW_MOON]) < 0) {
            foreach ($moonPhases as $moonPhase => $value) {
                if ($this->dateTimeDiffToSecond($initialDateTime, $value) > 0) {
                    $return = $moonPhase - 1;
                    break;
                }
            }
        } elseif ($this->dateTimeDiffToSecond($initialDateTime, $moonPhases[MoonPhases::NEW_MOON]) == 0) {
            $return =  MoonPhases::NEW_MOON;
        }

        return $return;
    }

    /**
     * Return an array of all moon phases
     *
     * @return Array
     */
    public function getAllMoonPhases()
    {
        $moonPhases[MoonPhases::NEW_MOON]        = $this->getNewMoon();
        $moonPhases[MoonPhases::WAXING_CRESCENT] = $this->getWaxingCrescent();
        $moonPhases[MoonPhases::FIRST_QUARTER]   = $this->getFirstQuarter();
        $moonPhases[MoonPhases::WAXING_GIBBOUS]  = $this->getWaxingGibbous();
        $moonPhases[MoonPhases::FULL_MOON]       = $this->getFullMoon();
        $moonPhases[MoonPhases::WANING_GIBBOUS]  = $this->getWaningGibbous();
        $moonPhases[MoonPhases::LAST_QUARTER]    = $this->getLastQuarter();
        $moonPhases[MoonPhases::WANING_CRESCENT] = $this->getWaningCrescent();

        return $moonPhases;
    }

    /**
     * Convert a php DateTime object in a decimal year
     *
     * @param DateTime $dateTime
     * @return int
     */
    private function convertDateTimeToFloat(\DateTime $dateTime)
    {
        return ((Integer)$dateTime->format("Y")) + $dateTime->format("z") * 24 * 3600 / self::NB_SECOND_PER_YEAR;
    }

    /**
     * Convert a julian days number in a php DateTime object
     *
     * @param $julianDays
     * @return DateTime
     */
    private function convertJDToDateTime($julianDays)
    {
        $h = floor(24 * ($julianDays - floor($julianDays))) + 12;
        $m =  floor(1440 * (($julianDays - floor($julianDays)) - (($h - 12) / 24)));
        $s = 86400 * (($julianDays - floor($julianDays)) - (($h - 12) / 24) - ($m / 1440) ) ;
        $dateTime = new DateTime(jdtogregorian(floor($julianDays)), $this->timeZone);
        $dateTime->add(new DateInterval('PT'.$h.'H'.$m.'M'.floor($s).'S'));

        return $dateTime;
    }

    /**
     * Convert an angle > 360° or < 0° on a 0°-360° based angle
     *
     * @param $angle
     * @return float
     */
    private function convertAngleOn360DegInterval($angle)
    {
        return ($angle / 360 - floor($angle / 360)) * 360;
    }

    /**
     * Compute interval in second between two php DateTime object
     *
     * @param DateTime $start
     * @param DateTime $end
     * @return string
     */
    private function dateTimeDiffToSecond(DateTime $start, DateTime $end)
    {
        $diff = $start->diff($end);

        $daysToSec = (Integer) $diff->days * 86400;
        $hoursToSec = (Integer) $diff->format('%H') * 3600;
        $minutesToSec = (Integer) $diff->format('%I') * 60;
        $seconds = (Integer) $diff->format('%S');
        $diffSec = $daysToSec + $hoursToSec + $minutesToSec + $seconds;

        $signedDiffSec = $diff->format('%r').$diffSec;
        return (Integer) $signedDiffSec;
    }

    /**
     * Compute K argument
     *
     * @param $moonPhase
     * @return float
     */
    private function computeK($moonPhase)
    {
        $k = round(($this->decimalYear - 2000) * 12.3685);
        switch ($moonPhase) {
            case MoonPhases::NEW_MOON:
            case MoonPhases::WAXING_CRESCENT:
                break;
            case MoonPhases::FIRST_QUARTER:
            case MoonPhases::WAXING_GIBBOUS:
                $k += 0.25;
                break;
            case MoonPhases::FULL_MOON:
            case MoonPhases::WANING_GIBBOUS:
                $k += 0.50;
                break;
            case MoonPhases::LAST_QUARTER:
            case MoonPhases::WANING_CRESCENT:
                $k += 0.75;
                break;
            default:
                break;
        }

        return $k;
    }

    /**
     * Compute T argument
     *
     * @param $k
     * @return float
     */
    private function computeT($k)
    {
        return $k / 1236.85;
    }

    /**
     * Compute E argument
     *
     * @param $t
     * @return int
     */
    private function computeE($t)
    {
        return $e = 1 - 0.002516 * $t - 0.0000074 * pow($t, 2);
    }

    /**
     * Compute M argument
     *
     * @param $k
     * @param $t
     * @return float
     */
    private function computeM($k, $t)
    {
        return  $this->convertAngleOn360DegInterval(2.5534 + (29.10535669 * $k) - (0.00000218 * pow($t, 2)) - (0.00000011 * pow($t, 3)));
    }

    /**
     * Compute MP argument
     *
     * @param $k
     * @param $t
     * @return float
     */
    private function computeMP($k, $t)
    {
        return $this->convertAngleOn360DegInterval(201.5643 + (385.81693528 * $k) + (0.0107438 * pow($t, 2)) + (0.00001239 * pow($t, 3)) - (0.000000058 * pow($t, 4)));
    }

    /**
     * Compute F argument
     *
     * @param $k
     * @param $t
     * @return float
     */
    private function computeF($k, $t)
    {
        return $this->convertAngleOn360DegInterval(160.7108 + (390.67050274 * $k) - (0.0016341 * pow($t, 2)) - (0.00000227 * pow($t, 3)) + (0.000000011 * pow($t, 4)));
    }

    /**
     * Compute Ohm argument
     *
     * @param $k
     * @param $t
     * @return float
     */
    private function computeOhm($k, $t)
    {
        return $this->convertAngleOn360DegInterval(124.7746 - 1.56375580 * $k + 0.0020691 * pow($t, 2) + 0.00000215 * pow($t, 3));
    }

    /**
     * Compute W argument
     *
     * @param $e
     * @param $m
     * @param $mp
     * @param $f
     * @return float
     */
    private function computeW($e, $m, $mp, $f)
    {
        return 0.00306 - (0.00038 * $e * cos(deg2rad($m))) + (0.00026 * cos(deg2rad($mp))) - (0.00002 * cos(deg2rad($mp - $m))) + (0.00002 * cos(deg2rad($mp + $m))) + (0.00002 * cos(deg2rad(2 * $f)));

    }

    /**
     * Compute Julian Date Ephemeris
     *
     * @param $k
     * @param $t
     * @return float
     */
    private function computeJDE($k, $t)
    {
        return 2451550.09765 + (self::MOON_SYNODIC_PERIOD * $k) + (0.0001337 * pow($t, 2)) - (0.000000150 * pow($t, 3)) + (0.00000000073 * pow($t, 4));
    }

    /**
     * Get Planetary arguments
     *
     * @param $k
     * @param $t
     * @return array
     */
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

    /**
     * Get a sum of first corrections factors group
     *
     * @param array $planetaryArguments
     * @return number
     */
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

    /**
     * Get a sum of second corrections factors group
     *
     * @param $e
     * @param $mp
     * @param $m
     * @param $f
     * @param $ohm
     * @return number
     */
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

    /**
     * Get a sum of third corrections factors group
     *
     * @param $e
     * @param $mp
     * @param $m
     * @param $f
     * @param $ohm
     * @return number
     */
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

    /**
     * Get a sum of fourth corrections factors group
     *
     * @param $e
     * @param $mp
     * @param $m
     * @param $f
     * @param $ohm
     * @return number
     */
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
}
