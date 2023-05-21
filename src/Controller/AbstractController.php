<?php
namespace Donner\Controller;

use Donner\Request\RequestParams;
use Donner\Result\ControllerException;
use Donner\Result\ControllerResponse;

/**
 * Class AbstractController
 * @package Donner\Controller
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
abstract class AbstractController implements ControllerInterface {
  protected RequestParams $request;

  private function initRequest(): void {
    switch (self::ALLOWED_METHOD) {
      case self::METHOD_ALL: {
        $this->request = RequestParams::create(array_merge($_GET, $_POST), $_FILES);
        break;
      }
      case self::METHOD_POST: {
        $this->request = RequestParams::create($_POST, $_FILES);
        break;
      }
      case self::METHOD_GET: {
        $this->request = RequestParams::create($_GET, $_FILES);
        break;
      }
    }
  }

  public function __construct() {
    $this->initRequest();
  }

  public function resolve($params): ControllerResponse {
    throw new ControllerException(ControllerException::INVALID_REQUEST, 'Unknown request');
  }
}
