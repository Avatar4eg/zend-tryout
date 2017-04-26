<?php
namespace CustomUser\Entity;

use Application\Entity\SampleEntity;
use BjyAuthorize\Acl\HierarchicalRoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles", uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"role_id"})})
 */
class Role extends SampleEntity implements HierarchicalRoleInterface
{
    const CLASS_NAME_RU = 'Роль пользователя';

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", name="role_id", length=255, unique=true, nullable=true)
     */
    protected $roleId;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="CustomUser\Entity\Role", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var Collection|Role[]
     * @ORM\OneToMany(targetEntity="CustomUser\Entity\Role", mappedBy="parent")
     */
    protected $children;

    /**
     * @var Collection|User[]
     * @ORM\ManyToMany(targetEntity="CustomUser\Entity\User", mappedBy="roles")
     */
    protected $users;

    /**
     * Initializes users and children roles variable.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Get role title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set role title.
     *
     * @param string $title
     * @return Role
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get role id.
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set role id.
     *
     * @param string $roleId
     * @return Role
     */
    public function setRoleId($roleId)
    {
        $this->roleId = (string)$roleId;
        return $this;
    }

    /**
     * Get parent role
     *
     * @return Role|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent role.
     *
     * @param Role $parent
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function setParent(Role $parent)
    {
        if ($parent === $this->parent) {
            return $this;
        }
        if ($parent === null) {
            if($this->parent !== null) {
                $this->parent->removeChild($this);
            }
            $this->parent = null;
        } else {
            if (!$parent instanceof Role) {
                throw new \InvalidArgumentException('$parent must be null or instance of CustomUser\Entity\Role');
            }
            if ($this->parent !== null) {
                $this->parent->removeChild($this);
            }
            $this->parent = $parent;
            $parent->addChild($this);
        }
        return $this;
    }

    /**
     * Get children roles.
     *
     * @return Role[]|Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children roles.
     *
     * @param Role[]|Collection $children
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function setChildren($children)
    {
        $children = $children instanceof Collection || is_array($children) ? $children : [$children];
        $this->removeChildren($this->getChildren());
        $this->addChildren($children);
        return $this;
    }

    /**
     * Add children roles.
     *
     * @param Role[]|Collection $children
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function addChildren($children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    /**
     * Remove children roles.
     *
     * @param Role[]|Collection $children
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function removeChildren($children)
    {
        foreach ($children as $child) {
            $this->removeChild($child);
        }
        return $this;
    }

    /**
     * Add child role.
     *
     * @param Role $child
     * @throws \InvalidArgumentException
     */
    public function addChild(Role $child)
    {
        if (!$child instanceof Role) {
            throw new \InvalidArgumentException('$child must be null or instance of CustomUser\Entity\Role');
        }
        if ($this->getChildren()->contains($child)) {
            return;
        }
        $this->getChildren()->add($child);
        $child->setParent($this);
    }

    /**
     * Remove child role.
     *
     * @param Role $child
     * @throws \InvalidArgumentException
     */
    public function removeChild(Role $child)
    {
        if (!$child instanceof Role) {
            throw new \InvalidArgumentException('$child must be null or instance of CustomUser\Entity\Role');
        }
        if (!$this->getChildren()->contains($child)) {
            return;
        }
        $this->getChildren()->removeElement($child);
        $child->setParent(null);
    }

    /**
     * Get users.
     *
     * @param bool $with_children
     * @return User[]|Collection
     */
    public function getUsers($with_children = false)
    {
        if ($with_children === false) {
            return $this->users;
        }
        $users = $this->getUsers(false);
        foreach ($this->getChildren() as $child) {
            foreach ($child->getUsers(true) as $user) {
                $users->add($user);
            }
        }
        return $users;
    }

    /**
     * Set users.
     *
     * @param User[]|Collection $users
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function setUsers($users)
    {
        $users = $users instanceof Collection || is_array($users) ? $users : [$users];
        $this->removeUsers($this->getUsers());
        $this->addUsers($users);
        return $this;
    }

    /**
     * Add users.
     *
     * @param User[]|Collection $users
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function addUsers($users)
    {
        foreach ($users as $user) {
            $this->addUser($user);
        }
        return $this;
    }

    /**
     * Remove users.
     *
     * @param User[]|Collection $users
     * @return Role
     * @throws \InvalidArgumentException
     */
    public function removeUsers($users)
    {
        foreach ($users as $user) {
            $this->removeUser($user);
        }
        return $this;
    }

    /**
     * Add user.
     *
     * @param User $user
     * @throws \InvalidArgumentException
     */
    public function addUser(User $user)
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('$user must be null or instance of CustomUser\Entity\User');
        }
        if ($this->getUsers()->contains($user)) {
            return;
        }
        $this->getUsers()->add($user);
        $user->addRole($this);
    }

    /**
     * Remove user.
     *
     * @param User $user
     * @throws \InvalidArgumentException
     */
    public function removeUser(User $user)
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('$user must be null or instance of CustomUser\Entity\User');
        }
        if (!$this->getUsers()->contains($user)) {
            return;
        }
        $this->getUsers()->removeElement($user);
        $user->removeRole($this);
    }
}
