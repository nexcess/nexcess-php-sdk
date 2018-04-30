<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

/**
 * Assorted utility functions.
 */
class Util {

  /**
   * Looks up a value at given path in an array (if it exists).
   *
   * @param array $subject The subject array
   * @param string $path Dot-delimited path of keys to follow
   * @return mixed The value at the given path if it exists; null otherwise
   */
  public static function dig(array $subject, string $path) {
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
   * Reads and decodes a .json file.
   *
   * @param string $filepath Path to json file to read
   * @return array The parsed json
   * @throws SdkException If file cannot be read, or parsing fails
   */
  public static function readJsonFile(string $filepath) : array {
    if (! is_readable($filepath)) {
      throw new SdkException(
        SdkException::FILE_NOT_READABLE,
        ['filepath' => $filepath]
      );
    }

    $data = json_decode(file_get_contents($filepath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new SdkException(
        SdkException::JSON_DECODE_FAILURE,
        ['error' => json_last_error_msg()]
      );
    }

    if (! is_array($data)) {
      throw new SdkException(
        SdkException::INVALID_JSON_DATA,
        ['invalid' => $data]
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
    return is_object($value) ?
      get_class($value) :
      strtolower(gettype($value));
  }
}
