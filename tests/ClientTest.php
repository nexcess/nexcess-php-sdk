<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests;

use Throwable,
  Exception;

use GuzzleHttp\ {
  Exception\ConnectException,
  Exception\RequestException,
  Exception\ServerException,
  Psr7\Request as GuzzleRequest,
  Psr7\Response as GuzzleResponse
};

use function GuzzleHttp\default_user_agent as guzzle_user_agent;

use Nexcess\Sdk\ {
  ApiException,
  Client,
  SdkException,
  Tests\TestCase,
  Util\Config
};

/**
 * Tests for Sdk Client.
 */
class ClientTest extends TestCase {

  /**
   * @covers Client::__call
   */
  public function testCall() {
    $this->markTestIncomplete('depends on endpoint classes being available');
  }

  /**
   * @covers Client::__get
   */
  public function testGet() {
    $this->markTestIncomplete('depends on endpoint classes being available');
  }

  /**
   * @covers Client::getEndpoint
   */
  public function testGetEndpoint() {
    $this->markTestIncomplete('depends on endpoint classes being available');
  }

  /**
   * @covers Client::getModel
   */
  public function testGetModel() {
    $this->markTestIncomplete('depends on endpoint classes being available');
  }

  /**
   * @covers Client::getRequestLog
   */
  public function testGetRequestLog() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $sandbox->makeResponse('GET /test', 200);
      $api->request('GET', '/test');

      $log = $api->getRequestLog();
      $this->assertTrue(is_array($log), 'Must return an array of log entires');

      $entry = $log[0];
      $this->assertArrayHasKey(
        'request',
        $entry,
        'Each log entry must contain info about the "request"'
      );
      $this->assertArrayHasKey(
        'response',
        $entry,
        'Each log entry must contain info about the "response"'
      );
      $this->assertEquals(
        '/test',
        $entry['request']->getUri()->__toString(),
        'Log entry must describe the request we made'
      );
    });
  }

  /**
   * @covers Client::selfUpdate
   */
  public function testSelfUpdate() {
    $this->markTestIncomplete('self-update is not yet implemented');
  }

  /**
   * @covers Client::shouldUpdate
   */
  public function testShouldUpdate() {
    $this->markTestIncomplete('self-update is not yet implemented');
  }

  /**
   * @covers Client::request
   */
  public function testRequest() {
    $config = new Config(['api_token' => '123', 'language' => 'en_US']);
    $this->_getSandbox($config)
      ->play(function ($api, $sandbox) use ($config) {
        $sandbox->makeResponse('GET /test', 200);
        $api->request('GET', '/test', ['json' => ['foo' => 'bar']]);

        $request = $api->getRequestLog()[0]['request'];
        $this->assertEquals(
          'application/json',
          $request->getHeader('Accept')[0],
          'Must set json accept header'
        );
        $this->assertEquals(
          $config->get('language'),
          $request->getHeader('Accept-language')[0],
          'Must set accept-language header'
        );
        $this->assertEquals(
          Client::API_VERSION,
          $request->getHeader('Api-version')[0],
          'Must set api version header'
        );
        $this->assertEquals(
          "Bearer {$config->get('api_token')}",
          $request->getHeader('Authorization')[0],
          'Must set provided api token in autorization header'
        );
        $this->assertEquals(
          'Nexcess-PHP-SDK/' . Client::SDK_VERSION .
            ' (' . guzzle_user_agent() . ')',
          $request->getHeader('User-agent')[0],
          'Must set user-agent header'
        );
        $this->assertEquals(
          'application/json',
          $request->getHeader('Content-type')[0],
          'Must set json content-type header when provided "json" params'
        );
        $this->assertEquals(
          '{"foo":"bar"}',
          $request->getBody()->__toString(),
          'Must json-encode provided "json" $params'
        );
      });
  }

  /**
   * @covers Client::request
   * @dataProvider requestFailureProvider
   *
   * @param int|Throwable $response Response to queue for request
   * @param Throwable $expected The exception the Client should throw
   */
  public function testRequestFailure($response, Throwable $expected) {
    $this->setExpectedException($expected);
    $this->_getSandbox()->play(function ($api, $sandbox) use ($response) {
      (is_int($response)) ?
        $sandbox->makeResponse('GET /test', $response) :
        $sandbox->queueResponse('GET /test', $response);
      $api->request('GET', '/test');
    });
  }

  /**
   * @return array[] {
   *    @var int|Throwable $0 Response status code or exception
   *    @var Throwable $1 Exception request() should throw
   *  }
   */
  public function requestFailureProvider() : array {
    $message = 'fail';
    $request = new GuzzleRequest('GET', '/test');
    return [
      [
        new ConnectException($message, $request),
        new ApiException(ApiException::CANNOT_CONNECT)
      ],
      [400, new ApiException(ApiException::BAD_REQUEST)],
      [401, new ApiException(ApiException::UNAUTHORIZED)],
      [403, new ApiException(ApiException::FORBIDDEN)],
      [404, new ApiException(ApiException::NOT_FOUND)],
      [422, new ApiException(ApiException::UNPROCESSABLE_ENTITY)],
      [500, new ApiException(ApiException::SERVER_ERROR)],
      [
        new RequestException($message, $request),
        new ApiException(ApiException::REQUEST_FAILED)
      ],
      [new Exception(), new SdkException(SdkException::UNKNOWN_ERROR)]
    ];
  }
}
