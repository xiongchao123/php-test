<?php

namespace App\Foundation;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventManager as BaseEventManager;

class EventManager extends BaseEventManager
{
    public function dispatchEvent($event, ?EventArgs $eventArgs = null)
    {
        $this->dispatch($event);
    }

    /**
     * @param $event
     * @param bool $halt
     * @return array|mixed|null
     */
    public function dispatch($event, $halt = false)
    {
        $eventName = $this->getEventName($event);

        if (!$this->hasListeners($eventName)) {
            return null;
        }

        $responses = [];
        foreach ($this->getListeners($eventName) as $listener) {
            $response = call_user_func_array([$listener, 'handle'], [$event]);

            if ($halt && !is_null($response)) {
                return $response;
            }

            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }

    protected function getEventName($event)
    {
        return is_object($event) ? get_class($event) : $event;
    }
}