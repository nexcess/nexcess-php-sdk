<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Endpoint as ReadableEndpoint,
  Resource\Ssl\Entity,
  Util\Util
};

/**
 * API endpoint for orders.
 */
class Endpoint extends ReadableEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Ssl';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _URI = 'ssl-cert';

  /**
   * Retrieve a certificate by it's service_id
   *
   * @param int $service_id a valid service_id for a certificate
   */
  public function retrieveByServiceId(int $service_id) : Entity {
    $filter = ['filter' => ['service_id' => $service_id]];
    $response = $this->_client->request(
      'GET',
      static::_URI . "?{$this->_buildListQuery($filter)}"
    );
    return $this->getModel()->sync(Util::decodeResponse($response)[0]);
  }

  /**
   * Import an existing certificate
   *
   * @param Entity $entity Cloud Server model
   * @param string $key the key to the crt
   * @param string $crt the crt
   * @param string $chain The chain certificate
   *
   * @return Entity
   * @throws GuzzleHttp\Exception\ClientException If request fails
   */
  public function importCertificate(
    string $key,
    string $crt,
    string $chain = ''
  ) : Entity {
    $response = $this->_client->post(
      self::_URI,
      ['json' => ['key' => $key, 'crt' => $crt, 'chain' => $chain]]
    );

    return $this->getModel()->sync(Util::decodeResponse($response));
  }

}
