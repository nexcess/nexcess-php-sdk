<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Client\Entity as Client,
  Resource\Model
};

/**
 * Orders.
 */
class Entity extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Ssl';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['cert_id' => 'id'];

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
  ];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'broker_id',
    'cert_id',
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

  public function create() : Entity {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);
    
    return $endpoint->createCertificate(
      $this->get('domain'),
      $this->get('distinguished_name'),
      $this->get('months'),
      $this->get('package_id'),
      $this->get('approver_email')
    );
  }

}
