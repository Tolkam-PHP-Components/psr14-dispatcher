<?php declare(strict_types=1);

namespace Tolkam\PSR14\Provider;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Tolkam\PSR14\ListenerProviderException;
use Tolkam\Utils\Str;

class ContainerAwareListenerProvider implements ListenerProviderInterface
{
    private const WILDCARD = '*';
    
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;
    
    /**
     * @var array
     */
    private array $listeners = [];
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $eventType => $listeners) {
            if ($event instanceof $eventType || $eventType === self::WILDCARD) {
                foreach ($listeners as $listener) {
                    yield $this->resolveListener($event, $listener);
                }
            }
        }
    }
    
    /**
     * Adds a listener
     *
     * @param string|null $eventType
     * @param string      $listenerName
     * @param string|null $methodName
     *
     * @return self
     */
    public function addListener(
        ?string $eventType,
        string $listenerName,
        string $methodName = null
    ): self {
        $eventType ??= self::WILDCARD;
        $this->listeners[$eventType] ??= [];
        
        if (!in_array($listenerName, $this->listeners[$eventType], true)) {
            $this->listeners[$eventType][] = [$listenerName, $methodName];
        }
        
        return $this;
    }
    
    /**
     * Adds listeners from array of arrays of form [$eventType, $listener]
     *
     * @param array $listeners
     *
     * @return self
     */
    public function addListeners(array $listeners): self
    {
        foreach ($listeners as [$eventType, $listener]) {
            $this->addListener($eventType, $listener);
        }
        
        return $this;
    }
    
    /**
     * @param object $event
     * @param array  $listener
     *
     * @return callable
     */
    private function resolveListener(object $event, array $listener): callable
    {
        [$serviceId, $methodName] = $listener;
        
        if (!$this->container->has($serviceId)) {
            throw new ListenerProviderException(sprintf(
                '"%s" was not found in container',
                $serviceId
            ));
        }
        
        $service = $this->container->get($serviceId);
        $isCallable = is_callable($service);
        $isObject = is_object($service);
        
        if (!$isObject && !$isCallable) {
            throw new ListenerProviderException(sprintf(
                'Listener "%s" must be object or callable', $serviceId
            ));
        }
        
        // simple callable
        if (!$isObject && $isCallable) {
            return $service;
        }
        
        // object and method is callable
        $methodName ??= 'on' . ucfirst(Str::classBasename(get_class($event)));
        $maybeCallable = [$service, $methodName];
        if ($isObject && is_callable([$service, $methodName])) {
            return $maybeCallable;
        }
        
        // object and callable
        if ($isObject && $isCallable) {
            return $service;
        }
        
        throw new ListenerProviderException(sprintf(
            'Listener "%s::%s" is not callable',
            get_class($service),
            $methodName
        ));
    }
}
