<?php

namespace mcg76\game\spleef;

use pocketmine\utils\TextFormat;

/**
 * MCG76 SPleef Setup
 *
 * Copyright (C) 2015 minecraftgenius76
 * 
 * This is a utility class use to testing purpose
 *
 * @author MCG76
 * @link http://www.youtube.com/user/minecraftgenius76
 *
 */
class SpleefTestMessages extends MiniGameBase {

	public function __construct(SpleefPlugin $plugin) {
		parent::__construct ( $plugin );
		$this->msgs = new SpleefMessages($plugin);
	}
	
	public function runTests() {
		return;
		$this->testMessage("spleef.name");
		$this->testMessage("spleef.welcome");		
		$this->testMessage("spleef.havefun");
		$this->testMessage("spleef.command.create.success");
		$this->testMessage("spleef.command.reset.success");
		$this->testMessage("spleef.command.blockon.success");
		$this->testMessage("spleef.command.blockoff.success");
		$this->testMessage("spleef.command.error.no-authorization");
		$this->testMessage("spleef.hits.toplay");		
		$this->testMessage("spleef.game.started");
		$this->testMessage("spleef.game.gogogo");
		$this->testMessage("spleef.game.round-winner");
		$this->testMessage("spleef.game.wait-for-reset");
		$this->testMessage("spleef.game.manual-reset");		
		$this->testMessage("teleporting.lobby.world");
		$this->testMessage("teleporting.lobby.location");
		$this->testMessage("teleporting.spleef.location");		
		$this->testMessage("configuration.error.missing.arena-entrace");		
		$this->testMessage("configuration.error.missing.level-not-found");		
		$this->testMessage("configuration.error.level-not-generated");		
		$this->testMessage("configuration.contact.admin");		
		$this->testMessage("plugin.name");				
		$this->testMessage("plugin.enable");
		$this->testMessage("plugin.disable");
		$this->testMessage("plugin.schedule.reset" );
		$this->testMessage("plugin.schedule.time" );
		$this->testMessage("spleef.game.stats" );
		$this->testMessage("spleef.game.players" );
		
	}
	
	public function testMessage($key) {
		$value = $this->getMsgKey($key);
		if ($value==null) {
			$value = TextFormat::RED ."* KEY NOT FOUND !!!";
		}
		if ($key==$value) {
			$value = TextFormat::RED ."* KEY NOT FOUND !!!";
		}
		$this->getPlugin()->getLogger()->info($key." = ".$value);
	}
	
	public function getMsgKey($key) {
		return $this->msgs->getMessageByKey($key);
	}
	
}
