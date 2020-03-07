<?php declare(strict_types=1);

namespace Tolkam\PSR14;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

trait EventDispatcherAwareTrait
{
    /**
     * @var EventDispatcherInterface|null
     */
    private ?EventDispatcherInterface $eventDispatcher = null;
    
    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * Gets the eventDispatcher
     *
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
    
    /**
     * @param object $event
     *
     * @return StoppableEventInterface
     */
    public function dispatchEvent(object $event): object
    {
        if ($this->eventDispatcher) {
            return $this->eventDispatcher->dispatch($event);
        }
        
        return $event;
    }
}
