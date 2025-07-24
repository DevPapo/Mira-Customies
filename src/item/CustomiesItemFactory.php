<?php
declare(strict_types=1);

namespace customiesdevs\customies\item;

use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use function array_values;

final class CustomiesItemFactory {
	use SingletonTrait;

	/**
	 * @var ItemTypeEntry[]
	 */
	private array $itemTableEntries = [];

	/**
	 * Get a custom item from its identifier. An exception will be thrown if the item is not registered.
	 */
	public function get(string $identifier, int $amount = 1): Item {
		$item = StringToItemParser::getInstance()->parse($identifier);
		if($item === null) {
			throw new InvalidArgumentException("Custom item " . $identifier . " is not registered");
		}
		return $item->setCount($amount);
	}

	/**
	 * Returns custom item entries for the StartGamePacket itemTable property.
	 * @return ItemTypeEntry[]
	 */
	public function getItemTableEntries(): array {
		return array_values($this->itemTableEntries);
	}

	/**
	 * Registers the item to the item factory and assigns it an ID. It also updates the required mappings and stores the
	 * item components if present.
     *
     * @param ItemComponents $item
     * @param string $itemTypeName
	 * @phpstan-param class-string $className
	 */
	public function registerItem(Item $item, string $itemTypeName): void {
        $itemTypeId = $item->getTypeId();
        $this->registerCustomItemMapping($itemTypeName, $itemTypeId);

		GlobalItemDataHandlers::getDeserializer()->map($itemTypeName, fn() => clone $item);
		GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($itemTypeName));

		StringToItemParser::getInstance()->register($itemTypeName, fn() => clone $item);

        $this->itemTableEntries[$itemTypeName] = new ItemTypeEntry(
            $itemTypeName,
            $itemTypeId,
            true,
            1,
            new CacheableNbt($item->getComponents()
                ->setInt("id", $itemTypeId)
                ->setString("name", $itemTypeName)
            )
        );

        // creative inventory part
        // we get creative infos from components to maintain backward compatibility
        $categoryComponentInt = $item->getComponents()->getCompoundTag("components")->getCompoundTag("item_properties")->getInt("creative_category");
        $stringCategory = CreativeInventoryInfo::getStringCategory($categoryComponentInt);
        if (is_null($stringCategory)) { // should not appear (there is a fallback category)
            CreativeInventory::getInstance()->add($item);
            return;
        }

        $PMCategory = CreativeInventoryInfo::getPMCategory($stringCategory);
        if (is_null($PMCategory)) {
            CreativeInventory::getInstance()->add($item);
            return;
        }

        $groupComponent = $item->getComponents()->getCompoundTag("components")->getCompoundTag("item_properties")->getString("creative_group");
        $PMGroup = CreativeInventoryInfo::getPMGroup($stringCategory, $groupComponent);

        //var_dump("Item : " . $item->getName() . "\n" . "Category : " . $PMCategory->name . "\nGroup : " . (is_null($PMGroup) ? "Not in group" : $PMGroup->getName()));
        CreativeInventory::getInstance()->add($item, $PMCategory, /*$PMGroup*/); // TODO: there is no way to add items to existing groups
	}

	/**
	 * Registers a custom item ID to the required mappings in the global ItemTypeDictionary instance.
	 */
	private function registerCustomItemMapping(string $identifier, int $itemId): void {
		$dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
		$reflection = new ReflectionClass($dictionary);

		$intToString = $reflection->getProperty("intToStringIdMap");
		/** @var int[] $value */
		$value = $intToString->getValue($dictionary);
		$intToString->setValue($dictionary, $value + [$itemId => $identifier]);

		$stringToInt = $reflection->getProperty("stringToIntMap");
		/** @var int[] $value */
		$value = $stringToInt->getValue($dictionary);
		$stringToInt->setValue($dictionary, $value + [$identifier => $itemId]);
	}

	/**
	 * Registers the required mappings for the block to become an item that can be placed etc. It is assigned an ID that
	 * correlates to its block ID.
	 */
	public function registerBlockItem(string $identifier, Block $block): void {
		$itemId = $block->getIdInfo()->getBlockTypeId();
		$this->registerCustomItemMapping($identifier, $itemId);
		StringToItemParser::getInstance()->registerBlock($identifier, fn() => clone $block);
		$this->itemTableEntries[] = new ItemTypeEntry(
            $identifier,
            $itemId,
            false,
            2,
            new CacheableNbt(new CompoundTag())
        );

		$blockItemIdMap = BlockItemIdMap::getInstance();
		$reflection = new ReflectionClass($blockItemIdMap);

		$itemToBlockId = $reflection->getProperty("itemToBlockId");
		/** @var string[] $value */
		$value = $itemToBlockId->getValue($blockItemIdMap);
		$itemToBlockId->setValue($blockItemIdMap, $value + [$identifier => $identifier]);
	}
}
