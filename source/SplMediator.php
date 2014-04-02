<?php

namespace P;

class SplMediator implements \ArrayAccess
{    
    const ATTACH_EAGER = 'eager';
    const ATTACH_LAZY = 'lazy';
    
    protected $attachMode = self::ATTACH_EAGER;
    protected $subjects = array();
    protected $unattachedObservers = array();
    
    public function __construct($attachMode = self::ATTACH_EAGER)
    {
        $this->attachMode = $attachMode;
    }
    
    public function registerObserver($name, $observer, $priority = 0)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }
        if (is_callable($observer)) {
            $observer = new SplObserverCallable($observer);
        }
        if (!$observer instanceof \SplObserver) {
            throw new \InvalidArgumentException('Observers must be SplObservers or callables');
        }
        if ($this->attachMode == self::ATTACH_EAGER && isset($this->subjects[$name])) {
            $this->subjects[$name]->attach($observer, $priority);
        } else {
            if (!isset($this->unattachedObservers[$name])) {
                $this->unattachedObservers[$name] = new \SplPriorityQueue;
            }
            $this->unattachedObservers[$name]->insert($observer, $priority);
        }
        return $observer;
    }
    
    public function registerSubject($name, \SplSubject $subject)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }
        if (isset($this->subjects[$name])) {
            throw new \InvalidArgumentException('A subject by name ' . $name . ' already exist');
        }
        $this->subjects[$name] = $subject;
        if ($this->attachMode == self::ATTACH_EAGER && isset($this->unattachedObservers[$name])) {
            $this->attachUnattachedObservers($name);
        }
        return $this;
    }
    
    public function notify($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }
        if (!isset($this->subjects[$name])) {
            throw new \InvalidArgumentException('A subject by name ' . $name . ' does not exist');
        }
        if (isset($this->unattachedObservers[$name])) {
            $this->attachUnattachedObservers($name);
        }
        $this->subjects[$name]->notify();
        return $this;
    }
    
    protected function attachUnattachedObservers($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }
        foreach ($this->unattachedObservers[$name] as $observer) {
            $this->subjects[$name]->attach($observer);
        }
        unset($this->unattachedObservers[$name]);
    }
    
    public function offsetGet($name)
    {
        return isset($this->subjects[$name]) ? $this->subjects[$name] : null;
    }

    public function offsetSet($name, $value)
    {
        $this->registerSubject($name, $value);
    }

    public function offsetUnset($name)
    {
        unset($this->subjects[$name]);
    }

    public function offsetExists($name)
    {
        return array_key_exists($this->subjects[$name]);
    }
    
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
}
