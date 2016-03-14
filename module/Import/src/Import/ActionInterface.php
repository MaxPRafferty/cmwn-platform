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
