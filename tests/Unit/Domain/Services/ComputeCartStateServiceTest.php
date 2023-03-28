<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\TicketDTO;
use App\Domain\DTO\UserDTO;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\TicketRepositoryInterface;
use App\Services\ComputeCartStateService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ComputeCartStateServiceTest extends TestCase
{
    /**
     * @test
     * @dataProvider noPreloadedCartAndTicketsDataProvider
     */
    public function should_call_its_repositories_if_nothing_is_preloaded(string $cart_uuid): void
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

        $cartRepository->expects($this->once())
            ->method('getCart')
            ->with($cart_uuid)
            ->willReturn(new CartDTO($cart_uuid, $user));

        $ticketRepository
            ->expects($this->once())
            ->method('findTicketsInCart')
            ->with($cart_uuid)
            ->willReturn(new Collection());

        $service = new ComputeCartStateService($cartRepository, $ticketRepository);
        $result = $service->execute($cart_uuid);
        $this->assertInstanceOf(CartDTO::class, $result);
    }

    public static function noPreloadedCartAndTicketsDataProvider(): array
    {
        return [
            ['cart-uuid-1'],
            ['cart-uuid-2'],
            ['cart-uuid-3'],
        ];
    }

    /**
     * @test
     * @dataProvider preloadedCartDataProvider
     */
    public function should_call_only_the_ticket_repository_if_cart_is_preloaded(CartDTO $cart, string $cart_uuid): void
    {
        $cartRepository = $this->createMock(CartRepositoryInterface::class);
        $ticketRepository = $this->createMock(TicketRepositoryInterface::class);

        $cartRepository->expects($this->never())->method('getCart');

        $ticketRepository
            ->expects($this->once())
            ->method('findTicketsInCart')
            ->with($cart_uuid)
            ->willReturn(new Collection());

        $service = new ComputeCartStateService($cartRepository, $ticketRepository);
        $service->setCart($cart);
        $result = $service->execute($cart_uuid);
        $this->assertInstanceOf(CartDTO::class, $result);
    }

    public static function preloadedCartDataProvider(): array
    {
        // Create different CartDTO instances for each dataset
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );
        $cart1 = new CartDTO('cart-uuid-1', $user);
        $cart2 = new CartDTO('cart-uuid-2', $user);
        $cart3 = new CartDTO('cart-uuid-3', $user);

        return [
            [$cart1, 'cart-uuid-1'],
            [$cart2, 'cart-uuid-2'],
            [$cart3, 'cart-uuid-3'],
        ];
    }

    /**
     * @test
     * @dataProvider preloadedCartAndTicketsDataProvider
     */
    public function should_not_call_the_repositories_if_both_tickets_and_cart_are_preloaded(
        CartDTO $cart,
        Collection $tickets,
        string $cart_uuid
    ): void
    {
        $cartRepository = $this->createMock(CartRepositoryInterface::class);
        $ticketRepository = $this->createMock(TicketRepositoryInterface::class);

        $cartRepository->expects($this->never())->method('getCart');
        $ticketRepository->expects($this->never())->method('findTicketsInCart');

        $service = new ComputeCartStateService($cartRepository, $ticketRepository);
        $service->setCart($cart);
        $service->setTickets($tickets);
        $result = $service->execute($cart_uuid);
        $this->assertInstanceOf(CartDTO::class, $result);
    }

    public static function preloadedCartAndTicketsDataProvider(): array
    {
        // Create different CartDTO instances and corresponding TicketDTO collections for each dataset
        $user = new UserDTO(
            'user-uuid',
            'John Doe',
            'johndoe@example.com',
            '2022-01-01 12:00:00',
            'password',
            'remember-token'
        );
        $cart1 = new CartDTO('cart-uuid-1', $user);
        $cart2 = new CartDTO('cart-uuid-2', $user);
        $cart3 = new CartDTO('cart-uuid-3', $user);

        $ticket1 = new TicketDTO('ticket-uuid-1', 'cart-uuid-1');
        $ticket2 = new TicketDTO('ticket-uuid-2', 'cart-uuid-2');
        $ticket3 = new TicketDTO('ticket-uuid-3', 'cart-uuid-3');

        return [
            [$cart1, new Collection([$ticket1]), 'cart-uuid-1'],
            [$cart2, new Collection([$ticket2]), 'cart-uuid-2'],
            [$cart3, new Collection([$ticket3]), 'cart-uuid-3'],
        ];
    }
}
