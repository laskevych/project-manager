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
        $user = User::signUpByNetwork(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $network = 'vk',
            $identity = '00001255'
        );

        self::assertTrue($user->isActive());

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals('vk', $first->getNetwork());
        self::assertEquals('00001255', $first->getIdentity());

        self::assertTrue($user->getRole()->isUser());
    }
}
