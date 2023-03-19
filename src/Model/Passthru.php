<?php

declare(strict_types = 1);

namespace PSX\Api\Model;

use PSX\Record\Record;
use PSX\Schema\Attribute\Description;

/**
 * @extends \PSX\Record\Record<mixed>
 */
#[Description('No schema information available')]
class Passthru extends Record
{
}
