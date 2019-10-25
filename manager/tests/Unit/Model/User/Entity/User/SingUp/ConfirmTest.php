<?php

namespace App\Tests\Unit\Model\User\Entity\User\SingUp;

use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{

    public function testSuccess(): void
    {
        $user = $this->buildSingUpUser();

        $user->confirmSingUp();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testAlready(): void
    {
        $user = $this->buildSingUpUser();

        $user->confirmSingUp();
        $this->expectExceptionMessage('User is already confirmed.');
        $user->confirmSingUp();
    }

    private function buildSingUpUser(): User
    {
        return new User(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('test@gmail.com'),
            'hash',
            'token'
        );
    }
}
