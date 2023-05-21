<?php
use Donner\Controller\AbstractController;

class HomeController extends AbstractController {
  public const URI             = '/';

  public function resolve($params): \Donner\Result\ControllerResponse {
    return new \Donner\Result\ControllerResponse('success');
  }
}
