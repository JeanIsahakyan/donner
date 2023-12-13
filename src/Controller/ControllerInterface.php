<?php
namespace Donner\Controller;

use Donner\Exception\DonnerException;
use Donner\Response\ResponseInterface;

/**
 * Interface ControllerInterface
 * @package Donner\Controller
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
interface ControllerInterface {
  public const URI             = '';
  public const METHOD_ALL      = 'ALL';
  public const METHOD_GET      = 'GET';
  public const METHOD_POST     = 'POST';
  public const ALLOWED_METHOD = self::METHOD_ALL;

  /**
   * @throws DonnerException
   * @return ResponseInterface
   */
  public function resolve(): ResponseInterface;
}
