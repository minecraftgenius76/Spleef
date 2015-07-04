<?php

namespace mcg76\game\spleef\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use mcg76\game\spleef\SpleefPlugin;
use mcg76\game\spleef\SpleefSetup;
use pocketmine\level\Level;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;

/**
 * Scheduled Game Reset
 *
 * Copyright (C) 2014 minecraftgenius76
 * YouTube Channel: http://www.youtube.com/user/minecraftgenius76
 *
 * @author MCG76
 *        
 */
class PlayResetTimeout extends PluginTask {
	private $plugin;
	public function __construct(SpleefPlugIn $plugin) {
		$this->plugin = $plugin;
		parent::__construct ( $plugin );
	}
	public function onRun($ticks) {
		try {
			// update tank type
			$this->getController ()->resetGameTankType ();
			
			// update arena
			$spleefGameWorld = $this->getSetup ()->getHomeWorldName ();
			$arenaPos = $this->getSetup ()->getArenaPos ();
			$arenaSize = $this->getSetup ()->getArenaSize ();
			$resetOption = $this->getSetup ()->getRoundResetOption ();
			$resetValue = $this->getSetup ()->getRoundResetTime ();
			
			if ($resetOption != null && $resetOption == "FULL") {
				$this->getBuilder ()->buildStadium ( $spleefGameWorld, $arenaPos, $arenaSize );
			} else {
				$this->getBuilder ()->buildStadiumFloorOnly ( $spleefGameWorld, $arenaPos, $arenaSize );
			}
			// reset stats
			$this->getPlugIn ()->gameMode = 0;
			$this->getPlugIn ()->alertCount = 0;
			
			// display winners
			if (count ( $this->plugin->arenaPlayers ) > 0) {
				$this->getController ()->broadCastWinning ();
			}
			$output = $this->getMsg ( "plugin.name" ) . " " . $this->getMsg ( "spleef.game.reset-in" ) . " " . $resetValue . $this->getMsg ( "plugin.schedule.time" ) . "\n";
			
			$level = $this->plugin->controller->getLevel ( $spleefGameWorld );
			if ($level != null) {
				foreach ( $level->getPlayers () as $p ) {
					if ($p instanceof Player) {
						$p->sendTip ( TextFormat::DARK_AQUA . $output );
					}
				}
			} else {
				$this->getPlugIn ()->getServer ()->broadcastMessage ( TextFormat::GRAY . $output ,$level->getPlayers ());
			}
			// $this->log("PlayResetTimeout.onRun:" . $output);
			$this->updateSigns ();
		} catch ( \Exception $e ) {
			$this->plugin->getLogger ()->info ( $e->getMessage () . "|" . $e->getLine () . "|" . $e->getTraceAsString () . "\n" );
		}
	}
	private function updateSigns() {
		$spleefGameWorld = $this->getSetup ()->getHomeWorldName ();
		$level = $this->plugin->controller->getLevel ( $spleefGameWorld );
		$signPos = $this->plugin->setup->getSignPos ( SpleefSetup::CLICK_SIGN_JOIN1_GAME );
		if (! empty ( $signPos )) {
			$tile = $level->getTile ( $signPos );
			if (! empty ( $tile )) {
				$tile->setText ( TextFormat::AQUA . "[Spleef]", TextFormat::GREEN . "Minigame", TextFormat::WHITE . " [Enter Arena]", TextFormat::GRAY . "No.Players: " . TextFormat::GOLD . count ( $this->plugin->arenaPlayers ) );
			}
		}
		
		$signPos = $this->plugin->setup->getSignPos ( SpleefSetup::CLICK_SIGN_VIEW_GAME_STATS );
		if (! empty ( $signPos )) {
			$tile = $level->getTile ( $signPos );
			if (! empty ( $tile )) {
				$tile->setText ( TextFormat::AQUA . "[Spleef]", TextFormat::GREEN . "Minigame", TextFormat::WHITE . " [View Stats]", "" );
			}
		}
		
		$signPos = $this->plugin->setup->getSignPos ( SpleefSetup::CLICK_SIGN_GO_LOBBY );
		if (! empty ( $signPos )) {
			$tile = $level->getTile ( $signPos );
			if (! empty ( $tile )) {
				$tile->setText ( TextFormat::AQUA . "[Server]", TextFormat::GREEN . "Lobby", TextFormat::RED . " [EXIT]", "" );
			}
		}
	}
	public function onCancel() {
	}
	protected function getMsg($key) {
		return $this->plugin->messages->getMessageByKey ( $key );
	}
	protected function getController() {
		return $this->getPlugIn ()->controller;
	}
	protected function getPlugIn() {
		return $this->plugin;
	}
	protected function getSetup() {
		return $this->getPlugIn ()->setup;
	}
	protected function getBuilder() {
		return $this->getPlugIn ()->builder;
	}
	protected function log($msg) {
		$this->getPlugIn ()->getLogger ()->info ( $msg );
	}
}
