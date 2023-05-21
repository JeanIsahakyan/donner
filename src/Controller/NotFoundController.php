<?php
namespace Donner\Controller;

use Donner\Utils\HTTPCode;
use Donner\Result\ControllerResponse;

/**
 * Controller NotFoundController
 * @package Donner\Controller
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class NotFoundController extends AbstractController {

  public function resolve($params): ControllerResponse {
    return new ControllerResponse('Not found', HTTPCode::NOT_FOUND);
  }
}
