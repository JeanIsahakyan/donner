<?php
namespace Donner\Result;

use Donner\Utils\HTTPCode;

/**
 * Class ControllerException
 * @package Donner\Result
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class ControllerRedirectResponse extends ControllerResponse {
  private string $redirect_uri;

  public function __construct(string $redirect_uri, array $response = [], $http_code = HTTPCode::FOUND) {
    $this->redirect_uri = $redirect_uri;
    if (!$redirect_uri) {
      throw new ControllerException(ControllerException::INVALID_REQUEST, 'redirect_uri is invalid');
    }
    parent::__construct($response, $http_code);
  }

  public function getResponse(): string|array {
    $response = parent::getResponse();
    $redirect_uri = $this->buildRedirectURI($response);
    if (!$redirect_uri) {
      throw new ControllerException(ControllerException::INVALID_REQUEST, 'redirect_uri is invalid');
    }
    HTTPCode::set($this->getHTTPCode());
    header("Location: {$redirect_uri}");
    exit;
  }

  private function buildRedirectURI(array $response): ?string {
    $redirect_uri = $this->redirect_uri;
    if (!$redirect_uri) {
      return null;
    }
    if ($response) {
      $redirect_uri .= (str_contains($redirect_uri, '?') ? '&' : '?') . http_build_query($response);
    }
    return $redirect_uri;
  }
}
