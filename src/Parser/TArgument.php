<?php
namespace Testify\Parser;

/**
 * Class TArgument
 *
 * This handles an argument to a function
 * Example: public function add(\Example\Test\Number $x, $y)
 * Type: Number
 * Name: $x
 * Namespace: \Example\Test\
 *
 * @package Testify\Meta
 */
class TArgument
{
    /**
     * @var String
     */
    protected $type;

    /**
     * @var String
     */
    protected $name;

    /**
     * @var String
     */
    protected $namespace;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
