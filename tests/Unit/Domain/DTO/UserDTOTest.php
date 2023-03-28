<?php

namespace Tests\Unit\Domain\DTO;

use App\Domain\DTO\UserDTO;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserDTOTest extends TestCase
{
    /** @test */
    public function should_instantiate_a_user_dto_instance_with_valid_data(): void
    {
        $data = [
            'uuid' => '123',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'email_verified_at' => '2022-01-01 00:00:00',
            'password' => 'secret',
            'remember_token' => 'abc123',
        ];

        $user = UserDTO::fromArray($data);

        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals($data['uuid'], $user->uuid);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['password'], $user->password);
        $this->assertEquals($data['remember_token'], $user->remember_token);
        $this->assertInstanceOf(Carbon::class, $user->email_verified_at);
        $this->assertEquals($data['email_verified_at'], $user->email_verified_at->toDateTimeString());
    }

    /** @test */
    public function should_throw_an_exception_if_name_is_empty(): void
    {
        $data = [
            'uuid' => '123',
            'name' => '',
            'email' => 'john.doe@example.com',
            'email_verified_at' => '2022-01-01 00:00:00',
            'password' => 'secret',
            'remember_token' => 'abc123',
        ];

        $this->expectException(InvalidArgumentException::class);

        UserDTO::fromArray($data);
    }

    /** @test */
    public function should_throw_an_exception_if_uuid_is_empty(): void
    {
        $data = [
            'uuid' => '',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'email_verified_at' => '2022-01-01 00:00:00',
            'password' => 'secret',
            'remember_token' => 'abc123',
        ];

        $this->expectException(InvalidArgumentException::class);

        UserDTO::fromArray($data);
    }
}
