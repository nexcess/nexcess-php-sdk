<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\ApiToken;

use Nexcess\Sdk\ {
  Resource\ApiToken\Resource,
  Resource\Create,
  Resource\Createable,
  Resource\Delete,
  Resource\Deleteable,
  Resource\Endpoint as BaseEndpoint,
  Resource\Update,
  Resource\Updateable
};

/**
 * API actions for portal Login.
 */
class Endpoint
  extends BaseEndpoint
  implements Creatable, Deletable, Updatable {
  use Create, Delete, Update;

  /** {@inheritDoc} */
  protected const _URI = 'api-token';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;
}
