# Changelog

## 1.0.0

* PHP 8 support
* Compatibility with Symfony 6 and Sylius 1.12
* Use PHP attributes instead of annotations
* Fix(ci): rerun workflows on synchronize
* Load full calendar scripts and styles from jsdelivr CDN instead of unpkg
* Replace Location typehints by LocationInterface
* Add approved by Sylius badge in README.md
* Fix time slot stays booked when selecting another shipping method
* Fix rrule script URL

## 0.2.0

* Add SlotAvailableInterface
* Add new duration field
* Fix end date field
* Do not validate if the shipping method is not click and collect
* Check for slot selection before submitting
* Fix slots not displayed if the rule contains more than one month old dates
* Fix shipment choice modification in order tunnel when the plugin is active

## 0.1.6

* Fix: Switch unpkg.com with jakubroztocil.github.io
* Fix(ci): use custom chromedriver for PHPunit & Panther

## 0.1.5

* Add endate by count
* Addition of ClickNCollect resources for export of orders
* Fix unit test with current month
* Fix typo on db migrate command in README.md

## 0.1.4

* Fix month select issue in the location configuration page

## 0.1.3

* Add Polish translation

## 0.1.2

* Add Spanish translation

## 0.1.1

* Add Bulgarian translation
* Localize the calendar
* Add collection details in the confirmation mail
* Allow to override the `Location` entity
