<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource;

use Throwable;
use GuzzleHttp\ {
  Psr7\Response as GuzzleResponse
};
use Nexcess\Sdk\ {
  Resource\Collector as Collection,
  Resource\Modelable as Model,
  Sandbox\Sandbox,
  Tests\TestCase,
  Tests\TestException,
  Util\Util
};

/**
 * Base unit testcase for endpoints.
 */
abstract class EndpointTestCase extends TestCase {

  /** @var array Resource fromarray:toarray map for retrieve action. */
  protected const _RESOURCE_INSTANCES = [];

  /** @var array Resource name:filter map for list action. */
  protected const _RESOURCE_LISTS = [];

  /** @var string Fully qualified classname of model for subject endpoint. */
  protected const _SUBJECT_MODEL_FQCN = '';

  /**
   * @covers Endpoint::getModel
   */
  public function testGetModel() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $endpoint = $api->getEndpoint(static::_SUBJECT_FQCN);
      $actual = $endpoint->getModel()->set('id', 1);

      $this->assertInstanceOf(static::_SUBJECT_MODEL_FQCN, $actual);
      $this->assertEquals(1, $actual->getId(), 'Must set given model id');

      $this->assertEquals(
        $this->_getNonpublicProperty($actual, '_endpoint'),
        $endpoint,
        'Must assign endpoint to new model'
      );
    });
  }

  /**
   * @covers Endpoint::getParams
   * @dataProvider getParamsProvider
   *
   * @param string $action The action to get params for
   * @param array $expected The expected parameter list
   */
  public function testGetParams(string $action, array $expected) {
    $this->_getSandbox()
      ->play(function ($api, $sandbox) use ($action, $expected) {
        $endpoint = $api->getEndpoint(static::_SUBJECT_FQCN);
        $this->assertEquals($expected, $endpoint->getParams($action));
      });
  }

  /**
   * @return array[] List of testcases
   */
  abstract public function getParamsProvider() : array;

  /**
   * @covers Endpoint::list
   * @dataProvider listProvider
   *
   * @param array $filter List filter to use
   * @param GuzzleResponse|callable|Throwable $response Response to queue
   */
  public function testList(array $filter, $response) {
    if ($response instanceof Throwable) {
      $this->setExpectedException($response);
    }

    $handler = function ($request, $options) use ($filter, $response) {
      parse_str($request->getUri()->getQuery(), $query);
      foreach ($filter as $key => $value) {
        $this->assertArrayHasKey($key, $query);
        $this->assertEquals($value, $query[$key]);
      }
      return is_array($response) ?
        new GuzzleResponse(
          200,
          ['Content-type' => 'application/json'],
          Util::jsonEncode($response)
        ) :
        $response;
    };
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) use ($filter) {
        $list = $api->getEndpoint(static::_SUBJECT_FQCN)->list($filter);

        $this->assertEquals(
          static::_SUBJECT_MODEL_FQCN,
          $list->of(),
          'Must return a collection of the associated model type'
        );
        $this->assertTrue(
          $list->filter($filter)->equals($list),
          'Must apply filter to retrieved list'
        );
      });
  }

  /**
   * @return array List of testcases
   */
  public function listProvider() : array {
    $testcases = [];
    foreach (static::_RESOURCE_LISTS as $resource => $filter) {
      $testcases[] = [$filter, $this->_getResource($resource)];
    }

    return $testcases;
  }

  /**
   * @covers Endpoint::retrieve
   * @covers Endpoint::sync
   * @dataProvider retrieveProvider
   *
   * @param GuzzleResponse|callable|Throwable $response Response to queue
   * @param array $expected Map of property:value pairs (including id) to check
   */
  public function testRetrieve($response, array $expected) {
    if ($response instanceof Throwable) {
      $this->setExpectedException($response);
    }

    $this->_getSandbox()
      ->play(function ($api, $sandbox) use ($response, $expected) {
        $sandbox->makeResponse('*', 200, $response);
        $endpoint = $api->getEndpoint(static::_SUBJECT_FQCN);

        $actual = $endpoint->retrieve($expected['id']);
        $this->assertInstanceOf(static::_SUBJECT_MODEL_FQCN, $actual);

        // disconnect from endpoint;
        // we don't want any attempts at api requests while making assertions
        $actual->setApiEndpoint(null);
        foreach ($expected as $property => $value) {
          if ($value instanceof Model || $value instanceof Collection) {
            $this->assertTrue($value->equals($actual->get($property)));
          } else {
            $this->assertEquals($value, $actual->get($property));
          }
        }
      });
  }

  /**
   * @return array List of testcases
   */
  public function retrieveProvider() : array {
    $testcases = [];
    foreach (static::_RESOURCE_INSTANCES as $resource => $expected) {
      $testcases[] = [
        $this->_getResource($resource),
        $this->_getResource($expected)
      ];
    }

    return $testcases;
  }

  /**
   * {@inheritDoc}
   * Use the sandbox to get subject endpoint instances
   * (getting an endpoint here would have a different api client and config).
   */
  protected function _getSubject(...$constructor_args) {
    throw new TestException(TestException::USE_SANDBOX_INSTEAD);
  }
}
