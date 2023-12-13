<?php
namespace Donner\Response;

use Donner\Response\Internal\AbstractResponseInternal;
use Donner\Utils\HTTPCode;
use ReflectionClass;

abstract class AbstractResponse extends AbstractResponseInternal {
  private ReflectionClass $_reflection;

  public function __construct(HTTPCode $http_code = HTTPCode::OK) {
    $this->_reflection = new ReflectionClass($this);
    $this->setHTTPCode($http_code);
  }

  public static function create(HTTPCode $http_code = HTTPCode::OK): static {
    return new static($http_code);
  }

  private function arrayToResponse(array $rows): array {
    $result = [];
    foreach ($rows as $key => $value) {
      if (is_array($value)) {
        $value = $this->arrayToResponse($value);
      }
      if ($value instanceof AbstractResponse) {
        $value = $value->getResponse();
      }
      $result[$key] = $value;
    }
    return $result;
  }

  public function getResponse(): array {
    $row = [];
    foreach ($this->_reflection->getProperties() as $property) {
      $name = $property->getName();
      if (str_starts_with($name, '_')) {
        continue;
      }
      $value = $property->getValue($this);
      if ($value === null) {
        continue;
      }
      $type = $property->getType();
      if (!$type->isBuiltin() && $value instanceof AbstractResponse) {
        $value = $value->getResponse();
      }
      if (is_array($value)) {
        $value = $this->arrayToResponse($value);
      }
      $row[$name] = $value;
    }
    return $row;
  }
}
