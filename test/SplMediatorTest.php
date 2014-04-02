<?php

namespace P\Test;

use P\SplMediator;

class SplMediatorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterObserver()
    {
        $sm = new SplMediator;
        $observer = $this->getMock('SplObserver');
        $ret = $sm->registerObserver('foo', $observer);
        $this->assertSame($observer, $ret);
    }
    
    public function testRegisterSubject()
    {
        $sm = new SplMediator;
        $subject = $this->getMock('SplSubject');
        $ret = $sm->registerSubject('foo', $subject);
        $this->assertSame($sm, $ret);
    }
    
    public function testNotify()
    {
        $sm = new SplMediator;
        $sm->registerSubject('foo', $this->getMock('SplSubject'));
        $this->assertSame($sm, $sm->notify('foo'));        
    }
}

