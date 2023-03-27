<?php

namespace App\Domain\DTO;

use Carbon\Carbon;
use InvalidArgumentException;
use JsonSerializable;

class UserDTO implements JsonSerializable
{
    public readonly string $uuid;
    public string $name;
    public string $email;
    public Carbon $email_verified_at;
    public string $password;
    public string $remember_token;

    public const ATTRIBUTES = [
        'uuid',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
    ];

    public function __construct(
        string $uuid,
        string $name,
        string $email,
        Carbon|string|int $email_verified_at,
        string $password,
        string $remember_token
    )
    {
        if (empty($uuid)) {
            throw new InvalidArgumentException('Name must not be empty');
        }

        if (empty($name)) {
            throw new InvalidArgumentException('Name must not be empty');
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
        return new static(...array_intersect_key($data, array_flip(self::ATTRIBUTES)));
    }

    public function jsonSerialize(): array
    {
        $data = (array) $this;
        unset($data['password']);
        return $data;
    }
}
