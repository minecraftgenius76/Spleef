<?php

namespace mcg76\game\spleef;

use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\math\Vector2 as Vector2;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\block\Block;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\LoginPacket;
use pocketmine\nbt\NBT;
use pocketmine\item\ItemBlock;
use pocketmine\block\SignPost;
use pocketmine\item\Sign;

/**
 * Spleef Arena Builder
 *
 * Copyright (C) 2015 minecraftgenius76
 * YouTube Channel: http://www.youtube.com/user/minecraftgenius76
 *
 * @author MCG76
 *        
 */
class SpleefArenaBuilder extends MiniGameBase {
	public $boardBlocksTypes = [ ];
	const BUILDING_FLOOR_INSIDE = "inside";
	const BUILDING_FLOOR_ARENA = "arena";
	const BUILDING_FLOOR_OTHER = "other";
	
	/**
	 * constructor
	 *
	 * @param SpleefPlugin $plugin        	
	 */
	public function __construct(SpleefPlugin $plugin) {
		parent::__construct ( $plugin );
		$this->initBoardBlockType ();
	}
	private function initBoardBlockType() {
		$buildingblocks = $this->getSetup ()->getArenaBuildingBlocks ( SpleefSetup::SPLEEF_ARENA_BUILDING_BOARD_BLOCKS )->getAll ();
		$this->boardBlocksTypes = $buildingblocks ["blocks"];
	}
	public function buildStadiumFloorOnly($spleefGameWorld, Position $statiumLoc, $statiumSize) {
		
		// retrieve spleef game world
		$level = $this->getController ()->getLevel ( $spleefGameWorld );
		
		$px = $statiumLoc->x;
		$py = $statiumLoc->y;
		$pz = $statiumLoc->z;
		$arenaSize = $statiumSize == null ? $this->getSetup ()->getArenaSize () : $statiumSize;
		// build big ring - 35 - wool
		$this->buildFloor ( $level, $px, ($py + 16), $pz, $arenaSize + 10, 49, self::BUILDING_FLOOR_INSIDE );
		// build small ring - 80 - snow
		$this->buildFloor ( $level, $px, ($py + 16), $pz, $arenaSize, 49, self::BUILDING_FLOOR_ARENA );
		
		$pos = new Position ();
		$pos->x = $px + 2;
		$pos->y = ($py + $arenaSize + 16);
		$pos->z = $pz + 2;
		// $player->teleport( $pos, 332, 334);
		$arenaInfo = array (
				"entrance_x" => $pos->x,
				"entrance_y" => $pos->y,
				"entrance_z" => $pos->z,
				"floor_x" => $px,
				"floor_y" => ($py + 16),
				"floor_z" => $pz 
		);
		return $arenaInfo;
	}
	
	/**
	 * Build Arena
	 *
	 * @param Player $player        	
	 */
	public function buildStadium($spleefGameWorld, Position $statiumLoc, $statiumSize) {
		
		// retrieve spleef game world
		$level = $this->getController ()->getLevel ( $spleefGameWorld );
		
		$px = $statiumLoc->x;
		$py = $statiumLoc->y;
		$pz = $statiumLoc->z;
		$arenaSize = $statiumSize == null ? $this->getSetup ()->getArenaSize () : $statiumSize;
		
		// water tank floor
		$this->buildWaterTank ( $level, $arenaSize, $arenaSize, $px, $py, $pz, 8 );
		// build big ring - 35 - wool
		$this->buildFloor ( $level, $px, ($py + 16), $pz, $arenaSize + 10, 49, self::BUILDING_FLOOR_INSIDE );
		// build small ring - 80 - snow
		$this->buildFloor ( $level, $px, ($py + 16), $pz, $arenaSize, 49, self::BUILDING_FLOOR_ARENA );
		// fire ring
		$this->buildWallType ( $level, $statiumSize + 5, 1, $px, $py, $pz, 42, 42 );
		// big wall
		$this->buildWallType ( $level, $statiumSize + 10, 3, $px, ($py + 16), $pz, 35, 42 );
		// small wall
		$this->buildWallType ( $level, $statiumSize, 3, $px, ($py + 16), $pz, 102, 49 );
		$this->buildFloor ( $level, $px, ($py + 24), $pz, $arenaSize + 10, 50, self::BUILDING_FLOOR_OTHER );
		// ceiling floor
		$this->buildFloor ( $level, $px, ($py + 25), $pz, $arenaSize, 102, self::BUILDING_FLOOR_OTHER );
		// top floor 1
		$this->buildFloor ( $level, $px, ($py + 26), $pz, $arenaSize + 10, 89, self::BUILDING_FLOOR_OTHER );
		$this->buildWallType ( $level, $arenaSize, 3, $px, ($py + 25), $pz, 89, 0 );
		
		$pos = new Position ();
		$pos->x = $px + 2;
		$pos->y = ($py + $arenaSize + 16);
		$pos->z = $pz + 2;
		// $player->teleport( $pos, 332, 334);
		$arenaInfo = array (
				"entrance_x" => $pos->x,
				"entrance_y" => $pos->y,
				"entrance_z" => $pos->z,
				"floor_x" => $px,
				"floor_y" => ($py + 16),
				"floor_z" => $pz 
		);
		
		return $arenaInfo;
	}
	
	public function setFloor(Position $p1,Position $p2, $block, &$output = null) {
		$send = false;
		$level = $p1->getLevel();
		$bcnt = 1;
		$startX = min ($p1->x, $p2->x);
		$endX = max ($p1->x, $p2->x);
		$startY = min ($p1->y, $p2->y );
		$endY = max ( $p1->y, $p2->y );
		$startZ = min ( $p1->z, $p2->z  );
		$endZ = max ( $p1->z, $p2->z);
		$count = 0; 
		for($x = $startX; $x <= $endX; ++ $x) {
			for($y = $startY; $y <= $endY; ++ $y) {
				for($z = $startZ; $z <= $endZ; ++ $z) {
					$level->setBlock ( new Position ( $x, $y, $z ), $block, false, true );
					$count ++;
					for($i = 1; $i < 5; $i ++) {
						$v = round ( $x ) . "," . round ( $y + $i ) . "," . round ( $z );
						if (! isset ( $this->getPlugin ()->arenablocks [$v] )) {
							$this->getPlugin ()->arenablocks [$v] = $v;
						}
					}
				}
			}
		}
		$output .= "$count block(s) have been updated.\n";
		return true;
	}
	
	/**
	 * Build Floor
	 *
	 * @param Player $player        	
	 */
	public function buildFloor(Level $level, $px, $py, $pz, $size, $blockType, $floorType) {
		$this->buildBoardLayer ( $level, $px, $py, $pz, $blockType, $size, $floorType );
	}
	
	/**
	 * build board layer
	 *
	 * @param Player $p        	
	 * @param string $px
	 * @param string $py
	 * @param string $pz
	 * @param string $btype
	 * @return multitype:\pocketmine\block\Block
	 */
	public function buildBoardLayer(Level $level, $px, $py, $pz, $btype, $bsize, $floorType) {
		$ret = [ ];
		$fx = $px;
		$fy = $py;
		$fz = $pz;
		for($rx = 0; $rx < $bsize; $rx ++) {
			// item = nulll can break anything
			$x = $fx + $rx;
			$y = $fy;
			$z = $fz;
			for($rz = 0; $rz < $bsize; $rz ++) {
				$rb = $level->getBlock ( new Vector3 ( $x, $y, $z ) );
				$this->replaceBlockType ( $level, $rb, $btype );
				if ($floorType == self::BUILDING_FLOOR_INSIDE) {
					for($i = 1; $i < 5; $i ++) {
						$v = round ( $x ) . "," . round ( $y + $i ) . "," . round ( $z );
						if (! isset ( $this->getPlugin ()->arenablocks [$v] )) {
							$this->getPlugin ()->arenablocks [$v] = $v;
						}
					}
				}
				$z ++;
			}
		}
		return $ret;
	}
	
	/**
	 * Render Wall
	 *
	 * @param Player $player        	
	 * @param Block $block        	
	 */
	public function renderWall(Player $player, $width, $height, $x, $y, $z, $wallType) {
		if ($wallType == null) {
			$wallType = 2;
		}
		$this->buildWall ( $player, $width, $height, $x, $y, $z, $wallType );
	}
	
	/**
	 * Render Water Tank
	 *
	 * @param Player $player        	
	 * @param string $radius
	 * @param string $height
	 * @param string $dataX
	 * @param string $dataY
	 * @param string $dataZ
	 * @param string $wallType
	 * @return boolean
	 */
	public function buildWaterTank(Level $level, $radius, $height, $dataX, $dataY, $dataZ, $blockType) {
		$status = false;
		try {
			$x = $dataX;
			for($rx = 0; $rx < $radius; $rx ++) {
				$y = $dataY;
				for($ry = 0; $ry < $height; $ry ++) {
					$z = $dataZ;
					for($rz = 0; $rz < $radius; $rz ++) {
						$rb = $level->getBlock ( new Vector3 ( $x, $y, $z ) );
						switch ($this->getPlugin ()->gameType) {
							case 1 :
								$this->replaceBlockType ( $level, $rb, 51 );
								break;
							case 2 :
								$this->replaceBlockType ( $level, $rb, 51 );
								break;
							case 3 :
								$this->replaceBlockType ( $level, $rb, 10 );
								break;
							case 4 :
								$this->replaceBlockType ( $level, $rb, 51 );
								break;
							default :
								$this->replaceBlockType ( $level, $rb, 10 );
								break;
						}
						// build the wall at edge
						if ($rx == ($radius - 1) || $rz == ($radius - 1) || $rx == 0 || $rz == 0 || $ry == ($radius - 1) || $ry == 0) {
							if ($rx == 2 && $ry > 0 && $ry < ($radius - 1)) {
								$this->replaceRandomBlocks ( $level, $rb );
							} else if ($ry == 0) {
								// floor
								$this->replaceRandomBlocks ( $level, $rb );
							} else if ($ry == ($radius - 1)) {
								// roof
							} else if ($rx == 0 || $rz == 0) {
								$this->replaceRandomBlocks ( $level, $rb );
							} else if ($rx == ($radius - 1)) {
								$this->replaceRandomBlocks ( $level, $rb );
							} else {
								$this->replaceRandomBlocks ( $level, $rb );
							}
						}
						$z ++;
					}
					$y ++;
				}
				$x ++;
			}
			$status = true;
		} catch ( \Exception $e ) {
			$this->log ( "Error:" . $e->getMessage () );
		}
		return $status;
	}
	public function buildWallType(Level $level, $radius, $height, $dataX, $dataY, $dataZ, $wallType, $floorType) {
		$status = false;
		try {
			$doorExist = 0;
			$x = $dataX;
			for($rx = 0; $rx < $radius; $rx ++) {
				$y = $dataY;
				for($ry = 0; $ry < $height; $ry ++) {
					$z = $dataZ;
					for($rz = 0; $rz < $radius; $rz ++) {
						$rb = $level->getBlock ( new Vector3 ( $x, $y, $z ) );
						$this->replaceBlockType ( $level, $rb, 0 );
						if ($rx == ($radius - 1) || $rz == ($radius - 1) || $rx == 0 || $rz == 0 || $ry == ($radius - 1) || $ry == 0) {
							if ($rx == 2 && $ry > 0 && $ry < ($radius - 1)) {
							} else if ($ry == 0) {
								$this->replaceBlockType ( $level, $rb, $floorType );
							} else if ($ry == ($radius - 1)) {
								$this->replaceBlockType ( $level, $rb, $wallType );
							} else if ($rx == 0 || $rz == 0) {
								$this->replaceBlockType ( $level, $rb, $wallType );
							} else if ($rx == ($radius - 1)) {
								$this->replaceBlockType ( $level, $rb, $wallType );
							} else {
								$this->replaceBlockType ( $level, $rb, $wallType );
							}
						}
						$z ++;
					}
					$y ++;
				}
				$x ++;
			}
			$status = true;
		} catch ( \Exception $e ) {
			$this->log ( "Error:" . $e->getMessage () );
		}
		return $status;
	}
	public function builColumType(Level $level, $radius, $height, $dataX, $dataY, $dataZ, $wallType, $floorType) {
		$status = false;
		try {
			$doorExist = 0;
			$x = $dataX;
			for($rx = 0; $rx < $radius; $rx ++) {
				$y = $dataY;
				for($ry = 0; $ry < $height; $ry ++) {
					$z = $dataZ;
					for($rz = 0; $rz < $radius; $rz ++) {
						$rb = $level->getBlock ( new Vector3 ( $x, $y, $z ) );
						$this->replaceBlockType ( $level, $rb, 0 );
						if ($rx == ($radius - 1) || $rz == ($radius - 1) || $rx == 0 || $rz == 0 || $ry == ($radius - 1) || $ry == 0) {
							$this->replaceBlockType ( $level, $rb, $wallType );
							if ($rx == 2 && $ry > 0 && $ry < ($radius - 1)) {
								$this->replaceBlockType ( $level, $rb, $wallType );
							} else if ($ry == 0) {
							} else if ($ry == ($radius - 1)) {
							} else if ($rx == 0 || $rz == 0) {
							} else if ($rx == ($radius - 1)) {
							} else {
							}
						}
						$z ++;
					}
					$y ++;
				}
				$x ++;
			}
			$status = true;
		} catch ( \Exception $e ) {
			$this->log ( "Error:" . $e->getMessage () );
		}
		return $status;
	}
	
	/**
	 * Render Wall
	 *
	 * @param Player $player        	
	 * @param unknown $radius        	
	 * @param unknown $height        	
	 * @param unknown $dataX        	
	 * @param unknown $dataY        	
	 * @param unknown $dataZ        	
	 * @param unknown $wallType        	
	 * @return boolean
	 */
	public function buildWall(Player $player, $radius, $height, $dataX, $dataY, $dataZ, $wallType) {
		$status = false;
		try {
			$doorExist = 0;
			$x = $dataX;
			$level = $player->getLevel ();
			for($rx = 0; $rx < $radius; $rx ++) {
				$y = $dataY;
				for($ry = 0; $ry < $height; $ry ++) {
					$z = $dataZ;
					for($rz = 0; $rz < $radius; $rz ++) {
						$rb = $level->getBlock ( new Vector3 ( $x, $y, $z ) );
						$this->removeBlocks ( $rb, $player );
						if ($rx == ($radius - 1) || $rz == ($radius - 1) || $rx == 0 || $rz == 0 || $ry == ($radius - 1) || $ry == 0) {
							if ($rx == 2 && $ry > 0 && $ry < ($radius - 1)) {
								$this->renderRandomBlocks ( $rb, $player );
							} else if ($ry == 0) {
							} else if ($ry == ($radius - 1)) {
								$this->renderBlockByType ( $rb, $player, 0 );
							} else if ($rx == 0 || $rz == 0) {
								$this->renderRandomBlocks ( $rb, $player );
							} else if ($rx == ($radius - 1)) {
								$this->renderRandomBlocks ( $rb, $player );
							} else {
								$this->renderRandomBlocks ( $rb, $player );
							}
						}
						$z ++;
					}
					$y ++;
				}
				$x ++;
			}
			$status = true;
		} catch ( \Exception $e ) {
			$this->log ( "Error:" . $e->getMessage () );
		}
		return $status;
	}
	
	/**
	 * remove blocks
	 *
	 * @param array $blocks        	
	 * @param Player $p        	
	 */
	public function removeBlocks(Block $block, Player $xp) {
		$this->updateBlock ( $block, $xp, 0 );
	}
//	public function removeUpdateBlock($topblock, $tntblock) {
//		foreach ( $this->getPlugin ()->livePlayers as $livep ) {
//			if ($livep instanceof MGArenaPlayer) {
//				$this->removeBlocks ( $topblock, $livep->player );
//				$this->removeBlocks ( $tntblock, $livep->player );
//			} else {
//				$this->removeBlocks ( $topblock, $livep );
//				$this->removeBlocks ( $tntblock, $livep );
//			}
//		}
//	}
	
	/**
	 * Update block
	 *
	 * @param Block $block        	
	 * @param Player $xp        	
	 * @param string $blockType
	 */
	public function updateBlock(Block $block, Player $xp, $blockType) {
		$players = $xp->getLevel ()->getPlayers ();
		foreach ( $players as $p ) {
			$pk = new UpdateBlockPacket ();
			$pk->x = $block->getX ();
			$pk->y = $block->getY ();
			$pk->z = $block->getZ ();
			$pk->block = $blockType;
			$pk->meta = 0;
			$p->dataPacket ( $pk );
			$p->getLevel ()->setBlockIdAt ( $block->getX (), $block->getY (), $block->getZ (), $pk->block );
			
			$pos = new Position ( $block->x, $block->y, $block->z );
			$block = $p->getLevel ()->getBlock ( $pos );
			$direct = true;
			$update = true;
			$p->getLevel ()->setBlock ( $pos, $block, $direct, $update );
		}
	}
	
	/**
	 * render random blocks
	 *
	 * @param Block $block        	
	 * @param Player $p        	
	 */
	public function renderRandomBlocks(Block $block, Player $p) {
		$b = array_rand ( $this->boardBlocksTypes );
		$blockType = $this->boardBlocksTypes [$b];
		$this->updateBlock ( $block, $p, $blockType );
	}
	
	/**
	 *
	 * @param Block $block        	
	 * @param Player $p        	
	 * @param string $blockType
	 */
	public function renderBlockByType(Block $block, Player $p, $blockType) {
		$this->updateBlock ( $block, $p, $blockType );
	}
	
	/**
	 * replace random blocks
	 *
	 * @param Block $block        	
	 * @param Player $p        	
	 */
	public function replaceRandomBlocks(Level $level, Block $block) {
		$b = array_rand ( $this->boardBlocksTypes );
		$blockType = $this->boardBlocksTypes [$b];
		$this->replaceBlockType ( $level, $block, $blockType );
	}
	
	/**
	 *
	 * @param Block $block        	
	 * @param Player $p        	
	 * @param unknown $blockType        	
	 */
	public function replaceBlockType(Level $level, Block $block, $blockType) {
		// randomly place a mine
		$players = $level-> getPlayers();
		foreach ( $players as $p ) {
			$pk = new UpdateBlockPacket ();
			$pk->x = $block->getX ();
			$pk->y = $block->getY ();
			$pk->z = $block->getZ ();
			$pk->block = $blockType;
			$pk->meta = 0;
			$p->dataPacket ( $pk );
			$p->getLevel ()->setBlockIdAt ( $block->getX (), $block->getY (), $block->getZ (), $pk->block );			
			
			$pos = new Position ( $block->x, $block->y, $block->z, $level);
			$block = $p->getLevel ()->getBlock ( $pos );
			$p->getLevel ()->setBlock ( $pos, $block, false, true );
		}
		$level->setBlock(new Vector3((int)$block->getX (), (int)$block->getY (), (int)$block->getZ ()), new Block($blockType), false, false);
	}
	
	/**
	 * remove arena
	 *
	 * @param unknown $player        	
	 * @param unknown $xx        	
	 * @param unknown $yy        	
	 * @param unknown $zz        	
	 */
	public function removeArena($player, $xx, $yy, $zz) {
		$wallheighSize = 70;
		$bsize = $this->boardsize;
		$xmax = $this->boardsize + 3;
		$ymax = $this->boardsize;
		For($z = 0; $z <= $xmax; $z ++) {
			For($x = 0; $x <= $xmax; $x ++) {
				For($y = 0; $y <= $wallheighSize; $y ++) {
					$mx = $xx + $x;
					$my = $yy + $y;
					$mz = $zz + $z;
					$bk = $player->getLevel ()->getBlock ( new Vector3 ( $mx, $my, $mz ) );
					$this->removeBlocks ( $bk, $player );
				}
			}
		}
	}
	public function removeGlassTop($size, $player, $xx, $yy, $zz) {
		$wallheighSize = 70;
		$bsize = $size;
		$xmax = $size + 3;
		$ymax = $size;
		
		For($z = 0; $z <= $xmax; $z ++) {
			For($x = 0; $x <= $xmax; $x ++) {
				For($y = 0; $y <= $wallheighSize; $y ++) {
					$mx = $xx + $x;
					$my = $yy + $y;
					$mz = $zz + $z;
					$bk = $player->getLevel ()->getBlock ( new Vector3 ( $mx, $my, $mz ) );
					$this->renderBlockByType ( $bk, $player, 0 );
				}
			}
		}
	}
}
