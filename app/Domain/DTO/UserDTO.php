<?php

namespace App\Domain\DTO;

use Carbon\Carbon;

class UserDTO
{
    public readonly string $uuid;
    public string $name;
    public string $email;
    public Carbon $email_verified_at;
    public string $password;
    public string $remember_token;

    public function __construct(
        string $uuid,
        string $name,
        string $email,
        Carbon|string|int $email_verified_at,
        string $password,
        string $remember_token
    )
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name must not be empty');
        }

        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
        $this->email_verified_at = new Carbon($email_verified_at);
        $this->password = $password;
        $this->remember_token = $remember_token;
    }

    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}
