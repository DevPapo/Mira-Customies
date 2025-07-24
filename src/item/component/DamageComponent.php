<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class DamageComponent implements ItemComponent {

	public function __construct(private int $damage) {}

	public function getName(): string {
		return "damage";
	}

	public function getValue(): int {
		return $this->damage;
	}

	public function isProperty(): bool {
		return true;
	}
}