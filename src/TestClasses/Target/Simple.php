<?php

namespace Testify\TestClasses\Target;

use Testify\TestClasses\Other\NumberService;

class Simple
{
    protected $numberService;

    public function __construct(NumberService $numberService)
    {
        $this->numberService = $numberService;
    }

    function add(Number $a, $b)
    {
        $val1 = $a->getValue();
        return $this->numberService->add($val1, $b);
    }

    function subtract(Number $a, $b)
    {
        return $this->numberService->sub($a, $b);
    }
}
