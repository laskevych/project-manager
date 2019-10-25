<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));


        $user = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }

    public function testAlready():void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));


        $user = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);

        self::expectExceptionMessage('Resetting is already requested.');

        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $now = new \DateTimeImmutable();
        $token1 = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpByEmailUser();
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

        $user = $this->buildUser();

        self::expectExceptionMessage('User has not email.');
        $user->requestPasswordReset($token, $now);


    }
    private function buildUser(): User
    {
        return new User(
            Id::next(),
            new \DateTimeImmutable()
        );
    }

    private function buildSignedUpByEmailUser(): User
    {
        $user = $this->buildUser();

        $user->signUpByEmail(
            new Email('test@gmail.com'),
            'hash',
            'test'
        );

        $user->confirmSignUp();

        return $user;
    }
}
