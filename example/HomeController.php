<?php
use Donner\Controller\AbstractController;

class HomeController extends AbstractController {
  public const URI             = '/';

  public function resolve(): \Donner\Response\MixedResponse {
    return new \Donner\Response\MixedResponse('success');
  }
}
