<?php

namespace Tests\Unit\Domain\DTO;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\UserDTO;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

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
                collect()
            ],
            [
                'cart-uuid',
                $user,
                new CartStatusDTO('status-uuid', 'Status Name'),
                collect([new TicketDTO('ticket-uuid', 'cart-uuid')])
            ],
            [
                'cart-uuid',
                $user,
                new CartStatusDTO('status-uuid', 'Status Name'),
                collect([
                    new TicketDTO('ticket-uuid', 'cart-uuid'),
                    new TicketDTO('ticket-uuid', 'cart-uuid'),
                    new TicketDTO('ticket-uuid', 'cart-uuid')
                ])
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
                collect(),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                collect(['invalid-ticket']),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                collect([new stdClass()]),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                collect([new TicketDTO('ticket-uuid', 'cart-uuid'), 'invalid-ticket']),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                collect([new TicketDTO('ticket-uuid', 'cart-uuid'), new stdClass()]),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                collect([new TicketDTO('ticket-uuid', 'cart-uuid'), []]),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                collect([new TicketDTO('ticket-uuid', 'cart-uuid'), null]),
                InvalidArgumentException::class
            ],
            [
                'cart-uuid',
                $user,
                null,
                new EloquentCollection([new TicketDTO('ticket-uuid', 'cart-uuid'), null]),
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
        Collection $tickets
    ) {
        $cartDTO = new CartDTO($uuid, $user, $status, $tickets);

        $this->assertSame($uuid, $cartDTO->uuid);
        $this->assertSame($user, $cartDTO->user);
        $this->assertSame($status, $cartDTO->status);
        $this->assertEquals($tickets, $cartDTO->tickets);
    }

    /**
     * @dataProvider errorDataProvider
     */
    public function testConstructorError(
        string $uuid,
        UserDTO $user,
        ?CartStatusDTO $status,
        Collection $tickets,
        string $expectedException
    ) {
        $this->expectException($expectedException);

        new CartDTO($uuid, $user, $status, $tickets);
    }

    public function testNullTicketsParameterInitializesAsIlluminateSupportCollection()
    {
        $uuid = '12345';
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );
        $status = null;
        $tickets = null;

        $cartDTO = new CartDTO($uuid, $user, $status, $tickets);

        $this->assertInstanceOf(Collection::class, $cartDTO->tickets);
        $this->assertTrue($cartDTO->tickets->isEmpty());
    }

    public function testEloquentCollectionTicketsParameterInitializesAsIlluminateSupportCollection()
    {
        $uuid = '12345';
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );
        $status = null;
        $tickets = new EloquentCollection([
            new TicketDTO('1st ticket-uuid', 'cart-uuid'),
            new TicketDTO('2nd ticket-uuid', 'cart-uuid')
        ]);

        $cartDTO = new CartDTO($uuid, $user, $status, $tickets);

        $this->assertInstanceOf(Collection::class, $cartDTO->tickets);
        $this->assertCount(2, $cartDTO->tickets);
        $this->assertContainsOnlyInstancesOf(TicketDTO::class, $cartDTO->tickets);
    }
}
