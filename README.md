Deferred Event Dispatcher
=========================

This package extends [Symfony Event Dispatcher](https://symfony.com/doc/current/components/event_dispatcher.html) and adds an opportunity to defer a group of events to handle them later during request execution.

Bootstrap code:

```php
$eventsToDefer = ['event-to-defer', 'another-event'];
$dispatcher = new DeferredEventDispatcher();
$dispatcher->addSubscriber(new DeferredSubscriber($eventsToDefer));
```

When you dispatch event from the list, it will be intercepted by `DeferredSubscriber` and stored to execute later. So that, after you did all the essential things you need to fire special event to replay events:

```php
$dispatcher->dispatch(Events::PLAY_DEFERRED);
```

## Subscribe `DeferredSubscriber` to another event ##

The idea of playing deferred events by firing `Events::PLAY_DEFERRED` may be not that good, because it sticks your codebase (not configuration) to this dispatcher.

So that, there is another opportunity:
 
```php
$subscriber = new DeferredSubscriber($eventsList);
$dispatcher->addSubscriber($subscriber);
$dispatcher->addListener('kernel.terminate', [$subscriber, 'playDeferred']);
```
