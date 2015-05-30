<?php

namespace mcg76\game\spleef;

use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;

/**
 * MCG76 Spleef Game Kit
 *
 * Copyright (C) 2015 minecraftgenius76
 *
 * @author MCG76
 * @link http://www.youtube.com/user/minecraftgenius76
 *      
 */
class SpleefGameKit extends MiniGameBase {
	const DIR_KITS = "kits/";
	//kit types
	const KIT_DIAMOND_ARMOR = "diamond_kit";
	const KIT_GOLD_ARMOR = "gold_kit";
	const KIT_IRON_ARMOR = "iron_kit";
	const KIT_LEATHER_ARMOR = "leather_kit";
	const KIT_CHAIN_ARMOR = "chain_kit";
	const KIT_NO_ARMOR = "no_armor_kit";
	const KIT_UNKNOWN = "Unknown";
	
	private $kits = [];
	
	public function __construct(SpleefPlugIn $plugin) {
		parent::__construct ( $plugin );
		$this->init ();
	}
	private function init() {
		@mkdir ( $this->getPlugin()->getDataFolder () . self::DIR_KITS, 0777, true );
		$this->getKit ( self::KIT_GOLD_ARMOR );
		$this->getKit ( self::KIT_IRON_ARMOR );
		$this->getKit ( self::KIT_DIAMOND_ARMOR );
		$this->getKit ( self::KIT_LEATHER_ARMOR );
		$this->getKit ( self::KIT_CHAIN_ARMOR );
		$this->getKit ( self::KIT_NO_ARMOR );
		
		$this->kits=array(
				self::KIT_GOLD_ARMOR=>self::KIT_GOLD_ARMOR,
				self::KIT_IRON_ARMOR=>self::KIT_IRON_ARMOR,
				self::KIT_DIAMOND_ARMOR=>self::KIT_DIAMOND_ARMOR,
				self::KIT_LEATHER_ARMOR=>self::KIT_LEATHER_ARMOR,
				self::KIT_NO_ARMOR=>self::KIT_NO_ARMOR,
				self::KIT_CHAIN_ARMOR=>self::KIT_CHAIN_ARMOR
		);
	}
	
	/**
	 * generate a random kits for player
	 * 
	 * @param Player $p
	 */
	public function putOnRandomGameKit(Player $p) {
		$kittype = array_rand ($this->kits );
		$selectedKit = $this->kits [$kittype];		
		$this->putOnGameKit($p, $selectedKit);
	}
	
   /**
    * wear game kits
    * 
    * @param Player $p
   * @param unknown $kitType
   */
	public function putOnGameKit(Player $p, $kitType) {
		switch ($kitType) {
			case self::KIT_GOLD_ARMOR :
				$this->loadKit ( self::KIT_GOLD_ARMOR, $p );
				break;
			case self::KIT_IRON_ARMOR :
				$this->loadKit ( self::KIT_IRON_ARMOR, $p );
				break;
			case self::KIT_DIAMOND_ARMOR :
				$this->loadKit ( self::KIT_DIAMOND_ARMOR, $p );
				break;
			case self::KIT_LEATHER_ARMOR :
				$this->loadKit ( self::KIT_LEATHER_ARMOR, $p );
				break;
			case self::KIT_CHAIN_ARMOR :
				$this->loadKit ( self::KIT_CHAIN_ARMOR, $p );
				break;
			case self::KIT_NO_ARMOR :
				$this->loadKit ( self::KIT_NO_ARMOR, $p );
				break;								
			default :
				// no armors kit
				$this->loadKit ( self::KIT_UNKNOWN, $p );
		}
	}
	
	/**
	 * Get Game Kit By Name
	 * 
	 * @param unknown $kitName
	 * @return \pocketmine\utils\Config
	 */
	public function getKit($kitName) {
		if (! (file_exists ( $this->getPlugin()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml" ))) {			
			if ($kitName == self::KIT_GOLD_ARMOR) {				
				return new Config ( $this->plugin->getDataFolder () . self::DIR_KITS . strtolower ( self::KIT_GOLD_ARMOR ) . ".yml", Config::YAML, array (
						"kitName" => self::KIT_GOLD_ARMOR,
						"isDefault" => false,
						"cost" => 0,
						"health" => 20,
						"armors" => array (
								"helmet" => array (
										Item::GOLD_HELMET,
										"0",
										"1" 
								),
								"chestplate" => array (
										Item::GOLD_CHESTPLATE,
										"0",
										"1" 
								),
								"leggings" => array (
										Item::GOLD_LEGGINGS,
										"0",
										"1" 
								),
								"boots" => array (
										Item::GOLD_BOOTS,
										"0",
										"1" 
								) 
						),
						"weapons" => array (
								Item::IRON_SHOVEL => array (
										Item::IRON_SHOVEL,
										"0",
										"1" 
								),
								item::SNOWBALL => array (
										item::SNOWBALL,
										"0",
										"64" 
								) 
						),
						"foods" => array (
								Item::APPLE => array (
										Item::APPLE,
										"0",
										"2" 
								),
								Item::CARROT => array (
										Item::CARROT,
										"0",
										"2" 
								) 
						) 
				) );
			} elseif ($kitName == self::KIT_IRON_ARMOR) {
				return new Config ( $this->getPlugin()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml", Config::YAML, array (
						"kitName" => self::KIT_IRON_ARMOR,
						"isDefault" => false,
						"cost" => 0,
						"health" => 20,
						"armors" => array (
								"helmet" => array (
										Item::IRON_HELMET,
										"0",
										"1" 
								),
								"chestplate" => array (
										Item::IRON_CHESTPLATE,
										"0",
										"1" 
								),
								"leggings" => array (
										Item::IRON_LEGGINGS,
										"0",
										"1" 
								),
								"boots" => array (
										Item::IRON_BOOTS,
										"0",
										"1" 
								) 
						),
						"weapons" => array (
								Item::IRON_SHOVEL => array (
										Item::IRON_SHOVEL,
										"0",
										"1" 
								),
								item::SNOWBALL => array (
										item::SNOWBALL,
										"0",
										"64" 
								) 
						),
						"foods" => array (
								item::COOKED_BEEF => array (
										item::COOKED_BEEF,
										"0",
										"2" 
								),
								Item::COOKED_CHICKEN => array (
										Item::COOKED_CHICKEN,
										"0",
										"2" 
								) 
						) 
				) );
			} elseif ($kitName == self::KIT_CHAIN_ARMOR) {
				return new Config ( $this->getPlugin()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml", Config::YAML, array (
						"kitName" => self::KIT_CHAIN_ARMOR,
						"isDefault" => false,
						"cost" => 0,
						"health" => 20,
						"armors" => array (
								"helmet" => array (
										Item::CHAIN_HELMET,
										"0",
										"1" 
								),
								"chestplate" => array (
										Item::CHAIN_CHESTPLATE,
										"0",
										"1" 
								),
								"leggings" => array (
										Item::CHAIN_LEGGINGS,
										"0",
										"1" 
								),
								"boots" => array (
										Item::CHAIN_BOOTS,
										"0",
										"1" 
								) 
						),
						"weapons" => array (
								Item::IRON_SHOVEL => array (
										Item::IRON_SHOVEL,
										"0",
										"1" 
								),
								item::SNOWBALL => array (
										item::SNOWBALL,
										"0",
										"64" 
								) 
						),
						"foods" => array (
								item::COOKED_PORKCHOP => array (
										item::COOKED_PORKCHOP,
										"0",
										"2" 
								),
								Item::COOKED_CHICKEN => array (
										Item::COOKED_CHICKEN,
										"0",
										"2" 
								) 
						) 
				) );
			} elseif ($kitName == self::KIT_DIAMOND_ARMOR) {
				return new Config ( $this->getPlugin()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml", Config::YAML, array (
						"kitName" => self::KIT_DIAMOND_ARMOR,
						"isDefault" => false,
						"cost" => 0,
						"health" => 20,
						"armors" => array (
								"helmet" => array (
										Item::DIAMOND_HELMET,
										"0",
										"1" 
								),
								"chestplate" => array (
										Item::DIAMOND_CHESTPLATE,
										"0",
										"1" 
								),
								"leggings" => array (
										Item::DIAMOND_LEGGINGS,
										"0",
										"1" 
								),
								"boots" => array (
										Item::DIAMOND_BOOTS,
										"0",
										"1" 
								) 
						),
						"weapons" => array (
								Item::IRON_SHOVEL => array (
										Item::IRON_SHOVEL,
										"0",
										"1" 
								),
								item::SNOWBALL => array (
										item::SNOWBALL,
										"0",
										"64" 
								) 
						),
						"foods" => array (
								item::APPLE => array (
										item::APPLE,
										"0",
										"2" 
								),
								Item::CAKE => array (
										Item::CAKE,
										"0",
										"2" 
								) 
						) 
				) );
			} elseif ($kitName == self::KIT_LEATHER_ARMOR) {
				return new Config ( $this->getPlugin()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml", Config::YAML, array (
						"kitName" => self::KIT_CHAIN_ARMOR,
						"isDefault" => false,
						"cost" => 0,
						"health" => 20,
						"armors" => array (
								"helmet" => array (
										Item::LEATHER_CAP,
										"0",
										"1" 
								),
								"chestplate" => array (
										Item::LEATHER_TUNIC,
										"0",
										"1" 
								),
								"leggings" => array (
										Item::LEATHER_PANTS,
										"0",
										"1" 
								),
								"boots" => array (
										Item::LEATHER_BOOTS,
										"0",
										"1" 
								) 
						),
						"weapons" => array (
								Item::IRON_SHOVEL => array (
										Item::IRON_SHOVEL,
										"0",
										"1" 
								),
								item::SNOWBALL => array (
										item::SNOWBALL,
										"0",
										"64" 
								) 
						),
						"foods" => array (
								item::COOKED_PORKCHOP => array (
										item::COOKED_PORKCHOP,
										"0",
										"2" 
								),
								Item::COOKED_CHICKEN => array (
										Item::COOKED_CHICKEN,
										"0",
										"2" 
								) 
						) 
				) );
			} elseif ($kitName == self::KIT_NO_ARMOR) {
				return new Config ( $this->getPlugin()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml", Config::YAML, array (
						"kitName" => self::KIT_NO_ARMOR,
						"isDefault" => false,
						"cost" => 0,
						"health" => 20,
						"armors" => array (
								"helmet" => array (
										Item::AIR,
										"0",
										"0" 
								),
								"chestplate" => array (
										Item::AIR,
										"0",
										"0" 
								),
								"leggings" => array (
										Item::AIR,
										"0",
										"0" 
								),
								"boots" => array (
										Item::AIR,
										"0",
										"0" 
								) 
						),
						"weapons" => array (
								Item::IRON_SHOVEL => array (
										Item::IRON_SHOVEL,
										"0",
										"1" 
								),
								item::SNOWBALL => array (
										item::SNOWBALL,
										"0",
										"64" 
								) 
						),
						"foods" => array (
								item::COOKED_PORKCHOP => array (
										item::COOKED_PORKCHOP,
										"0",
										"2" 
								),
								Item::COOKED_CHICKEN => array (
										Item::COOKED_CHICKEN,
										"0",
										"2" 
								) 
						) 
				) );
			}
		} else {
			return new Config ( $this->getPlugin ()->getDataFolder () . self::DIR_KITS . strtolower ( $kitName ) . ".yml", Config::YAML, array () );
		}
	}
	
	/**
	 * Load Game Kit
	 * 
	 * @param unknown $teamkitName
	 * @param Player $p
	 */
	public function loadKit($teamkitName, Player $p) {
		$teamKit = $this->getKit ( $teamkitName )->getAll ();
		
		// player must clear all equipments
		$p->getInventory ()->clearAll ();
		// add armors
		if ($teamKit ["armors"] ["helmet"] [0] != null) {
			$p->getInventory ()->setHelmet ( new Item ( $teamKit ["armors"] ["helmet"] [0], $teamKit ["armors"] ["helmet"] [1], $teamKit ["armors"] ["helmet"] [2] ) );
		}
		if ($teamKit ["armors"] ["chestplate"] [0] != null) {
			$p->getInventory ()->setChestplate ( new Item ( $teamKit ["armors"] ["chestplate"] [0], $teamKit ["armors"] ["chestplate"] [1], $teamKit ["armors"] ["chestplate"] [2] ) );
		}
		if ($teamKit ["armors"] ["leggings"] [0] != null) {
			$p->getInventory ()->setLeggings ( new Item ( $teamKit ["armors"] ["leggings"] [0], $teamKit ["armors"] ["leggings"] [1], $teamKit ["armors"] ["leggings"] [2] ) );
		}
		if ($teamKit ["armors"] ["boots"] [0] != null) {
			$p->getInventory ()->setBoots ( new Item ( $teamKit ["armors"] ["boots"] [0], $teamKit ["armors"] ["boots"] [1], $teamKit ["armors"] ["boots"] [2] ) );
		}
		// notify viewers
		$p->getInventory ()->sendArmorContents ( $p );
		// set health 
		// $MCG76 - I DISABLED FOR THIS GAME
// 		if ($teamKit ["health"] != null && $teamKit ["health"] > 1) {
// 			$p->setHealth ( $teamKit ["health"] );
// 		} else {
// 			$p->setHealth ( 20 );
// 		}
		// add iron sword, if not exist
		$weapons = $teamKit ["weapons"];
		foreach ( $weapons as $w ) {
			$item = new Item ( $w [0], $w [1], $w [2] );
			$p->getInventory ()->addItem ( $item );
		}
		$foods = $teamKit ["foods"];
		foreach ( $foods as $w ) {
			$item = new Item ( $w [0], $w [1], $w [2] );
			$p->getInventory ()->addItem ( $item );
		}
		$p->getInventory ()->setHeldItemIndex ( 0 );		
		$p->getInventory()->sendArmorContents($p->getInventory()->getViewers());
		//$p->updateMovement ();
	}
	
	/**
	 * Remove Game Kits and Inventory
	 * 
	 * @param Player $bp
	 */
	public function removePlayerGameKit(Player $bp) {
		$bp->getInventory ()->setBoots ( new Item ( Item::AIR) );
		$bp->getInventory ()->setChestplate ( new Item ( Item::AIR) );
		$bp->getInventory ()->setHelmet ( new Item ( Item::AIR) );
		$bp->getInventory ()->setLeggings ( new Item ( Item::AIR) );
		$bp->getInventory ()->clearAll ();
		$bp->getInventory ()->sendContents ( $bp );
		$bp->getInventory()->sendArmorContents($bp->getInventory()->getViewers());
		// $bp->updateMovement ();
	}
}
