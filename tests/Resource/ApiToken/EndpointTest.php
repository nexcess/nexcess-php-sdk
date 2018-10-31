<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\ApiToken;

use Nexcess\Sdk\ {
  Resource\ApiToken\Endpoint,
  Resource\ApiToken\Entity,
  Tests\Resource\EndpointTestCase,
  Util\Language,
  Util\Util
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [
    'api-token-1.fromArray.json' => 'api-token-1.toArray-shallow.json'
  ];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [
    'GET-%2Fapi-token%3F.json' => []
  ];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Entity::class;

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [
      [
        'create',
        [
          'name' => [
            Util::TYPE_STRING,
            true,
            'name (string): Required. ' .
              Language::get('resource.ApiToken.create.name')
          ]
        ]
      ]
    ];
  }
}
