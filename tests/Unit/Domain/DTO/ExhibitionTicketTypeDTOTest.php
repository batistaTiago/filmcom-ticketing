<?php

namespace Domain\DTO;

use App\Domain\DTO\ExhibitionTicketTypeDTO;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ExhibitionTicketTypeDTOTest extends TestCase
{
    /**
     * @test
     * @dataProvider priceAndSuccessExpectation
     */
    public function should_validate_the_price_to_be_greater_than_zero($price, $expectException)
    {
        if ($expectException) {
            $this->expectException(InvalidArgumentException::class);
        }

        $instance = new ExhibitionTicketTypeDTO(
            'arbitrary-uuid',
            'arbitrary-exhibition-uuid',
            'arbitrary-ticket-type-uuid',
            $price
        );

        $this->assertInstanceOf(ExhibitionTicketTypeDTO::class, $instance);
    }

    public static function priceAndSuccessExpectation(): array
    {
        return [
            [-100, true],
            [-10, true],
            [-1, true],
            [0, false],
            [1, false],
            [10, false],
            [100, false],
        ];
    }
}
