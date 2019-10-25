<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use PHPUnit\Framework\TestCase;
use App\Model\User\Entity\User\User;


class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'vk',
            $identity = '00001255'
        );

        self::assertTrue($user->isActive());



        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals('vk', $first->getNetwork());
        self::assertEquals('00001255', $first->getIdentity());
    }

    public function testAlready(): void
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'vk',
            $identity = '00001255'
        );

        $this->expectExceptionMessage('User is already signed up.');

        $user->signUpByNetwork($network, $identity);
    }
}
