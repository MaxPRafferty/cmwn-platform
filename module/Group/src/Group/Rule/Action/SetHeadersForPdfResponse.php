<?php

namespace Group\Rule\Action;

use Rule\Action\ActionInterface;
use Rule\Event\Provider\EventProvider;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class SetHeadersForPdfResponse implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $eventProvider;

    /**
     * @var string
     */
    protected $pdfProvider;

    /**
     * AddAcknowledgeHeaders constructor.
     *
     * @param string $eventProvider
     * @param string $pdfProvider
     */
    public function __construct(
        string $eventProvider = EventProvider::PROVIDER_NAME,
        string $pdfProvider = PdfProvider::PROVIDER_NAME
    ) {
        $this->eventProvider = $eventProvider;
        $this->pdfProvider  = $pdfProvider;
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

        $response->getHeaders();
    }
}
