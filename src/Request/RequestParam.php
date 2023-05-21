<?php
namespace Donner\Request;

use Donner\Utils\HTTPCode;
use Donner\Result\ControllerException;

/**
 * Class RequestParam
 * @package Donner\Request
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class RequestParam {
  private const ERROR_MESSAGE_REQUIRED = 'Param `{param}` is required';
  private const ERROR_MESSAGE_POSITIVE = 'Param `{param}` must be positive';
  private const ERROR_MESSAGE_EMPTY = 'Param `{param}` cant be empty';
  private const ERROR_MESSAGE_ENUM = 'Param `{param}` is incorrect, supported: {enum}';

  private ?string $value;
  private string $param;
  private mixed $default_value = null;
  private bool $is_required = false;


  public function __construct(string $param, ?string $value) {
    $this->param = $param;
    $this->value = $value;
  }

  private function replace(string $text, array $replace): string {
    $keys = array_map(fn($key) => '{'.$key.'}', array_keys($replace));
    $values = array_values($replace);
    return str_replace($keys, $values, $text);
  }

  public function defaultValue(mixed $value): self {
    $this->default_value = $value;
    return $this;
  }

  public function required(string $message = self::ERROR_MESSAGE_REQUIRED, int $error_code = ControllerException::INVALID_REQUEST): self {
    if ($this->value === null) {
      throw new ControllerException($error_code, $this->replace($message, [
        'param' => $this->param,
      ]), HTTPCode::BAD_REQUEST);
    }
    $this->is_required = true;
    return $this;
  }

  public function positive(string $message = self::ERROR_MESSAGE_POSITIVE, int $error_code = ControllerException::INVALID_REQUEST): int {
    $value = $this->int();
    if ($value >= 0) {
      return $value;
    }
    if ($this->is_required) {
      throw new ControllerException($error_code, $this->replace($message, [
        'param' => $this->param,
      ]), HTTPCode::BAD_REQUEST);
    }
    return (int)$this->default_value;
  }

  public function int(string $message = self::ERROR_MESSAGE_EMPTY, int $error_code = ControllerException::INVALID_REQUEST): int {
    $value = $this->value;
    if ($value || $value === '0') {
      return (int)$value;
    }
    if ($this->is_required) {
      throw new ControllerException($error_code, $this->replace($message, [
        'param' => $this->param,
      ]), HTTPCode::BAD_REQUEST);
    }
    return (int)$this->default_value;
  }

  public function string(string $message = self::ERROR_MESSAGE_EMPTY, int $error_code = ControllerException::INVALID_REQUEST): string {
    $value = $this->value;
    if ($value || $value === '0') {
      return $value;
    }
    if ($this->is_required) {
      throw new ControllerException($error_code, $this->replace($message, [
        'param' => $this->param,
      ]), HTTPCode::BAD_REQUEST);
    }
    return (string)$this->default_value;
  }

  public function bool(): bool {
    return (bool)filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
  }


  /**
   * @return int[]
   */
  public function positiveList(string $error_message = self::ERROR_MESSAGE_POSITIVE, int $error_code = ControllerException::INVALID_REQUEST): array {
    $values = explode(',', $this->value);
    $result = [];
    foreach ($values as $value) {
      $value = intval(trim($value));
      if ($value <= 0) {
        continue;
      }
      $result[] = $value;
    }
    if (!$result && $this->is_required) {
      throw new ControllerException($error_code, $this->replace($error_message, [
        'param' => $this->param,
      ]), HTTPCode::BAD_REQUEST);
    }
    return $result;
  }

  public function enum(array $enum, string $error_message = self::ERROR_MESSAGE_ENUM, int $error_code = ControllerException::INVALID_REQUEST): string {
    if (in_array($this->value, $enum, true)) {
      return (string)$this->value;
    }
    if ($this->is_required) {
      throw new ControllerException($error_code, $this->replace($error_message, [
        'param' => $this->param,
        'enum' => implode(', ', $enum),
      ]), HTTPCode::BAD_REQUEST);
    }
    return (string)$this->default_value;
  }
}
