<?php

namespace MoonPhaseCalculator;

/**
 * Class MoonPhases
 *
 * This class enumerate each moon phases.
 *
 * @author Nicolas Le Borgne <le.borgne.nicolas44@gmail.com>
 * @package MoonPhaseCalculator
 */
abstract class MoonPhases
{
    /**
     * Integer value for new moon
     */
    const NEW_MOON = 0;

    /**
     * Integer value for waxing crescent
     */
    const WAXING_CRESCENT = 1;

    /**
     * Integer value for first quarter
     */
    const FIRST_QUARTER = 2;

    /**
     * Integer value for waxing gibbous
     * @var integer
     */
    const WAXING_GIBBOUS = 3;

    /**
     * Integer value for full moon
     */
    const FULL_MOON = 4;

    /**
     * Integer value for waning gibbous
     */
    const WANING_GIBBOUS = 5;

    /**
     * Integer value for last quarter
     */
    const LAST_QUARTER = 6;

    /**
     * Integer value for waning crescent
     */
    const WANING_CRESCENT = 7;
}
