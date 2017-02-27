<?php

namespace SearchTest;

use Api\V1\Rest\User\UserEntity;
use PHPUnit\Framework\TestCase;
use Search\ElasticHydrator;
use Search\Exception\RuntimeException;
use User\Adult;
use User\Child;
use User\UserInterface;
use Zend\Hydrator\ArraySerializable;
use Zend\Hydrator\ObjectProperty;
use Zend\Stdlib\ArrayObject;

/**
 * Test ElasticHydratorTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ElasticHydratorTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateWhenClassConfiguredInHydrator()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
                'user'             => [
                    'hydrator'          => \User\UserHydrator::class,
                    'default_prototype' => null,
                ],
            ],
        ]);

        $data = [
            '_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            '_index'  => 'qa',
            '_score'  => 1.0,
            '_source' => [
                'birthdate'   => '2001-01-01 00:00:00',
                'created'     => '2016-08-18 16:12:56',
                'deleted'     => null,
                'email'       => 'green-hamster008@changemyworldnow.com',
                'external_id' => '80Y998-20022',
                'first_name'  => '289First 22',
                'gender'      => 'F',
                'last_name'   => '289Last 22',
                'meta'        => [],
                'type'        => 'CHILD',
                'updated'     => '2016-08-18 16:55:34',
                'user_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
                'username'    => 'green-hamster008',
            ],
            '_type'   => 'user',
        ];

        $object = $hydrator->hydrate($data, null);
        $this->assertInstanceOf(
            Child::class,
            $object,
            ElasticHydrator::class . ' did not make a ' . Child::class
        );

        $this->assertEquals(
            'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            $object->getUserId(),
            ElasticHydrator::class . ' did not set the user id on the child'
        );
    }

    /**
     * @test
     */
    public function testItShouldHydrateWithCustomProtypeWhenConfigured()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
                'user'             => [
                    'hydrator'          => \User\UserHydrator::class,
                    'default_prototype' => null,
                ],
            ],
        ]);

        $data = [
            '_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            '_index'  => 'qa',
            '_score'  => 1.0,
            '_source' => [
                'birthdate'   => '2001-01-01 00:00:00',
                'created'     => '2016-08-18 16:12:56',
                'deleted'     => null,
                'email'       => 'green-hamster008@changemyworldnow.com',
                'external_id' => '80Y998-20022',
                'first_name'  => '289First 22',
                'gender'      => 'F',
                'last_name'   => '289Last 22',
                'meta'        => [],
                'middle_name' => null,
                'type'        => 'CHILD',
                'updated'     => '2016-08-18 16:55:34',
                'user_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
                'username'    => 'green-hamster008',
            ],
            '_type'   => 'user',
        ];

        $object = $hydrator->hydrate($data, new UserEntity());
        $this->assertInstanceOf(
            UserEntity::class,
            $object,
            ElasticHydrator::class . ' did not make a ' . UserEntity::class
        );

        $this->assertEquals(
            'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            $object->getUserId(),
            ElasticHydrator::class . ' did not set the user id on the user'
        );
    }

    /**
     * @test
     */
    public function testItShouldUseDefaultsWhenTypeIsNotConfigured()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
            ],
        ]);

        $data = [
            '_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            '_index'  => 'qa',
            '_score'  => 1.0,
            '_source' => [
                'birthdate'   => '2001-01-01 00:00:00',
                'created'     => '2016-08-18 16:12:56',
                'deleted'     => null,
                'email'       => 'green-hamster008@changemyworldnow.com',
                'external_id' => '80Y998-20022',
                'first_name'  => '289First 22',
                'gender'      => 'F',
                'last_name'   => '289Last 22',
                'meta'        => [],
                'middle_name' => null,
                'type'        => 'CHILD',
                'updated'     => '2016-08-18 16:55:34',
                'user_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
                'username'    => 'green-hamster008',
            ],
            '_type'   => 'user',
        ];

        $object = $hydrator->hydrate($data, null);
        $this->assertInstanceOf(
            ArrayObject::class,
            $object,
            ElasticHydrator::class . ' did not make a ' . ArrayObject::class
        );

        $this->assertEquals(
            'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            $object['user_id'],
            ElasticHydrator::class . ' did not set the user id on the user'
        );
    }

    /**
     * @test
     */
    public function testItShouldUseDefaultHydratorWhenTypeIsNotConfiguredButObjectPassedIn()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
            ],
        ]);

        $data = [
            '_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            '_index'  => 'qa',
            '_score'  => 1.0,
            '_source' => [
                'birthdate'   => '2001-01-01 00:00:00',
                'created'     => '2016-08-18 16:12:56',
                'deleted'     => null,
                'email'       => 'green-hamster008@changemyworldnow.com',
                'external_id' => '80Y998-20022',
                'first_name'  => '289First 22',
                'gender'      => 'F',
                'last_name'   => '289Last 22',
                'meta'        => [],
                'middle_name' => null,
                'type'        => 'CHILD',
                'updated'     => '2016-08-18 16:55:34',
                'user_id'     => 'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
                'username'    => 'green-hamster008',
            ],
            '_type'   => 'user',
        ];

        $object = $hydrator->hydrate($data, new UserEntity());
        $this->assertInstanceOf(
            UserEntity::class,
            $object,
            ElasticHydrator::class . ' did not make a ' . UserEntity::class
        );

        $this->assertEquals(
            'a00acb06-655e-11e6-b14f-ccc0a4f58b11',
            $object->getUserId(),
            ElasticHydrator::class . ' did not set the user id on the user'
        );
    }

    /**
     * @test
     */
    public function testItShouldUseCorrectHydratorToExtract()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
                'user'             => [
                    'hydrator'          => \User\UserHydrator::class,
                    'default_prototype' => null,
                    'interface'         => UserInterface::class,
                ],
            ],
        ]);

        $user = new Adult(['user_id' => 'foo-bar']);

        $extracted = $hydrator->extract($user);

        $this->assertTrue(
            is_array($extracted),
            ElasticHydrator::class . ' did not return an array'
        );

        $this->assertEquals(
            'foo-bar',
            $extracted['user_id'],
            ElasticHydrator::class . ' did not correctly extract array'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenMissingDefaultHydrator()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No default hydrator class provided to: ' . ElasticHydrator::class);

        new ElasticHydrator([]);
    }

    /**
     * @test
     */
    public function testItShouldGetCorrectTypeFromConfig()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
                'user'             => [
                    'hydrator'          => \User\UserHydrator::class,
                    'default_prototype' => null,
                    'interface'         => UserInterface::class,
                ],
            ],
        ]);

        /** @var \Mockery\MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);
        $user->shouldReceive('getDocumentType')
            ->andReturn('user');

        $this->assertEquals(
            'user',
            $hydrator->getTypeFromObject($user),
            ElasticHydrator::class . ' did not return the correct type from a user'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenGetTypeFromObjectIsNotConfigured()
    {
        $hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
                'user'             => [
                    'hydrator'          => \User\UserHydrator::class,
                    'default_prototype' => null,
                    'interface'         => UserInterface::class,
                ],
            ],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No Type configured for: ' . \stdClass::class);

        $hydrator->getTypeFromObject(new \stdClass);
    }
}
