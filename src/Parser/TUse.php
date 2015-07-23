<?php
namespace Testify\Parser;

class TUse
{
    /**
     * @var string Full class definition
     */
    protected $class;

    /**
     * @var string The name for this class as referred to in code 'Xyz as Abc'
     */
    protected $alias;

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
