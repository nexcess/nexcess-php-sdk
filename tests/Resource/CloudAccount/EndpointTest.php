<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\CloudAccount;

use Nexcess\Sdk\ {
  Resource\CloudAccount\Endpoint,
  Resource\CloudAccount\Resource,
  Tests\Resource\EndpointTestCase,
  Util\Language,
  Util\Util
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [
    'cloud-account-1.fromArray.json' => 'cloud-account-1.toArray-shallow.php'
  ];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [
    'GET %2Fcloud-account%3F.json' => []
  ];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Resource::class;

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [
      [
        'create',
        [
          'app_id' => [
            Util::TYPE_INT,
            true,
            'app_id (integer): Required. ' .
              Language::get('resource.CloudAccount.create.app_id')
          ],
          'cloud_id' => [
            Util::TYPE_INT,
            true,
            'cloud_id (integer): Required. ' .
              Language::get('resource.CloudAccount.create.cloud_id')
          ],
          'domain' => [
            Util::TYPE_STRING,
            true,
            'domain (string): Required. ' .
              Language::get('resource.CloudAccount.create.domain')
          ],
          'install_app' => [
            Util::TYPE_BOOL,
            false,
            'install_app (boolean): Optional. ' .
              Language::get('resource.CloudAccount.create.install_app')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
              Language::get('resource.CloudAccount.create.package_id')
          ]
        ]
      ],
      [
        'setPhpVersion',
        [
          'version'=> [
            Util::TYPE_STRING,
            true,
            'version (string): Required. ' .
              Language::get('resource.CloudAccount.setPhpVersion.version')
          ]
        ]
      ]
    ];
  }
}
