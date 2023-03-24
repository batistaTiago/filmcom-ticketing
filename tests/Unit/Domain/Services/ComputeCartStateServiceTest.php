<?php

namespace Domain\Services;

use App\Domain\DTO\Cart\CartDTO;
use App\Domain\DTO\UserDTO;
use App\Domain\Services\ComputeCartStateService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;
use PHPUnit\Framework\TestCase;

class ComputeCartStateServiceTest extends TestCase
{
    private AuthManager $authManagerMock;
    private ComputeCartStateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped();

        $this->authManagerMock = $this->createMock(AuthManager::class);
        $this->service = new ComputeCartStateService($this->authManagerMock);
    }

    /**
     * @dataProvider executeProvider
     */
    public function testExecute(string|CartDTO $input)
    {
        $expectedUuid = $input instanceof CartDTO ? $input->uuid : $input;

        $userDto = new UserDTO();

        $this->authManagerMock
            ->expects($this->once())
            ->method('user')
            ->willReturn(new User());

        $result = $this->service->execute($input);

        $this->assertInstanceOf(CartDTO::class, $result);
        $this->assertEquals($expectedUuid, $result->uuid);
        $this->assertInstanceOf(UserDTO::class, $result->user);
    }

    public static function executeProvider(): array
    {
        $userDto1 = new UserDTO(
            'user1-uuid',
            'John Doe',
            'johndoe@example.com',
            Carbon::now(),
            'password123',
            'token123'
        );

        $userDto2 = new UserDTO(
            'user2-uuid',
            'Jane Smith',
            'janesmith@example.com',
            Carbon::now(),
            'password456',
            'token456'
        );

        $cartDto1 = new CartDTO('cart1-uuid', $userDto1);
        $cartDto2 = new CartDTO('cart2-uuid', $userDto2);

        return [
            'cart DTO input' => [$cartDto1],
            'string input' => ['cart2-uuid', $userDto2],
        ];
    }

}
