<?php declare(strict_types=1);

namespace Tolkam\PSR14;

use Exception;
use Generator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array|ListenerProviderInterface[]
     */
    private array $providers = [];
    
    /**
     * @param ListenerProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        foreach ($providers as $provider) {
            $this->addListenerProvider($provider);
        }
    }
    
    /**
     * @param ListenerProviderInterface $listenerProvider
     *
     * @return $this
     */
    public function addListenerProvider(ListenerProviderInterface $listenerProvider): self
    {
        $this->providers[] = $listenerProvider;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        $stoppable = $event instanceof StoppableEventInterface;
        
        if ($stoppable && $event->isPropagationStopped()) {
            return $event;
        }
        
        foreach ($this->getListenersForEvent($event) as $k => $listener) {
            if (!is_callable($listener)) {
                throw new Exception(sprintf(
                    'Listener for "%s" event at index "'
                    . '%s" must be callable, %s given',
                    get_class($event),
                    $k,
                    gettype($listener)
                ));
            }
            
            $listener($event);
            
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
        }
        
        return $event;
    }
    
    /**
     * @param $event
     *
     * @return Generator
     */
    private function getListenersForEvent($event): Generator
    {
        foreach ($this->providers as $provider) {
            yield from $provider->getListenersForEvent($event);
        }
    }
}
