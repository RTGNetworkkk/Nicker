<?php

namespace RTG\Nick;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Config;

class NickName extends PluginBase implements Listener {

/**
* All rights reserved RTGNetworkkk
* InspectorGadget (c)
* GitHub: <https://github.com/RTGNetworkkk>
*/
	
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveResource("bannednames.txt");
		$this->saveResource("config.yml");
		$this->getLogger()->warning("
* Nicker 1.0.3
* Starting..
		");
		$this->bans = new Config($this->getDataFolder() . "bannednames.txt");
		$this->cfg = new Config($this->getDataFolder() . "config.yml");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $param) {
		switch(strtolower($cmd->getName())) {
		
			case "nicker":
			if($sender->hasPermission("nick.command")) {
				if(isset($param[0])) {
					switch(strtolower($param[0])) {
					
						case "set":
							if(isset($param[1])) {
							
								$n = $param[1];
								$w = $this->cfg->get("words");
								
								if($sender instanceof Player) {
									if(strlen($n) < $w) { // character filter
										if($this->bans->get($n)) {
												$sender->sendMessage("You cant use username called §c$n");
										}
										else {
											$sender->setNameTag("** $n");
											$sender->setDisplayName("** $n");
											$sender->sendMessage("You nick has been set to $n");
										}
									}
									else {
										$sender->sendMessage("§cPlease dont use nicks above §a$w §cwords§f!");
									}
								}
							}
							else {
								$sender->sendMessage("/nicker set <name>");
							}
							return true;
						break;
						
						case "off":
							$name = $sender->getName();
							
							if($sender instanceof Player) {
							
								$sender->setNameTag($name);
								$sender->setDisplayName($name);
								$sender->sendMessage("You nick has been reset!");
							
							}
							return true;
						break;
						
						case "reload":
							if($sender->hasPermission("nick.command.admin")) {
								foreach($this->getServer()->getOnlinePlayers() as $p) {
									$p->setDisplayName($p->getName());
									$p->setNameTag($p->getName());
									$p->sendMessage("Your nick has been reset by an Admin!");
									$this->getLogger()->warning("[Nicker] All nick's has been reset!");
								}
							}
							else {
								$sender->getMessage("You have no permission to use this command.");
							}
							return true;
						break;
						
						case "add":
							if($sender->hasPermission("nick.command.admin")) {
								if(isset($param[1])) {
									$n = $param[1];
									
									$this->bans->set($n);
									$this->bans->save();
									$sender->sendMessage("You have added §c$n");
								}
								else {
									$sender->sendMessage("Usage: /nicker add <name>");
								}
							}
							else {
								$sender->sendMessage("You have no permission to use this command.");
							}
							return true;
						break;
						
						case "remove":
							if($sender->hasPermission("nick.command.admin")) {
								if(isset($param[1])) {
								
									$w = $param[1];
									
									if($this->bans->get($w)) {
										$this->bans->remove($w);
										$this->bans->save();
										$sender->sendMessage("You have removed §w");
									}
									else {
										$sender->sendMessage("$w doesn't exist on the system!");
									}
								}
								else {
									$sender->sendMessage("Usage: /nicker remove <name>");
								}
							}
							else {
								$sender->sendMessage("You have no permission to use this command.");
							}
							return true;
						break;
					}
				}
				else {
					$sender->sendMessage("/nicker < set | off | reload | add | remove >");
				}
			}
			else {
				$sender->sendMessage("You have no permission to use this command!");
			}
				return true;
			break;
		}
	}
	
	public function onJoin(PlayerQuitEvent $e) { // To prevent client crashing!
	
		$p = $e->getPlayer();
		$n = $p->getName();
		
		$p->setNameTag($n);
		$p->setDisplayName($n);
	
	}
	
	
	public function onDisable() {
	}
	
}
