<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Service;

use Nexcess\Sdk\ {
  ApiException,
  Resource\CloudServer\Endpoint as CloudServer,
  Resource\Endpoint as BaseEndpoint,
  Resource\Service\ServiceException,
  Resource\ServiceCancellation\Resource as ServiceCancellation,
  Resource\VirtGuestCloud\Endpoint as VirtGuestCloud
};

/**
 * Represents an API endpoint for client services.
 */
abstract class Endpoint extends BaseEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Service';

  /** {@inheritDoc} */
  protected const _URI = 'service';

  /** @var string API endpoint for service cancellation. */
  protected const _URI_CANCEL = 'service-cancellation/add';

  /** @var string Service type. */
  protected const _SERVICE_TYPE = '';

  /** @var array Map of service type:classname pairs. */
  protected const _SERVICE_TYPE_MAP = [
    CloudServer::_SERVICE_TYPE => CloudServer::_MODEL_FQCN,
    VirtGuestCloud::_SERVICE_TYPE => VirtGuestCloud::_MODEL_FQCN
  ];

  /**
   * Looks up a Service model classname given a service type.
   *
   * It's very sad that we must know about subclasses :(
   *
   * @param string $type The service type
   * @return string Service model FQCN on success
   * @throws ResourceException If no service model is found for given type
   */
  public static function findServiceModel(string $type) : string {
    if (! isset(self::_SERVICE_TYPE_MAP[$type])) {
      throw new ResourceException(
        ResourceException::NO_SUCH_SERVICE_MODEL,
        ['model' => $type]
      );
    }

    return self::_SERVICE_TYPE_MAP[$type];
  }

  /**
   * Gets the questions for the cancellation survey for a service.
   *
   * @param Service $model Service model to cancel
   * @return array List of cancellation survey questions + metadata
   */
  public function getCancellationSurvey() : array {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );

    $this->_checkModelType($model);

    if ($model->get('is_cancellable') !== true) {
      throw new ServiceException(
        ServiceException::NOT_CANCELLABLE,
        ['service' => static::_SERVICE_TYPE, 'id' => $model->getId()]
      );
    }

    // @todo
  }

  /**
   * Requests a service cancellation.
   *
   * @param Service $service Service model to cancel
   * @param array $survey Cancellation survey
   * @return ServiceCancellation
   * @throws ApiException If request fails
   */
  public function cancel(
    Service $service,
    array $survey
  ) : ServiceCancellation {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );

    $this->_checkModelType($service);

    if ($service->get('is_cancellable') !== true) {
      throw new ServiceException(
        ServiceException::NOT_CANCELLABLE,
        ['service' => static::_SERVICE_TYPE, 'id' => $service->getId()]
      );
    }

    $survey['service_id'] = $service->getId();
    return $this->getModel(ServiceCancellation::class)->sync(
      $this->_client->request('POST', static::_URI_CANCEL, $survey)
    );
  }

  /**
   * {@inheritDoc}
   * Overridden to set service type on list queries.
   */
  protected function _buildListQuery(array $filter) : string {
    return parent::_buildListQuery(
      ['type' => static::_SERVICE_TYPE] + $filter
    );
  }
}
