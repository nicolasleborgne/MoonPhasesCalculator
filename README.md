[![Latest Stable Version](https://poser.pugx.org/nicolasleborgne/moon-phases-calculator/v/stable)](https://packagist.org/packages/nicolasleborgne/moon-phases-calculator)
[![Latest Unstable Version](https://poser.pugx.org/nicolasleborgne/moon-phases-calculator/v/unstable)](https://packagist.org/packages/nicolasleborgne/moon-phases-calculator)
[![Total Downloads](https://poser.pugx.org/nicolasleborgne/moon-phases-calculator/downloads)](https://packagist.org/packages/nicolasleborgne/moon-phases-calculator)
[![License](https://poser.pugx.org/nicolasleborgne/moon-phases-calculator/license)](https://packagist.org/packages/nicolasleborgne/moon-phases-calculator)
# MoonPhaseCalculator
A php package that allowed to compute moon phases. Accuracy is about a few minutes.

##Installation
Install the latest version with
```bash
$ composer require nicolasleborgne/moon-phases-calculator
```
## Basic Usage
```php
<?php

use MoonPhaseCalculator\MoonPhaseCalculator;

/**
 * Create a new calculator object, 
 * it takes a mandatory DateTime first parameter and an optionnal timezone parameter
 */
$moonPhasesCalculator = new MoonPhaseCalculator(
                        new DateTime("2016-11-16", new DateTimeZone('Europe/Paris')), 
                        new DateTimeZone('Europe/Paris')
                        );
/**
 * Call getMoonPhaseFromDateTime() method to get the moon phase
 * associated to the dateTime given in param at the instanciation
 */
$moonPhase = $moonPhasesCalculator->getMoonPhaseFromDateTime();

/**
 * You can also change the DateTime wich used for calcul
 */
$moonPhasesCalculator->setDateTime(new DateTime("2016-12-08", new DateTimeZone('Europe/Paris'));
 
/**
 * To get directly the moon phase from the current date
 */
$moonPhasesCalculator->getCurrentMoonPhase();

/**
 * To get moon phases date for the current synodic period
 */
$moonPhasesCalculator->getNewMoon();
$moonPhasesCalculator->getWaxingCrescent();
$moonPhasesCalculator->getFirstQuarter();
$moonPhasesCalculator->getWaxingGibbous();
$moonPhasesCalculator->getFullMoon();
$moonPhasesCalculator->getWaningGibbous();
$moonPhasesCalculator->getLastQuarter();
$moonPhasesCalculator->getWaningCrescent();
```
