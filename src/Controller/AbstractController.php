<?php
namespace Donner\Controller;

use Donner\Exception\DonnerException;
use Donner\Request\RequestParams;
use Donner\Response\ResponseInterface;

/**
 * Class AbstractController
 * @package Donner\Controller
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
abstract class AbstractController implements ControllerInterface {
  protected RequestParams $request;
  /**
   * @var string[] $params
   */
  protected array $params = [];

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

  /**
   * @param string[] $params
   */
  final public function setParams(array $params): static {
    $this->params = $params;
    return $this;
  }

  public function resolve(): ResponseInterface {
    throw new DonnerException(DonnerException::INVALID_REQUEST, 'Unknown request');
  }
}
