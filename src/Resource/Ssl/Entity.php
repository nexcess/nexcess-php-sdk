<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Order;

use Nexcess\Sdk\ {
  Resource\Client\Entity as Client,
  Resource\Service\Endpoint as ServiceEndpoint,
  Resource\Cloud\Entity as Cloud
};

/**
 * Orders.
 */
class Entity extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Ssl';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [];

  /**
   * Gets the Client this order belongs to.
   *
   * @return Client
   */
  public function getClient() : Client {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );
  }

  /**
   * Gets the Cloud (if any) associated with this order.
   *
   * @return Cloud
   */
  public function getCloud() : Cloud {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );
  }

  /**
   * Builds a Service object associated with this order.
   *
   * @param Service|array $service
   */
  public function setService($service) {
    if ($service instanceof Service) {
      $this->_values['service'] = $service;
      return;
    }

    if (! is_array($service) || ! isset($service['type'], $service['id'])) {
      throw new OrderException(
        OrderException::INVALID_SERVICE_DATA,
        ['data' => is_array($service) ? $service : Util::type($service)]
      );
    }

    $this->_values['service'] = $this->_getModel(
      ServiceEndpoint::findServiceModel($service['type'])
    )->sync($service);
  }
}
