<?php
namespace Donner\Controller;

use Donner\Result\ControllerException;
use Donner\Result\ControllerResponse;

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
   * @param string[] $params
   * @throws ControllerException
   * @return ControllerResponse
   */
  public function resolve(array $params): ControllerResponse;
}
