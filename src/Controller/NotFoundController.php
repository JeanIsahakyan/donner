<?php
namespace Donner\Controller;

use Donner\Response\ResponseInterface;
use Donner\Utils\HTTPCode;
use Donner\Response\MixedResponse;

/**
 * Controller NotFoundController
 * @package Donner\Controller
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class NotFoundController extends AbstractController {

  public function resolve(): ResponseInterface {
    return new MixedResponse('Not found', HTTPCode::NOT_FOUND);
  }
}
