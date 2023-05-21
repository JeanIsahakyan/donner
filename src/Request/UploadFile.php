<?php
namespace Donner\Request;

/**
 * Class UploadFile
 * @package Donner\Request
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
class UploadFile {
  private ?string $path          = null;
  private ?string $mime          = null;
  private ?string $name          = null;
  private ?int    $size          = null;
  private ?string $ext           = null;

  private const MIME_IMAGE       = 'image';

  public function __destruct() {
    $this->clear();
  }

  public function getPath(): ?string {
    return $this->path;
  }

  public function setPath(?string $path): self {
    $this->path = $path;
    return $this;
  }

  public function getMime(): ?string {
    return $this->mime;
  }

  public function setMime(?string $mime): self {
    $this->mime = $mime;
    return $this;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(?string $name): self {
    $this->name = $name;
    return $this;
  }


  public function getSize(): ?int {
    return $this->size;
  }


  public function setSize(?int $size): self {
    $this->size = $size;
    return $this;
  }

  public function getExt(): ?string {
    return $this->ext;
  }

  public function setExt(?string $ext): self {
    $this->ext = $ext;
    return $this;
  }

  private function checkMime(string $mime): bool {
    return str_starts_with($this->getMime(), $mime);
  }

  public function isImage(): bool {
    return $this->checkMime(self::MIME_IMAGE);
  }

  public function clear(): void {
    if (!file_exists($this->path)) {
      return;
    }
    @unlink($this->path);
  }

  private function processTempFile(): ?self {
    $path = $this->getPath();
    if (!$path) {
      return null;
    }
    if (!is_uploaded_file($path)) {
      return null;
    }
    $mime = mime_content_type($this->getPath());
    $size = filesize($this->getPath());
    if (!$mime || !$size) {
      return null;
    }
    return $this
      ->setSize($size)
      ->setMime($mime);
  }

  public static function create(array $input): ?self {
    $file = new self();
    $name = $input['name'];
    $path = $input['tmp_name'];
    if (!$name || !$path) {
      return null;
    }
    return $file->setName(basename($name))
      ->setPath($path)
      ->setExt(strtolower(pathinfo($name, PATHINFO_EXTENSION)))
      ->processTempFile();
  }
}


