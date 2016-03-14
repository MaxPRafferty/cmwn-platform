<?php

namespace ImportTest\Importer\Nyc;

use Import\Importer\Nyc\DoeImporter;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\Event;

/**
 * Class NycDoeImporterTest
 * @package ImportTest\Importer
 */
class DoeImporterTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Parser\DoeParser
     */
    protected $parser;

    /**
     * @var DoeImporter
     */
    protected $importer;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpDoeParser()
    {
        $this->parser = \Mockery::mock('\Import\Importer\Nyc\Parser\DoeParser');
        $this->parser->shouldReceive('setLogger')->byDefault();
    }

    /**
     * @before
     */
    public function setUpImporter()
    {
        $this->importer = new DoeImporter($this->parser);
        $this->importer->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }


    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams()
        ];
    }

    public function testItShouldPassAlongFileInExchangeArray()
    {
        $fileName = '/path/to/file.xsls';
        $this->assertEquals(['file_name' => null], $this->importer->getArrayCopy());
        $this->importer->exchangeArray(['file_name' => $fileName]);
        $this->assertEquals(['file_name' => $fileName], $this->importer->getArrayCopy());
    }

    public function testItShouldExecuteActions()
    {
        $fileName = '/path/to/file.xsls';
        /** @var \Mockery\MockInterface|\Import\ActionInterface $action */
        $action = \Mockery::mock('\Import\ActionInterface');

        $action->shouldReceive('execute')->once();

        $actionQueue = new \SplPriorityQueue();
        $actionQueue->insert($action, 1);

        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->once();
        $this->parser->shouldReceive('getActions')->once()->andReturn($actionQueue);
        $this->parser->shouldReceive('hasErrors')->once()->andReturn(false);

        $this->importer->setFileName($fileName);
        $this->importer->perform();

        $this->assertEquals(3, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => []
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.run',
                'target' => $this->parser,
                'params' => []
            ],
            $this->calledEvents[1]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.complete',
                'target' => $this->parser,
                'params' => []
            ],
            $this->calledEvents[2]
        );
    }

    public function testItShouldNotExecuteActionsWhenParserProducesErrors()
    {
        $fileName = '/path/to/file.xsls';
        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->once();
        $this->parser->shouldReceive('getActions')->never();
        $this->parser->shouldReceive('hasErrors')->once()->andReturn(true);

        $this->importer->setFileName($fileName);
        $this->importer->perform();

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => []
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.error',
                'target' => $this->parser,
                'params' => []
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotExecuteActionsWhenEventStops()
    {
        $fileName = '/path/to/file.xsls';
        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->never();
        $this->parser->shouldReceive('getActions')->never();
        $this->parser->shouldReceive('hasErrors')->never()->andReturn(true);

        $this->importer->getEventManager()->attach('nyc.import.excel', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->importer->setFileName($fileName);
        $this->importer->perform();

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }
}
