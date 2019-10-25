<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;

class UserBuilder
{
    private $id;
    private $date;

    private $email;
    private $passwordHash;
    private $confirmToken;

    private $network;
    private $identity;

    public function __construct()
    {
        $this->id = Id::next();
        $this->date = new \DateTimeImmutable();
    }

    public function withEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $clone = clone $this;
        $clone->email = $email ?? new Email('test@app.com');
        $clone->passwordHash = $hash ?? 'hash';
        $clone->confirmToken = $token ?? 'token';

        return $clone;
    }

    public function withNetwork(string $network = null, string $identity = null): self
    {
        $clone = clone $this;
        $clone->network = $network ?? 'vk';
        $clone->identity = $identity ?? '00001255';

        return $clone;
    }

    public function build(): User
    {
        $user = new User(
          $this->id,
          $this->date
        );

        if($this->email) {
            $user->signUpByEmail(
                $this->email,
                $this->passwordHash,
                $this->confirmToken
            );
        }

        if($this->network) {
            $user->signUpByNetwork(
                $this->network,
                $this->identity
            );
        }

        return $user;
    }
}