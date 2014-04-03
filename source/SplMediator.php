<?php
/**
 * P Framework
 * @link http://github.com/pframework
 * @license UNLICENSE http://unlicense.org/UNLICENSE
 * @copyright Public Domain
 * @author Ralph Schindler <ralph@ralphschindler.com>
 */

namespace P;

class SplMediator implements \ArrayAccess
{    
    const ATTACH_EAGER = 'eager';
    const ATTACH_LAZY = 'lazy';

    /** @var string */
    protected $attachMode = self::ATTACH_EAGER;
    protected $allowAnonymousSubjects = true;
    /** @var \SplSubject[] */
    protected $subjects = array();
    /** @var \SplObserver */
    protected $unattachedObservers = array();

    /**
     * Constructor
     * @param string $attachMode
     */
    public function __construct($attachMode = self::ATTACH_EAGER, $allowAnonymousSubjects = true)
    {
        $this->attachMode = $attachMode;
        $this->allowAnonymousSubjects = (bool) $allowAnonymousSubjects;
    }

    /**
     * registerObserver() is used to register a callable or \SplObserver to a named subject
     *
     * @param $name
     * @param callable|\SplObserver $observer
     * @param int $priority Conditionally passed to subjects that implement
     * @return SplObserverCallable
     * @throws \InvalidArgumentException
     */
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
            $subject = $this->subjects[$name];
            ($subject instanceof \SplPriorityQueue) ? $subject->insert($observer, $priority) : $subject->attach($observer);
        } else {
            if (!isset($this->unattachedObservers[$name])) {
                $this->unattachedObservers[$name] = new \SplPriorityQueue;
            }
            /** @var \SplPriorityQueue $observerSet */
            $observerSet = $this->unattachedObservers[$name];
            $observerSet->insert($observer, $priority);
        }
        return $observer;
    }

    /**
     * registerSubject() is used to bind a SplSubject to an arbitrary name
     *
     * @param $name
     * @param \SplSubject $subject
     * @return $this
     * @throws \InvalidArgumentException
     */
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

    /**
     * notify() subjects of a particular name
     *
     * @param $name
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function notify($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }
        if (!isset($this->subjects[$name]) && $this->allowAnonymousSubjects) {
            $this->subjects[$name] = new SplSubjectPriorityQueue;
        } elseif (!isset($this->subjects)) {
            throw new \RuntimeException(
                'A subject for ' . $name . ' was not registered, and anonymous subjects are not allowed'
            );
        }
        if (isset($this->unattachedObservers[$name])) {
            $this->attachUnattachedObservers($name);
        }
        $this->subjects[$name]->notify();
        return $this;
    }

    /**
     * attachUnattachedObservers() is used when lazy attach mode is enabled and
     * observers were registered before a subject
     *
     * @param $name
     * @throws \InvalidArgumentException
     */
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

    /**
     * @param mixed $name
     * @return mixed|null|\SplSubject
     */
    public function offsetGet($name)
    {
        $subject = isset($this->subjects[$name]) ? $this->subjects[$name] : null;
        if ($subject === null && $this->allowAnonymousSubjects) {
            $this->subjects[$name] = $subject = new SplSubjectPriorityQueue;
        }
        return $subject;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->registerSubject($name, $value);
    }

    /**
     * @param mixed $name
     */
    public function offsetUnset($name)
    {
        unset($this->subjects[$name]);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->subjects);
    }

    /**
     * @param $name
     * @return mixed|null|\SplSubject
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
}
