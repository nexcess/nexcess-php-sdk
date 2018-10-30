<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\ {
  Resource\Modelable as Model,
  Resource\PromisedResource,
  Resource\Readable
};

/**
 * Interface for API endpoints which can delete existing resources.
 */
interface Deletable extends Readable {

  /**
   * Deletes an existing item.
   *
   * @param Model|int $model_or_id Model or item id to delete
   * @return PromisedResource A promise wrapping the deleted model
   * @throws ApiException If request fails
   */
  public function delete($model_or_id) : PromisedResource;
}
