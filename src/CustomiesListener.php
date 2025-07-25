<?php
declare(strict_types=1);

namespace customiesdevs\customies;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\item\CustomiesItemFactory;
use customiesdevs\customies\player\CustomPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ItemRegistryPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use ReflectionException;
use function array_merge;
use function count;

final class CustomiesListener implements Listener {
	/** @var ItemTypeEntry[] */
	private array $cachedItemTable = [];
	/** @var BlockPaletteEntry[] */
	private array $cachedBlockPalette = [];
	private Experiments $experiments;

	public function __construct() {
		$this->experiments = new Experiments([
			// "data_driven_items" is required for custom blocks to render in-game. With this disabled, they will be
			// shown as the UPDATE texture block.
			"data_driven_items" => true,
		], true);
	}

    /** @throws ReflectionException */
    public function onDataPacketSend(DataPacketSendEvent $event): void {
        $packets = $event->getPackets();
        foreach($packets as $packet){
            if ($packet instanceof StartGamePacket) {
                if (count($this->cachedItemTable) === 0) {
                    $this->cachedItemTable = CustomiesItemFactory::getInstance()->getItemTableEntries();
                    $this->cachedBlockPalette = CustomiesBlockFactory::getInstance()->getBlockPaletteEntries();
                }
                $packet->levelSettings->experiments = $this->experiments;
                $packet->blockPalette = $this->cachedBlockPalette;
            } elseif ($packet instanceof ResourcePackStackPacket) {
                $packet->experiments = $this->experiments;
            } elseif ($packet instanceof ItemRegistryPacket) {
                $entries = (new \ReflectionClass($packet))->getProperty("entries");
                $value = $entries->getValue($packet);
                // TODO: custom entries (depending on the player's settings)
                $entries->setValue($packet, array_merge($value, CustomiesItemFactory::getInstance()->getItemTableEntries()));
            }
        }
        $event->setPackets($packets);
    }

    public function onPlayerCreation(PlayerCreationEvent $event): void {
        $event->setPlayerClass(CustomPlayer::class);
    }
}
