<?php
namespace Donner\Response\Internal;

use Donner\Response\ResponseInterface;
use Donner\Utils\HTTPCode;

/**
 * Class AbstractResponseInternal
 * @package Donner\Response
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class AbstractResponseInternal implements ResponseInterface {

  private HTTPCode $http_code;

  public function getResponse(): mixed {
    return [];
  }

  public final function getHTTPCode(): HTTPCode {
    return $this->http_code;
  }

  public final function setHTTPCode(HTTPCode $http_code): static {
    $this->http_code = $http_code;
    return $this;
  }
}
