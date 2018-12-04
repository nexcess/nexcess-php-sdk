<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Ssl;

use Throwable;
use GuzzleHttp\ {
  Psr7\Response as GuzzleResponse
};
use Nexcess\Sdk\ {
  Resource\Ssl\Endpoint,
  Resource\Ssl\Ssl,
  Resource\ResourceException,
  Tests\Resource\EndpointTestCase,
  Util\Language,
  Util\Util
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_GET = 'GET-%2Fssl-cert%2F1.json';
// what is returned by API ssl-cert/CERT_ID

  /** @var string Resource sll cert by service_id */
  protected const _RESOURCE_GET_1 = 'ssl-by-service-id.json';
  
  /** {@inheritDoc} */
  protected const _RESOURCE_IMPORT = 'POST-%2Fssl-cert%2F.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [
    'GET-%2Fssl-cert%2F1.json' => 'ssl-cert-1.toArray.php'
  ];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [
  ];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Ssl::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODULE = 'Ssl';

  /** @var string Chain Cert */
  protected const _CHAIN = 'chain.txt';

  /** @var string Certificate */
  protected const _CRT = 'crt.txt';

  /** @var string Private Key */
  protected const _KEY = 'key.txt';

  /** @var string Private Key */
  protected const _CSR_2 = 'csr_2.txt';

  /** @var string Private Key */
  protected const _KEY_2 = 'key_2.txt';

  /** @var string The decoded csr payload */
  protected const _RESOURCE_GET_DECODED_CSR = 'decoded_csr.txt';

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [
      [
        'retrieveByServiceId',
        [
          'service_id' => [
              Util::TYPE_INT,
              true,
              'service_id (integer): Required. ' .
                Language::get('resource.Ssl.retrieveByServiceId.service_id')
            ]
        ]
      ],
      ['importCertificate',
        [
          'key' => [
            Util::TYPE_STRING,
            true,
              'key (string): Required. ' .
                Language::get('resource.Ssl.importCertificate.key')
          ],
          'crt' => [
            Util::TYPE_STRING,
            true,
              'crt (string): Required. ' .
                Language::get('resource.Ssl.importCertificate.crt')
          ],
          'chain' => [
            Util::TYPE_STRING,
            true,
              'chain (string): Required. ' .
                Language::get('resource.Ssl.importCertificate.chain')
          ]

        ]
      ],
      ['createCertificateFromCsr',
        [
          'csr' => [
            Util::TYPE_STRING,
            true,
            'csr (string): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.csr')
          ],
          'key' => [
            Util::TYPE_STRING,
            true,
            'key (string): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.key')
          ],
          'months' => [
            Util::TYPE_INT,
            true,
            'months (integer): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.months')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.package_id')
          ],
          'approver_email' => [
            Util::TYPE_ARRAY,
            true,
            'approver_email (array): Required. ' .
            Language::get(
              'resource.Ssl.createCertificateFromCsr.approver_email'
            )
          ],
        ]
      ],
      ['createCertificate',
        [
          'domain' => [
            Util::TYPE_STRING,
            true,
            'domain (string): Required. ' .
            Language::get('resource.Ssl.createCertificate.domain')
          ],
          'distinguished_name' => [
            Util::TYPE_ARRAY,
            true,
            'distinguished_name (array): Required. ' .
            Language::get('resource.Ssl.createCertificate.distinguished_name')
          ],
          'months' => [
            Util::TYPE_INT,
            true,
            'months (integer): Required. ' .
            Language::get('resource.Ssl.createCertificate.months')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
            Language::get('resource.Ssl.createCertificate.package_id')
          ],
          'approver_email' => [
            Util::TYPE_ARRAY,
            true,
            'approver_email (array): Required. ' .
            Language::get('resource.Ssl.createCertificate.approver_email')
          ]

        ]
      ]
    ];
  }

  /**
   * @covers Ssl::retrieveByServiceId
   */
  public function testRetrieveByServiceId() {
    $handler = function ($request, $options) {
      $this->assertEquals('ssl-cert', $request->getUri()->getPath());
      $this->assertEquals(
        'filter[service_id]=58887',
        urldecode($request->getUri()->getQuery())
      );

      // assertions passed; return 200 response
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_GET_1, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) {
        $results = $api->getEndpoint(static::_SUBJECT_MODULE)
          ->retrieveByServiceId(58887);
        $this->assertEquals(123, $results->get('cert_id'));
        $this->assertEquals(
          'admin@example.com',
          $results->get('approver_email')
        );
      });
  }

  /**
   * @covers Ssl::importCertificate
   */
  public function testImportCertificate() {
    $handler = function ($request, $options) {
      $this->assertEquals('ssl-cert', $request->getUri()->getPath());
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_GET, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) {
        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $results = $endpoint->importCertificate(
          $this->_getResource(static::_KEY),
          $this->_getResource(static::_CRT),
          $this->_getResource(static::_CHAIN)
        );
        $this->assertEquals(637, $results->get('cert_id'));
      });
  }

  /**
   * @covers Ssl::createCertificateFromCsr
   */
  public function testCreateCertificateFromCsr() {
    // kick off
    $this->_getSandbox()
      ->play(function ($api, $sandbox) {

        $sandbox->makeResponse(
          'POST ssl-cert',
          200,
          $this->_getResource(static::_RESOURCE_IMPORT)
        );
        $sandbox->makeResponse(
          'GET ssl-cert',
          200,
          $this->_getResource(static::_RESOURCE_GET_1)
        );
        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $results = $endpoint->createCertificateFromCsr(
          $this->_getResource(static::_CSR_2),
          $this->_getResource(static::_KEY_2),
          179,
          12,
          ['example.com' => 'admin@example.com']
        );
        $this->assertEquals(
          123,
          $results->get('cert_id'), 'Checking Certificate ID'
        );
      });
  }

  /**
   * @covers Ssl::createCertificate
   */
  /*
    string $domain,
    array $distinguished_name,
    int $months,
    int $package_id,
    array $approver_email
  */
  public function testCreateCertificate() {
    $this->_getSandbox()
      ->play(function ($api, $sandbox) {
        $sandbox->makeResponse(
          'POST ssl-cert',
          200,
          $this->_getResource(static::_RESOURCE_IMPORT)
        );
        $sandbox->makeResponse(
          'GET ssl-cert',
          200,
          $this->_getResource(static::_RESOURCE_GET_1)
        );
        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $results = $endpoint->createCertificate(
          'example.com',
          [
            'email' => 'john@example.com',
            'street' => '123 Main Street',
            'locality' => 'Anytown',
            'state' => 'MI',
            'country' => 'US',
            'organization' => 'Acme Examples',
            'organizational_unit' => 'marketing'
          ],
          179,
          12,
          ['example.com' => 'admin@example.com']
        );
        $this->assertEquals(
          123,
          $results->get('cert_id'), 'Checking Certificate ID'
        );
        $this->assertEquals(
          'example.com',
          $results->get('common_name'), 'Checking common name'
        );
      });
  }

  /**
   * @covers Ssl::decodeCsr
   */
  public function testDecodeCsr() {
    $handler = function ($request, $options) {
      $this->assertEquals('ssl-cert/decode-csr', $request->getUri()->getPath());
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_GET_DECODED_CSR, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) {
        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $results = $endpoint->decodeCsr(
          $this->_getResource(static::_CSR_2),
          179
        );

        // san
        $this->assertArrayHasKey('san', $results);
        $this->assertEmpty($results['san']);

        // distinguished name
        $this->assertArrayHasKey('dn', $results);
        $this->assertNotEmpty($results['dn']);
        $this->assertEquals('example.com', $results['dn']['commonName']);
        $this->assertEquals('US', $results['dn']['countryName']);
        $this->assertEquals('MI', $results['dn']['stateOrProvinceName']);
        $this->assertEquals('Anytown', $results['dn']['localityName']);

        // approvers
        $this->assertArrayHasKey('approvers', $results);
        $this->assertNotEmpty($results['approvers']);
        $this->assertArrayHasKey('example.com', $results['approvers']);
        $this->assertEquals(5, count($results['approvers']['example.com']));
        $this->assertequals(
          'admin@example.com',
          $results['approvers']['example.com'][0]
        );
      });
  }

  /**
   * @covers Ssl::getCsrDetails
   */
  public function testGetCsrDetails() {
    $handler = function ($request, $options) {
      $this->assertEquals(
        'ssl-cert/get-csr-details',
        $request->getUri()->getPath()
      );
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_GET_DECODED_CSR, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) {
        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $results = $endpoint->getCsrDetails(
          'example.com',
          [
            'email' => 'john@example.com',
            'street' => '123 Main Street',
            'locality' => 'Anytown',
            'state' => 'MI',
            'country' => 'US',
            'organization' => 'Acme Examples',
            'organizational_unit' => 'marketing'
          ],
          179
        );

        // san
        $this->assertArrayHasKey('san', $results);
        $this->assertEmpty($results['san']);

        // distinguished name
        $this->assertArrayHasKey('dn', $results);
        $this->assertNotEmpty($results['dn']);
        $this->assertEquals('example.com', $results['dn']['commonName']);
        $this->assertEquals('US', $results['dn']['countryName']);
        $this->assertEquals('MI', $results['dn']['stateOrProvinceName']);
        $this->assertEquals('Anytown', $results['dn']['localityName']);

        // approvers
        $this->assertArrayHasKey('approvers', $results);
        $this->assertNotEmpty($results['approvers']);
        $this->assertArrayHasKey('example.com', $results['approvers']);
        $this->assertEquals(5, count($results['approvers']['example.com']));
        $this->assertequals(
          'admin@example.com',
          $results['approvers']['example.com'][0]
        );
      });  }

  public function testGetParams(string $action = '', array $expected = []) {
    $this->markTestSkipped();
  }

}
