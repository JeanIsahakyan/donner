<?php
namespace Donner\Request;

/**
 * Class RequestParams
 * @package Donner\Request
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class RequestParams {
  private array $params;
  private array $files;

  public function __construct(array $params, array $files) {
    $this->params = $params;
    $this->files = $files;
  }

  public static function create(array $params, array $files): self {
    return new self($params, $files);
  }

  public function exists(string $param): bool {
    return array_key_exists($param, $this->params);
  }

  public function fileExists(string $param): bool {
    return array_key_exists($param, $this->files);
  }

  public function get(string $param): RequestParam {
    return new RequestParam($param, $this->params[$param]);
  }

  public function getFile(string $param): RequestParamUploadFile {
    return new RequestParamUploadFile($param, $this->files[$param]);
  }

  public function getAll(): array {
    return array_merge($this->params, $this->files);
  }
}


