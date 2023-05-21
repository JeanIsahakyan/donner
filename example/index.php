<?php
use Donner\BasicRouter;

require_once 'HomeController.php';

BasicRouter::create()
  ->addController(new HomeController())
  ->run();
