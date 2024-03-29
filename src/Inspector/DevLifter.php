<?php

namespace PSX\Api\Inspector;

use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;

/**
 * The dev lifter increases the minor version in case the operation and definition count has changed, otherwise we
 * always increase the patch version independent whether the spec contains breaking changes. The dev lifter will never
 * increase the major version
 */
class DevLifter
{
    private const MINOR_PERCENTAGE_CHANGE = 24;

    public function elevate(string $version, SpecificationInterface $left, ?SpecificationInterface $right = null): string
    {
        $parts = explode('.', $version);
        $major = (int) ($parts[0] ?? 0);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        if ($this->getPercentageChange($left, $right) > self::MINOR_PERCENTAGE_CHANGE) {
            $minor++;
            $patch = 0;
        } else {
            $patch++;
        }

        return implode('.', [$major, $minor, $patch]);
    }

    private function getPercentageChange(SpecificationInterface $left, ?SpecificationInterface $right = null): int
    {
        if (!$right instanceof SpecificationInterface) {
            return 0;
        }

        $leftCount = count($left->getOperations()->getAll()) + count($left->getDefinitions()->getTypes(DefinitionsInterface::SELF_NAMESPACE));
        $rightCount = count($right->getOperations()->getAll()) + count($right->getDefinitions()->getTypes(DefinitionsInterface::SELF_NAMESPACE));

        $changes = abs($rightCount - $leftCount);
        $percentage = ($changes * 100) / $rightCount;

        return $percentage;
    }
}
