<?php declare(strict_types=1);

namespace Tolkam\PSR14;

use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ListenerProviderInterface
     */
    private ListenerProviderInterface $listenerProvider;
    
    /**
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
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
        
        foreach ($this->listenerProvider->getListenersForEvent($event) as $k => $listener) {
            if (!is_callable($listener)) {
                throw new Exception(sprintf(
                    'Listener for "%s" event provided by "%s" at index "'
                    . '%s" must be callable, %s given',
                    get_class($event),
                    get_class($this->listenerProvider),
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
}
