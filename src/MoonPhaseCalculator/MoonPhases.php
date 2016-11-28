<?php

namespace MoonPhaseCalculator;

/**
 *
 */

abstract class MoonPhases
{
    /**
     * [NEW_MOON description]
     * @var integer
     */
    const NEW_MOON = 0;

    /**
     * [WAXING_CRESCENT description]
     * @var integer
     */
    const WAXING_CRESCENT = 1;

    /**
     * [FIRST_QUARTER description]
     * @var integer
     */
    const FIRST_QUARTER = 2;

    /**
     * [WAXING_GIBBOUS description]
     * @var integer
     */
    const WAXING_GIBBOUS = 3;

    /**
     * [FULL_MOON description]
     * @var integer
     */
    const FULL_MOON = 4;

    /**
     * [WANING_GIBBOUS description]
     * @var integer
     */
    const WANING_GIBBOUS = 5;

    /**
     * [LAST_QUARTER description]
     * @var integer
     */
    const LAST_QUARTER = 6;

    /**
     * [WANING_CRESCENT description]
     * @var integer
     */
    const WANING_CRESCENT = 7;
}
