<?php

/*
 * This file is part of Les-Tilleuls.coop's Click 'N' Collect project.
 *
 * (c) Les-Tilleuls.coop <contact@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CoopTilleuls\SyliusClickNCollectPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * A location where the user can collect the order.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface LocationInterface extends ResourceInterface, CodeAwareInterface, TimestampableInterface, ToggleableInterface
{
    public function getId();

    public function setId($id): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getCountryCode(): ?string;

    public function setCountryCode(?string $countryCode): void;

    public function getProvinceCode(): ?string;

    public function setProvinceCode(?string $provinceCode): void;

    public function getProvinceName(): ?string;

    public function setProvinceName(?string $provinceName): void;

    public function getStreet(): ?string;

    public function setStreet(?string $street): void;

    public function getCity(): ?string;

    public function setCity(?string $city): void;

    public function getPostcode(): ?string;

    public function setPostcode(?string $postcode): void;

    /**
     * A valid RFC5545 recurrence rule (RRULE).
     * Example: Every 20 minutes, for 20 minutes, from 9:00 AM to 4:40 PM every weekday, starting from 16/03/2020.
     *
     * FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16;BYDAY=MO,TU,WE,TH,FR;DTSTART=20200316T080000;DTEND=20200316T082000
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.5.3
     */
    public function getRrule(): string;

    public function setRrule(string $rrule): void;

    /**
     * To compute DTSTART of the RRULE. In minutes.
     */
    public function getOrderPreparationDelay(): int;

    public function setOrderPreparationDelay(int $orderPreparationDelay): void;

    /**
     * Number people who can collect an order at the same time. Ex: number of lockers, of boxes...
     */
    public function getThroughput(): int;

    public function setThroughput(int $throughput): void;

    /**
     * If true, an 4 numbers PIN will be generated and associated to this order. Useful when using lockers.
     */
    public function isGeneratePin(): bool;

    public function setGeneratePin(bool $generatePin): void;

    /**
     * @return ClickNCollectShippingMethodInterface[]|Collection
     */
    public function getShippingMethods(): Collection;

    public function addShippingMethod(ClickNCollectShippingMethodInterface $shippingMethod): void;

    public function removeShippingMethod(ClickNCollectShippingMethodInterface $shippingMethod): void;
}
