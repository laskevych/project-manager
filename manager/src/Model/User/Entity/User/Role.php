<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;


use Webmozart\Assert\Assert;

class Role
{
    private const USER = 'ROLE_USER';
    private const ADMIN = 'ROLE_ADMIN';

    private $role;

    public function __construct(string $role)
    {
        Assert::oneOf($role, [
            self::USER,
            self::ADMIN
        ]);

        $this->role = $role;
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public function isUser(): bool
    {
        return $this->role === self::USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function isEqual(self $role): bool
    {
        return $this->getRole() === $role->getRole();
    }

    public function getRole(): string
    {
        return $this->role;
    }
}