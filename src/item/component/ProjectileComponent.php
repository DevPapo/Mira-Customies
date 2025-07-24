<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class ProjectileComponent implements ItemComponent {

	public function __construct(private string $projectileEntity) {}

	public function getName(): string {
		return "minecraft:projectile";
	}

	public function getValue(): array {
		return [
			"projectile_entity" => $this->projectileEntity
		];
	}

	public function isProperty(): bool {
		return false;
	}
}