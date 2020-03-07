<?php declare(strict_types=1);

namespace Tolkam\PSR14;

use Psr\EventDispatcher\EventDispatcherInterface;

interface EventDispatcherAwareInterface
{
    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);
    
    /**
     * Gets the eventDispatcher
     *
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface;
}
