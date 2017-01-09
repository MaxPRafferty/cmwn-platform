<?php

namespace FlipTest\Rule\Action;

use Flip\EarnedFlip;
use Flip\Rule\Action\AddAcknowledgeHeaders;
use Flip\Rule\Provider\AcknowledgeFlip;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Event\Provider\EventProvider;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use Zend\EventManager\Event;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

/**
 * Test AddAcknowledgeHeadersTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddAcknowledgeHeadersTest extends TestCase
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @before
     */
    public function setUpResponse()
    {
        $this->response = new Response();
    }

    /**
     * @before
     */
    public function setUpEvent()
    {
        $this->event = new MvcEvent();
        $this->event->setResponse($this->response);
    }

    /**
     * @test
     * @dataProvider goodResponseCodeProvider
     */
    public function testItShouldAppendHeadersWithDefaultValues($statusCode)
    {
        $this->response->setStatusCode($statusCode);
        $flip   = new EarnedFlip();
        $action = new AddAcknowledgeHeaders();
        $item   = new BasicRuleItem(
            new BasicValueProvider(EventProvider::PROVIDER_NAME, $this->event),
            new BasicValueProvider(AcknowledgeFlip::PROVIDER_NAME, $flip)
        );

        $flip->setTitle('Manchuck Farmville Edition')
            ->setFlipId('manchuck-farmville')
            ->setAcknowledgeId('foo-bar');

        $action($item);

        $this->assertEquals(
            $this->response->getHeaders()->get('X-Flip-Earned')->getFieldValue(),
            $flip->getTitle(),
            AddAcknowledgeHeaders::class . ' did not add X-Flip-Earned'
        );

        $this->assertEquals(
            $this->response->getHeaders()->get('X-Flip-Id')->getFieldValue(),
            $flip->getFlipId(),
            AddAcknowledgeHeaders::class . ' did not add X-Flip-Id'
        );

        $this->assertEquals(
            $this->response->getHeaders()->get('X-Flip-Ack')->getFieldValue(),
            $flip->getAcknowledgeId(),
            AddAcknowledgeHeaders::class . ' did not add X-Flip-Ack'
        );
    }

    /**
     * @test
     * @dataProvider badResponseCodeProvider
     */
    public function testItShouldDoNothingIfResponseCodeIsIncorrect($statusCode)
    {
        $this->response->setStatusCode($statusCode);
        $flip   = new EarnedFlip();
        $action = new AddAcknowledgeHeaders();
        $item   = new BasicRuleItem(
            new BasicValueProvider(EventProvider::PROVIDER_NAME, $this->event),
            new BasicValueProvider(AcknowledgeFlip::PROVIDER_NAME, $flip)
        );

        $flip->setTitle('Manchuck Farmville Edition')
            ->setFlipId('manchuck-farmville')
            ->setAcknowledgeId('foo-bar');

        $action($item);

        $this->assertFalse(
            $this->response->getHeaders()->has('X-Flip-Earned'),
            AddAcknowledgeHeaders::class . ' added X-Flip-Earned on bad response code'
        );

        $this->assertFalse(
            $this->response->getHeaders()->has('X-Flip-Id'),
            AddAcknowledgeHeaders::class . ' added X-Flip-Id on bad response code'
        );

        $this->assertFalse(
            $this->response->getHeaders()->has('X-Flip-Ack'),
            AddAcknowledgeHeaders::class . ' added X-Flip-Ack on bad response code'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenEventWrongType()
    {
        $this->expectException(InvalidProviderType::class);
        $flip   = new EarnedFlip();
        $action = new AddAcknowledgeHeaders();
        $item   = new BasicRuleItem(
            new BasicValueProvider(EventProvider::PROVIDER_NAME, new Event()),
            new BasicValueProvider(AcknowledgeFlip::PROVIDER_NAME, $flip)
        );

        $flip->setTitle('Manchuck Farmville Edition')
            ->setFlipId('manchuck-farmville')
            ->setAcknowledgeId('foo-bar');

        $action($item);
    }

    /**
     * @return array
     */
    public function goodResponseCodeProvider()
    {
        return [
            [Response::STATUS_CODE_200],
            [Response::STATUS_CODE_201],
            [Response::STATUS_CODE_202],
            [Response::STATUS_CODE_203],
            [Response::STATUS_CODE_204],
            [Response::STATUS_CODE_205],
            [Response::STATUS_CODE_206],
            [Response::STATUS_CODE_207],
            [Response::STATUS_CODE_208],
        ];
    }

    /**
     * @return array
     */
    public function badResponseCodeProvider()
    {
        return [
            [Response::STATUS_CODE_100],
            [Response::STATUS_CODE_101],
            [Response::STATUS_CODE_102],
            [Response::STATUS_CODE_300],
            [Response::STATUS_CODE_301],
            [Response::STATUS_CODE_302],
            [Response::STATUS_CODE_303],
            [Response::STATUS_CODE_304],
            [Response::STATUS_CODE_305],
            [Response::STATUS_CODE_306],
            [Response::STATUS_CODE_307],
            [Response::STATUS_CODE_400],
            [Response::STATUS_CODE_401],
            [Response::STATUS_CODE_402],
            [Response::STATUS_CODE_403],
            [Response::STATUS_CODE_404],
            [Response::STATUS_CODE_405],
            [Response::STATUS_CODE_406],
            [Response::STATUS_CODE_407],
            [Response::STATUS_CODE_408],
            [Response::STATUS_CODE_409],
            [Response::STATUS_CODE_410],
            [Response::STATUS_CODE_411],
            [Response::STATUS_CODE_412],
            [Response::STATUS_CODE_413],
            [Response::STATUS_CODE_414],
            [Response::STATUS_CODE_415],
            [Response::STATUS_CODE_416],
            [Response::STATUS_CODE_417],
            [Response::STATUS_CODE_418],
            [Response::STATUS_CODE_422],
            [Response::STATUS_CODE_423],
            [Response::STATUS_CODE_424],
            [Response::STATUS_CODE_425],
            [Response::STATUS_CODE_426],
            [Response::STATUS_CODE_428],
            [Response::STATUS_CODE_429],
            [Response::STATUS_CODE_431],
            [Response::STATUS_CODE_500],
            [Response::STATUS_CODE_501],
            [Response::STATUS_CODE_502],
            [Response::STATUS_CODE_503],
            [Response::STATUS_CODE_504],
            [Response::STATUS_CODE_505],
            [Response::STATUS_CODE_506],
            [Response::STATUS_CODE_507],
            [Response::STATUS_CODE_508],
            [Response::STATUS_CODE_511],
        ];
    }
}
