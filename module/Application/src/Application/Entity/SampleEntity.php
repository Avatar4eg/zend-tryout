<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class SampleEntity
{
    const CLASS_NAME_RU = 'Базовый класс';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, options={"unsigned" = true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SampleEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = self::methodName($name, 'get');
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return $this->{$name};
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $method = self::methodName($name, 'set');
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        $this->{$name} = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return property_exists($this, $name) && isset($this->{$name});
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return property_exists($this, 'title') ? (string)$this->{'title'} : self::CLASS_NAME_RU . ' №' . $this->getId();
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param string $property_name
     * @param string $pre
     * @return string
     */
    protected static function methodName($property_name, $pre = '')
    {
        $words = explode('_', $property_name);
        $name = $pre;
        foreach ($words as $word) {
            $name .= ucfirst($word);
        }
        return $name;
    }
}
