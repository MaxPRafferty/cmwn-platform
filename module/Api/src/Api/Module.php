<?php

namespace Api;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use ZF\Apigility\Provider\ApigilityProviderInterface;

/**
 * Class Module
 *
 * @SWG\Swagger(
 *     schemes={"https"},
 *     host="api.changemyworldnow.com",
 *     basePath="/",
 *     produces={"application/vnd.api.v1+json","application/hal+json","application/json"},
 *     consumes={"application/vnd.api.v1+json","application/hal+json","application/json"},
 *
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Change My World now API",
 *         description="This is the API for change my world now",
 *     ),
 *
 *     @SWG\Definition(
 *         definition="ValidationError",
 *         type="object",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/Error")
 *         }
 *     ),
 *
 *     @SWG\Definition(
 *         definition="Error",
 *         description="Common error format",
 *         type="object",
 *         @SWG\Property(
 *             property="detail",
 *             type="string",
 *             description="HTTP Status"
 *         ),
 *         @SWG\Property(
 *             property="status",
 *             type="integer",
 *             description="HTTP Status Code"
 *         ),
 *         @SWG\Property(
 *             property="title",
 *             type="string",
 *             description="Detailed message"
 *         ),
 *         @SWG\Property(
 *             property="type",
 *             type="string",
 *             format="uri",
 *             description="RFC for the status code"
 *         )
 *     ),
 *
 *     @SWG\Definition(
 *         definition="NotFoundError",
 *         description="Entity was not found",
 *         type="object",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/Error")
 *         }
 *     ),
 *
 *     @SWG\Definition(
 *         definition="Pagination",
 *         description="Standards pagination links",
 *         type="object",
 *         @SWG\Property(
 *             property="page",
 *             type="integer",
 *             format="int32",
 *             description="The current page"
 *         ),
 *         @SWG\Property(
 *             property="page_count",
 *             type="integer",
 *             format="int32",
 *             description="The total number of pages"
 *         ),
 *         @SWG\Property(
 *             property="per_page",
 *             type="integer",
 *             format="int32",
 *             description="The total number of items on a page"
 *         ),
 *         @SWG\Property(
 *             property="total_items",
 *             type="integer",
 *             format="int32",
 *             description="Total count of items"
 *         ),
 *         @SWG\Property(
 *             property="_links",
 *             type="object",
 *             description="HAL Links",
 *             allOf={
 *                 @SWG\Schema(ref="#/definitions/FindLink"),
 *                 @SWG\Schema(ref="#/definitions/NextLink"),
 *                 @SWG\Schema(ref="#/definitions/PrevLink"),
 *                 @SWG\Schema(ref="#/definitions/SelfLink"),
 *                 @SWG\Schema(ref="#/definitions/FirstLink"),
 *                 @SWG\Schema(ref="#/definitions/LastLink"),
 *             }
 *         ),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="FindLink",
 *         description="HAL Link that describes a find endpoint",
 *         type="object",
 *         readOnly=true,
 *         @SWG\Property(
 *             type="object",
 *             property="find",
 *             readOnly=true,
 *             @SWG\Property(
 *                 readOnly=true,
 *                 property="href",
 *                 description="HREF to find items",
 *                 type="string",
 *                 format="uri"
 *             ),
 *             @SWG\Property(
 *                 readOnly=true,
 *                 property="templated",
 *                 type="boolean",
 *                 description="Whether this uri is templated or not"
 *             )
 *         )
 *     ),
 *
 *     @SWG\Definition(
 *         definition="NextLink",
 *         description="HAL Link for the next page",
 *         type="object",
 *         readOnly=true,
 *         @SWG\Property(
 *             type="object",
 *             property="next",
 *             @SWG\Property(
 *                 property="href",
 *                 description="HREF to the next page",
 *                 type="string",
 *                 format="uri"
 *             )
 *         )
 *     ),
 *
 *     @SWG\Definition(
 *         definition="PrevLink",
 *         description="HAL Link for the previous page",
 *         type="object",
 *         readOnly=true,
 *         @SWG\Property(
 *             type="object",
 *             property="prev",
 *             @SWG\Property(
 *                 property="href",
 *                 description="HREF to the previous page",
 *                 type="string",
 *                 format="uri"
 *             )
 *         )
 *     ),
 *
 *     @SWG\Definition(
 *         definition="LastLink",
 *         description="HAL Link for the last page",
 *         type="object",
 *         readOnly=true,
 *         @SWG\Property(
 *             type="object",
 *             property="last",
 *             @SWG\Property(
 *                 property="href",
 *                 description="HREF to the last page",
 *                 type="string",
 *                 format="uri"
 *             )
 *         )
 *     ),
 *
 *     @SWG\Definition(
 *         definition="FirstLink",
 *         description="HAL Link for the first page",
 *         type="object",
 *         readOnly=true,
 *         @SWG\Property(
 *             type="object",
 *             property="first",
 *             @SWG\Property(
 *                 property="href",
 *                 description="HREF to the first page",
 *                 type="string",
 *                 format="uri"
 *             )
 *         )
 *     ),
 *
 *     @SWG\Definition(
 *         definition="SelfLink",
 *         description="HAL Link for the requested page",
 *         type="object",
 *         readOnly=true,
 *         @SWG\Property(
 *             type="object",
 *             property="self",
 *             readOnly=true,
 *             @SWG\Property(
 *                 property="href",
 *                 readOnly=true,
 *                 description="HREF to the the current endpoint",
 *                 type="string",
 *                 format="uri"
 *             )
 *         )
 *     )
 * )
 */
class Module implements ApigilityProviderInterface, ConfigProviderInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
