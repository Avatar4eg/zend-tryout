<?php
namespace CustomUser\Entity;

use Application\Entity\SampleEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Math\Rand;

/**
 * @ORM\Entity
 * @ORM\Table(name="conformation_tokens", uniqueConstraints={@ORM\UniqueConstraint(name="token_idx", columns={"token"})})
 */
class ConformationToken extends SampleEntity
{
    const CLASS_NAME_RU = 'Токен активации';
    const TOKEN_LENGTH = 32;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, unique=true)
     */
    protected $token;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $valid_till;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="CustomUser\Entity\User", inversedBy="conformation_token")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * Initializes roles variable.
     * @throws \Zend\Math\Exception\DomainException
     */
    public function __construct()
    {
        $this->setToken(Rand::getString(self::TOKEN_LENGTH, '0123456789abcdefghijklmnopqrstuvwxyz'));
        $this->setValidTill((new \DateTime('now'))->modify('+1 day'));
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return ConformationToken
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getValidTill()
    {
        return $this->valid_till;
    }

    /**
     * @param \DateTime $valid_till
     * @return ConformationToken
     */
    public function setValidTill($valid_till)
    {
        $this->valid_till = $valid_till;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return ConformationToken
     * @throws \InvalidArgumentException
     */
    public function setUser($user)
    {
        if ($user === $this->user) {
            return $this;
        }
        if ($user === null) {
            if($this->user !== null) {
                $this->user->setConformationToken(null);
            }
            $this->user = null;
        } else {
            if (!$user instanceof User) {
                throw new \InvalidArgumentException('$user must be null or instance of CustomUser\Entity\User');
            }
            if ($this->user !== null) {
                $this->user->setConformationToken(null);
            }
            $this->user = $user;
            $user->setConformationToken($this);
        }
        return $this;
    }
}
