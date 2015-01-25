===============================================================================================
MCG76 Minigame "Spleef" plugin for MCPE Server | PocketMine alpha 1.4x
===============================================================================================
New MCPE version of Spleef a self-running game that reset the arena itself after each play.

What is new in version Spleef 1.5?

- Enable multi-lingual support
- Code Refactoring to improve stability
- Add New features 
  - provide player random equipments such as armors / snowballs 
  - add new easy setup commands 

------------------------------------------------------------------------------------------------
Project: MCG76 Minigame - SPLEEF

Player Commands: 
------------------------------------------------------------------------------------------------
"/spleef stats"  -- show number of players inside Sleef Arena
"/spleef home"   -- send player to spleef home world 
"/spleef lobby"  -- send player to server lobby world 

Admin Commands: 
------------------------------------------------------------------------------------------------
"/spleef create" - re-build spleef arena from scratch
"/spleef reset"  - reset arena to fix any broken parts 
"/spleef blockon"  - enable display of block location 
"/spleef blockoff"  - disable display of block location

** setup button
"/speef setbuttonjoin"  --setup join button location
"/speef setbuttonstart"  --setup start button location

** setup sign
"/speef setsignjoin"  --setup join sign location
"/speef setsignstart"  --setup start sign location
"/speef setsignstats"  --setup view stats sign location
"/speef setsignhome"  --setup sign for spleef home 
"/speef setsignlobby"  --setup sign for server lobby

**setup postion
"/speef setposhome"  --setup position for spleef home world
"/speef setposlobby"  --setup position for server lobby world
"/speef setposplayenter"  --setup postion for player landing in arena

------------------------------------------------------------------------------------------------
Default timeou: 120 Seconds

Youtube Videos

Latest Version 1.5.0 : https://www.youtube.com/watch?v=hZOCwUuEWI4&feature=youtu.be

Older Version 1.0.5  : https://www.youtube.com/watch?v=MMSw9US8xn4

Initial Version 1.0  : https://www.youtube.com/watch?v=ItNnbLQeszE


How to setup and Play?
------------------------------------------------------------------------------------------------

Option #1 
download the demo maps and drop this plugin in server folder. you are ready to go. 
(recommend) - no additional setup required

Option #2
download this plugin, drop to server folder. 
use admin console issue command /spleef create

Option #3
Customized, location of signin/exit

Download links

PlugIn: 
World Map: 


KNOW ISSUES
- none--

Installation: 
-------------------------
NOTE: 
Upgrade from previous version, please remove old configuration file, 
or copy your setting over to new configuration file
-------------------------
Just drop mcg76_Spleef_v1.5.phar into PocketMine Server plugin folder 
Restart server

Configuration: (config.xml)
# ---------------------------
# SPLEEF MINI-GAME PLUGIN
# CONFIGURATION FILE
# Version 1.5.0
# ---------------------------
# specify language to use
language: "EN"
run_selftest_message: "NO"
# ---------------------------------
# Enforce Player alway goto server lobby
# Default is OFF 
# spawn on map savespawn location
# ---------------------------------
enable_spaw_lobby: "NO"
# ---------------------------------
server_lobby_world: "world"
server_lobby_x: "489"
server_lobby_y: "5"
server_lobby_z: "388"
# ---------------------------------
# EnableGame Self-Reset 
# set to YES to turn-on
# otherwise set to NO for manual
# ---------------------------------
enable_self_reset: "YES"
# ---------------------------------
# Game Reset Time out in Seconds
# ---------------------------------
reset_timeout: "120"
# ---------------------------------
# Reset Option: FLOOR OR FULL
# ---------------------------------
reset_option: "FLOOR"
# ---------------------------------
spleef_home_world: "world"
spleef_home_x: "502"
spleef_home_y: "4"
spleef_home_z: "412"
#----------------------------------
# Spleef Arena Location
#---------------------------------- 
spleef_arena_name: "Spleef Self-Generate Arena"
spleef_arena_size: "16"
spleef_arena_x: "535"
spleef_arena_y: "4"
spleef_arena_z: "409"
#------------------------------------
# Arena Player Entrace When on Join
#------------------------------------
spleef_arena_entrance_x: "542"
spleef_arena_entrance_y: "22"
spleef_arena_entrance_z: "430"
#
# Game Join Buttton Location
#---------------------------
spleef_join_button_1_x: "522"
spleef_join_button_1_y: "5"
spleef_join_button_1_z: "418"
#---------------------------
# Game Start Buttton Location
#---------------------------
spleef_start_button_1_x: "537"
spleef_start_button_1_y: "22"
spleef_start_button_1_z: "408"
# -------------------------------
# SPLEEF STATIC ACTION SIGNS
# -------------------------------
# STATIC SIGN GOTO LOBBY LOCATION
# -------------------------------
spleef_sign_lobby_x: "487"
spleef_sign_lobby_y: "5"
spleef_sign_lobby_z: "387"
# ------------------------------
# STATIC SIGN GOTO HOME LOCATION
# ------------------------------
spleef_sign_home_x: "487"
spleef_sign_home_y: "5"
spleef_sign_home_z: "388"
# ------------------------------
# STATIC SIGN JOIN ARENA
# ------------------------------
spleef_sign_join_x: "496"
spleef_sign_join_y: "5"
spleef_sign_join_z: "412"
# ------------------------------
# STATIC SIGN START GAME
# ------------------------------
spleef_sign_start_x: "487"
spleef_sign_start_y: "5"
spleef_sign_start_z: "386"
# ------------------------------
# STATIC SIGN VIEW STATS
# ------------------------------
spleef_sign_stats_x: "496"
spleef_sign_stats_y: "5"
spleef_sign_stats_z: "411"
# ------------------------------
---------------------------------------------------------------------------------------------------

PERMISIONS
---------
commands:
 spleef:
  description: "Start minecraftgenius76 Minigame Spleef"
  permission: mcg76.spleef.command
permissions:
 mcg76.spleef.command:
  description: "mcg76.spleef"
  default: true


------------------
HAVE FUN!

Bug Report:
Author: MinecraftGenius76 

================================================================================================

Youtube Channel: https://www.youtube.com/user/minecraftgenius76/videos
(Likes and Subscribe for more future videos)

Twitter: https://twitter.com/minecraftgeni76
Facebook: https://www.facebook.com/minecraftgenius76

Planetminecraft: http://www.planetminecraft.com/member/minecraftgenius76/
(Posted Projects)

Thanks
MinecraftGenius76