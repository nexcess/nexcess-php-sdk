<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\ {
  Resource\Model,
  Resource\Readable
};

/**
 * Interface for API endpoints which can delete existing resources.
 */
interface Deletable extends Readable {

  /**
   * Deletes an existing item.
   *
   * @param Model $model Model to delete
   * @return Model The deleted model
   * @throws ApiException If request fails
   */
  public function delete(Model $model) : Model;
}
