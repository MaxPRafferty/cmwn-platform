<?php

namespace Application\Utils\Type;

/**
 * An Interface that allows setting a type property
 *
 * @SWG\Definition(
 *     definition="OuType",
 *     description="Adds a qualifier to an Organization or Group that the user can understand",
 *     @SWG\Property(
 *          type="string",
 *          property="type",
 *          description="The type",
 *          enum={"generic","district","school","class"}
 *     )
 * )
 */
interface TypeInterface
{
    const TYPE_GENERIC  = 'generic';
    const TYPE_DISTRICT = 'district';
    const TYPE_SCHOOL   = 'school';
    const TYPE_CLASS    = 'class';

    /**
     * Sets the type property
     *
     * @param string $type
     *
     * @return TypeInterface
     */
    public function setType(string $type);

    /**
     * Gets the type
     *
     * SHOULD return static::TYPE_GENERIC
     *
     * @return string
     */
    public function getType(): string;
}
