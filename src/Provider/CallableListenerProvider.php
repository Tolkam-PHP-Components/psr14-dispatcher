<?php declare(strict_types=1);

namespace Tolkam\PSR14\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

class CallableListenerProvider implements ListenerProviderInterface
{
    private const WILDCARD = '*';
    
    /**
     * @var array
     */
    private array $listeners = [];
    
    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $eventType => $listeners) {
            if ($event instanceof $eventType || $eventType === self::WILDCARD) {
                foreach ($listeners as $listener) {
                    yield $listener;
                }
            }
        }
    }
    
    /**
     * Adds a listener
     *
     * @param string|null $eventType
     * @param callable    $listener
     *
     * @return void
     */
    public function addListener(?string $eventType, callable $listener): void
    {
        $eventType ??= self::WILDCARD;
        $this->listeners[$eventType] ??= [];
        
        if (!in_array($listener, $this->listeners[$eventType], true)) {
            $this->listeners[$eventType][] = $listener;
        }
    }
}
