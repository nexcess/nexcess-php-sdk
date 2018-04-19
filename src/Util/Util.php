<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

/**
 * Assorted utility functions.
 */
class Util {

  /**
   * Merges arrays recursively, but never merges non-array values.
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
}
