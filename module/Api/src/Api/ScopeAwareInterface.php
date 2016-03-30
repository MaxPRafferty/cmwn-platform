<?php

namespace Api;

/**
 * Interface ScopeAwareInterface
 */
interface ScopeAwareInterface
{
    /**
     * Gets the entity type to allow the rbac to set the correct scope
     *
     * @return string
     */
    public function getEntityType();
}
