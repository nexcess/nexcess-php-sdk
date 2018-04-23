<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use Nexcess\Sdk\ {
  Client,
  Exception\SdkException,
  Util\Util
};

/**
 * Utility class for looking up translations.
 * Defaults to supporting English.
 */
class Language {

  /** @var string English, United States. */
  const EN_US = 'en_US';

  /** @var string[] List of supported languages, indexed by locale. */
  const SUPPORTED_LANGUAGES = [
    self::EN_US => 'English (United States)'
  ];

  /** @var string Default language to translate if none specified. */
  const DEFAULT_LANGUAGE = self::EN_US;

  /** @var Language Default instance. */
  protected static $_instance;

  /**
   * Makes the default instance available globally.
   *
   * @return Language
   */
  public static function getInstance() : Language {
    if (! self::$_instance) {
      self::init();
    }

    return self::$_instance;
  }

  /**
   * Gets a list of natively supported languages, indexed by locale string.
   *
   * @return string[]
   */
  public static function getSupportedLanguages() : array {
    return self::SUPPORTED_LANGUAGES;
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
   * @param string[] $paths List of paths to find language files in
   * @throws SdkException If loading a language file fails
   */
  public static function init(
    string $language = self::DEFAULT_LANGUAGE,
    string ...$paths
  ) {
    self::$_instance = new self($language, ...$paths);
  }

  /** @var string Current language. */
  protected $_language = '';

  /** @var array Map of translations in current language. */
  protected $_translations = [];

  /**
   * @param string $language Identifier for language to translate to
   * @param string[] $paths List of filepaths to find language files on
   */
  public function __construct(string $language, string ...$paths) {
    $this->_language = $language;

    array_unshift($paths, __DIR__ . '/lang');
    foreach ($paths as $path) {
      $this->addFile("{$path}/{$this->_language}.json");
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
    $translations = Util::readJsonFile($filepath);
    $this->_translations = $translations + $this->_translations;

    return $this;
  }

  /**
   * Gets the language of these translations.
   *
   * @return string Locale string
   */
  public function getLanguage() : string {
    return $this->_language;
  }

  /**
   * Gets a translation.
   *
   * @param string $key Identifier for the desired translation
   * @return string Translation on success; unchanged key otherwise
   */
  public function getTranslation(string $key) : string {
    return $this->_translations[$key] ?? $key;
  }
}
