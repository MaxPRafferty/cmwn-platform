<?php

namespace Rule\Provider;


/**
 * A Rule Provider will supply data requested from rules into the Rule Item
 */
interface ProviderInterface
{
    /**
     * Gets the name of the value this provides
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets the value this provides
     *
     * Event is passed just in case the parameter is inside the event
     *
     * @return mixed
     */
    public function getValue();
}
