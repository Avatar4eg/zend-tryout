<?php
namespace CustomUser\Entity;

use Application\Entity\SampleEntity;
use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends SampleEntity implements UserInterface, ProviderInterface
{
    const CLASS_NAME_RU = 'Пользователь';

    /**
     * @var string
     * @ORM\Column(type="string", length=128, unique=true, nullable=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string",  length=128, unique=true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $first_name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $last_name;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $state;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="CustomUser\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @var ConformationToken
     * @ORM\OneToOne(targetEntity="CustomUser\Entity\ConformationToken", mappedBy="user", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $conformation_token;

    /**
     * Initializes roles variable.
     * @throws \InvalidArgumentException
     * @throws \Zend\Math\Exception\DomainException
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get username.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string|null $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string|null
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set first name.
     *
     * @param string $first_name
     * @return User
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * Get second name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set second name.
     *
     * @param string $last_name
     * @return User
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get state.
     *
     * @return bool
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param bool $state
     * @return User
     */
    public function setState($state)
    {
        $this->state = (bool)$state;
        return $this;
    }

    /**
     * Get roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Set roles.
     *
     * @param Role[]|Collection $roles
     * @return User
     * @throws \InvalidArgumentException
     */
    public function setRoles($roles)
    {
        $roles = $roles instanceof Collection || is_array($roles) ? $roles : [$roles];
        $this->removeRoles($this->roles);
        $this->addRoles($roles);
        return $this;
    }

    /**
     * Add roles.
     *
     * @param Role[]|Collection $roles
     * @return User
     * @throws \InvalidArgumentException
     */
    public function addRoles($roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }

    /**
     * Remove roles.
     *
     * @param Role[]|Collection $roles
     * @return User
     * @throws \InvalidArgumentException
     */
    public function removeRoles($roles)
    {
        foreach ($roles as $role) {
            $this->removeRole($role);
        }
        return $this;
    }

    /**
     * Add role.
     *
     * @param Role $role
     * @throws \InvalidArgumentException
     */
    public function addRole(Role $role)
    {
        if (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role must be null or instance of CustomUser\Entity\Role');
        }
        if ($this->roles->contains($role)) {
            return;
        }
        $this->roles->add($role);
        $role->addUser($this);
    }

    /**
     * Remove role.
     *
     * @param Role $role
     * @throws \InvalidArgumentException
     */
    public function removeRole(Role $role)
    {
        if (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role must be null or instance of CustomUser\Entity\Role');
        }
        if (!$this->roles->contains($role)) {
            return;
        }
        $this->roles->removeElement($role);
        $role->removeUser($this);
    }

    /**
     * @return ConformationToken
     */
    public function getConformationToken()
    {
        return $this->conformation_token;
    }

    /**
     * @param ConformationToken $conformation_token
     * @return User
     * @throws \InvalidArgumentException
     */
    public function setConformationToken($conformation_token)
    {
        if ($conformation_token === $this->conformation_token) {
            return $this;
        }
        if ($conformation_token === null) {
            if($this->conformation_token !== null) {
                $this->conformation_token->setUser(null);
            }
            $this->conformation_token = null;
        } else {
            if (!$conformation_token instanceof ConformationToken) {
                throw new \InvalidArgumentException('$conformation_token must be null or instance of CustomUser\Entity\ConformationToken');
            }
            if ($this->conformation_token !== null) {
                $this->conformation_token->setUser(null);
            }
            $this->conformation_token = $conformation_token;
            $conformation_token->setUser($this);
        }
        return $this;
    }
}
