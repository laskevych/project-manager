<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use App\Model\User\Entity\User\User;


class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->withNetwork(
            $network = 'vk',
            $identity = '00001255'
        )->build();

        self::assertTrue($user->isActive());



        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals('vk', $first->getNetwork());
        self::assertEquals('00001255', $first->getIdentity());
    }
}
