<?php
namespace Testify\Parser;

use Tree\Node\Node;

class TFunction
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type; //public, private

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var TControl $rootControl
     */
    protected $rootControl;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param mixed $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    public function addArgument(TArgument $argument)
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    public function getArgument($name)
    {
        if (isset($this->arguments[$name])) {
            return $this->arguments[$name];
        }

        return null;
    }

    /**
     * @return TControl
     */
    public function getRootControl()
    {
        return $this->rootControl;
    }

    /**
     * @param TControl $rootControl
     */
    public function setRootControl($rootControl)
    {
        $this->rootControl = $rootControl;
    }

    public function toArray()
    {
        $args = [];
        foreach ($this->getArguments() as $arg) {
            $args []= $arg->toArray();
        }

        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'arguments' => $args,
            'paths' => [$this->getRootControl()->toArray()]
        ];
    }
}
