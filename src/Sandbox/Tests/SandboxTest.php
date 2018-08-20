<?php
/**
 * @package Nexcess-SDK
 * @subpackage Sandbox
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Sandbox\Tests;

use GuzzleHttp\ {
  Exception\ServerException,
  Psr7\Request as GuzzleRequest,
  Psr7\Response as GuzzleResponse
};

use Nexcess\Sdk\ {
  ApiException,
  Client,
  Endpoint\Response,
  Sandbox\Sandbox,
  Tests\TestCase
};

/**
 * Tests for Sdk Sandbox.
 */
class SandboxTest extends TestCase {

  /**
   * @covers Sandbox::__construct
   * @covers Sandbox::handle
   */
  public function testDelegatedExceptions() {
    $msg = 'something went wrong with the play';
    $response = $this->_getSandbox(
      null,
      null,
      function ($e) use ($msg) { return $msg; }
    )->play(function ($api, $sandbox) {
      $sandbox->makeResponse('GET /test', 404);
      $api->request('GET', '/test');
    });

    $this->assertEquals(
      $msg,
      $response,
      'Must return value returned from exception handler'
    );
  }

  /**
   * @covers Sandbox::__construct
   * @covers Sandbox::handle
   */
  public function testDelegatedResponses() {
    $expected = new GuzzleResponse(200, [], 'foo');

    $this->_getSandbox(
      null,
      function ($r) use ($expected) { return $expected; }
    )->play(function ($api, $sandbox) use ($expected) {
      $api->request('GET', '/test');
      $this->assertSame(
        $expected,
        $api->getRequestLog()[0]['response'],
        'Must use guzzle response returned from request handler'
      );
    });
  }

  /**
   * @covers Sandbox::handle
   */
  public function testEmptyResponses() {
    $this->setExpectedException(
      new ApiException(ApiException::SERVER_ERROR)
    );
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $api->request('GET', '/test');
    });
  }

  /**
   * @covers Sandbox::makeResponse
   * @covers Sandbox::queueResponse
   * @covers Sandbox::handle
   */
  public function testQueuedResponses() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $data = ['foo' => 'ok'];

      $sandbox->makeResponse('GET /test', 200, $data);
      $api->request('GET', '/test');
      $response = $api->getRequestLog()[0]['response'];

      $this->assertEquals(
        200,
        $response->getStatusCode(),
        'Must return correct http status code'
      );
      $this->assertEquals(
        'application/json',
        $response->getHeader('Content-type')[0],
        'Must return data as json and set correct content-type header'
      );
      $this->assertEquals(
        $data,
        json_decode($response->getBody()->__toString(), true),
        'Must return correct data'
      );

      $sandbox->makeResponse('GET /test', 404);
      $this->setExpectedException(
        new ApiException(ApiException::NOT_FOUND)
      );
      $response = $api->request('GET', '/test');
    });
  }

  /**
   * @covers Sandbox::newClient
   * @covers Sandbox::play
   */
  public function testPlay() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $this->assertInstanceOf(
        Client::class,
        $api,
        'Must provide a Nexcess-SDK Client instance to first arg of callback'
      );
      $this->assertInstanceOf(
        Sandbox::class,
        $sandbox,
        'Must provide the Sandbox instance to second arg of callback'
      );

      $sandbox->play(function ($api2, $sandbox) use ($api) {
        $this->assertNotSame(
          $api,
          $api2,
          'Must provide a new Nexcess-SDK Client instance for each play'
        );
      });
    });
  }
}
