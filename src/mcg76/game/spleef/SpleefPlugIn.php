<?php

namespace mcg76\game\spleef;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\event\Listener;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\block\Block;
use mcg76\game\spleef\tasks\PlayResetTimeout;

/**
 * MCPE Spleef mini-game PlugIn
 *
 * Copyright (C) 2015 minecraftgenius76
 * YouTube Channel: http://www.youtube.com/user/minecraftgenius76
 *
 * @author MCG76
 *        
 */
class SpleefPlugin extends PluginBase implements CommandExecutor {
	// object variables
	public $builder;
	public $controller;
	public $messages;
	public $setup;
	public $gamekit;
	// session variables
	public $arenaPlayers = [ ];
	public $arenablocks = [ ];
	// tracking variables
	public $gameMode = 0;
	public $alertTimeOut = 0;
	public $alertCount = 0;
	public $gameType = 0;
	public $pos_display_flag = 0;
	
	// setup mode
	public $setupModeAction = "";
	
	/**
	 * OnLoad
	 * (non-PHPdoc)
	 *
	 * @see \pocketmine\plugin\PluginBase::onLoad()
	 */
	public function onLoad() {
		$this->initMinigameComponents();
	}
	
	/**
	 * OnEnable
	 *
	 * (non-PHPdoc)
	 *
	 * @see \pocketmine\plugin\PluginBase::onEnable()
	 */
	public function onEnable() {
		$this->initConfigFile ();
		//register listener
		$this->getServer ()->getPluginManager ()->registerEvents ( new SpleefListener ( $this ), $this );
		$this->getLogger ()->info ( TextFormat::GREEN . "Spleef Enabled" );
		$this->getLogger ()->info ( TextFormat::GREEN . "-------------------------------------------------" );		
		$this->initScheduler();
		$this->initMessageTests();
		$this->enabled = true;
	}
	
	public function setGameType($type) {
		$this->gameType = $type;
	}
	public function getGameType() {
		return $this->gameType;
	}
	
	/**
	 * OnDisable
	 * (non-PHPdoc)
	 *
	 * @see \pocketmine\plugin\PluginBase::onDisable()
	 */
	public function onDisable() {
		$this->enabled = false;
		$this->getLogger ()->info ( TextFormat::GREEN . "Spleef Disabled" );
	}
	private function initConfigFile() {
		try {
			$this->saveDefaultConfig ();
			if (! file_exists ( $this->getDataFolder () )) {
				@mkdir ( $this->getDataFolder (), 0777, true );
				file_put_contents ( $this->getDataFolder () . "config.yml", $this->getResource ( "config.yml" ) );
			}
			$this->reloadConfig ();
			$this->getConfig ()->getAll ();
		} catch ( \Exception $e ) {
			$this->getLogger ()->error ( $e->getMessage());
		}
	}
	private function initMinigameComponents() {
		// move initialization here
		try {
			$this->setup = new SpleefSetup ( $this );
			$this->messages = new SpleefMessages ( $this );
			$this->builder = new SpleefArenaBuilder ( $this );
			$this->controller = new SpleefController ( $this );
			$this->gamekit = new SpleefGameKit ( $this );
			
		} catch ( \Exception $ex ) {
			$this->getLogger ()->info ( $e->getMessage() );
		}
	}
	
	private function initScheduler() {
		// run reset scheduler
		$resetValue = $this->setup->getRoundResetTime ();
		$resetTask = new PlayResetTimeout ( $this );
		$taskWaitTime = $resetValue * $this->getServer ()->getTicksPerSecond ();
		$this->getServer ()->getScheduler ()->scheduleRepeatingTask ( $resetTask, $taskWaitTime );
		$this->getLogger ()->info ( TextFormat::GREEN . "server ticks per second: " . $this->getServer ()->getTicksPerSecond () . " or " . $taskWaitTime . " ticks" );
		$this->getLogger ()->info ( TextFormat::GREEN . "Reset schedule task to run every " . $resetValue . " seconds" );
		$this->getLogger ()->info ( TextFormat::GREEN . "-------------------------------------------------" );		
	}
	
	private function initMessageTests() {
		// test language selftest messages
		if ($this->getConfig ()->get ( "run_selftest_message" ) == "YES") {
			$stmsg = new SpleefTestMessages ( $this );
			$stmsg->runTests ();
		}
	}
	
	/**
	 * OnCommand
	 * (non-PHPdoc)
	 *
	 * @see \pocketmine\plugin\PluginBase::onCommand()
	 */
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		$this->controller->onCommand ( $sender, $command, $label, $args );
	}
	protected function getMsg($key) {
		return $this->messages->getMessageByKey ( $key );
	}
}
