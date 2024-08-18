<?php
/**
 * app automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace App\Controller;

use App\Model\Entry;
use App\Model\EntryCollection;
use App\Model\EntryCreate;
use App\Model\EntryMessage;
use App\Model\EntryUpdate;
use App\Model\EntryDelete;
use App\Model\EntryPatch;
use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Delete;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Patch;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\Put;
use PSX\Api\Attribute\Query;
use PSX\Api\Attribute\StatusCode;
use PSX\Framework\Controller\ControllerAbstract;

class App extends ControllerAbstract
{
    #[Get]
    #[Path('/foo/:name/:type')]
    #[StatusCode(200)]
    public function get(#[Param] string $name, #[Param] string $type, #[Query] int $startIndex, #[Query] float $float, #[Query] bool $boolean, #[Query] \PSX\DateTime\LocalDate $date, #[Query] \PSX\DateTime\LocalDateTime $datetime, #[Query] Entry $args): EntryCollection
    {
        // @TODO implement method
    }

    #[Post]
    #[Path('/foo/:name/:type')]
    #[StatusCode(201)]
    public function create(#[Param] string $name, #[Param] string $type, #[Body] EntryCreate $payload): EntryMessage
    {
        // @TODO implement method
    }

    #[Put]
    #[Path('/foo/:name/:type')]
    #[StatusCode(200)]
    public function update(#[Param] string $name, #[Param] string $type, #[Body] \PSX\Record\Record $payload): \PSX\Record\Record
    {
        // @TODO implement method
    }

    #[Delete]
    #[Path('/foo/:name/:type')]
    #[StatusCode(204)]
    public function delete(#[Param] string $name, #[Param] string $type, #[Body] EntryDelete $payload): EntryMessage
    {
        // @TODO implement method
    }

    #[Patch]
    #[Path('/foo/:name/:type')]
    #[StatusCode(200)]
    public function patch(#[Param] string $name, #[Param] string $type, #[Body] array $payload): array
    {
        // @TODO implement method
    }

}
