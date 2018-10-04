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
  Resource\Readable
};

/**
 * Interface for API endpoints which can update existing resources.
 */
interface Updatable extends Readable {

  /**
   * Updates an existing item.
   *
   * Note that not all properties of a given Model can be updated directly
   * (e.g., aliases and readonly properties are not updatable).
   * In such cases, check for Endpoint methods that do the update you want.
   *
   * @param int $id Item id
   * @param array|null $data Map of properties:values to set before update
   * @return Updatable $this
   * @throws ApiException If request fails
   */
  public function update(Model $model, array $data = []) : Updatable;
}
