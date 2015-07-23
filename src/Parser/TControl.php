<?php
namespace Testify\Parser;

class TControl 
{
    /**
     * @var String type (if, else, return, for)
     */
    protected $type;

    /**
     * @var int Line number it occurs on
     */
    protected $line;

    /**
     * @var Parent node
     */
    protected $parent;

    /**
     * @var array children nodes
     */
    protected $children;

    /**
     * @var array function calls
     */
    protected $functionCalls;

    public function __construct($type, $line)
    {
        $this->type = $type;
        $this->line = $line;
        $this->children = [];
        $this->functionCalls = [];
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param String $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param int $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return Parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param TControl $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @param array $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function addChild(TControl $child)
    {
        $child->setParent($this);
        $this->children []= $child;
    }

    /**
     * @return array
     */
    public function getFunctionCalls()
    {
        return $this->functionCalls;
    }

    /**
     * @param array $functionCalls
     */
    public function setFunctionCalls($functionCalls)
    {
        $this->functionCalls = $functionCalls;
    }

    public function addFunctionCall(TFunctionCall $functionCall)
    {
        $this->functionCalls []= $functionCall;
    }

    public function toArray()
    {
        $children = [];
        foreach ($this->getChildren() as $child) {
            $children []= $child->toArray();
        }

        $functionCalls = [];
        foreach ($this->getFunctionCalls() as $call) {
            $functionCalls []= $call->toArray();
        }

        return [
            'type' => $this->getType(),
            'line' => $this->getLine(),
            'children' => $children,
            'functionCalls' => $functionCalls
        ];
    }
}
