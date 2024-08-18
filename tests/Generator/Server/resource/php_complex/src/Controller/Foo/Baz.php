<?php

namespace App\Controller;

class Baz extends ControllerAbstract
{
  #[Get]
  #[Path('/bar/$year<[0-9]+>')]
  #[StatusCode(200)]
  public function get(#[Param] string $year): EntryCollection {
    // @TODO implement method
  }

  #[Post]
  #[Path('/bar/$year<[0-9]+>')]
  #[StatusCode(201)]
  public function create(#[Body] EntryCreate $payload): EntryMessage {
    // @TODO implement method
  }

}
