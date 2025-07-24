<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class RarityComponent implements ItemComponent {

	public const RARITY_COMMON = "common";
	public const RARITY_UNCOMMON = "uncommon";
	public const RARITY_RARE = "rare";
	public const RARITY_EPIC = "epic";


	public function __construct(private string $rarity) {}

	public function getName(): string {
		return "minecraft:rarity";
	}

	public function getValue(): array {
		return [
			"value" => $this->rarity
		];
	}

	public function isProperty(): bool {
		return false;
	}
}