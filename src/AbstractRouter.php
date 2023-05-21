<?php
namespace Donner;

use Donner\Utils\HTTPCode;
use Donner\Controller\AbstractController;
use Donner\Controller\ControllerInterface;
use Donner\Controller\NotFoundController;
use Donner\Result\ControllerException;
use Donner\Result\ControllerResponse;

/**
 * Class AbstractRouter
 * @package Donner
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
abstract class AbstractRouter implements RouterInterface {
  /**
   * @var AbstractController[][] $controllers
   */
  private array $controllers = [];
  private ?AbstractController $not_found_controller = null;

  public final function setNotFoundController(AbstractController $controller): static {
    $this->not_found_controller = $controller;
    return $this;
  }

  private function getNotFoundController(): AbstractController {
    if ($this->not_found_controller !== null) {
      return $this->not_found_controller;
    }
    return new NotFoundController();
  }

  public final function addController(AbstractController $controller): static {
    $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $controller::URI);
    $this->controllers[$controller::ALLOWED_METHOD][$pattern] = $controller;
    return $this;
  }

  public final static function create(): static {
    $router = new static();
    $router->checkOptions();
    return $router;
  }

  private function getUri(): string {
    $uri = $_SERVER['REQUEST_URI'];
    $str = strpos($uri, '?');
    if (!$str) {
      return $uri;
    }
    return substr($uri, 0, $str);
  }

  private function getMethod(): string {
    return $_SERVER['REQUEST_METHOD'] ?? ControllerInterface::METHOD_GET;
  }

  /**
   * @param AbstractController[] $controllers
   *
   * @return ControllerResponse|null
   */
  private function tryInvokeController(array $controllers): ?ControllerResponse {
    $uri    = $this->getUri();
    foreach ($controllers as $pattern => $controller) {
      if (!preg_match_all('#^' .$pattern. '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
        continue;
      }
      $matches = array_slice($matches, 1);
      $params = array_map(function ($match, $index) use ($matches) {
        if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
          return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
        }
        return isset($match[0][0]) ? trim($match[0][0], '/') : null;
      }, $matches, array_keys($matches));
      return $controller->resolve($params);
    }
    return null;
  }

  /**
   * @throws ControllerException
   */
  protected final function tryRun(): ControllerResponse {
    $method = $this->getMethod();
    if (!array_key_exists($method, $this->controllers)) {
      $method = ControllerInterface::METHOD_ALL;
    }
    $response = $this->tryInvokeController($this->controllers[$method]);
    if ($response === null) {
      $controllers = $this->controllers[ControllerInterface::METHOD_ALL];
      if ($controllers) {
        $response = $this->tryInvokeController($controllers);
      }
    }
    if ($response !== null) {
      return $response;
    }
    return $this->getNotFoundController()->resolve([]);
  }

  private function checkOptions(): void {
    if ($this->getMethod() === 'OPTIONS') {
      HTTPCode::set(HTTPCode::OK);
      echo 'ok';
      exit;
    }
  }

  protected final function renderResponse(array|string $response, HTTPCode $http_code): void {
    HTTPCode::set($http_code);
    if (is_array($response)) {
      header('Pragma: no-cache');
      header('Cache-Control: no-cache');
      header('Content-Type: application/json');
      die(json_encode($response));
    }
    header('Content-type: text/html; charset=utf8');
    die($response);
  }
}
