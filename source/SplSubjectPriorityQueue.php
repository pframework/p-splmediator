<?php
/**
 * P Framework
 * @link http://github.com/pframework
 * @license UNLICENSE http://unlicense.org/UNLICENSE
 * @copyright Public Domain
 * @author Ralph Schindler <ralph@ralphschindler.com>
 */

namespace P;

class SplSubjectPriorityQueue extends \SplPriorityQueue implements \SplSubject, \ArrayAccess
{
    /** @var array */
    protected $data = array();

    /**
     * attach() observers to subject, with priority
     *
     * @param \SplObserver $observer
     * @param null $priority
     */
    public function attach(\SplObserver $observer, $priority = null)
    {
        $this->insert($observer, $priority);
    }

    /**
     * detach() observer from subject
     *
     * @param \SplObserver $observer
     */
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

    /**
     * notify() observers
     *
     * @return void
     */
    public function notify()
    {
        /** @var \SplObserver $observer */
        foreach (clone $this as $observer) {
            $observer->update($this);
        }
    }

    /**
     * @param mixed $name
     * @return mixed|null
     */
    public function offsetGet($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param mixed $name
     */
    public function offsetUnset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        return $this->offsetUnset($name);
    }

}
