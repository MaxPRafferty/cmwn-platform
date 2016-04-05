<?php

namespace Application\Log\Rollbar;

use Zend\Log\Writer\AbstractWriter;

/**
 * Class Writer
 */
class Writer extends AbstractWriter
{
    /**
     * \RollbarNotifier
     */
    protected $rollbar;

    /**
     * Writer constructor.
     *
     * @param \RollbarNotifier $rollbar
     * @param array|\Traversable|null $options
     */
    public function __construct(\RollbarNotifier $rollbar, $options = null)
    {
        $this->rollbar = $rollbar;
        parent::__construct($options);
    }

    /**
     * Write a message to the log.
     *
     * @param  array $event Event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        if (isset($event['timestamp']) && $event['timestamp'] instanceof \DateTime) {
            $event['timestamp'] = $event['timestamp']->format(\DateTime::W3C);
        }

        $extra = array_diff_key($event, ['message' =>'', 'priorityName' => '', 'priority' => 0]);
        $this->rollbar->report_message($event['message'], $event['priorityName'], $extra);
    }

    public function shutdown()
    {
        $this->rollbar->flush();
    }
}
