<?php
namespace PHPJava\Tests;

use PHPUnit\Framework\TestCase;

class GetFieldTest extends Base
{
    protected $fixtures = [
        'GetFieldTest',
    ];

    public function testGetField()
    {
        $actual = $this->initiatedJavaClasses['GetFieldTest']
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call('getField')
            ->getValue();

        $this->assertEquals(1, $actual);
    }
}
