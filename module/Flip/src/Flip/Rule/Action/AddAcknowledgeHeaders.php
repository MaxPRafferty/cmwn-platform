<?php

namespace Flip\Rule\Action;

use Flip\EarnedFlipInterface;
use Flip\Rule\Provider\AcknowledgeFlip;
use Rule\Action\ActionInterface;
use Rule\Event\Provider\EventProvider;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

/**
 * This Action will add the acknowledge flip headers to an HTTP response
 */
class AddAcknowledgeHeaders implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $eventProvider;

    /**
     * @var string
     */
    protected $flipProvider;

    /**
     * AddAcknowledgeHeaders constructor.
     *
     * @param string $eventProvider
     * @param string $flipProvider
     */
    public function __construct(
        string $eventProvider = EventProvider::PROVIDER_NAME,
        string $flipProvider = AcknowledgeFlip::PROVIDER_NAME
    ) {
        $this->eventProvider = $eventProvider;
        $this->flipProvider  = $flipProvider;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        // Get the event and check for MVC Event
        /** @var MvcEvent $event */
        $event = $item->getParam($this->eventProvider);
        static::checkValueType($event, MvcEvent::class);

        // Make sure this is an Http Response and bail if not
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        // Only give on success codes
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return;
        }

        /** @var EarnedFlipInterface $flip */
        $flip = $item->getParam($this->flipProvider);
        static::checkValueType($flip, EarnedFlipInterface::class);

        $response->getHeaders()
            ->addHeaderLine('X-Flip-Earned', $flip->getTitle())
            ->addHeaderLine('X-Flip-Id', $flip->getFlipId())
            ->addHeaderLine('X-Flip-Ack', $flip->getAcknowledgeId());
    }
}
