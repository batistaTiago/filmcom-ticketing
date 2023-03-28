<?php

namespace Tests\Feature\Repositories;

use App\Domain\DTO\Cart\CartStatusDTO;
use App\Domain\Repositories\CartStatusRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Models\CartStatus;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MysqlCartStatusRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->sut = $this->app->make(CartStatusRepositoryInterface::class);

    }

    /**
     * @dataProvider getByNameSuccessDataProvider
     */
    public function testGetByNameSuccess(string $statusName): void
    {
        CartStatus::factory()->create(['name' => $statusName]);

        $statusDTO = $this->sut->getByName($statusName);

        $this->assertInstanceOf(CartStatusDTO::class, $statusDTO);
        $this->assertEquals($statusName, $statusDTO->name);

        $this->assertTrue(Cache::has(md5("get-cart-status-by-name-$statusName")));
    }

    public static function getByNameSuccessDataProvider(): array
    {
        return [
            ['active'],
            ['expired'],
            ['reserved'],
            ['finished'],
        ];
    }

    /**
     * @dataProvider getByNameFailureDataProvider
     */
    public function testGetByNameFailure(string $statusName): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->sut->getByName($statusName);

        $this->assertFalse(Cache::has(md5("get-cart-status-by-name-$statusName")));
    }

    public static function getByNameFailureDataProvider(): array
    {
        return [
            ['non-existent'],
            [''],
        ];
    }
}
