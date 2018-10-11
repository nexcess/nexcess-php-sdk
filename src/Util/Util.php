<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use JsonSerializable,
  stdClass;
use Nexcess\Sdk\Util\UtilException;

/**
 * Assorted utility functions.
 */
class Util {

  /** @var int Validate as boolean. */
  public const FILTER_BOOL = FILTER_VALIDATE_BOOLEAN;

  /** @var int Validate as float. */
  public const FILTER_FLOAT = FILTER_VALIDATE_FLOAT;

  /** @var int Validate as integer. */
  public const FILTER_INT = FILTER_VALIDATE_INT;

  /** @var int Default options for jsonDecode(). */
  public const JSON_DECODE_DEFAULT_OPTS = JSON_BIGINT_AS_STRING;

  /** @var int Default options for jsonEncode(). */
  public const JSON_ENCODE_DEFAULT_OPTS = JSON_BIGINT_AS_STRING |
    JSON_PRESERVE_ZERO_FRACTION |
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE;

  /** @var int Pretty-printing options for jsonEncode(). */
  public const JSON_ENCODE_PRETTY = self::JSON_ENCODE_DEFAULT_OPTS |
    JSON_PRETTY_PRINT;

  /** @var string Php datatype. */
  public const TYPE_ARRAY = 'array';

  /** @var string Php datatype. */
  public const TYPE_BOOL = 'boolean';

  /** @var string Php datatype. */
  public const TYPE_FLOAT = 'float';

  /** @var string Php datatype. */
  public const TYPE_INT = 'integer';

  /** @var string Php datatype. */
  public const TYPE_NULL = 'null';

  /** @var string Php datatype. */
  public const TYPE_OBJECT = 'object';

  /** @var string Php datatype. */
  public const TYPE_RESOURCE = 'resource';

  /** @var string Php datatype. */
  public const TYPE_STRING = 'string';

  /** @var array Map of gettype => TYPE_* replacements. */
  protected const _TYPE_TR = ['double' => 'float', 'NULL' => 'null'];

  /**
   * Looks up a value at given path in an array-like subject.
   *
   * @param array|ArrayAccess $subject The subject
   * @param string $path Dot-delimited path of keys to follow
   * @return mixed The value at the given path if it exists; null otherwise
   */
  public static function dig($subject, string $path) {
    foreach (explode('.', $path) as $key) {
      if (! isset($subject[$key])) {
        return null;
      }

      $subject = $subject[$key];
    }

    return $subject;
  }

  /**
   * Merges arrays recursively, but replaces (never merges) non-array values.
   * @see https://gist.github.com/adrian-enspired/e766b37334130ea04eaf
   *
   * @param array $subject The subject array
   * @param array $extenders The arrays to extend the subject array
   * @return array the extended array
   */
  public static function extendRecursive(
    array $subject,
    array ...$extenders
  ) : array {
    foreach ($extenders as $extend) {
      foreach ($extend as $k => $v) {
        if (is_int($k)) {
          $subject[] = $v;
          continue;
        }
        if (isset($subject[$k]) && is_array($subject[$k]) && is_array($v)) {
          $subject[$k] = self::extendRecursive($subject[$k], $v);
          continue;
        }
        $subject[$k] = $v;
      }
    }
    return $subject;
  }

  /**
   * Wraps filter_var() with preferred flags.
   *
   * @param mixed $value The value to filter
   * @param int $filter The filter to use
   * @return mixed|null The filtered value on success; null otherwise
   */
  public static function filter($value, int $filter) {
    $flags = 0;
    if ($filter !== self::FILTER_BOOL) {
      $flags |= FILTER_NULL_ON_FAILURE;
    }

    return filter_var($value, $filter, $flags);
  }

  /**
   * Checks if a value is json-encodable.
   * Accepts scalar values, arrays, and stdClass or JsonSerializable objects.
   *
   * @param mixed $value The value to check
   * @return bool True if value is suitable for json-encoding; false otherwise
   */
  public static function isJsonable($value) : bool {
    if ($value instanceof JsonSerializable || $value === null) {
      return true;
    }

    if (is_array($value) || $value instanceof stdClass) {
      foreach ($value as $nested) {
        if (! self::isJsonable($nested)) {
          return false;
        }
      }
      return true;
    }

    return is_scalar($value);
  }

  /**
   * Wraps json_decode() with error handling and preferred options.
   *
   * @param string $json To be decoded
   * @param int $opts Bitmask of decoding options
   * @return array Data on success
   * @throws UtilException On failure
   */
  public static function jsonDecode(
    string $json,
    int $opts = self::JSON_DECODE_DEFAULT_OPTS
  ) : array {
    $data = json_decode($json, true, 512, $opts);
    if (json_last_error() === JSON_ERROR_NONE) {
      return $data;
    }

    throw new UtilException(
      UtilException::JSON_DECODE_FAILURE,
      ['message' => json_last_error_msg()]
    );
  }

  /**
   * Wraps json_encode() with error handling and preferred options.
   *
   * @param mixed $data To be encoded
   * @param int $opts Bitmask of encoding options
   * @return string Json on success
   * @throws UtilException On failure
   */
  public static function jsonEncode(
    $data,
    int $opts = self::JSON_ENCODE_DEFAULT_OPTS
  ) : string {
    $json = json_encode($data, $opts);
    if (json_last_error() === JSON_ERROR_NONE) {
      return $json;
    }

    throw new UtilException(
      UtilException::JSON_ENCODE_FAILURE,
      ['message' => json_last_error_msg()]
    );
  }

  /**
   * Recursively sorts an array by key.
   * @see https://php.net/ksort
   *
   * @param array $subject The array to sort
   * @return void Operates on subject by reference
   */
  public static function kSortRecursive(array &$subject) : void {
    ksort($subject);
    foreach ($subject as &$item) {
      if (is_array($item)) {
        self::kSortRecursive($item);
      }
    }
  }

  /**
   * Reads and decodes a .json file.
   *
   * @param string $filepath Path to json file to read
   * @return array The parsed json
   * @throws UtilException If file cannot be read, or parsing fails
   */
  public static function readJsonFile(string $filepath) : array {
    if (! is_readable($filepath)) {
      throw new UtilException(
        UtilException::FILE_NOT_READABLE,
        ['filepath' => $filepath]
      );
    }

    $data = self::jsonDecode(file_get_contents($filepath));
    if (! is_array($data)) {
      throw new UtilException(
        UtilException::INVALID_JSON_DATA,
        ['file' => $filepath]
      );
    }

    return $data;
  }

  /**
   * Gets the classname/type of the given value.
   *
   * @param mixed $value The value to check
   * @return string The value's classname if an object, type otherwise
   */
  public static function type($value) : string {
    return strtr(
      is_object($value) ? get_class($value) : gettype($value),
      self::_TYPE_TR
    );
  }
}
