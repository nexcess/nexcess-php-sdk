<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use Nexcess\Sdk\Exception;

class UtilException extends Exception {

  /** @var int Failed to read a file. */
  const FILE_NOT_READABLE = 1;

  /** @var int Error from json_decode. */
  const JSON_DECODE_FAILURE = 3;

  /** @var int Invalid translation map or value. */
  const INVALID_LANGUAGE_MAP = 4;

  /** @var int A config option failed validation. */
  const INVALID_CONFIG_OPTION = 13;

  /** @var int Error encoding json. */
  const JSON_ENCODE_FAILURE = 17;

  /** @var int Json files are expected to contain objects/arrays. */
  const INVALID_JSON_DATA = 18;

  /** @var int A key was translated as a non-string value. */
  const INVALID_TRANSLATION = 19;

  /** {@inheritDoc} */
  const INFO = [
    self::FILE_NOT_READABLE => ['message' => 'util.file_not_readable'],
    self::JSON_DECODE_FAILURE => ['message' => 'util.json_decode_failure'],
    self::INVALID_LANGUAGE_MAP => ['message' => 'util.invalid_language_map'],
    self::INVALID_CONFIG_OPTION => ['message' => 'util.invalid_config_option'],
    self::JSON_ENCODE_FAILURE => ['message' => 'util.json_encode_failure'],
    self::INVALID_JSON_DATA => ['message' => 'util.invalid_json_data'],
    self::INVALID_TRANSLATION => ['message' => 'util.invalid_translation']
  ];
}
