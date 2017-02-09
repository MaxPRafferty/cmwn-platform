<?php

namespace Application\Utils\Date;

/**
 * A Trait that combines Standard Dates into one
 */
trait StandardDatesTrait
{
    use DateCreatedTrait;
    use DateUpdatedTrait;
    use DateDeletedTrait;
}
