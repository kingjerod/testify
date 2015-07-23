<?php
namespace Testify\Parser;

/**
 * Class TFunctionCall
 *
 * For function calls using class variables inside functions
 * Example: $this->service->login($user)
 * Variable will be service, function will be $login, arguments will be $user
 *
 * @package Testify\Meta
 */
class TFunctionCall
{
    /**
     * @var string
     */
    protected $variable;

    /**
     * @var string
     */
    protected $function;

    /**
     * @var string
     */
    protected $args;

    public function __construct($variable, $function, $args)
    {
        $this->variable = $variable;
        $this->function = $function;
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param mixed $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    }

    /**
     * @return mixed
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
