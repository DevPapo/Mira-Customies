<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class FoodComponent implements ItemComponent {

	public function __construct(private bool $canAlwaysEat = false) {}

	public function getName(): string {
		return "minecraft:food";
	}

	public function getValue(): array {
		return [
			"can_always_eat" => $this->canAlwaysEat
		];
	}

	public function isProperty(): bool {
		return false;
	}
}