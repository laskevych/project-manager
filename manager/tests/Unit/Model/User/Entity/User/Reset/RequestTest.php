<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));


        $user = (new UserBuilder())->withEmail()->confirmed()->build();

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }

    public function testAlready():void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));


        $user = (new UserBuilder())->withEmail()->confirmed()->build();
        $user->requestPasswordReset($token, $now);

        self::expectExceptionMessage('Resetting is already requested.');

        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $now = new \DateTimeImmutable();
        $token1 = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->withEmail()->confirmed()->build();
        $user->requestPasswordReset($token1, $now);

        self::assertEquals($token1, $user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 day'));
        $user->requestPasswordReset($token2, $now->modify('+2 day'));

        self::assertEquals($token2, $user->getResetToken());
    }

    public function testWithoutEmail(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->withNetwork()->build();

        self::expectExceptionMessage('User has not email.');
        $user->requestPasswordReset($token, $now);
    }

    public function testUserAreNotConfirmed(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->withEmail()->build();

        self::expectExceptionMessage('User is not confirmed.');

        $user->requestPasswordReset($token, $now);
    }
}
