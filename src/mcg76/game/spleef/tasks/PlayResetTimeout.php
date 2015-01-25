<?php

namespace mcg76\game\spleef\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use mcg76\game\spleef\SpleefPlugin;

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
		//update tank type
		$this->getController()->resetGameTankType();
		
		//update arena
		$spleefGameWorld = $this->getSetup()->getHomeWorldName();
		$arenaPos = $this->getSetup()->getArenaPos();
		$arenaSize = $this->getSetup()->getArenaSize();
		$resetOption = $this->getSetup()->getRoundResetOption();	
		$resetValue = $this->getSetup()->getRoundResetTime();
		
		if ($resetOption != null && $resetOption == "FULL") {
			$this->getBuilder()->buildStadium ( $spleefGameWorld, $arenaPos, $arenaSize );
		} else {
			$this->getBuilder()->buildStadiumFloorOnly ($spleefGameWorld, $arenaPos, $arenaSize );
		}
		//reset stats
		$this->getPlugIn()->gameMode = 0;
		$this->getPlugIn()->alertCount == 0;
		
		// display winners
		$this->getController()->broadCastWinning();
		$output =$this->getMsg("plugin.name")." ".$this->getMsg("spleef.game.reset-in")." " . $resetValue . $this->getMsg("plugin.schedule.time")."\n";
		$this->getPlugIn()->getServer ()->broadcastMessage ( $output );
		$this->log("PlayResetTimeout.onRun:" . $output);
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
		return $this->getPlugIn()->getLogger()->info($msg);
	}
}
