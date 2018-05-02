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
    if (self::$_instance) {
      return;
    }

    self::$_instance = new self($language, ...$paths);
  }

  /** @var string Current language. */
  protected $_language = '';

  /** @var string[] List of translation file paths. */
  protected $_paths = [__DIR__ . '/lang'];

  /** @var array Map of translations in current language. */
  protected $_translations = [];

  /**
   * @param string $language Identifier for language to translate to
   * @param string[] $paths List of filepaths to find language files on
   */
  public function __construct(string $language, string ...$paths) {
    $this->setLanguage($language);

    if (! empty($paths)) {
      $this->addPaths(...$paths);
    }
  }

  /**
   * Adds filesystem path(s) to look for .json translation files.
   *
   * Paths must not include a trailing slash.
   *
   * Each path is expected to be a directory containing .json file(s),
   * named with their BCP-47 code, with dashes replaced by underscores.
   * @example "en_US.json"
   * @see https://tools.ietf.org/html/bcp47
   *
   * @param string[] $paths Filepaths to add
   * @return Language $this
   */
  public function addPaths(string ...$paths) : Language {
    array_push($this->_paths, ...$paths);

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
   * @return string Translation on success; untranslated key otherwise
   */
  public function getTranslation(string $key) : string {
    if (empty($this->_translations)) {
      $this->_loadTranslations();
    }

    return Util::dig($this->_translations, $key) ?? $key;
  }

  /**
   * Sets the language to get translations in.
   *
   * Note, this does NOT check whether any translations
   * are actually available in the given language.
   *
   * @param string $language Desired language
   * @return Language $this
   */
  public function setLanguage(string $language) : Language {
    if ($language !== $this->_language) {
      $this->_translations = [];
      $this->_language = $language;
    }

    return $this;
  }

  /**
   * Loads translations from .json files on configured file paths.
   */
  protected function _loadTranslations() {
    foreach ($this->_paths as $path) {
      $file = "{$path}/{$this->_language}.json";
      if (! is_readable($file)) {
        continue;
      }

      $this->_translations = Util::extendRecursive(
        $this->_translations,
        Util::readJsonFile($file)
      );
    }
  }
}
