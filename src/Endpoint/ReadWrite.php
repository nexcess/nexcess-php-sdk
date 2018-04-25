<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Read,
  Endpoint\ReadWritable,
  Exception\ApiException,
  Model\Modelable as Model,
  Response
};

/**
 * Represents a writable API endpoint.
 */
abstract class ReadWrite extends Read implements ReadWritable {

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Model {
    $model = static::MODEL_NAME;

    return (new $model())->sync(
      $this->_client
        ->request('POST', static::ENDPOINT, ['json' => $data])
        ->toArray()
    );
  }

  /**
   * {@inheritDoc}
   */
  public function delete($model_or_id) {
    $fqcn = static::MODEL_NAME;
    $id = ($model_or_id instanceof $fqcn) ?
      $model_or_id->offsetGet('id') :
      $model_or_id;
    if (! is_int($id)) {
      throw new ApiException(ApiException::MISSING_ID, ['model' => $fqcn]);
    }

    $this->_client->request('DELETE', static::ENDPOINT . "/{$id}");
  }

  /**
   * {@inheritDoc}
   */
  public function update(Model $model, array $data = []) : Model {
    $this->_checkModelType($model);

    $id = $model->offsetGet('id');
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::MODEL_NAME]
      );
    }

    foreach ($data as $key => $value) {
      $model->offsetSet($key, $value);
    }

    $update = empty($this->_stored[$id]) ?
      $model->toArray() :
      array_udiff_assoc(
        $model->toArray(true),
        $this->_stored[$id],
        function ($value, $stored) { return ($value === $stored) ? 0 : 1; }
      );

    if (! empty($update)) {
      return $this->_sync(
        $this->_client
          ->request('PATCH', static::ENDPOINT . "/{$id}/edit", $update)
          ->toArray()
      );
    }

    return $model;
  }
}
