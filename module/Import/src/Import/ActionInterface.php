<?php

namespace Import;

/**
 * ActionInterface
 *
 * Parsers will create a list of actions to be applied.
 */
interface ActionInterface
{

    /**
     * Make this pretty human readable so we can understand what is going on
     *
     * @return string
     */
    public function __toString();

    /**
     * Process the action
     *
     * @return void
     */
    public function execute();

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority();
}
