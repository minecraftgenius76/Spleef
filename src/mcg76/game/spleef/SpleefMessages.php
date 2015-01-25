<?php

namespace mcg76\game\spleef;

use pocketmine\utils\Config;
/**
 * MCG76 Spleef Messages
 *
 * Copyright (C) 2015 minecraftgenius76
 *
 * @author MCG76
 * @link http://www.youtube.com/user/minecraftgenius76
 *        
 */
class SpleefMessages extends MiniGameBase {
	private $messages;
	public function __construct(SpleefPlugin $plugin) {
		parent::__construct ( $plugin );
		$this->loadLanguageMessages ();
	}
	public function getMessageByKey($key) {
		return isset ( $this->messages [$key] ) ? $this->messages [$key] : $key;
	}
	public function getMessageWithVars($node, $vars) {
		$msg = $this->messages->getNested ( $node );
		
		if ($msg != null) {
			$number = 0;
			foreach ( $vars as $v ) {
				$msg = str_replace ( "%var$number%", $v, $msg );
				$number ++;
			}
			return $msg;
		}
		return null;
	}
	public function getVersion() {
		return $this->messages->get ( "version" );
	}
	private function parseMessages(array $messages) {
		$result = [];
		foreach ( $messages as $key => $value ) {
			if (is_array ( $value )) {
				foreach ( $this->parseMessages ( $value ) as $k => $v ) {
					$result [$key . "." . $k] = $v;
				}
			} else {
				$result [$key] = $value;
			}
		}
		return $result;
	}
	
	public function loadLanguageMessages() {
		$configlang = $this->getSetup()->getMessageLanguage();
		$messageFile = $this->getPlugin ()->getDataFolder () . "messages_" . $configlang . ".yml";
		$this->getPlugin ()->getLogger ()->info ( "SPLEEF Message Language = " . $messageFile );
		if (! file_exists ( $messageFile )) {
			$this->getPlugin ()->saveResource ( "messages_EN.yml", false );
			$messages = (new Config ( "messages_EN.yml" ))->getAll ();
			$this->messages = $this->parseMessages ( $messages );
			$this->getPlugin ()->getLogger ()->info ( "Warning!, specify configuration language not found!, fall back to use English" );
		} else {
			$this->getPlugin ()->saveResource ( "messages_" . $configlang . ".yml", false );
			$messages = (new Config ( $messageFile ))->getAll ();
			$this->messages = $this->parseMessages ( $messages );
		}
	}
	public function reloadMessages() {
		$this->messages->reload ();
	}
	
	public static function prefixMsg(&$msg) {
		return "[SPLEEF]".	$msg;	
	}
}