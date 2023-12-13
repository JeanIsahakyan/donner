<?php
namespace Donner\Response;

use Donner\Response\Internal\AbstractResponseInternal;
use Donner\Utils\HTTPCode;

/**
 * Class MixedResponse
 * @package Donner\Response
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class MixedResponse extends AbstractResponseInternal {
  private mixed $response;

  public function __construct(mixed $response, HTTPCode $http_code = HTTPCode::OK) {
    $this->response  = $response;
    $this->setHTTPCode($http_code);
  }

  public function getResponse(): mixed {
    return $this->response;
  }
}
