<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use Nexcess\Sdk\ {
  Client,
  Exception\SdkException,
  Util\UsesJsonFile
};

/**
 * Utility class for looking up translations.
 * Defaults to supporting English.
 */
class Language {
  use UsesJsonFile;

  /** @var string Default language to translate if none specified. */
  const DEFAULT_LANGUAGE = 'en_US';

  /** @var Language Default instance. */
  protected static $_instance;

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
   * @return string Translation on success; unchanged key otherwise
   */
  public static function get(string $key) : string {
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
    string ...$paths
  ) {
    self::$_instance = new self($language, ...$paths);
  }

  /** @var array Map of translations in current language. */
  protected $_translations = [];

  /**
   * @param string $language Identifier for language to translate to
   * @param string[] $paths List of filepaths to find language files on
   */
  public function __construct(string $language, string ...$paths) {
    array_unshift($paths, Client::SDK_ROOT . '/src/config/lang');

    foreach ($paths as $path) {
      $this->addFile("/{$path}/{$language}.json");
    }
  }

  /**
   * Parses a json file and adds language stings to available translations.
   *
   * json files should contain an object with key:translation pairs.
   * Newer translations replace older ones.
   *
   * @param string $filepath Path to language json file to parse
   * @throws SdkException If file cannot be read, or parsing fails
   */
  public function addFile(string $filepath) {
    $translations = $this->_readJsonFile($filepath);
    array_walk(
      $translations,
      function ($v, $k) {
        if (! is_string($v)) {
          throw new SdkException(
            SdkException::INVALID_LANGUAGE_MAP,
            ['invalid' => $v]
          );
        }
      }
    );

    $this->_translations = $translations + $this->_translations;

    return $this;
  }

  /**
   * Gets a translation from the default instance.
   *
   * @param string $key Identifier for the desired translation
   * @return string Translation on success; unchanged key otherwise
   */
  public function getTranslation(string $key) : string {
    return $this->_translations[$key] ?? $key;
  }
}
