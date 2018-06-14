<?php

use Nexcess\Sdk\ {
  Resource\App\App,
  Resource\Cloud\Cloud,
  Resource\CloudAccount\CloudAccount,
  Resource\Collection,
  Resource\Order\Order,
  Resource\Package\Package
};

return [
  'id' => 1,
  'auto_renew' => 'yes',
  'bandwidth' => [
    'given' => '100',
    'overage_fee' => 0,
    'override' => 0,
    'previous' => [
      'total' => 0,
      'billable' => 0
    ],
    'profile_id' => 2,
    'projected' => [
      'total' => 0,
      'billable' => 0
    ],
    'type' => 'GB',
    'used' => [
      'total' => 0,
      'billable' => 0
    ]
  ],
  'billing_term' => [
    'amount' => 50,
    'description' => 'Monthly',
    'months' => 1,
    'type' => 'monthly',
    'years' => 0
  ],
  'cancellable_override_expire_date' => 0,
  'can_change_root_password' => false,
  'child_cloud_accounts' => Collection::__set_state([
    '_models' => [],
    '_of' => 'Nexcess\\Sdk\\Resource\\CloudAccount\\CloudAccount'
  ]),
  'client_id' => 1,
  'cloud_account' => CloudAccount::__set_state([
    '_values' => [
      'account_id' => 1,
      'identity' => 'nexcess-cli.example.com',
      'ip' => '203.0.113.1',
      'domain' => 'nexcess-cli.example.com',
      'is_dev_account' => false,
      'temp_domain' => 'example.nxcli.net',
      'unix_username' => 'abcd1234',
      'app' => App::__set_state([
        '_values' => [
          'app_id' => 13,
          'identity' => 'Flexible ',
          'type' => 'generic',
          'status' => 'active',
          'name' => 'Flexible',
          'version' => ''
        ],
      ]),
      'environment' => [
        'software' => [
          'php' => [
            'cli' => '/opt/remi/php71/root/usr/bin/php',
            'path' => '/opt/remi/php71',
            'version' => '7.1'
          ],
          'redis' => [
            'host' => '',
            'port' => '',
            'socket' => '/var/run/redis-multi-example.redis/redis.sock',
            'version' => '3.2'
          ]
        ],
        'options' => [
          'autoscale_enabled' => true,
          'nxcache_nocache' => false,
          'nxcache_varnish' => false,
          'nxcache_varnish_static' => false,
          'nxcache_varnish_ttl' => 120
        ]
      ]
    ]
  ]),
  'cloud_id' => 3,
  'description' => 'nc.xsmall-test',
  'dev_account_count' => 0,
  'discount_id' => 0,
  'environment_type' => 'production',
  'has_console' => false,
  'has_paypal_subscription' => false,
  'has_stored_password' => false,
  'host' => 'nexcess-cli.example.com',
  'identity' => 'nexcess-cli.example.com - nc.xsmall-test',
  'is_cancellable' => true,
  'is_rebootable' => false,
  'last_bill_date' => '06/11/2018 04:00:00 +00:00',
  'cloud' => Cloud::__set_state([
    '_values' => [
      'cloud_id' => 3,
      'identity' => 'us-midwest-1',
      'type' => 'openstack',
      'status' => 'active',
      'location' => 'Southfield, MI',
      'location_code' => 'us-midwest-1',
      'country' => 'US'
    ],
  ]),
  'next_bill_date' => '07/11/2018 04:00:00 +00:00',
  'nickname' => '',
  'order' => Order::__set_state([
    '_values' => [
      'order_id' => 55119,
      'identity' => '(1) Alice McAllison',
      'type' => 'virt-guest-cloud',
      'status' => 'completed'
    ]
  ]),
  'override_addons' => 'no',
  'override_percent' => 0,
  'override_price' => 50,
  'package' => Package::__set_state([
    '_values' => [
      'package_id' => 700,
      'identity' => 'nc.xsmall-test',
      'type' => 'virt-guest-cloud'
    ]
  ]),
  'parent_id' => 0,
  'paypal_subscribe_link' => 'https://www.sandbox.paypal.com',
  'pricing_type' => 'monthly',
  'settings' => [
    'extranet.disable-networking.enabled' => false
  ],
  'start_date' => '05/11/2018 13:42:51 +00:00',
  'state' => 'stable',
  'status' => 'enabled',
  'term' => 1,
  'turnup_date' => '05/11/2018 13:42:51 +00:00',
  'type' => 'virt-guest-cloud'
];
