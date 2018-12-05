<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Client\Client,
  Resource\Model
};

/**
 * Certificates
 */
class Ssl extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Ssl';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [
    'id' => 'cert_id'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [
    'client' => Client::class
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [
    'approver_email',
    'cert_id',
    'chain',
    'common_name',
    'crt',
    'csr',
    'distinguished_name',
    'domain',
    'id',
    'key',
    'months',
    'package_id'
  ];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'alt_names',
    'client_id',
    'identity',
    'is_expired',
    'is_installable',
    'is_multi_domain',
    'is_wildcard',
    'valid_from_date',
    'valid_to_date'
  ];

  /**
   * Create a new Ssl
   * @param array $distinguished_name
   *
   * @return Ssl
   * @throws \GuzzleHttp\Exception\ClientException on fail
   */
  public function create(array $distinguished_name) : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->create(
      $this->get('domain'),
      $distinguished_name,
      $this->get('months'),
      $this->get('package_id'),
      $this->get('approver_email')
    );
  }

  /**
   * Create a new Ssl from a CSR
   * @param string $csr a valid CSR
   *
   * @return Ssl
   * @throws \GuzzleHttp\Exception\ClientException on fail
   */
  public function createFromCsr(string $csr) : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

      return $endpoint->createFromCsr(
        $csr,
        $this->get('key'),
        $this->get('months'),
        $this->get('package_id'),
        $this->get('approver_email')
      );
  }

  /**
   * Import a certificate
   *
   * @return Ssl
   * @throws \GuzzleHttp\Exception\ClientException on fail
   */
  public function import() : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

      return $endpoint->importCertificate(
        $this->get('key'),
        $this->get('crt'),
        $this->get('chain')
      );
  }
}
