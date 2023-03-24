<?php

namespace Tests\Unit\Domain\Traits;

use App\Domain\Traits\NamedDTO;
use PHPUnit\Framework\TestCase;
use Throwable;

class Subject { use NamedDTO; }

class NamedDTOTest extends TestCase
{
    /**
     * @dataProvider provideValidData
     */
    public function testFromArray(array $data)
    {
        $namedDTO = Subject::fromArray($data);

        $this->assertEquals($data['uuid'], $namedDTO->uuid);
        $this->assertEquals($data['name'], $namedDTO->name);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFromArrayThrowsException(array $data)
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
