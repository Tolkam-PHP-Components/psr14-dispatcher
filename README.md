# tolkam/psr14-dispatcher

PSR-14 event dispatcher.

## Documentation

The code is rather self-explanatory and API is intended to be as simple as possible. Please, read the sources/Docblock if you have any questions. See [Usage](#usage) for quick start.

## Usage

````php
use Tolkam\PSR14\EventDispatcher;
use Tolkam\PSR14\Provider\CallableListenerProvider;

$event = new stdClass;
$event->value = 'value';

$listenerProvider = new CallableListenerProvider;

$listenerProvider->addListener(stdClass::class, function (object $event) {
    echo 'Event value: ' . $event->value . PHP_EOL;
});

$eventDispatcher = new EventDispatcher([
    $listenerProvider,
]);

$event = $eventDispatcher->dispatch($event);
````

## License

Proprietary / Unlicensed 🤷
