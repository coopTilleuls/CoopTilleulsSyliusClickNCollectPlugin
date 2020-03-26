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

namespace Tests\CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints;

use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\Rrule;
use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\RruleValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class RruleValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new RruleValidator();
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues(?string $value): void
    {
        $this->validator->validate($value, new Rrule());
        $this->assertNoViolation();
    }

    public function getValidValues(): iterable
    {
        yield ['FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16;BYDAY=MO,TU,WE,TH,FR;DTSTART=20200316T080000;DTEND=20200316T082000'];
        yield [''];
        yield [null];
    }

    public function testInvalidValue(): void
    {
        $this->validator->validate('invalid', new Rrule(['message' => 'myMessage']));
        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', 'invalid')
            ->assertRaised();
    }

    public function testUnexpectedType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(22, new NotBlank());
    }

    public function testUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(22, new Rrule());
    }
}
