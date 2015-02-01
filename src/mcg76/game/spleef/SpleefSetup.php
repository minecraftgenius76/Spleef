<?php

namespace mcg76\game\spleef;

use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\block\Block;
use pocketmine\Player;

/**
 * MCG76 SPLEEF Setup
 *
 * Copyright (C) 2015 minecraftgenius76
 *
 * @author MCG76
 * @link http://www.youtube.com/user/minecraftgenius76
 *      
 */
class SpleefSetup extends MiniGameBase {
	const DIR_ARENA = "arena/";
	const SERVER_LOBBY_NAME = 1000;
	const SERVER_LOBBY_WORLD = 1001;
	const SERVER_LOBBY_POSITION = 1002;
	const SPLEEF_HOME_NAME = 2002;
	const SPLEEF_HOME_WORLD = 2041;
	const SPLEEF_HOME_POSITION = 2003;
	const SPLEEF_ARENA_NAME = 2001;
	const SPLEEF_ARENA_POSITION = 2010;
	const SPLEEF_ARENA_ENTRANCE_POSITION = 2020;
	const SPLEEF_ARENA_BUILDING_BOARD_BLOCKS = 2030;
	const SPLEEF_ARENA_BUILDING_BOARD = "ArenaBoardTypes";
	const CLICK_BUTTON_JOIN1_GAME = 3001;
	const CLICK_BUTTON_START_GAME = 3010;
	const CLICK_BUTTON_STOP_GAME = 3020;
	const CLICK_BUTTON_RESET_GAME = 3030;
	const CLICK_SIGN_VIEW_GAME_STATS = 4000;
	const CLICK_SIGN_JOIN1_GAME = 4001;
	const CLICK_SIGN_GO_HOME = 4002;
	const CLICK_SIGN_START_GAME = 4003;
	const CLICK_SIGN_GO_LOBBY = 4004;
	
	/**
	 * Constructor
	 *
	 * @param
	 *        	SPLEEF PlugIn $plugin
	 */
	public function __construct(SpleefPlugIn $plugin) {
		parent::__construct ( $plugin );
		$this->init ();
	}
	private function init() {
		@mkdir ( $this->plugin->getDataFolder () . self::DIR_ARENA, 0777, true );
		$this->getArenaBuildingBlocks ( self::SPLEEF_ARENA_BUILDING_BOARD_BLOCKS );
	}
	/**
	 * Arena building blocks
	 *
	 * @param unknown $blockType        	
	 * @return \pocketmine\utils\Config
	 */
	public function getArenaBuildingBlocks($blockType) {
		if (! (file_exists ( $this->getPlugin ()->getDataFolder () . self::DIR_ARENA . strtolower ( $blockType ) . ".yml" ))) {
			if ($blockType == self::SPLEEF_ARENA_BUILDING_BOARD_BLOCKS) {
				return new Config ( $this->plugin->getDataFolder () . self::DIR_ARENA . strtolower ( self::SPLEEF_ARENA_BUILDING_BOARD ) . ".yml", Config::YAML, array (
						"blockType" => self::SPLEEF_ARENA_BUILDING_BOARD,
						"blocks" => array (
								"stone1" => "91",
								"stone2" => "91",
								"stone3" => "91",
								"stone4" => "91",
								"stone5" => "42",
								"stone6" => "42",
								"stone8" => "42",
								"GLOWSTONE_BLOCK" => "89" 
						) 
				) );
			}
		} else {
			return new Config ( $this->getPlugin ()->getDataFolder () . self::DIR_ARENA . strtolower ( $blockType ) . ".yml", Config::YAML, array () );
		}
	}
	public function getMessageLanguage() {
		$configlang = $this->getPlugIn ()->getConfig ()->get ( "language" );
		if ($configlang == null) {
			$configlang = "EN";
		}
		return $configlang;
	}
	
	public function isSpleefWorldBlockBreakDisable() {
		return $this->getConfig("disable_Spleef_world_blockBreak", true );
	}
	
	public function isSpleefWorldBlockPlaceDisable() {
		return $this->getConfig("disable_Spleef_world_blockPlace", true );
	}
	
	public function getHomeWorldName() {
		$worldName = $this->getConfig ( "spleef_home_world" );
		return $worldName;
	}
	
	public function setSpeefHomeLocation(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_home_world", $pos->getLevel ()->getName () );
			$config->set ( "spleef_home_x", round ( $pos->x ) );
			$config->set ( "spleef_home_y", round ( $pos->y ) );
			$config->set ( "spleef_home_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	public function setServerLobbyLocation(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "server_lobby_world", $pos->getLevel ()->getName () );
			$config->set ( "server_lobby_x", round ( $pos->x ) );
			$config->set ( "server_lobby_y", round ( $pos->y ) );
			$config->set ( "server_lobby_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	public function enablePlayerOnJoinGoToLobby() {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "enable_spaw_lobby", "YES" );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	public function disablePlayerOnJoinGoToLobby() {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "enable_spaw_lobby", "NO" );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	public function enableSelfReset() {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "enable_self_reset", "YES" );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	 
	public function disableSelfReset() {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "enable_self_reset", "NO" );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	public function getRoundResetTime() {
		$resetValue = $this->getConfig ( "reset_timeout" );
		if ($resetValue == null) {
			$resetValue = 10000;
		}
		return $resetValue;
	}
	public function getRoundResetOption() {
		$resetoption = $this->getConfig ( "reset_option" );
		if ($resetoption == null) {
			$resetoption = "FULL";
		}
		return $resetoption;
	}
	public function getArenaName() {
		$arenaName = $this->getConfig ( "spleef_arena_name" );
		return $arenaName;
	}
	public function getArenaSize() {
		$arenaSize = $this->getConfig ( "spleef_arena_size" );
		if ($arenaSize == null) {
			$arenaSize = 16;
		}
		return $arenaSize;
	}
	public function getArenaPos() {
		$dataX = $this->getConfig ( "spleef_arena_x" );
		$dataY = $this->getConfig ( "spleef_arena_y" );
		$dataZ = $this->getConfig ( "spleef_arena_z" );
		return new Position ( $dataX, $dataY, $dataZ );
	}
	public function getArenaEntrancePos() {
		$dataX = $this->getConfig ( "spleef_arena_entrance_x" );
		$dataY = $this->getConfig ( "spleef_arena_entrance_y" );
		$dataZ = $this->getConfig ( "spleef_arena_entrance_z" );
		return new Position ( $dataX, $dataY, $dataZ );
	}
	public function getHomeWorldPos() {
		$dataX = $this->getConfig ( "spleef_home_x" );
		$dataY = $this->getConfig ( "spleef_home_y" );
		$dataZ = $this->getConfig ( "spleef_home_z" );
		return new Position ( $dataX, $dataY, $dataZ );
	}

	public function isEnableSpanwToLobby() {
		$enableSpawnLobby = $this->getConfig ( "enable_spaw_lobby" );
		if ($enableSpawnLobby != null && $enableSpawnLobby == "YES") {
			return true;
		}
		return false;
	}
	public function getServerLobbyWorldName() {
		return $this->getConfig ( "server_lobby_world" );
	}
	public function getServerLobbyPos() {
		$lobbyX = $this->getConfig ( "server_lobby_x" );
		$lobbyY = $this->getConfig ( "server_lobby_y" );
		$lobbyZ = $this->getConfig ( "server_lobby_z" );
		return new Position ( $lobbyX, $lobbyY, $lobbyZ );
	}
	public function getGameWorldName() {
		$gameworld = $this->getConfig ( "spleef_home_world" );
		return $gameworld;
	}
	public function getGameWorldPos($posTypeId) {
		switch ($posTypeId) {
			case self::SPLEEF_ARENA_POSITION :
				$sx = $this->getConfig ( "stadium_x" );
				$sy = $this->getConfig ( "stadium_y" );
				$sz = $this->getConfig ( "stadium_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::SPLEEF_ARENA_POSITION :
				$sx = $this->getConfig ( "stadium_x" );
				$sy = $this->getConfig ( "stadium_y" );
				$sz = $this->getConfig ( "stadium_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::SPLEEF_ARENA_ENTRANCE_POSITION :
				$sx = $this->getConfig ( "stadium_entrance_x" );
				$sy = $this->getConfig ( "stadium_entrance_y" );
				$sz = $this->getConfig ( "stadium_entrance_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::SPLEEF_HOME_POSITION :
				$gameX = $this->getConfig ( "home_x" );
				$gameY = $this->getConfig ( "home_y" );
				$gameZ = $this->getConfig ( "home_z" );
				return new Position ( $gameX, $gameY, $gameZ );
				break;
			default :
				return null;
		}
	}
	public function getButtonPos($buttonTypeId) {
		switch ($buttonTypeId) {
			case self::CLICK_BUTTON_JOIN1_GAME :
				$sx = $this->getConfig ( "spleef_join_button_1_x" );
				$sy = $this->getConfig ( "spleef_join_button_1_y" );
				$sz = $this->getConfig ( "spleef_join_button_1_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::CLICK_BUTTON_START_GAME :
				$sx = $this->getConfig ( "spleef_start_button_1_x" );
				$sy = $this->getConfig ( "spleef_start_button_1_y" );
				$sz = $this->getConfig ( "spleef_start_button_1_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			default :
				return null;
		}
	}
	public function getSignPos($signTypeId) {
		switch ($signTypeId) {
			case self::CLICK_SIGN_VIEW_GAME_STATS :
				$sx = $this->getConfig ( "spleef_sign_stats_x" );
				$sy = $this->getConfig ( "spleef_sign_stats_y" );
				$sz = $this->getConfig ( "spleef_sign_stats_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::CLICK_SIGN_JOIN1_GAME :
				$sx = $this->getConfig ( "spleef_sign_join_x" );
				$sy = $this->getConfig ( "spleef_sign_join_y" );
				$sz = $this->getConfig ( "spleef_sign_join_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::CLICK_SIGN_START_GAME :
				$sx = $this->getConfig ( "spleef_sign_start_x" );
				$sy = $this->getConfig ( "spleef_sign_start_y" );
				$sz = $this->getConfig ( "spleef_sign_start_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::CLICK_SIGN_GO_HOME :
				$sx = $this->getConfig ( "spleef_sign_home_x" );
				$sy = $this->getConfig ( "spleef_sign_home_y" );
				$sz = $this->getConfig ( "spleef_sign_home_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			case self::CLICK_SIGN_GO_LOBBY :
				$sx = $this->getConfig ( "spleef_sign_lobby_x" );
				$sy = $this->getConfig ( "spleef_sign_lobby_y" );
				$sz = $this->getConfig ( "spleef_sign_lobby_z" );
				return new Position ( $sx, $sy, $sz );
				break;
			default :
				return null;
		}
	}
	
	/**
	 * Handle Click Sign Setup Actions
	 *
	 * @param Player $player        	
	 * @param unknown $setupAction        	
	 * @param Position $pos        	
	 */
	public function handleSetupPosition(Player $player, $setupAction, Position $pos) {
		// handle setup selection
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_POSITION_SPLEEF_HOME) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setSpeefHomeLocation ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		} elseif ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_POSITION_SERVER_LOBBY) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setServerLobbyLocation ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		} elseif ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_POSITION_ARENA_ENTRANCE) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setArenaEntrancePos ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		}
	}
	
	/**
	 * Handle Click Sign Setup Actions
	 *
	 * @param Player $player        	
	 * @param unknown $setupAction        	
	 * @param Position $pos        	
	 */
	public function handleClickSignSetup(Player $player, $setupAction, Position $pos) {
		// handle setup selection
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_SIGN_GO_HOME_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setSignPosGoHome ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		}
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_SIGN_GO_LOBBY_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setSignPosGoToLobby ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		}
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_SIGN_JOIN_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setSignPosJoinGame ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		}
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_SIGN_START_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setSignPosStartGame ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		}
		
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_SIGN_VIEW_STATS_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setSignPosViewStats ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" );
			}
			return;
		}
	}
	
	/**
	 * Setup Sign for goto Server Lobby
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setSignPosGoToLobby(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_sign_lobby_x", round ( $pos->x ) );
			$config->set ( "spleef_sign_lobby_y", round ( $pos->y ) );
			$config->set ( "spleef_sign_lobby_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	
	/**
	 * setup player entrance position to arena
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setArenaEntrancePos(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_arena_entrance_x", round ( $pos->x ) );
			$config->set ( "spleef_arena_entrance_y", round ( $pos->y ) );
			$config->set ( "spleef_arena_entrance_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	
	/**
	 * Setup Sign for Go to Spleef Home
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setSignPosGoHome(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_sign_home_x", round ( $pos->x ) );
			$config->set ( "spleef_sign_home_y", round ( $pos->y ) );
			$config->set ( "spleef_sign_home_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	/**
	 * Setup Sign for Join Game
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setSignPosJoinGame(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_sign_join_x", round ( $pos->x ) );
			$config->set ( "spleef_sign_join_y", round ( $pos->y ) );
			$config->set ( "spleef_sign_join_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	/**
	 * Setup Sign for Start Game
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setSignPosStartGame(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_sign_start_x", round ( $pos->x ) );
			$config->set ( "spleef_sign_start_y", round ( $pos->y ) );
			$config->set ( "spleef_sign_start_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	
	/**
	 * Setup Sign for View Game Stats
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setSignPosViewStats(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_sign_start_x", round ( $pos->x ) );
			$config->set ( "spleef_sign_stats_y", round ( $pos->y ) );
			$config->set ( "spleef_sign_stats_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	
	/**
	 * Handle Click Button Setup Actions
	 *
	 * @param Player $player        	
	 * @param unknown $setupAction        	
	 * @param Position $pos        	
	 */
	public function handleClickButtonSetup(Player $player, $setupAction, Position $pos) {
		// handle setup selection
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_BUTTON_JOIN_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setButtonPosJoinGame ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			}
			return;
		}
		if ($setupAction == SpleefController::SPLEEF_COMMAND_SETUP_BUTTON_START_POSITION) {
			$this->getPlugIn ()->setupModeAction = "";
			if ($this->setButtonPosStartGame ( $pos )) {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.success" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			} else {
				$player->sendMessage ( $this->getMsg ( "spleef.setup.failed" ) . "\n" . round ( $pos->x ) . " " . round ( $pos->y ) . " " . round ( $pos->z ) );
			}
			return;
		}
	}
	
	/**
	 * Setup Button for Start Game
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setButtonPosStartGame(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_start_button_1_x", round ( $pos->x ) );
			$config->set ( "spleef_start_button_1_y", round ( $pos->y ) );
			$config->set ( "spleef_start_button_1_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	
	/**
	 * Setup Button for Start Game
	 *
	 * @param Position $pos        	
	 * @return boolean
	 */
	public function setButtonPosJoinGame(Position $pos) {
		$success = false;
		try {
			$config = $this->getPlugIn ()->getConfig ();
			$config->set ( "spleef_join_button_1_x", round ( $pos->x ) );
			$config->set ( "spleef_join_button_1_y", round ( $pos->y ) );
			$config->set ( "spleef_join_button_1_z", round ( $pos->z ) );
			$config->save ();
			$success = true;
		} catch ( \Exception $e ) {
			$this->getPlugIn ()->getLogger ()->error ( $e->getMessage () );
		}
		return $success;
	}
	public static function getPlugInConfigFile(SpleefPlugIn $plugin) {
		$path = $plugin->getDataFolder ();
		if (! file_exists ( $path )) {
			@mkdir ( $plugin->getDataFolder (), 0777, true );
		}
		return new Config ( $path . "config.yml", Config::YAML, array (
				"language" => "EN",
				"run_selftest_message" => "NO",
				"enable_spaw_lobby" => "NO",
				"server_lobby_world" => "world",
				"server_lobby_x" => "489",
				"server_lobby_y" => "5",
				"server_lobby_z" => "388",
				"enable_self_reset" => "YES",
				"reset_timeout" => "120",
				"reset_option" => "FLOOR",
				"spleef_home_world" => "world",
				"spleef_home_x " => "502",
				"spleef_home_y " => "4",
				"spleef_home_z" => "412",
				"spleef_arena_name" => "Spleef Self-Generate Arena",
				"spleef_arena_size" => "16",
				"spleef_arena_x" => "535",
				"spleef_arena_y" => "4",
				"spleef_arena_z" => "409",
				"spleef_arena_entrance_x" => "542",
				"spleef_arena_entrance_y" => "22",
				"spleef_arena_entrance_z" => "430",
				"spleef_join_button_1_x" => "522",
				"spleef_join_button_1_y" => "5",
				"spleef_join_button_1_z" => "418",
				"spleef_start_button_1_x" => "537",
				"spleef_start_button_1_y" => "22",
				"spleef_start_button_1_z" => "408",
				"spleef_sign_lobby_x" => "487",
				"spleef_sign_lobby_y" => "5",
				"spleef_sign_lobby_z" => "387",
				"spleef_sign_home_x" => "487",
				"spleef_sign_home_y" => "5",
				"spleef_sign_home_z" => "388",
				"spleef_sign_join_x" => "496",
				"spleef_sign_join_y" => "5",
				"spleef_sign_join_z" => "412",
				"spleef_sign_start_x" => "487",
				"spleef_sign_start_y" => "5",
				"spleef_sign_start_z" => "386",
				"spleef_sign_stats_x" => "496",
				"spleef_sign_stats_y" => "5",
				"spleef_sign_stats_z" => "411" 
		) );
	}
	
	private function getConfig($key) {
		return $this->getPlugin ()->getConfig ()->get ( $key );
	}
	private function setConfig($key, $value) {
		return $this->getPlugin ()->getConfig ()->set ( $key, $value );
	}
}