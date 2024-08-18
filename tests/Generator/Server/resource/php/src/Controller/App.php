<?php

namespace App\Controller;

class App extends ControllerAbstract
{
  #[Get]
  #[Path('/foo/:name/:type')]
  #[StatusCode(200)]
  public function get(#[Param] string $name, #[Param] string $type, #[Query] int $startIndex, #[Query] float $float, #[Query] bool $boolean, #[Query] \PSX\DateTime\LocalDate $date, #[Query] \PSX\DateTime\LocalDateTime $datetime, #[Query] Entry $args): EntryCollection {
    // @TODO implement method
  }

  #[Post]
  #[Path('/foo/:name/:type')]
  #[StatusCode(201)]
  public function create(#[Param] string $name, #[Param] string $type, #[Body] EntryCreate $payload): EntryMessage {
    // @TODO implement method
  }

  #[Put]
  #[Path('/foo/:name/:type')]
  #[StatusCode(200)]
  public function update(#[Param] string $name, #[Param] string $type, #[Body] \PSX\Record\Record $payload): \PSX\Record\Record {
    // @TODO implement method
  }

  #[Delete]
  #[Path('/foo/:name/:type')]
  #[StatusCode(204)]
  public function delete(#[Param] string $name, #[Param] string $type, #[Body] EntryDelete $payload): EntryMessage {
    // @TODO implement method
  }

  #[Patch]
  #[Path('/foo/:name/:type')]
  #[StatusCode(200)]
  public function patch(#[Param] string $name, #[Param] string $type, #[Body] array $payload): array {
    // @TODO implement method
  }

}
