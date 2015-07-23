<?php
namespace Testify\Parser;

class TClass
{
    /**
     * @var string Name of class
     */
    protected $name;

    /**
     * @var string The namespace this class exists in
     */
    protected $namespace;

    /**
     * @var array An array of an 'use' statements
     */
    protected $uses;

    /**
     * @var array An array of functions the class has
     */
    protected $functions;

    public function __construct()
    {
        $this->uses = [];
        $this->functions = [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * @return array
     */
    public function getUseByAlias($alias)
    {
        if (isset($this->uses[$alias])) {
            return $this->uses[$alias];
        }

        return null;
    }

    /**
     * @param array $uses
     */
    public function setUses($uses)
    {
        $this->uses = $uses;
    }

    public function addUse(TUse $use)
    {
        $this->uses[$use->getAlias()] = $use;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @param array $functions
     */
    public function setFunctions($functions)
    {
        $this->functions = $functions;
    }

    public function addFunction(TFunction $function)
    {
        $this->functions[$function->getName()]= $function;
    }

    public function toArray()
    {
        $uses = [];
        $functions = [];
        foreach ($this->getUses() as $use) {
            $uses[$use->getAlias()] = $use->toArray();
        }

        foreach ($this->getFunctions() as $function) {
            $functions[$function->getName()] = $function->toArray();
        }

        return [
            'name' => $this->getName(),
            'namespace' => $this->getNamespace(),
            'uses' => $uses,
            'functions' => $functions
        ];
    }
}
