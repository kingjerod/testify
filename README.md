#Testify
###A PHP library to auto generate most of the boilerplate for unit testing.

While unit testing, you want to mock class dependencies and add expectations for function calls on those mocked dependencies. This process involves writing a lot of similar code, which is tedious and can be boring. This library aims to automate that task somewhat by determining class dependencies, mocking them, and also figuring out when those dependencies are used inside a function. The best way to show you what it does is through an example. 

Here is the class we want to test:
```php
namespace Xyz\House;

use Xyz\Garage\Garage;

class House
{
    protected $garage;

    public function __construct(Garage $garage)
    {
        $this->garage = $garage;
    }

    public function isGarageEmpty()
    {
        return $this->garage->isEmpty();
    }
}
```

And here is what a Testify generated test class would look like:
```php
namespace XYZ\House;

use XYZ\Garage\Garage as Garage;

class HouseTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Xyz\Garage\Garage */
    protected $garage;

    /** @var House */
    protected $house;

    public function setup()
    {
        $this->garage = $this->getMockBuilder('Xyz\Garage\Garage')->getMock();
        $this->house = new House($this->garage);
    }

    public function tearDown()
    {
        unset($this->garage);
        unset($this->house);
    }

    public function testIsGarageEmpty()
    {
        $this->garage->method('isEmpty')->willReturn(?); //TODO

        $expected = ?; //TODO
        $result = $this->house->isGarageEmpty();
        $this->assertSame($expected, $result);
    }
}
```

####So how do I generate test classes?

Like this:

`php console.php testify:create path/to/target/class.php path/to/save/test/class.php`

If you want to use the mocking library [Mockery](https://github.com/padraic/mockery), simple append `--mockery` onto the end. 

####That looked pretty simple, is that all Testify can do?
Testify can do more than that. It can analyze each function, determine execution paths and mock passed in variables to functions. This helps you to make sure you've tested each execution path, and possibly optimize things if you have too many different paths in one function. Let's have a look:

```php
class NumberService
{
    function addButOnlyIfAllPositive(Number $a, Number $b)
    {
        if ($a->isPositive() && $b->isPositive()) { //line #5
            return $a->getValue() + $b->getValue();
        } else {
            return false;
        }
    }
}
```

The generated test class:
```php
class NumberServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var NumberService */
    protected $numberService;

    public function setUp()
    {
        $this->numberService = new NumberService();
    }

    public function tearDown()
    {
        unset($this->numberService);
    }

    public function testAddButOnlyIfAllPositive()
    {
        /**
         * This function has 2 different code paths. They look like:
         * 1) if(Line 5) -> return(Line 6)
         * 2) else(Line 7) -> return(Line 8)
         */

        // Arguments to pass into the function being tested
        $a = $this->getMockBuilder('Testify\TestClasses\Target\Number')->getMock();
        $b = $this->getMockBuilder('Testify\TestClasses\Target\Number')->getMock();

        $expected = ?; //TODO
        $result = $this->numberService->addButOnlyIfAllPositive($a, $b);
        $this->assertSame($expected, $result);
    }
}
```

####So what's this business about Mockery?

Testify can generate mocks with the Mockery library instead of just PHPUnit. Let's see it in action:

```php
class ServiceA
{
    protected $b;
    public function __construct(ServiceB $b)
    {
        $this->b = $b;
    }

    public function isThisBad($value)
    {
        if ($this->b->isBad($value)) {
            return true;
        }

        return false;
    }
}
```

The generated test class:
```php
use Mockery as m;

class ServiceATest extends \PHPUnit_Framework_TestCase
{
    /** @var \TargetServiceB */
    protected $b;

    /** @var ServiceA */
    protected $serviceA;

    public function setUp()
    {
        $this->b = m::mock('TargetServiceB');
        $this->serviceA = new ServiceA($this->b);
    }

    public function tearDown()
    {
        unset($this->b);
        unset($this->serviceA);
    }

    public function testIsThisBad()
    {
        /**
         * This function has 2 different code paths. They look like:
         * 1) if(Line 11) -> return(Line 12)
         * 2) return(Line 15)
         */

        // Arguments to pass into the function being tested
        $value = ?; //TODO

        // Function calls to expect inside the if(Line 11) control block
        $this->b->shouldReceive('isBad')->with($value))->andReturn(?); //TODO

        $expected = ?; //TODO
        $result = $this->serviceA->isThisBad($value);
        $this->assertSame($expected, $result);
    }
}
```

###More updates hopefully in the future!
