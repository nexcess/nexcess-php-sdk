<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Ssl\Endpoint,
  Resource\Ssl\Ssl,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for Ssl.
 */
class SslTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'GET-%2Fssl-cert%2F1.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'ssl-cert-1.toArray.php';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'ssl-cert-1.toCollapsedArray.json';

  protected const _RESOURCE_DISTINGUISHED_NAME = 'distinguished_name.json';
  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Ssl::class;

  protected const _RESOURCE_GET_BY_SERVICE_ID = 'ssl-by-service-id.json';

  protected const _RESOURCE_CSR = 'csr_2.txt';

  protected const _RESOURCE_KEY = 'key_2.txt';

  /**
   * @covers Ssl::create
   */
  public function testCreate() {

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('create')
      ->willReturn(
        (new Ssl())->sync(
          $this->_getResource(self::_RESOURCE_GET_BY_SERVICE_ID)[0]
        )
      );

    $ssl = Ssl::__set_state(
      [
        '_endpoint' => $endpoint,
        '_values' => [
          'months' => 12,
          'package_id' => 179,
          'domain' => 'example.com',
          'approver_email' => ['example.com' => ['admin@example.com']]
        ]
      ]
    );

    $response = $ssl->create(
          $this->_getResource(self::_RESOURCE_DISTINGUISHED_NAME)
        );

    $this->assertEquals(Ssl::class, get_class($response));
    $this->assertEquals(123, $response->get('id'));
    $this->assertEquals('example.com', $response->get('common_name'));
  }

  public function testCreateFromCsr() {
    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('createFromCsr')
      ->willReturn(
        (new Ssl())->sync(
          $this->_getResource(self::_RESOURCE_GET_BY_SERVICE_ID)[0]
        )
      );

    $ssl = Ssl::__set_state(
      [
        '_endpoint' => $endpoint,
        '_values' => [
          'months' => 12,
          'package_id' => 179,
          'domain' => 'example.com',
          'approver_email' => ['example.com' => ['admin@example.com']],
          'key' => $this->_getResource(self::_RESOURCE_KEY)
        ]
      ]
    );

    $response = $ssl->createFromCsr(
          $this->_getResource(self::_RESOURCE_CSR)
        );

    $this->assertEquals(Ssl::class, get_class($response));
    $this->assertEquals(123, $response->get('id'));
    $this->assertEquals('example.com', $response->get('common_name'));
  }

  public function testImport() {
  }



}
