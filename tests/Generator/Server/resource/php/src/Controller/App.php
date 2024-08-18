<?php

namespace App\Controller;

class App extends ControllerAbstract
{
  #[Get]
  #[Path('/foo/:name/:type')]
  #[StatusCode(200)]
  public function get(#[Param('name')] string $name, #[Param('type')] string $type, #[Query('startIndex')] int $startIndex, #[Query('float')] float $float, #[Query('boolean')] bool $boolean, #[Query('date')] \PSX\DateTime\LocalDate $date, #[Query('datetime')] \PSX\DateTime\LocalDateTime $datetime, #[Query('args')] Entry $args): EntryCollection {
    // @TODO implement method
  }

  #[Post]
  #[Path('/foo/:name/:type')]
  #[StatusCode(201)]
  public function create(#[Param('name')] string $name, #[Param('type')] string $type, #[Body] EntryCreate $payload): EntryMessage {
    // @TODO implement method
  }

  #[Put]
  #[Path('/foo/:name/:type')]
  #[StatusCode(200)]
  public function update(#[Param('name')] string $name, #[Param('type')] string $type, #[Body] \PSX\Record\Record $payload): \PSX\Record\Record {
    // @TODO implement method
  }

  #[Delete]
  #[Path('/foo/:name/:type')]
  #[StatusCode(204)]
  public function delete(#[Param('name')] string $name, #[Param('type')] string $type, #[Body] EntryDelete $payload): EntryMessage {
    // @TODO implement method
  }

  #[Patch]
  #[Path('/foo/:name/:type')]
  #[StatusCode(200)]
  public function patch(#[Param('name')] string $name, #[Param('type')] string $type, #[Body] array $payload): array {
    // @TODO implement method
  }

}
