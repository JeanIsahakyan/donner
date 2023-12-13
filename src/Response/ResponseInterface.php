<?php
namespace Donner\Response;

use Donner\Utils\HTTPCode;

/**
 * Class ResponseInterface
 * @package Donner\Response
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
interface ResponseInterface {
  public function getResponse(): mixed;

  public function getHTTPCode(): HTTPCode;
}
