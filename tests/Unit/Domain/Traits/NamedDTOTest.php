<?php

namespace Tests\Unit\Domain\Traits;

use App\Domain\Traits\NamedDTO;
use PHPUnit\Framework\TestCase;
use Throwable;

class Subject { use NamedDTO; }

class NamedDTOTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideValidData
     */
    public function should_initiate_both_fields(array $data)
    {
        $namedDTO = Subject::fromArray($data);

        $this->assertEquals($data['uuid'], $namedDTO->uuid);
        $this->assertEquals($data['name'], $namedDTO->name);
    }

    /**
     * @test
     * @dataProvider provideInvalidData
     */
    public function should_throw_an_exception_if_the_data_is_invalid(array $data)
    {
        $this->expectException(Throwable::class);

        Subject::fromArray($data);
    }

    public static function provideValidData(): array
    {
        return [
            [['uuid' => '123', 'name' => 'John Doe']],
            [['uuid' => '456', 'name' => 'Jane Smith']],
            [['uuid' => '789', 'name' => 'Bob Johnson']],
            [['uuid' => 'abc', 'name' => 'Alice Brown']],
            [['uuid' => 'def', 'name' => 'Eve Green']],
        ];
    }

    public static function provideInvalidData(): array
    {
        return [
            [['uuid' => '', 'name' => 'John Doe']],
            [['uuid' => '123', 'name' => '']],
            [['uuid' => '', 'name' => '']],
            [['invalid_key' => '123', 'name' => 'John Doe']],
            [['uuid' => '123', 'invalid_key' => 'John Doe']],
        ];
    }
}
