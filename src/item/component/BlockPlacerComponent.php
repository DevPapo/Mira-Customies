<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

final class BlockPlacerComponent implements ItemComponent {

    public function __construct(
        private string $blockIdentifier,
        private bool $useBlockDescription = false
    ) {}

    public function getName(): string {
        return "minecraft:block_placer";
    }

    public function getValue(): array {
        return [
            "block" => $this->blockIdentifier,
            "use_block_description" => $this->useBlockDescription
        ];
    }

    public function isProperty(): bool {
        return false;
    }
}
