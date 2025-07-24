<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class FoilComponent implements ItemComponent {


	public function __construct(private bool $foil = true) {}

	public function getName(): string {
		return "foil";
	}

	public function getValue(): bool {
		return $this->foil;
	}

	public function isProperty(): bool {
		return true;
	}
}