<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="user_users', uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"}),
 * })
 */
class User
{
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';

    /**
     * @var Id
     * @ORM\Column(type="user_user_id")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="date_immutable")
     */
    private $date;

    /**
     * @var Email|null
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $passwordHash;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, name="password_hash")
     */
    private $confirmToken;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=16, nullable=true, name="confirm_token")
     */
    private $status;

    /**
     * @var Network[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $networks;

    /**
     * @var Role
     * @ORM\Column(type="user_user_role")
     */
    private $role;

    /**
     * @var ResetToken|null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    private function __construct(Id $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function signUpByEmail(Id $id, \DateTimeImmutable $date, Email $email, string $hash, string $token): self
    {
        $user = new self($id, $date);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    public static function signUpByNetwork(Id $id, \DateTimeImmutable $date, string $network, string $identity): self
    {
        $user = new self($id, $date);
        if ($user->isActive()) {
            throw new \DomainException('User is already signed up.');
        }

        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    public function attachNetwork(string $network, string $identity): void
    {

        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }


    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if ($this->isWait()) {
            throw new \DomainException('User is not confirmed.');
        }

        if (!$this->email) {
            throw new \DomainException('User has not email.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }

        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $hash;
        $this->resetToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }

        $this->role = $role;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return string|null
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    /**
     * @return ResetToken
     */
    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }
    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }
}