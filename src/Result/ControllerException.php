<?php
namespace Donner\Result;

use Donner\Utils\HTTPCode;
use Exception;

/**
 * Class ControllerException
 * @package Donner\Result
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class ControllerException extends Exception {
  public const INVALID_REQUEST = 0;

  private HTTPCode $http_code;

  public function __construct(int $code, string $message, HTTPCode $http_code = HTTPCode::BAD_REQUEST) {
    parent::__construct($message, $code);
    $this->http_code = $http_code;
  }

  public function getHTTPCode(): HTTPCode {
    return $this->http_code;
  }
}
