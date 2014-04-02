<?php

namespace P\Test;

use P\SplSubjectPriorityQueue;

class SplSubjectPriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    public function testAttach()
    {
        $sspq = new SplSubjectPriorityQueue();
        $observer = $this->getMock('SplObserver');
        $this->assertNull($sspq->attach($observer));
    }

    public function testDetach()
    {
        $sspq = new SplSubjectPriorityQueue();
        $observer = $this->getMock('SplObserver');
        $this->assertNull($sspq->detach($observer));
    }

    public function testNotify()
    {
        $sspq = new SplSubjectPriorityQueue();
        $sspq->notify();
    }


    public function testOffsetGet()
    {
        $sspq = new SplSubjectPriorityQueue();
        $sspq->offsetSet('foo', 5);
        $this->assertEquals(5, $sspq->offsetGet('foo'));
    }

    public function testOffsetSet()
    {
        $sspq = new SplSubjectPriorityQueue();
        $this->assertNull($sspq->offsetSet('foo', 5));
    }

    public function testOffsetUnset()
    {
        $sspq = new SplSubjectPriorityQueue();
        $this->assertNull($sspq->offsetUnset('foo'));
    }

    public function testOffsetExists()
    {
        $sspq = new SplSubjectPriorityQueue();
        $this->assertFalse($sspq->offsetExists('foo'));
        $sspq->offsetSet('foo', 5);
        $this->assertTrue($sspq->offsetExists('foo'));
    }

    public function test__get()
    {
        $sspq = new SplSubjectPriorityQueue();
        $sspq->__set('foo', 5);
        $this->assertEquals(5, $sspq->__get('foo'));
    }

    public function test__set()
    {
        $sspq = new SplSubjectPriorityQueue();
        $this->assertNull($sspq->__set('foo', 5));
    }

    public function test__isset()
    {
        $sspq = new SplSubjectPriorityQueue();
        $this->assertFalse($sspq->__isset('foo'));
        $sspq->__set('foo', 5);
        $this->assertTrue($sspq->__isset('foo'));
    }

    public function test__unset()
    {
        $sspq = new SplSubjectPriorityQueue();
        $this->assertNull($sspq->__unset('foo'));
    }

}
 