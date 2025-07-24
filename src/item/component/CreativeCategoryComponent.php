<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

use customiesdevs\customies\item\CreativeInventoryInfo;

final class CreativeCategoryComponent implements ItemComponent {

	public function __construct(private CreativeInventoryInfo $creativeInfo) {}

	public function getName(): string {
		return "creative_category";
	}

	public function getValue(): int {
		return $this->creativeInfo->getNumericCategory();
	}

    public function getStringValue(): string {
        return $this->creativeInfo->getCategory();
    }

	public function isProperty(): bool {
		return true;
	}
}