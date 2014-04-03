<?php

namespace P\Test;

use P\SplMediator;

class SplMediatorIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testObserverIsUpdatedWithCorrectSubjectState()
    {
        $sm = new SplMediator;
        $phpunit = $this;
        $sm->registerObserver('foo', function ($subject) use ($phpunit) {
            $phpunit->assertEquals('bar', $subject->value);
        });
        $sm->foo->value = 'bar';
        $sm->notify('foo');
    }
}
 