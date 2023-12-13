<?php
namespace Donner\Response;


class ItemsResponse extends AbstractResponse {
  public array $items       = [];
  public ?int $total_count;
  public $previous_cursor;
  public $next_cursor;

  /**
   * @return int
   */
  public function getTotalCount(): int {
    return $this->total_count;
  }

  /**
   * @param int $total_count
   * @return self
   */
  public function setTotalCount(int $total_count): self {
    $this->total_count = $total_count;
    return $this;
  }

  /**
   * @return array
   */
  public function getItems(): array {
    return $this->items;
  }

  /**
   * @param array $items
   * @return self
   */
  public function setItems(array $items): self {
    $this->items = $items;
    return $this;
  }

  /**
   * @return null
   */
  public function getPreviousCursor() {
    return $this->previous_cursor;
  }

  /**
   * @param null $previous_cursor
   * @return self
   */
  public function setPreviousCursor($previous_cursor) {
    $this->previous_cursor = $previous_cursor;
    return $this;
  }

  /**
   * @return null
   */
  public function getNextCursor() {
    return $this->next_cursor;
  }

  /**
   * @param null $next_cursor
   * @return self
   */
  public function setNextCursor($next_cursor) {
    $this->next_cursor = $next_cursor;
    return $this;
  }
}
