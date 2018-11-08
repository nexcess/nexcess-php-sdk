<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\ {
  ApiException,
  Resource\Modelable,
  Resource\Readable
};

/**
 * Interface for API endpoints which can delete existing resources.
 */
interface Deletable extends Readable {

  /**
   * Deletes an existing item.
   *
   * @param Modelable $model Model to delete
   * @return Modelable The deleted model
   * @throws ApiException If request fails
   */
  public function delete(Modelable $model) : Modelable;
}
