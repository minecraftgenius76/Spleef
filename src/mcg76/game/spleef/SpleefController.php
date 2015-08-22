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
use pocketmine\math\Vector3 as Vector3;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\block\Block;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\LoginPacket;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\DoorSound;

/**
 * MCG76 Spleef Controller
 *
 * Copyright (C) 2015 minecraftgenius76
 * YouTube Channel: http://www.youtube.com/user/minecraftgenius76
 *
 * @author MCG76
 *        
 */
class SpleefController extends MiniGameBase {
	// commands
	const SPLEEF_COMMAND = "spleef";
	const SPLEEF_COMMAND_CREATE = "create";
	const SPLEEF_COMMAND_RESET = "reset";
	const SPLEEF_COMMAND_BLOCK_ON = "blockon";
	const SPLEEF_COMMAND_BLOCK_OFF = "blockoff";
	const SPLEEF_COMMAND_STATS = "stats";
	const SPLEEF_COMMAND_LOBBY = "lobby";
	const SPLEEF_COMMAND_HOME = "home";
	// setup button
	const SPLEEF_COMMAND_SETUP_BUTTON_JOIN_POSITION = "setbuttonjoin";
	const SPLEEF_COMMAND_SETUP_BUTTON_START_POSITION = "setbuttonstart";
	// setup sign
	const SPLEEF_COMMAND_SETUP_SIGN_JOIN_POSITION = "setsignjoin";
	const SPLEEF_COMMAND_SETUP_SIGN_START_POSITION = "setsignstart";
	const SPLEEF_COMMAND_SETUP_SIGN_VIEW_STATS_POSITION = "setsignstats";
	const SPLEEF_COMMAND_SETUP_SIGN_GO_HOME_POSITION = "setsignhome";
	const SPLEEF_COMMAND_SETUP_SIGN_GO_LOBBY_POSITION = "setsignlobby";
	// setup positions
	const SPLEEF_COMMAND_SETUP_POSITION_SPLEEF_HOME = "setposhome";
	const SPLEEF_COMMAND_SETUP_POSITION_SERVER_LOBBY = "setposlobby";
	const SPLEEF_COMMAND_SETUP_POSITION_ARENA_ENTRANCE = "setposplayenter";
	// scheduler reset options
	const SPLEEF_GAME_RESET_OPTION_FULL_REBUILD = "FULL";
	const SPLEEF_GAME_RESET_OPTION_FLOOR_REBUILD = "FLOOR";
	
	const SPLEEF_PERMISSIONS_PLAY ="mcg76.spleef.command";
	
	/**
	 *
	 * @param Spleef $pg        	
	 */
	public function __construct(SpleefPlugIn $plugin) {
		parent::__construct ( $plugin );
	}
	
	/**
	 * onCommand
	 *
	 * @param CommandSender $sender        	
	 * @param Command $command        	
	 * @param unknown $label        	
	 * @param array $args        	
	 * @return boolean
	 */
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		// check command names
		if ((strtolower ( $command->getName () ) == self::SPLEEF_COMMAND) && isset ( $args [0] )) {
			if (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_CREATE) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->createGame ( $sender );
				$output = "";
				$output .= $this->getMsg ( "plugin.name" ) . "--------------------------------\n";
				$output .= $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.create.success" ) . "\n";
				$output .= $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.hits.toplay" ) . "\n";
				$output .= $this->getMsg ( "plugin.name" ) . "--------------------------------\n";
				$sender->sendMessage ( $output );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_RESET) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->resetGame ( $sender );
				$output = "";
				$output .= $this->getMsg ( "plugin.name" ) . "--------------------------------\n";
				$output .= $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.reset.success" ) . "." . $this->getMsg ( "spleef.game.started" ) . "\n";
				$output .= $this->getMsg ( "plugin.name" ) . "--------------------------------\n";
				$sender->sendMessage ( $output );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_BLOCK_ON) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugin ()->pos_display_flag = 1;
				$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.blockon.success" ) );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_BLOCK_OFF) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugin ()->pos_display_flag = 0;
				$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.blockoff.success" ) );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_LOBBY) {
				if (! $sender instanceof Player) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.error.in-game-only" ) );
					return;
				}
				$this->teleportPlayerToLobby ( $sender );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_HOME) {
				if (! $sender instanceof Player) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.error.in-game-only" ) );
					return;
				}
				$this->teleportPlayerToHome ( $sender );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_STATS) {
				if (! $sender instanceof Player) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.error.in-game-only" ) );
					return;
				}
				$this->showArenaStats ( $sender );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_BUTTON_JOIN_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_BUTTON_JOIN_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_BUTTON_JOIN_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_BUTTON_START_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_BUTTON_START_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_BUTTON_START_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_SIGN_GO_HOME_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_SIGN_GO_HOME_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_SIGN_GO_HOME_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_SIGN_GO_LOBBY_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_SIGN_GO_LOBBY_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_SIGN_GO_LOBBY_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_SIGN_START_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_SIGN_START_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_SIGN_START_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_SIGN_JOIN_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_SIGN_JOIN_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_SIGN_JOIN_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_SIGN_VIEW_STATS_POSITION) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$this->getPlugIn ()->setupModeAction = self::SPLEEF_COMMAND_SETUP_SIGN_VIEW_STATS_POSITION;
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_SIGN_VIEW_STATS_POSITION );
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.select" ) );				
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_POSITION_ARENA_ENTRANCE) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_POSITION_ARENA_ENTRANCE );
				$this->getSetup ()->handleSetupPosition ( $sender, self::SPLEEF_COMMAND_SETUP_POSITION_ARENA_ENTRANCE, $sender->getPosition () );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_POSITION_SERVER_LOBBY) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_POSITION_SERVER_LOBBY );
				$this->getSetup ()->handleSetupPosition ( $sender, self::SPLEEF_COMMAND_SETUP_POSITION_SERVER_LOBBY, $sender->getPosition () );
				return true;
			} elseif (strtolower ( $args [0] ) == self::SPLEEF_COMMAND_SETUP_POSITION_SPLEEF_HOME) {
				if (! $sender->isOp ()) {
					$sender->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.command.error.no-authorization" ) );
					return;
				}
				$sender->sendMessage ( $this->getMsg ( "spleef.setup.action" ) . self::SPLEEF_COMMAND_SETUP_POSITION_SPLEEF_HOME );
				$this->getSetup ()->handleSetupPosition ( $sender, self::SPLEEF_COMMAND_SETUP_POSITION_SPLEEF_HOME, $sender->getPosition () );
				return true;
			}
		}
	}
	public function resetGameTankType() {
		$tankTypes = array (
				"fire" => "1",
				"water" => "2",
				"lava" => "3",
				"lava2" => "3" 
		);
		$b = array_rand ( $tankTypes );
		$tankType = $tankTypes [$b];
		$this->getPlugin ()->setGameType ( $tankType );
	}
	public function broadCastWinning() {
		$output = "";
		if (count ( $this->getPlugin ()->arenaPlayers ) > 0) {
			$output .= TextFormat::GRAY.$this->getMsg ( "plugin.name" ) . "************************|\n";
			$output .= TextFormat::AQUA.$this->getMsg ( "plugin.name" ) . "* " . $this->getMsg ( "spleef.game.conglatulation" ) . "*|\n";
			$output .= TextFormat::GRAY.$this->getMsg ( "plugin.name" ) . "************************\n";
			$output .= TextFormat::WHITE.$this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.game.round-winner" ) . count ( $this->getPlugin ()->arenaPlayers ) . "\n";
			foreach ( $this->getPlugin ()->arenaPlayers as $player ) {
				$output .= TextFormat::GOLD.$this->getMsg ( "plugin.name" ) . "> " . $player->getName () . "\n";
			}
			$output .= TextFormat::GRAY.$this->getMsg ( "plugin.name" ) . "************************|\n";			
			$spleefGameWorld = $this->getSetup()->getHomeWorldName ();
			$level = $this->plugin->controller->getLevel ( $spleefGameWorld );		
			if ($level instanceof Level) {	
				$this->getPlugin ()->getServer ()->broadcastMessage ( $output , $level->getPlayers());
			}
		}
	}
	
	/**
	 * Create Game
	 *
	 * @param CommandSender $sender        	
	 */
	public function createGame(CommandSender $sender) {
		$arenaPos = $this->getSetup ()->getArenaPos ();
		$arenaSize = $this->getSetup ()->getArenaSize ();
		$spleefworld = $this->getSetup ()->getHomeWorldName ();
		$this->getBuilder ()->buildStadium ( $spleefworld, $arenaPos, $arenaSize );
		$this->getPlugin ()->gameMode = 0;
		$this->getPlugin ()->alertCount = 0;
	}
	
	/**
	 * Handle Player Leave, Quit or Die from the game
	 *
	 * @param Player $player        	
	 */
	public function leaveGameWorld(Player $player) {
		if (isset ( $this->getPlugin ()->arenaPlayers [$player->getName ()] )) {
			unset ( $this->getPlugin ()->arenaPlayers [$player->getName ()] );
		}
	}
	
	/**
	 * Handle Player Join or Respawn into game world
	 *
	 * @param Player $player        	
	 */
	public function enterGameWorld(Player $player) {
		if ($this->getSetup ()->isEnableSpanwToLobby ()) {
			$lobbyPos = $this->getSetup ()->getLobbyPos ();
			$player->teleport ( $lobbyPos );
			$this->log ( TextFormat::RED . "player spawn to lobby  " . $player->getName () . " at " . $lobbyPos->x . " " . $lobbyPos->y . " " . $lobbyPos->z );
		}
		$this->grantPlayerDefaultPermissions($player);
	}
	
	public function showArenaStats(Player $player) {
		$player->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.game.stats" ) );
		$player->sendMessage ( $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.game.players" ) . count($this->getPlugin ()->arenaPlayers ));
	}
	
	/**
	 * Give default permissions to players
	 * @param Player $player
	 */
	private function grantPlayerDefaultPermissions(Player $player) {
		$player->addAttachment($this->getPlugIn(),self::SPLEEF_PERMISSIONS_PLAY, TRUE);
	}
	
	/**
	 *
	 * Touched Join Button
	 *
	 * @param PlayerInteractEvent $event        	
	 */
	public function handleCLickJoinGame(Player $player, $blockTouched) {
		// JOIN BUTTON
		$joinButtonPos = $this->getSetup ()->getButtonPos ( SpleefSetup::CLICK_BUTTON_JOIN1_GAME );
		// JOIN SIGN
		$joinSignPos = $this->getSetup ()->getSignPos ( SpleefSetup::CLICK_SIGN_JOIN1_GAME );
		
		if ((round ( $blockTouched->x ) == round ( $joinButtonPos->x ) && round ( $blockTouched->y ) == round ( $joinButtonPos->y ) && round ( $blockTouched->z ) == round ( $joinButtonPos->z )) || (round ( $blockTouched->x ) == round ( $joinSignPos->x ) && round ( $blockTouched->y ) == round ( $joinSignPos->y ) && round ( $blockTouched->z ) == round ( $joinSignPos->z ))) {
			if($this->getPlugin ()->gameMode == 1) {
				$player->sendMessage ( $this->getMsg ( "spleef.game.game-already-in-progress" ) );
				return;
			}
			$arenaEntracePos = $this->getSetup ()->getArenaEntrancePos ();
			if ($arenaEntracePos == null) {
				$player->sendMessage ( $this->getMsg ( "configuration.error.missing.arena-entrace" ) );
				$player->sendMessage ( $this->getMsg ( "configuration.contact.admin" ) );
			} else {
				$player->teleport ( $arenaEntracePos );
				$player->getLevel()->updateAround($player->getPosition());
				$player->getLevel()->updateAllLight($player->getPosition());
				for ($i=0; $i<10; $i++) {
					$player->sendTip (TextFormat::BOLD.TextFormat::GOLD. $this->getMsg ( "plugin.name" ) . " " . $this->getMsg ( "spleef.welcome" )."\n".TextFormat::BOLD.TextFormat::GOLD. $this->getMsg ( "plugin.name" ) . " " . $this->getMsg ( "spleef.havefun" ) );
				}
			}
		}
	}
	
	/**
	 * Reset Game
	 *
	 * @param CommandSender $sender        	
	 */
	public function resetGame(CommandSender $sender) {
		$arenaPos = $this->getSetup ()->getArenaPos ();
		$arenaSize = $this->getSetup ()->getArenaSize ();
		// re-build arena
		$spleefworld = $this->getSetup ()->getHomeWorldName ();
		$resetOption = $this->getSetup ()->getRoundResetOption ();
		
		if ($resetOption != null && $resetOption == "FULL") {
			$this->getBuilder ()->buildStadium ( $spleefworld, $arenaPos, $arenaSize );
		} else {
			$this->getBuilder ()->buildStadiumFloorOnly ( $spleefworld, $arenaPos, $arenaSize );
		}

		// reset
		$this->getPlugin ()->gameMode = 0;
		$this->getPlugin ()->alertCount = 0;
	}
	
	/**
	 * Handle Sign Teleporting
	 *
	 * @param PlayerInteractEvent $event        	
	 */
	public function handleClickSignTeleporting(Player $player, $blockTouched) {
		$lobbyPos = $this->getSetup ()->getServerLobbyPos ();
		// GO TO LOBBY
		if (round ( $blockTouched->x ) == round ( $lobbyPos->x ) && round ( $blockTouched->y ) == round ( $lobbyPos->y ) && round ( $blockTouched->z ) == round ( $lobbyPos->z )) {
			$this->teleportPlayerToLobby ( $player );
			return;
		}
		
		$homePos = $this->getSetup ()->getHomeWorldPos ();
		// GO SPLEEF HOME
		if (round ( $blockTouched->x ) == round ( $homePos->x ) && round ( $blockTouched->y ) == round ( $homePos->y ) && round ( $blockTouched->z ) == round ( $homePos->z )) {
			$this->teleportPlayerToHome ( $player );
			return;
		}
		
		$viewStatPos = $this->getSetup ()->getSignPos(SpleefSetup::CLICK_SIGN_VIEW_GAME_STATS);
		// GO SPLEEF HOME
		if (round ( $blockTouched->x ) == round ( $viewStatPos->x ) && round ( $blockTouched->y ) == round ( $viewStatPos->y ) && round ( $blockTouched->z ) == round ( $viewStatPos->z )) {
			$this->teleportPlayerToHome ( $player );
			return;
		}
		
	}
	/**
	 * Teleport Player to Server Lobby World
	 *
	 * @param Player $player        	
	 */
	public function teleportPlayerToLobby(Player $player) {
		$levelname = $this->getSetup ()->getServerLobbyWorldName ();
		$level = $this->getLevel ( $levelname );
		if ($player->getServer ()->isLevelLoaded ( $levelname )) {
			$level = $player->getServer ()->getLevelByName ( $levelname );
			if ($level == null) {
				$player->sendMessage ( $this->getMsg ( "configuration.error.missing.level-not-found" ) );
				$player->sendMessage ( $this->getMsg ( "configuration.contact.admin" ) );
				$this->log ( "level not found: " . $levelname );
				return;
			}
			$message = $this->getMsg ( "teleporting.lobby.world" ) . " [" . $level->getName () . "]";
			$player->sendMessage ( $message );
			$level->getChunk ( $level->getSafeSpawn ()->x, $level->getSafeSpawn ()->z );
			$player->teleport ( $level->getSafeSpawn () );
			if ($this->getSetup ()->isEnableSpanwToLobby ()) {
				$lobbyPos = $this->getSetup ()->getServerLobbyPos ();
				$message = $this->getMsg ( "teleporting.lobby.location" );
				$level->getChunk ( $lobbyPos->x, $lobbyPos->z );
				$player->sendMessage ( $message );
				$player->teleport ( $lobbyPos );
			}
		}
	}
	
	/**
	 * Teleporting Player to Spleef Home World
	 *
	 * @param Player $player        	
	 */
	public function teleportPlayerToHome(Player $player) {
		$levelhome = $this->getSetup ()->getHomeWorldName ();
		$level = $this->getLevel ( $levelhome );
		if ($player->getServer ()->isLevelLoaded ( $levelhome )) {
			$level = $player->getServer ()->getLevelByName ( $levelhome );
			if ($level == null) {
				$player->sendMessage ( $this->getMsg ( "configuration.error.missing.level-not-found" ) );
				$player->sendMessage ( $this->getMsg ( "configuration.contact.admin" ) );
				return;
			}
			$player->teleport ( $level->getSafeSpawn () );
			// move player to new level
			$homePos = $this->getSetup ()->getHomeWorldPos ();
			$level->getChunk ( $homePos->x, $homePos->z );
			$message = $message = $this->getMsg ( "teleporting.spleef.location" ) . "-" . $levelhome;
			$player->sendMessage ( $message );
			$player->teleport ( $homePos );
		}
	}
	
	/**
	 * Retrieve Level
	 *
	 * @param string $levelhome
	 * @return void|NULL
	 */
	public function getLevel($levelhome) {
		if (! Server::getInstance ()->isLevelGenerated ( $levelhome )) {
			//$player->sendMessage ( $this->getMsg ( "configuration.error.level-not-generated" ) );
			$this->log ( "Error :" . $levelhome . " has NOT generated yet!" );
			return null;
		}
		if (! Server::getInstance ()->isLevelLoaded ( $levelhome )) {
			Server::getInstance ()->loadLevel ( $levelhome );
		}
		return Server::getInstance ()->getLevelByName ( $levelhome );
	}
	
	/**
	 * Keep track of players inside arena
	 *
	 * @param Player $player        	
	 */
	public function trackArenaPlayers(Player $player, $v) {
		if (isset ( $this->getPlugin ()->arenablocks [$v] )) {
			if (! isset ( $this->getPlugin ()->arenaPlayers [$player->getName ()] )) {
				// if game is on make player leave
				if($this->getPlugin ()->gameMode == 1) {
					$player->sendMessage ( $this->getMsg ( "spleef.game.game-already-in-progress" ) );
					$this->teleportPlayerToHome($player);
					return;
				}
				$this->getPlugin ()->arenaPlayers [$player->getName ()] = $player;
				// $this->log ( "Player arrived, Spleef arena players count:" . count ( $this->getPlugin ()->arenaPlayers ) );
				$this->givePlayerGameKit ( $player );
			}
		} else {
			if (isset ( $this->getPlugin ()->arenaPlayers [$player->getName ()] )) {
				// $this->log ( "Player departed, Spleef arena players count:" . count ( $this->getPlugin ()->arenaPlayers ) );
				$this->removePlayerGameKit ( $player );
				if(count ( $this->getPlugin ()->arenaPlayers ) < 2) {
					$arenaPos = $this->getSetup ()->getArenaPos ();
					$arenaSize = $this->getSetup ()->getArenaSize ();
					// re-build arena
					$spleefworld = $this->getSetup ()->getHomeWorldName ();
					$resetOption = $this->getSetup ()->getRoundResetOption ();
					
					if ($resetOption != null && $resetOption == "FULL") {
						$this->getBuilder ()->buildStadium ( $spleefworld, $arenaPos, $arenaSize );
					} else {
						$this->getBuilder ()->buildStadiumFloorOnly ( $spleefworld, $arenaPos, $arenaSize );
					}

					// reset
					$this->getPlugin ()->gameMode = 0;
					$this->getPlugin ()->alertCount = 0;
				}
				if(count ( $this->getPlugin ()->arenaPlayers ) == 1) {
					$this->broadCastWinning ();
				}
				unset ( $this->getPlugin ()->arenaPlayers [$player->getName ()] );
			}
		}
	}
	private function givePlayerGameKit(Player $player) {
		if ($player->getInventory ()->contains ( new Item ( Item::IRON_SHOVEL ) )) {
			return;
		}
		if ($player->getInventory ()->getItemInHand ()->getId () != Item::IRON_SHOVEL) {
			$player->getInventory ()->setItemInHand ( new Item ( Item::IRON_SHOVEL ) );
			$this->getGameKit ()->putOnRandomGameKit ( $player );
		}
	}
	private function removePlayerGameKit(Player $player) {
		if ($player->getInventory ()->getItemInHand ()->getId () == Item::IRON_SHOVEL) {
			$player->getInventory ()->setItemInHand ( new Item ( Item::AIR ) );
			$player->getInventory ()->remove ( new Item ( Item::IRON_SHOVEL ) );
		}
		$this->getGameKit ()->removePlayerGameKit ( $player );
	}
	
	/**
	 *
	 * Touched Start Button
	 *
	 * @param PlayerInteractEvent $event        	
	 */
	public function handleClickStartGame(Player $player, $blockTouched) {
		// START BUTTON
		$startButtonPos = $this->getSetup ()->getButtonPos ( SpleefSetup::CLICK_BUTTON_START_GAME );
		// START SIGN
		$startSignPos = $this->getSetup ()->getSignPos ( SpleefSetup::CLICK_SIGN_START_GAME );
		// START BUTTON
		if ((round ( $blockTouched->x ) == round ( $startButtonPos->x ) && round ( $blockTouched->y ) == round ( $startButtonPos->y ) && round ( $blockTouched->z ) == round ( $startButtonPos->z )) || (round ( $blockTouched->x ) == round ( $startSignPos->x ) && round ( $blockTouched->y ) == round ( $startSignPos->y ) && round ( $blockTouched->z ) == round ( $startSignPos->z ))) {
			if(count ( $this->getPlugin ()->arenaPlayers ) < 2) {
				$player->sendMessage ( $this->getMsg ( "spleef.game.only-one-player" ) );
				return;
			}
			// set the floor to be breakable
			$this->startGamePlay ( $player );
		}
	}
	public function startGamePlay(Player $player) {
		$output = "";
		// set the floor to be breakable
		if ($this->getPlugin ()->gameMode === 0) {
			$player->getLevel()->addSound(new ClickSound($player->getPosition()), array($player));			
			$arenaPos = $this->getSetup ()->getArenaPos ();
			$arenaSize = $this->getSetup ()->getArenaSize ();
			// build the floors
			$level = $player->level;
			$this->getBuilder ()->buildFloor ( $level, $arenaPos->x, ($arenaPos->y + 16), $arenaPos->z, ($arenaSize + 10), 80, "inside" );
			// build small ring - 80 - snow
			$this->getBuilder ()->buildFloor ( $level, $arenaPos->x, ($arenaPos->y + 16), $arenaPos->z, $arenaSize, 80, "arena" );
			// brodcast
			$output .= TextFormat::GREEN.$this->getMsg ( "plugin.name" ) . " " . $this->getMsg ( "spleef.game.started" ) . "\n";
			$output .= TextFormat::YELLOW.$this->getMsg ( "plugin.name" ) . " " . $this->getMsg ( "spleef.game.gogogo" ) . "\n";
			
// 			// send an explosion
// 			$explosion = new Explosion ( new Position ( $arenaPos->x, $arenaPos->y + 5, $arenaPos->z, $level ), 1 );
// 			$explosion->explodeA();
			
			for ($i=0;$i<10;$i++) {
				$player->getLevel()->addSound(new DoorSound($player->getPosition()), $this->plugin->arenaPlayers);	
			}
			$player->getServer ()->broadcastMessage ( $output, $this->plugin->arenaPlayers );
			$this->getPlugin ()->gameMode = 1;
			$this->getPlugin ()->alertCount = 0;
		} else {
			$player->getLevel()->addSound(new ClickSound($player->getPosition()), array($player));
			$output .= $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.game.wait-for-reset" ) . "\n";
			$output .= $this->getMsg ( "plugin.name" ) . $this->getMsg ( "spleef.game.manual-reset" ) . "\n";
			$player->sendTip( TextFormat::RED.$output );
		}
	}
	
	/**
	 * Restarting Game
	 *
	 * @param CommandSender $sender        	
	 */
	public function restartGame(CommandSender $sender) {
		$arenaPos = $this->getSetup ()->getArenaPos ();
		$arenaSize = $this->getSetup ()->getArenaSize ();

		$this->getBuilder ()->buildStadium ( $sender->getServer (), $arenaPos, $arenaSize );
		// build big ring - 35 - wool
		$this->getBuilder ()->buildFloor ( $sender->getLevel(), $arenaPos->x, ($arenaPos->y + 16), $arenaPos->z, ($arenaSize + 10), 35 );
		// build small ring - 80 - snow
		$this->getBuilder ()->buildFloor ( $sender->getLevel(), $arenaPos->x, ($arenaPos + 16), $arenaPos->z, $arenaSize, 80 );
		// reset
		$this->getPlugin ()->gameMode = 1;
		$this->getPlugin ()->alertCount = 0;
	}
}