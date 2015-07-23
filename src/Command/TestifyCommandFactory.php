<?php
namespace Testify\Command;

use Testify\Parser\Parser;
use Testify\Formatter\Formatter;

class TestifyCommandFactory
{
    public function create()
    {
        $loader = new \Twig_Loader_Filesystem('src/Twig');
        $twig = new \Twig_Environment($loader, ['debug' => true]);
        $twig->addExtension(new \Twig_Extension_Debug());

        $parser = new Parser();
        $formatter = new Formatter();
        return new TestifyCommand($parser, $formatter, $twig);
    }
}