<?php

use MoonPhaseCalculator\MoonPhaseCalculator;
use \DateTime;
use \DateTimeZone;

class MoonPhaseCalculatorTest extends PHPUnit_Framework_TestCase
{

    public function testGetNewMoon()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-10-30 16:39:34', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getNewMoon()->format('Y-m-d H:i:s'));
    }

    public function testGetWaxingCrescent()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-03 10:15:05', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getWaxingCrescent()->format('Y-m-d H:i:s'));
    }

    public function testGetFirstQuarter()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-07 19:53:23', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getFirstQuarter()->format('Y-m-d H:i:s'));
    }

    public function testGetWaxingGibbous()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-11 12:28:54', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getWaxingGibbous()->format('Y-m-d H:i:s'));
    }

    public function testGetFullMoon()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-14 13:53:15', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getFullMoon()->format('Y-m-d H:i:s'));
    }

    public function testGetWaningGibbous()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-18 06:28:45', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getWaningGibbous()->format('Y-m-d H:i:s'));
    }

    public function testGetLastQuarter()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-21 08:35:28', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getLastQuarter()->format('Y-m-d H:i:s'));
    }

    public function testGetWaningCrescent()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-01", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $expectedResult = new DateTime('2016-11-25 01:10:58', new DateTimeZone('Europe/Paris'));
        $this->assertEquals($expectedResult->format('Y-m-d H:i:s'), $moonPhaseCalculator->getWaningCrescent()->format('Y-m-d H:i:s'));
    }

    public function testGetMoonPhaseFromDateTime()
    {
        $moonPhaseCalculator = new MoonPhaseCalculator(new DateTime("2016-11-16", new DateTimeZone('Europe/Paris')), new DateTimeZone('Europe/Paris'));
        $this->assertEquals(4, $moonPhaseCalculator->getMoonPhaseFromDateTime());
    }
}