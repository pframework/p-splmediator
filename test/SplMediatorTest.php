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

    public function testRegisterObserverThrowsExceptionWhenNotObserver()
    {
        $sm = new SplMediator;
        $this->setExpectedException('InvalidArgumentException', 'Observers must be SplObservers or callables');
        $sm->registerObserver('foo', new \stdClass());
    }

    public function testRegisterObserverThrowsExceptionWhenNameIsNotAString()
    {
        $sm = new SplMediator;
        $this->setExpectedException('InvalidArgumentException', '$name must be a string');
        $sm->registerObserver(new \stdClass(), null);
    }
    
    public function testRegisterSubject()
    {
        $sm = new SplMediator;
        $subject = $this->getMock('SplSubject');
        $ret = $sm->registerSubject('foo', $subject);
        $this->assertSame($sm, $ret);
    }

    public function testRegisterSubjectThrowsExceptionWhenNameIsNotAString()
    {
        $sm = new SplMediator;
        $this->setExpectedException('InvalidArgumentException', '$name must be a string');
        $sm->registerSubject(new \stdClass(), $this->getMock('SplSubject'));
    }

    public function testRegisterSubjectThrowsExceptionWhenSubjectIsDoubleRegistered()
    {
        $sm = new SplMediator;
        $sm->registerSubject('one', $this->getMock('SplSubject'));
        $this->setExpectedException('InvalidArgumentException', 'A subject by name one already exist');
        $sm->registerSubject('one', $this->getMock('SplSubject'));
    }

    public function testNotify()
    {
        $sm = new SplMediator;
        $sm->registerSubject('foo', $this->getMock('SplSubject'));
        $this->assertSame($sm, $sm->notify('foo'));        
    }
}

