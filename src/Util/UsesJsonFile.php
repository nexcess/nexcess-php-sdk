<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use Nexcess\Sdk\Util\Util;

/** @var int Truncate existing json and overwrite with new data. */
const JSONFILE_OVERWRITE = 1;

/** @var int On collision, new data overwrites existing data. */
const JSONFILE_REPLACE = 2;

/** @var int Recursively merge new data with existing data. */
const JSONFILE_MERGE = 3;

/**
 * Methods for reading/writing json data to files.
 */
trait UsesJsonFile {

  /**
   * Validates that a filename ends in ".json".
   *
   * @param string $filepath The file name to check
   * @throws SdkException If check fails
   */
  private function _checkJsonFilename(string $filepath) {
    if (strpos($filepath, '.json') !== strlen($filepath) - 5) {
      throw new SdkException(
        SdkException::INVALID_JSON_FILENAME,
        ['filename' => $filename]
      );
    }
  }

  /**
   * Reads and decodes a .json file.
   *
   * @param string $filepath Path to json file to read
   * @return array The parsed json
   * @throws SdkException If file cannot be read, or parsing fails
   */
  protected function _readJsonFile(string $filepath) : array {
    $this->_checkJsonFilename($filepath);

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
   * Encodes data and writes it to a .json file.
   *
   * @param string $filepath Path to json file to write
   * @param array $data The data to write
   * @param int $flags Bitmask of JSONFILE_* constants
   * @return array The new json data
   * @throws SdkException If file cannot be written
   */
  protected function _writeJsonFile(
    string $filepath,
    array $data,
    int $flags = JSONFILE_MERGE
  ) : array {
    $this->_checkJsonFilename($filepath);

    $existing = file_exists($filepath) ?
      $this->_readJsonConfig($filepath) :
      [];

    $data = Util::extendRecursive($existing, $data);
    if (file_put_contents($filename, $data, LOCK_EX) === false) {
      throw new SdkException(
        SdkException::FILE_NOT_WRITABLE,
        ['filepath' => $filepath]
      );
    }

    return $data;
  }
}
