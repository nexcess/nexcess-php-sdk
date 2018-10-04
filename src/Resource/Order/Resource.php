<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Order;

use Nexcess\Sdk\ {
  Resource\Client\Resource as Client,
  Resource\Cloud\Resource as Cloud,
  Resource\Invoice\Resource as Invoice,
  Resource\Model,
  Resource\Order\OrderException,
  Resource\Package\Resource as Package,
  Resource\Service,
  Resource\ServiceEndpoint,
  SdkException,
  Util\Util
};

/**
 * Orders.
 */
class Resource extends Model {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'order_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [
    'invoice' => 'invoice_id',
    'package' => 'package_id',
    'service' => 'service_id'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [
    'invoice' => Invoice::class,
    'package' => Package::class,
    'service' => Service::class
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['order_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'client_id',
    'cloud_id',
    'identity',
    'invoice',
    'order_date',
    'order_id',
    'package',
    'recurring_total',
    'service',
    'setup_fee',
    'status',
    'total',
    'type'
  ];

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
