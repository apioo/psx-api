<?php

namespace App\Controller;

class Bar extends ControllerAbstract
{
  #[Get]
  #[Path('/bar/:foo')]
  #[StatusCode(200)]
  public function find(#[Param] string $foo): EntryCollection {
    // @TODO implement method
  }

  #[Post]
  #[Path('/bar/:foo')]
  #[StatusCode(201)]
  public function put(#[Body] EntryCreate $payload): EntryMessage {
    // @TODO implement method
  }

}
