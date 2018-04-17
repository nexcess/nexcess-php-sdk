<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

/**
 * Utility class for looking up translations.
 * Defaults to supporting English.
 */
class Language {

  /** @var string Default language to translate if none specified. */
  const DEFAULT_LANGUAGE = 'en_US';

  /** @var Language Default instance. */
  protected $_instance;

  /**
   * Makes the default instance available globally.
   */
  public static function getInstance() : Language {
    if (! self::$_instance) {
      self::init();
    }

    return self::$_instance;
  }

  /**
   * Gets a translation from the default instance.
   *
   * @param string $key Identifier for the desired translation
   */
  public static function get() : string {
    return self::getInstance()->getTranslation($key);
  }

  /**
   * Initializes an instance and makes it globally available.
   *
   * @param string $language Identifier for language to translate to
   * @param string[] $paths List of filepaths
   * @throws
   */
  public static function init(
    string $language = self::DEFAULT_LANGUAGE,
    array $paths = []
  ) {
    self::$_instance = new self($language, $paths);
  }

  /** @var array Map of translations in current language. */
  protected $_translations = [];

  public function __construct() {}
}
