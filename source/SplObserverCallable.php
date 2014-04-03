<?php
/**
 * P Framework
 * @link http://github.com/pframework
 * @license UNLICENSE http://unlicense.org/UNLICENSE
 * @copyright Public Domain
 * @author Ralph Schindler <ralph@ralphschindler.com>
 */

namespace P;

class SplObserverCallable implements \SplObserver 
{
    protected $callable = null;
    
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }
    
    public function update(\SplSubject $subject)
    {
        if ($this->callable instanceof \Closure) {
            $callable = $this->callable->bindTo($subject, get_class($subject));
        } else {
            $callable = $this->callable;
        }
        $callable($subject);
        return $this;
    }
}