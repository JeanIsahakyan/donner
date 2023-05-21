<?php
namespace Donner\Request;

use Donner\Utils\HTTPCode;
use Donner\Result\ControllerException;

/**
 * Class RequestParamUploadFile
 * @package Donner\Request
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class RequestParamUploadFile {
  private const ERROR_MESSAGE_REQUIRED = 'Param `{param}` is required';
  private const ERROR_MESSAGE_MAX_SIZE = 'Param `{param}` file size is incorrect';

  private const MAX_SIZE_DEFAULT = 10 * 1024 * 1024;

  private const FIELD_TMP_NAME = 'tmp_name';
  private const FIELD_NAME     = 'name';
  private const FIELD_SIZE     = 'size';

  private const REQUIRED_FIELDS = [
    self::FIELD_TMP_NAME,
    self::FIELD_NAME,
    self::FIELD_SIZE,
  ];

  private mixed $value;
  private string $param;

  public function __construct(string $param, ?string $value) {
    $this->param = $param;
    $this->value = $value;
  }

  private function replace(string $text, array $replace): string {
    $keys = array_map(fn($key) => '{'.$key.'}', array_keys($replace));
    $values = array_values($replace);
    return str_replace($keys, $values, $text);
  }

  public function required(string $message = self::ERROR_MESSAGE_REQUIRED, int $error_code = ControllerException::INVALID_REQUEST): self {
    $value = $this->value;
    if (!is_array($value)) {
      $value = [];
    }
    foreach (self::REQUIRED_FIELDS as $field) {
      if (array_key_exists($field, $value)) {
        continue;
      }
      throw new ControllerException($error_code, $this->replace($message, [
        'param' => $this->param,
      ]), HTTPCode::BAD_REQUEST);
    }
    return $this;
  }

  public function maxSize(int $max_size = self::MAX_SIZE_DEFAULT, string $message = self::ERROR_MESSAGE_MAX_SIZE, int $error_code = ControllerException::INVALID_REQUEST): self {
    $size = (int)$this->value[self::FIELD_SIZE];
    if ($size > 0 && $size <= $max_size) {
      return $this;
    }
    throw new ControllerException($error_code, $this->replace($message, [
      'param' => $this->param,
    ]), HTTPCode::BAD_REQUEST);
  }

  public function file(): ?UploadFile {
    return UploadFile::create($this->value);
  }
}
