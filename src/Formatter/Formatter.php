<?php
namespace Testify\Formatter;

use Testify\Parser\TClass;

class Formatter
{
    public function format(TClass $class, $mockType)
    {
        $data = $class->toArray();
        $data['mock'] = ['type' => $mockType];

        //Get things ready for the setUp function
        $setup = ['args' => [], 'testedClassVar' => lcfirst($data['name'])];
        if (isset($data['functions']['__construct'])) {
            //Fix arg names
            $constructorData = $data['functions']['__construct'];
            $constructorArgs = [];
            foreach ($constructorData['arguments'] as $arg) {
                $nameWithThis = '$this->' . str_replace('$', '', $arg['name']);
                $constructorArgs []= $nameWithThis;
                $newArg = [
                    'name' => $arg['name'],
                    'nameWithThis' => $nameWithThis,
                ];
                if (isset($arg['type']) && isset($arg['namespace'])) {
                    $newArg['mock'] = $arg['namespace'] . $arg['type'];
                }
                $setup['args'] []= $newArg;
            }
            $setup['argString'] = join(', ', $constructorArgs);
            unset($data['functions']['__construct']); //Don't want to test the constructor
        }
        $data['setup'] = $setup;

        //Fix up functions
        foreach ($data['functions'] as &$function) {
            $args = [];
            foreach ($function['arguments'] as &$arg) {
                if (!empty($arg['type']) && !empty($arg['namespace'])) {
                    $arg['mock'] = $arg['namespace'] . '\\' . $arg['type'];
                }
                $args []= $arg['name'];
            }
            $function['argString'] = join(', ', $args);
            $function['flatPaths'] = $this->calculateFlatPath($function['paths'][0]);
        }

        return $data;
    }

    protected function calculateFlatPath($current, &$paths = [], $currentPath = [])
    {
        if ($current['type'] !== 'function') {
            $currentPath []= $current['type'] . '(Line '. $current['line'] . ')';
        }

        if (count($current['children']) === 0) {
            $paths []= $currentPath;
            return $paths;
        }

        foreach ($current['children'] as $child) {
            $this->calculateFlatPath($child, $paths, $currentPath);
        }
        return $paths;
    }
}
