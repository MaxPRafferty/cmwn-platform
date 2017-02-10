<?php

namespace Api;

use Zend\Json\Json;

/**
 * Class SwaggerHelper
 */
class SwaggerHelper
{
    /**
     * @var string
     */
    protected $swaggerFile;

    /**
     * SwaggerHelper constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config            = $config[self::class] ?? [];
        $this->swaggerFile = $config['swagger_file'] ?? null;
    }

    /**
     * @return array
     */
    public function getSwagger(string $host = null): array
    {
        if (!file_exists($this->swaggerFile)) {
            return [];
        }

        $swagger = Json::decode(file_get_contents($this->swaggerFile), Json::TYPE_ARRAY);
        $swagger['host'] = $host ?? $swagger['host'];

        return $swagger;
    }
}
