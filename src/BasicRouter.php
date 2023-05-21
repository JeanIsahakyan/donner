<?php
namespace Donner;

use Donner\Result\ControllerException;

/**
 * Class BasicRouter
 * @package Donner
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class BasicRouter extends AbstractRouter {

  public function run(): void {
    try {
      $result = $this->tryRun();
      $response = $result->getResponse();
      $http_code = $result->getHTTPCode();
    } catch (ControllerException $exception) {
      $response = $exception->getMessage();
      $http_code = $exception->getHTTPCode();
    }
    $this->renderResponse($response, $http_code);
  }
}
