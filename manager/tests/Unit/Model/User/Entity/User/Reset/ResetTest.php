<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    public function testSuccess():void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);

        $oldHash = $user->getPasswordHash();

        self::assertNotNull($user->getResetToken());

        $user->passwordReset(
            $now,
            $newHash = 'hash2'
        );

        self::assertNotEquals($oldHash, $user->getPasswordHash());
        self::assertEquals($newHash, $user->getPasswordHash());
        self::assertNull($user->getResetToken());
    }

    public function testTokenNotRequested(): void
    {
        $user = $this->buildSignedUpByEmailUser();

        self::expectExceptionMessage('Resetting is not requested.');

        $user->passwordReset(new \DateTimeImmutable(), 'hash');
    }

    public function testTokenExpired(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());

        self::expectExceptionMessage('Reset token is expired.');

        $user->passwordReset(
            $now->modify('+2 day'),
            'hash2'
        );

        self::assertNull($user->getResetToken());
    }

    private function buildSignedUpByEmailUser(): User
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByEmail(
            new Email('test@gmail.com'),
            'hash',
            'test'
        );

        $user->confirmSignUp();

        return $user;
    }
}
