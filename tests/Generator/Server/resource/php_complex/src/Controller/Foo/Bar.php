<?php

namespace App\Controller;

class Bar extends ControllerAbstract
{
  #[Get]
  #[Path('/foo')]
  #[StatusCode(200)]
  public function get(): EntryCollection {
    // @TODO implement method
  }

  #[Post]
  #[Path('/foo')]
  #[StatusCode(201)]
  public function create(#[Body] EntryCreate $payload): EntryMessage {
    // @TODO implement method
  }

}
