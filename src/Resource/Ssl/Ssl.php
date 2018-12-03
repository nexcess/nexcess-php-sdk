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
    'id' => 'cert_id',
    'service_id' => 'service.id'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [
    'client' => Client::class
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [
    'alt_domains',
    'alt_names',
    'approver_email',
    'broker_id',
    'cert_id',
    'chain',
    'chain_crts',
    'client_id',
    'common_name',
    'crt',
    'csr',
    'distinguished_name',
    'domain',
    'domain_count',
    'duns',
    'id',
    'identity',
    'incorporating_agency',
    'is_expired',
    'is_installable',
    'is_multi_domain',
    'is_real',
    'is_wildcard',
    'key',
    'months',
    'package_id',
    'valid_from_date',
    'valid_to_date',
    'service.id'
  ];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'broker_id',
    'chain_crts',
    'client_id',
    'crt',
    'domain_count',
    'duns',
    'identity',
    'incorporating_agency',
    'is_expired',
    'is_installable',
    'is_multi_domain',
    'is_real',
    'is_wildcard',
    'valid_from_date',
    'valid_to_date'
  ];

  /**
   * Create a new backup
   * This method has two modes. If a CSR is present then it will attempt to
   * create the cert via the CSR. Otherwise, it will attempt to create the csr
   * and key and then create the certificate.
   *
   * @return Ssl
   * @throws \GuzzleHttp\Exception\ClientException on fail
   */
  public function create() : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    if (!empty($this->get('csr'))) {
      return $endpoint->createCertificateFromCsr(
        $this->get('csr'),
        $this->get('key'),
        $this->get('months'),
        $this->get('package_id'),
        $this->get('approver_email')
      );
    }

    return $endpoint->createCertificate(
      $this->get('domain'),
      $this->get('distinguished_name'),
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

  /**
   * Decode an existing CSR and compare it to the package_id
   *
   * @return array
   * @throws \GuzzleHttp\Exception\ClientException on fail
   */
  public function decodeCsr() : array {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

      return $endpoint->decodeCsr(
        $this->get('csr'),
        $this->get('package_id')
      );
  }

  /**
   * Create a CSR and make sure the type matches the package type.
   *
   * @return array
   * @throws \GuzzleHttp\Exception\ClientException on fail
   */
  public function getCsrDetails() : array {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->getCsrDetails(
      $this->get('domain'),
      $this->get('distinguished_name'),
      $this->get('package_id')
    );
  }
}
