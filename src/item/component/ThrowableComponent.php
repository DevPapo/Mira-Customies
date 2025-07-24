<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class ThrowableComponent implements ItemComponent {

	public function __construct(private bool $doSwingAnimation) {}

	public function getName(): string {
		return "minecraft:throwable";
	}

	public function getValue(): array {
		return [
			"do_swing_animation" => $this->doSwingAnimation
		];
	}

	public function isProperty(): bool {
		return false;
	}
}