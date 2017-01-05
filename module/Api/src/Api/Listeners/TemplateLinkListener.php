<?php

namespace Api\Listeners;

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Collection;

/**
 * Class TemplateLinkListener
 */
class TemplateLinkListener
{
    /**
     * @var array Encoded characters to find
     * @todo Expand this encoded list
     */
    protected $encoded = [
        '%7B', // {
        '%7D', // }
    ];

    /**
     * @var array endocded characters to replace
     */
    protected $replace = [
        '{', // %7B
        '}', // %7D
    ];
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderCollection.post', [$this, 'addTemplate']);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'fixTemplates']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach($listener, 'ZF\Hal\Plugin\Hal');
        }
    }

    /**
     * @param Event $event
     */
    public function addTemplate(Event $event)
    {
        $collection  = $event->getParam('collection');
        if (!$collection instanceof Collection) {
            return;
        }

        /** @var \ArrayObject $payload */
        $payload = $event->getParam('payload');
        if (!isset($payload['_links']) || !isset($payload['_links']['first'])) {
            return;
        }

        //take the 1st link and remove query params
        $firstParts = parse_url($payload['_links']['first']['href']);
        if (!is_array($firstParts)) {
            return;
        }

        $firstParts['query'] = '{?page,per_page}';

        $url = $firstParts['scheme'] . '://';
        unset($firstParts['scheme']);

        $url .= implode('', $firstParts);
        $payload['_links']['find'] = [
            'href'      => $url  ,
            'templated' => true,
        ];
    }

    /**
     * Helps remove the encoding from template links
     *
     * @param Event $event
     */
    public function fixTemplates(Event $event)
    {
        /** @var \ArrayObject $payload */
        $payload = $event->getParam('payload');
        if (!isset($payload['_links'])) {
            return;
        }

        foreach ($payload['_links'] as $posistion => $link) {
            if (!isset($link['templated'])) {
                continue;
            }

            $link['href'] = str_replace($this->encoded, $this->replace, $link['href']);
            $payload['_links'][$posistion] = $link;
        }
    }
}
