<?php

namespace Tests\Unit\Domain\DTO;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\UserDTO;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class CartDTOTest extends TestCase
{
    public static function successDataProvider(): array
    {
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );

        return [
            [
                'cart-uuid',
                $user,
                null,
                []
            ],
            [
                'cart-uuid',
                $user,
                new CartStatusDTO('status-uuid', 'Status Name'),
                [new TicketDTO('ticket-uuid')]
            ],
            [
                'cart-uuid',
                $user,
                new CartStatusDTO('status-uuid', 'Status Name'),
                [
                    new TicketDTO('ticket-uuid'),
                    new TicketDTO('ticket-uuid'),
                    new TicketDTO('ticket-uuid')
                ]
            ],
        ];
    }

    public static function errorDataProvider(): array
    {
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );

        return [
            [
                '',
                $user,
                null,
                [],
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                ['invalid-ticket'],
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                [new stdClass()],
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                [new TicketDTO('ticket-uuid'), 'invalid-ticket'],
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                [new TicketDTO('ticket-uuid'), new stdClass()],
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                [new TicketDTO('ticket-uuid'), []],
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                [new TicketDTO('ticket-uuid'), null],
                InvalidArgumentException::class
            ],
        ];
    }

    /**
     * @dataProvider successDataProvider
     */
    public function testConstructorSuccess(
        string $uuid,
        UserDTO $user,
        ?CartStatusDTO $status,
        array $tickets
    ) {
        $cartDTO = new CartDTO($uuid, $user, $status, $tickets);

        $this->assertSame($uuid, $cartDTO->uuid);
        $this->assertSame($user, $cartDTO->user);
        $this->assertSame($status, $cartDTO->status);
        $this->assertSame($tickets, $cartDTO->tickets);
    }

    /**
     * @dataProvider errorDataProvider
     */
    public function testConstructorError(
        string $uuid,
        UserDTO $user,
        ?CartStatusDTO $status,
        array $tickets,
        string $expectedException
    ) {
        $this->expectException($expectedException);

        new CartDTO($uuid, $user, $status, $tickets);
    }
}
