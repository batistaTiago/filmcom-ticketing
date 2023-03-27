<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\TicketRepositoryInterface;
use App\Services\ComputeCartStateService;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ComputeCartStateServiceTest extends TestCase
{
    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(?CartDTO $cart, ?Collection $tickets, string $cart_uuid): void
    {

        $cartRepository = $this->createMock(CartRepositoryInterface::class);
        $ticketRepository = $this->createMock(TicketRepositoryInterface::class);

        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );

        if ($cart === null) {
            $cartRepository->expects($this->once())
                ->method('getCart')
                ->with($cart_uuid)
                ->willReturn(new CartDTO('cart-uuid', $user));
        } else {
            $cartRepository->expects($this->never())->method('getCart');
        }

        if ($tickets === null) {
            $ticketRepository
                ->expects($this->once())
                ->method('findTicketsInCart')
                ->with($cart_uuid)
                ->willReturn(new Collection());
        } else {
            $ticketRepository->expects($this->never())->method('findTicketsInCart');
        }

        $service = new ComputeCartStateService($cartRepository, $ticketRepository);
        if ($cart !== null) {
            $service->setCart($cart);
        }
        if ($tickets !== null) {
            $service->setTickets($tickets);
        }

        $result = $service->execute($cart_uuid);
        $this->assertInstanceOf(CartDTO::class, $result);
    }

    public static function executeDataProvider(): array
    {
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );
        $cart = new CartDTO('cart-uuid', $user);
        $ticket = new TicketDTO('ticket-uuid', 'cart-uuid');

        return [
            [null, null, 'cart-uuid'],
            [$cart, null, 'cart-uuid'],
            [$cart, new Collection([$ticket]), 'cart-uuid'],
        ];
    }

    /**
     * @dataProvider setCartDataProvider
     */
    public function testSetCart(CartDTO $cart, ?Collection $tickets): void
    {
        $service = new ComputeCartStateService(
            $this->createMock(CartRepositoryInterface::class),
            $this->createMock(TicketRepositoryInterface::class)
        );

        if ($tickets !== null) {
            $service->setTickets($tickets);
        }

        $this->expectException(InvalidArgumentException::class);
        $service->setCart($cart);
    }

    public static function setCartDataProvider(): array
    {
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );
        $cart = new CartDTO('cart-uuid', $user);
        $ticket = new TicketDTO('ticket-uuid', 'another-cart-uuid');

        return [
            [$cart, new Collection([$ticket])],
        ];
    }

    /**
     * @dataProvider setTicketsDataProvider
     */
    public function testSetTickets(Collection $tickets): void
    {
        $service = new ComputeCartStateService(
            $this->createMock(CartRepositoryInterface::class),
            $this->createMock(TicketRepositoryInterface::class)
        );

        $this->expectException(InvalidArgumentException::class);
        $service->setTickets($tickets);
    }

    public static function setTicketsDataProvider(): array
    {
        $ticket = new TicketDTO('ticket-uuid', 'cart-uuid');
        $invalidTicket = new \stdClass();

        return [
            [new Collection([$invalidTicket])],
            [new Collection([$ticket, $invalidTicket])],
        ];
    }
}
