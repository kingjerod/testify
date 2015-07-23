<?php
namespace Testify\Parser;

class Parser
{
    protected $handle;
    protected $lineNum;
    protected $insideComment;

    protected $regexes = [
        'class' => '/class\s+(.+)/',
        'namespace' => '/namespace\s+(.+);/',
        'use' => '/use\s+(.+);/',
        'function' => '/(.*function .+)/'
    ];

    protected $controlRegexes = [
        'return' => '/return\s+/',
        'elseif' => '/\s*elseif\s*\(/',
        'if' => '/if\s*\(.+\)/',
        'else' => '/\s*else\s*/',
        'foreach' => '/foreach\s*\(.+\)/',
        'for' => '/for\s*\(.+\)/',
        'while' => '/while\s*\(.+\)/',
        'do' => '/do/',
        'switch' => '/switch\s*\(.+\)/'
    ];

    protected $typeHintable = [
        'array',
        'callable',
    ];

    /**
     * @var TClass
     */
    protected $class;

    public function parseFile($file)
    {
        $this->lineNum = 0;
        $this->insideComment = false;
        $this->openFile($file);
        $this->class = new TClass();
        $this->startParsing();
    }

    public function readLine()
    {
        $line = fgets($this->handle);
        if($line !== false) {
            $this->lineNum++;
            //Check for block comments
            if (strpos($line, '/*') !== false) {
                $this->insideComment = true;
            }
            if (strpos($line, '*/') !== false) {
                $this->insideComment = false;
            }
            if ($this->insideComment || strpos($line, '//') === 0) {
                return $this->readLine();
            }
            return trim($line);
        }
        return null;
    }

    public function getLineNumber()
    {
        return $this->lineNum;
    }

    public function getClassMeta()
    {
        return $this->class;
    }

    protected function openFile($file)
    {
        $this->handle = fopen($file, "r");
        if (!$this->handle) {
            // error opening the file.
            throw new \Exception('Failed to open file: ' . $file);
        }
    }

    protected function closeFile()
    {
        fclose($this->handle);
    }

    protected function startParsing()
    {
        while (($line = $this->readLine()) !== null) {
            foreach ($this->regexes as $type => $regex) {
                if (preg_match($regex, $line, $matches) === 1) {
                    $func = 'parse' . ucfirst($type);
                    $this->$func($matches[1]);
                }
            }
        }
    }

    protected function parseClass($match)
    {
        $this->class->setName($match);
    }

    protected function parseNamespace($match)
    {
        $this->class->setNamespace($match);
    }

    protected function parseUse($match)
    {
        $use = new TUse();
        if (strpos($match, ' as ') !== false) {
            //Using alias
            $parts = explode(' as ', $match);
            $use->setClass($parts[0]);
            $use->setAlias($parts[1]);
        } else {
            preg_match("/.+\\\(.+)/", $match, $matches); //get the class name, it becomes alias
            $alias = $matches[1];
            $use->setClass($match);
            $use->setAlias($alias);
        }
        $this->class->addUse($use);
    }

    protected function parseFunction($match)
    {
        $function = new TFunction();

        //Matches go [1] = type, [2] = name, [3] = arguments
        preg_match('/(public|protected|private|)\s*function (.+)\s*\((.*)\)/', $match, $matches);
        $function->setType(!empty($matches[1]) ? $matches[1] : 'public');
        $function->setName($matches[2]);

        //Parse arguments
        if (!empty($matches[3])) {
            $parts = explode(',', $matches[3]);
            foreach ($parts as $part) {
                $part = trim($part);
                $part = str_replace('&', '', $part); //remove by reference
                $arg = new TArgument();

                if (strpos($part, '=') !== false) {
                    //Default value, trim it off
                    preg_match('/(.+)=/', $part, $argMatches);
                    $part = trim($argMatches[1]);
                }

                $partExploded = explode(' ', $part);
                if (count($partExploded) === 1) {
                    //No type for argument
                    $arg->setName($partExploded[0]);
                } else {
                    $type = $partExploded[0];
                    $arg->setType($type);
                    $arg->setName($partExploded[1]);

                    if ($this->class->getUseByAlias($type) !== null) {
                        $class = $this->class->getUseByAlias($type)->getClass();
                        $arg->setNamespace(str_replace($type, '', $class));
                    } elseif (!in_array($type, $this->typeHintable)) {
                        $arg->setNamespace($this->class->getNamespace());
                    }
                }

                $function->addArgument($arg);
            }
        }

        //Read function lines to determine code blocks and when class variables are used
        $root = new TControl('function', $this->getLineNumber());
        $function->setRootControl($root);
        $currentNode = $root;

        while (($line = $this->readLine()) !== null) {
            $endFunction = false;
            $closingBrackets = substr_count($line, '}');
            while ($closingBrackets > 0) {
                $closingBrackets--;
                if ($currentNode != $root) {
                    $currentNode = $currentNode->getParent();
                } else {
                    $endFunction = true; //End of function
                }
            }

            //Determine control blocks like if, for etc
            foreach ($this->controlRegexes as $controlType => $regex) {
                if (preg_match($regex, $line) === 1) {
                    $node = new TControl($controlType, $this->getLineNumber());
                    $currentNode->addChild($node);
                    $currentNode = $node;
                    continue;
                }
            }

            //See if any class variables are used like $this->service->login($user)
            // [1] = variable, [2] = function, [3] = function args
            if (preg_match('/\$this->(\w+)->(\w+)\((.*)\)/', $line, $matches) === 1) {
                $functionCall = new TFunctionCall('$this->' . $matches[1], $matches[2], $matches[3]);
                $currentNode->addFunctionCall($functionCall);
            }

            //See if any arguments to the function are used like $param1->login($user)
            // [1] = variable, [2] = function, [3] = function args
            if (preg_match('/\$(\w+)->(\w+)\((.*)\)/', $line, $matches) === 1) {
                if ($matches[1] !== 'this' && $function->getArgument($matches[1]) !== null) {
                    $functionCall = new TFunctionCall('$' . $matches[1], $matches[2], $matches[3]);
                    $currentNode->addFunctionCall($functionCall);
                }
            }

            if ($currentNode->getType() == 'return') {
                //Return statements don't span more than 1 line
                $currentNode = $currentNode->getParent();
            }

            //Stop parsing more lines
            if ($endFunction) {
                break;
            }
        }
        $this->class->addFunction($function);
    }
}
