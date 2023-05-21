<?php
namespace Donner\Result;

use Donner\Utils\HTTPCode;

/**
 * Class ControllerResponse
 * @package Donner\Result
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class ControllerResponse {
  private HTTPCode $http_code;
  private array|string $response;

  public function __construct(array|string $response, HTTPCode $http_code = HTTPCode::OK) {
    $this->response  = $response;
    $this->http_code = $http_code;
  }

  public function getResponse(): string|array {
    return $this->response;
  }

  public function getHTTPCode(): HTTPCode {
    return $this->http_code;
  }
}
