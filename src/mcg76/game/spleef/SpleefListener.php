<?php

namespace mcg76\game\spleef;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\math\Vector2 as Vector2;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\block\Block;
use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\LoginPacket;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;

/**
 * MCG76 Spleef Listener
 *
 * Copyright (C) 2015 minecraftgenius76
 *
 * @author MCG76
 * @link http://www.youtube.com/user/minecraftgenius76
 *      
 */
class SpleefListener extends MiniGameBase implements Listener {
	public function __construct(SpleefPlugin $plugin) {
		parent::__construct ( $plugin );
	}
	
	/**
	 * OnBlockBreak
	 *
	 * @param BlockBreakEvent $event        	
	 */
	public function onBlockBreak(BlockBreakEvent $event) {
		$b = $event->getBlock ();
		if ($this->getPlugin ()->pos_display_flag == 1) {
			$event->getPlayer ()->sendMessage ( "BREAKED: [x=" . $b->x . " y=" . $b->y . " z=" . $b->z . "]" );
			return;
		}
		if ($event->getPlayer () instanceof Player) {
			$player = $event->getPlayer ();
			if (strtolower ( $player->level->getName () ) == strtolower ( $this->getSetup ()->getHomeWorldName () )) {
				if ($this->getSetup ()->isSpleefWorldBlockBreakDisable () || ! $player->isOp ()) {
					if ($b->getId () != 80) {
						$event->setCancelled ( true );
					}
				}
			}
		}
	}
		
	/**
	 * onBlockPlace
	 *
	 * @param BlockPlaceEvent $event
	 */
	public function onBlockPlace(BlockPlaceEvent $event) {
		$b = $event->getBlock ();
		if ($this->getPlugin ()->pos_display_flag == 1) {
			$event->getPlayer ()->sendMessage ( "PLACED: [x=" . $b->x . " y=" . $b->y . " z=" . $b->z . "]" );
			return;
		}
		if ($event->getPlayer () instanceof Player) {
			if (strtolower ( $event->getPlayer ()->level->getName () ) == strtolower ( $this->getSetup ()->getHomeWorldName () )) {
				if ($this->getSetup ()->isSpleefWorldBlockPlaceDisable () || ! $player->isOp ()) {
					if ($b->getId () != 80) {
						$event->setCancelled ( true );
					}
				}
			}
		}
	}
	
	/**
	 * OnPlayerJoin
	 *
	 * @param PlayerJoinEvent $event        	
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer ();
		if ($player instanceof Player) {
			$this->getController ()->enterGameWorld ( $player );
		}
	}
	
	/**
	 *
	 * @param PlayerRespawnEvent $event        	
	 */
	public function onPlayerRespawn(PlayerRespawnEvent $event) {
		$player = $event->getPlayer ();
		if ($player instanceof Player) {
			$this->getController ()->enterGameWorld ( $player );
		}
	}
	
	/**
	 * PlayerMoveEvent
	 *
	 * @param PlayerMoveEvent $event        	
	 */
	public function onPlayerMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer ();
		if ($player instanceof Player) {
			$v = round ( $event->getTo ()->x ) . "," . round ( $event->getTo ()->y ) . "," . round ( $event->getTo ()->z );
			$this->getController ()->trackArenaPlayers ( $player, $v );
		}
	}
	
	/**
	 * Player touch Block
	 *
	 * @param PlayerInteractEvent $event        	
	 */
	public function onPlayerInteract(PlayerInteractEvent $event) {
		$b = $event->getBlock ();
		$player = $event->getPlayer ();
		if ($player instanceof Player) {
			if ($this->getPlugin ()->pos_display_flag == 1) {
				// $event->getPlayer ()->sendMessage ( "TOUCHED: [" . $b . "]" );
				$event->getPlayer ()->sendMessage ( "TOUCHED: [x=" . $b->x . " y=" . $b->y . " z=" . $b->z . "]" );
			}
			$this->getController ()->handleCLickJoinGame ( $player, $b );
			$this->getController ()->handleClickStartGame ( $player, $b );
			$this->getController ()->handleClickSignTeleporting ( $player, $b );
			
			// process sign setup actions
			if ($this->getPlugin ()->setupModeAction != "") {
				$this->getSetup ()->handleClickButtonSetup ( $player, $this->getPlugin ()->setupModeAction, new Position ( $b->x, $b->y, $b->z ) );
				$this->getSetup ()->handleClickSignSetup ( $player, $this->getPlugin ()->setupModeAction, new Position ( $b->x, $b->y, $b->z ) );
			}
		}
	}
	
	/**
	 * Watch sign change
	 *
	 * @param SignChangeEvent $event        	
	 */
	public function onSignChange(SignChangeEvent $event) {
		$player = $event->getPlayer ();
		$block = $event->getBlock ();
		$line1 = $event->getLine ( 0 );
		$line2 = $event->getLine ( 1 );
		$line3 = $event->getLine ( 2 );
		$line4 = $event->getLine ( 3 );
		
		if ($line1 != null && $line1 == "spleef") {
			if ($line2 != null && $line2 == "stats") {
				$event->setLine ( 2, "Arena Players" );
				$event->setLine ( 3, count ( $this->getPlugin ()->arenaPlayers ) );
				return;
			}
			if ($line2 != null && $line2 == "home") {
				$this->getController ()->teleportPlayerToHome ( $player );
			}
		}
		if ($line2 != null && $line2 == "lobby") {
			$levelname = $line3;
			$this->getController ()->teleportPlayerToLobby ( $player );
		}
	}
	
	/**
	 * Player Disconnect
	 *
	 * @param PlayerQuitEvent $event        	
	 */
	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer ();
		if ($player instanceof Player) {
			$this->getController ()->leaveGameWorld ( $player );
		}
	}
	
	/**
	 *
	 * Player Death Event
	 *
	 * @param PlayerDeathEvent $event        	
	 */
	public function onPlayerDeath(PlayerDeathEvent $event) {
		if ($event->getEntity () instanceof Player) {
			$this->getController ()->leaveGameWorld ( $event->getEntity () );
		}
	}
	/**
	 * Player Got Kicked
	 *
	 * @param PlayerKickEvent $event        	
	 */
	public function onPlayerKick(PlayerKickEvent $event) {
		if ($event->getPlayer () instanceof Player) {
			$this->getController ()->leaveGameWorld ( $event->getPlayer () );
		}
	}
}