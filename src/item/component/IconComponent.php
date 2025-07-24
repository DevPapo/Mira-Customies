<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

/**
 * Determines the icon to represent the item in the UI and elsewhere.
 *
 * From https://github.com/CustomiesDevs/Customies/blob/master/src/item/component/IconComponent.php
 * @param string $default_texture the texture name should same as the `resource_pack/textures/item_texture.json` `texture_data`
 * @param string $dyed_texture Default is set to `None`
 * @param string $trim_texture Default is set to `None`
 */
final class IconComponent implements ItemComponent {

    public function __construct(
        private string $default_texture,
        private string $dyed_texture = "",
        private string $trim_texture = ""
    ) {}

    public function getName(): string {
        return "minecraft:icon";
    }

    public function getValue(): array {
        return [
            "texture" => $this->default_texture,
            "textures" => [
                "default" => $this->default_texture,
                "dyed" => $this->dyed_texture,
                "icon_trim" => $this->trim_texture
            ]
        ];
    }

    public function isProperty(): bool {
        return true;
    }
}