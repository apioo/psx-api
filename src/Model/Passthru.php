<?php

declare(strict_types = 1);

namespace PSX\Api\Model;

use PSX\Record\Record;
use PSX\Schema\Attribute\Description;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @extends \PSX\Record\Record<mixed>
 */
#[Description('No schema information available')]
class Passthru extends Record
{
    private mixed $payload;
    private PropertyAccessor $accessor;

    public function __construct(iterable $properties = [], mixed $payload = null)
    {
        parent::__construct($properties);

        $this->payload  = $payload;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function get(string $key): mixed
    {
        return $this->accessor->getValue($this->payload, $key);
    }

    public function containsKey(string $key): bool
    {
        return $this->accessor->isReadable($this->payload, $key);
    }

    public static function fromPayload(mixed $payload): iterable
    {
        if ($payload instanceof \stdClass) {
            $properties = (array) $payload;
        } elseif (is_iterable($payload)) {
            $properties = $payload;
        } else {
            $properties = [];
        }

        return new self($properties, $payload);
    }
}
