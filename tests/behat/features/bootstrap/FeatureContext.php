<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use PHPUnit_Framework_Assert as PHPUnit;

date_default_timezone_set('America/New_York');

/**
 * Features context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * The Guzzle HTTP Client.
     */
    protected $client;

    /**
     * The current resource.
     */
    protected $resource;

    /**
     * The request payload.
     */
    protected $requestPayload;

    /**
     * The Guzzle HTTP Response.
     */
    protected $response;

    /**
     * The decoded response object.
     */
    protected $responsePayload;

    /**
     * The current scope within the response payload
     * which conditions are asserted against.
     */
    protected $scope;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct() //(array $parameters)
    {
        $this->client = new Client(['base_uri' => 'http://api.cmwn.localhost/', 'cookies' => true]);
    }

    /**
     * @Given /^I have the payload:$/
     */
    public function iHaveThePayload(PyStringNode $requestPayload)
    {
        $this->requestPayload = $requestPayload;
    }

    /**
     * @Given I am logged in with the username :username and the password :password
     */
    public function iAmLoggedInWithTheUsernameAndThePassword($username, $password)
    {
        $this->iRequest('GET', 'csrf_token');
        $this->iCanTryToLogin($username, $password);
    }

    /**
     * @Then /^I can try to login with the username "([^"]*)" and the password "([^"]*)"$/
     */
    public function iCanTryToLogin($username, $password)
    {
        $payload = $this->getScopePayload();

        try {
            $this->response = $this->client->request('POST', 'auth/login', ['auth' => [$username, $password], 'headers' => ['X-CSRF-TOKEN' => $payload->token]]);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();

            // Sometimes the request will fail, at which point we have
            // no response at all. Let Guzzle give an error here, it's
            // pretty self-explanatory.
            if ($response === null) {
                throw $e;
            }

            $this->response = $e->getResponse();
        }
    }

    /**
     * @When /^I request "(GET|PUT|POST|DELETE) ([^"]*)"$/
     */
    public function iRequest($httpMethod, $resource)
    {
        $this->resource = $resource;

        $method = strtolower($httpMethod);

        try {
            switch ($httpMethod) {
                case 'PUT':
                case 'POST':
                    $this->response = $this
                        ->client
                        ->$method($resource, [], $this->requestPayload);
                    break;
                default:
                    $this->response = $this->client->$method($resource);

                    // if($resource == "sidebar"){
                    //     echo($this->response->getBody());
                    // }
            }
        } catch (BadResponseException $e) {
            $response = $e->getResponse();

            // Sometimes the request will fail, at which point we have
            // no response at all. Let Guzzle give an error here, it's
            // pretty self-explanatory.
            if ($response === null) {
                throw $e;
            }

            $this->response = $e->getResponse();
        }
    }

    /**
     * @Then /^I get a "([^"]*)" response$/
     */
    public function iGetAResponse($statusCode)
    {
        $response = $this->getResponse();

        $contentType = $response->getHeader('Content-Type');

        $contentType = $contentType[0];

        if ($contentType === 'application/json') {
            $bodyOutput = $response->getBody();
        } else {
            $bodyOutput = 'Output is '.$contentType.', which is not JSON and is therefore scary. Run the request manually.';
        }

        PHPUnit::assertSame((int) $statusCode, (int) $this->getResponse()->getStatusCode(), $bodyOutput);
    }

    /**
     * @Given /^the "([^"]*)" property equals "([^"]*)"$/
     */
    public function thePropertyEquals($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        assertEquals(
            $actualValue,
            $expectedValue,
            "Asserting the [$property] property in current scope equals [$expectedValue]: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property exists$/
     */
    public function thePropertyExists($property)
    {
        $payload = $this->getScopePayload();

        //var_dump($payload); // only one

        $message = sprintf(
            'Asserting the [%s] property exists in the scope [%s]: %s',
            $property,
            $this->scope,
            json_encode($payload)
        );

        if (is_object($payload)) {
            PHPUnit::assertTrue(array_key_exists($property, get_object_vars($payload)), $message);
        } else {
            PHPUnit::assertTrue(array_key_exists($property, $payload), $message);
        }
    }

    /**
     * @Given /^the "([^"]*)" property is an array$/
     */
    public function thePropertyIsAnArray($property)
    {
        $payload = $this->getScopePayload();

        $actualValue = $this->arrayGet($payload, $property);

        PHPUnit::assertTrue(
            is_array($actualValue),
            "Asserting the [$property] property in current scope [{$this->scope}] is an array: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an object$/
     */
    public function thePropertyIsAnObject($property)
    {
        $payload = $this->getScopePayload();

        $actualValue = $this->arrayGet($payload, $property);

        PHPUnit::assertTrue(
            is_object($actualValue),
            "Asserting the [$property] property in current scope [{$this->scope}] is an object: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an empty array$/
     */
    public function thePropertyIsAnEmptyArray($property)
    {
        $payload = $this->getScopePayload();
        $scopePayload = $this->arrayGet($payload, $property);

        PHPUnit::assertTrue(
            is_array($scopePayload) and $scopePayload === [],
            "Asserting the [$property] property in current scope [{$this->scope}] is an empty array: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property contains (\d+) items$/
     */
    public function thePropertyContainsItems($property, $count)
    {
        $payload = $this->getScopePayload();

        PHPUnit::assertCount(
            $count,
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property contains [$count] items: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an integer$/
     */
    public function thePropertyIsAnInteger($property)
    {
        $payload = $this->getScopePayload();

        isType(
            'int',
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property in current scope [{$this->scope}] is an integer: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string$/
     */
    public function thePropertyIsAString($property)
    {
        $payload = $this->getScopePayload();

        isType(
            'string',
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property in current scope [{$this->scope}] is a string: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string equalling "([^"]*)"$/
     */
    public function thePropertyIsAStringEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();

        $this->thePropertyIsAString($property);

        $actualValue = $this->arrayGet($payload, $property);

        PHPUnit::assertSame(
            $actualValue,
            $expectedValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is a string equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a boolean$/
     */
    public function thePropertyIsABoolean($property)
    {
        $payload = $this->getScopePayload();

        PHPUnit::assertTrue(
            gettype($this->arrayGet($payload, $property)) == 'boolean',
            "Asserting the [$property] property in current scope [{$this->scope}] is a boolean."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a boolean equalling "([^"]*)"$/
     */
    public function thePropertyIsABooleanEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        if (!in_array($expectedValue, ['true', 'false'])) {
            throw new \InvalidArgumentException('Testing for booleans must be represented by [true] or [false].');
        }

        $this->thePropertyIsABoolean($property);

        PHPUnit::assertSame(
            $actualValue,
            $expectedValue == 'true',
            "Asserting the [$property] property in current scope [{$this->scope}] is a boolean equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a integer equalling "([^"]*)"$/
     */
    public function thePropertyIsAIntegerEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->thePropertyIsAnInteger($property);

        PHPUnit::assertSame(
            $actualValue,
            (int) $expectedValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is an integer equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is either:$/
     */
    public function thePropertyIsEither($property, PyStringNode $options)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $valid = explode("\n", (string) $options);

        PHPUnit::assertTrue(
            in_array($actualValue, $valid),
            sprintf(
                "Asserting the [%s] property in current scope [{$this->scope}] is in array of valid options [%s].",
                $property,
                implode(', ', $valid)
            )
        );
    }

    /**
     * @Given /^scope into the first "([^"]*)" property$/
     */
    public function scopeIntoTheFirstProperty($scope)
    {
        $this->scope = "{$scope}.0";
    }

    /**
     * @Given /^scope into the "([^"]*)" property$/
     */
    public function scopeIntoTheProperty($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @Given /^the properties exist:$/
     */
    public function thePropertiesExist(PyStringNode $propertiesString)
    {
        foreach (explode("\n", (string) $propertiesString) as $property) {
            $this->thePropertyExists($property);
        }
    }

    /**
     * @Given /^reset scope$/
     */
    public function resetScope()
    {
        $this->scope = null;
    }

    /**
     * @Transform /^(\d+)$/
     */
    public function castStringToNumber($string)
    {
        return intval($string);
    }

    /**
     * Checks the response exists and returns it.
     *
     * @return Guzzle\Http\Message\Response
     */
    protected function getResponse()
    {
        if (!$this->response) {
            throw new Exception('You must first make a request to check a response.');
        }

        return $this->response;
    }

    /**
     * Return the response payload from the current response.
     *
     * @return mixed
     */
    protected function getResponsePayload()
    {
        //if (!$this->responsePayload) {
            $json = json_decode($this->getResponse()->getBody(true));

            if (json_last_error() !== JSON_ERROR_NONE) {
                $message = 'Failed to decode JSON body ';

                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $message .= '(Maximum stack depth exceeded).';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $message .= '(Underflow or the modes mismatch).';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $message .= '(Unexpected control character found).';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $message .= '(Syntax error, malformed JSON).';
                        break;
                    case JSON_ERROR_UTF8:
                        $message .= '(Malformed UTF-8 characters, possibly incorrectly encoded).';
                        break;
                    default:
                        $message .= '(Unknown error).';
                        break;
                }

                throw new Exception($message);
            }

            $this->responsePayload = $json;
        //}

        return $this->responsePayload;
    }

    /**
     * Returns the payload from the current scope within
     * the response.
     *
     * @return mixed
     */
    protected function getScopePayload()
    {
        $payload = $this->getResponsePayload();

        if (!$this->scope) {
            return $payload;
        }

        return $this->arrayGet($payload, $this->scope);
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @copyright   Taylor Otwell
     *
     * @link        http://laravel.com/docs/helpers
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function arrayGet($array, $key)
    {
        if (is_null($key)) {
            return $array;
        }

        // if (isset($array[$key])) {
        //     return $array[$key];
        // }

        foreach (explode('.', $key) as $segment) {
            if (is_object($array)) {
                if (!isset($array->{$segment})) {
                    return;
                }
                $array = $array->{$segment};
            } elseif (is_array($array)) {
                if (!array_key_exists($segment, $array)) {
                    return;
                }
                $array = $array[$segment];
            }
        }

        return $array;
    }
}
