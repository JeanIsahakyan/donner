<?php
namespace Donner\Response;

use Donner\Exception\DonnerException;
use Donner\Response\Internal\AbstractResponseInternal;
use Donner\Utils\HTTPCode;

/**
 * Class RedirectResponse
 * @package Donner\Response
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class RedirectResponse extends AbstractResponseInternal {
  private string $redirect_uri;
  private array $params = [];

  public function __construct(string $redirect_uri, array $params = [], $http_code = HTTPCode::FOUND) {
    $this->redirect_uri = $redirect_uri;
    if (!$redirect_uri) {
      throw new DonnerException(DonnerException::INVALID_REQUEST, 'redirect_uri is invalid');
    }
    $this->params = $params;
    $this->setHTTPCode($http_code);
  }

  public function getResponse(): string|array {
    $redirect_uri = $this->buildRedirectURI($this->params);
    if (!$redirect_uri) {
      throw new DonnerException(DonnerException::INVALID_REQUEST, 'redirect_uri is invalid');
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
