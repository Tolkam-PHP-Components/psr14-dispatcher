<?php declare(strict_types=1);

namespace Tolkam\PSR14\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

class AggregateListenerProvider implements ListenerProviderInterface
{
    /**
     * @var ListenerProviderInterface[]
     */
    private array $providers = [];
    
    /**
     * @param ListenerProviderInterface ...$providers
     */
    public function __construct(ListenerProviderInterface ...$providers)
    {
        $this->providers = $providers;
    }
    
    /**
     * @param ListenerProviderInterface $provider
     */
    public function addProvider(ListenerProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }
    
    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->providers as $provider) {
            yield from $provider->getListenersForEvent($event);
        }
    }
}
