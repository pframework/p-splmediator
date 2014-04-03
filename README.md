README
======

"A mediator to unleash the full potential of SplSubjects and SplObservers."

P\SplMediator and associated classes are intended to make full use of Spl's
SplSubject and SplObserver to create a robust Subject/Observer system.  This
implementation could serve as the basis for a light-weight event manager,
signal/slot, dispatcher, you-name-it.

The benefits of a mediator for subjects and observers is that all wiring and
subject/observer coordination can happen independent of one another, and
through the use of a commonly accepted name.  Subjects don't have to exist
yet for Observers to be registered and visa-versa.

Another large benefit is that the base interfaces are already a part of PHP,
making the constituent objects for this mediator already portable and well
known.

Hello World?
------------

```php
use P\SplMediator;

$sm = new SplMediator;

// create an observer for the hello.world subject
$sm->registerObserver(
    'hello.world',
    function (\SplSubject $subject) { echo 'Hello ' . $subject->name; }
);

// create an observer that is registered with a higher priority, to set some values
$sm->registerObserver(
    'hello.world',
    function (\SplSubject $subject) { $subject->name = 'Ralph'; },
    2
);

// since no specific \SplSubject was registered, \SplSubjectPriorityQueue will stand in
$sm->notify('hello.world'); // Hello Ralph
```

Hello World With SplSubjects and SplObservers
---------------------------------------------

```php
use P\SplMediator;

$sm = new SplMediator;

class HelloWorld implements \SplSubject
{
    public $name;
    protected $observers = array();

    public function __construct($name) {
        $this->name = $name;
    }

    public function attach(\SplObserver $observer) {
        $this->observers[] = $observer;
    }
    public function detach(\SplObserver $observer) {
        $index = array_search($observer, $this->observers);
        if ($index !== false) {
            unset($this->observers[$index]);
        }
    }
    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}

class HelloWorldObserver implements \SplObserver
{
    public function update(\SplSubject $subject) {
        echo 'Hello ' . $subject->name;
    }
}

$sm->registerObserver('hello.world', new HelloWorldObserver);
$sm->registerSubject('hello.world', new HelloWorld('Ralph'));
$sm->notify('hello.world'); // Hello Ralph
```
