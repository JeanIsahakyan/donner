<?php
namespace Donner\Response;

class SuccessResponse extends AbstractResponse {
  public bool $success = true;

  public function isSuccess(): bool {
    return $this->success;
  }

  public function setSuccess(bool $success): SuccessResponse {
    $this->success = $success;
    return $this;
  }
}
