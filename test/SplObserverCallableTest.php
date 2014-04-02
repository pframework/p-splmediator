<?php

namespace P\Test;

use P\SplObserverCallable;

class SplObserverCallableTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $soc = new SplObserverCallable(function () {});
        $this->assertSame($soc, $soc->update($this->getMock('\SplSubject')));
    }
}
