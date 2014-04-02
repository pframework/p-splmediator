<?php

namespace P;

class SplSubjectPriorityQueue extends \SplPriorityQueue implements \SplSubject, \ArrayAccess
{
    
    protected $data = array();
    
    public function attach(\SplObserver $observer, $priority = null)
    {
        $this->insert($observer, $priority);
    }

    public function detach(\SplObserver $observer)
    {
        $os = array();
        $flags = $this->setExtractFlags(self::EXTR_BOTH);
        foreach ($this as $o) {
            if ($observer === $o['data']) {
                continue;
            }
            $os[] = $o;
        }
        foreach ($os as $o) {
            $this->attach($o['data'], $o['priority']);
        }
        $this->setExtractFlags($flags);
    }
    
    public function notify()
    {
        foreach (clone $this as $observer) {
            $observer->update($this);
        }
    }
    
    public function offsetGet($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function offsetSet($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function offsetUnset($name)
    {
        unset($this->data[$name]);
    }

    public function offsetExists($name)
    {
        return array_key_exists($name, $this->data);
    }
    
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }
    
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }
    
    public function __unset($name)
    {
        return $this->offsetUnset($name);
    }

}
