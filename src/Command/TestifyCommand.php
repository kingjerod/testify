<?php
namespace Testify\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Testify\Formatter\Formatter;
use Testify\Parser\Parser;

class TestifyCommand extends Command
{
    protected $parser;
    protected $formatter;
    protected $twig;

    public function __construct(Parser $parser, Formatter $formatter, $twig)
    {
        parent::__construct();
        $this->parser = $parser;
        $this->formatter = $formatter;
        $this->twig = $twig;
    }

    protected function configure()
    {
        $this
            ->setName('testify:create')
            ->setDescription('Create a test for a class')
            ->addArgument(
                'input',
                InputArgument::REQUIRED,
                'File with class to generate test fors'
            )
            ->addArgument(
                'output',
                InputArgument::REQUIRED,
                'Where to generate test class'
            )
            ->addOption(
                'mockery',
                null,
                InputOption::VALUE_NONE,
                'If set, will use Mockery for the mocking of variables'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $src = $input->getArgument('input');
        $output = $input->getArgument('output');

        $mockType = 'PHPUnit';
        if ($input->getOption('mockery')) {
            $mockType = 'Mockery';
        }

        $this->parser->parseFile($src);
        $data = $this->formatter->format($this->parser->getClassMeta(), $mockType);
        $test = $this->twig->render('class.twig', $data);
        file_put_contents($output, $test);
    }
}