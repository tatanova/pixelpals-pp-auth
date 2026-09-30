<?php
namespace PP_Auth;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\block\BlockBreakEvent;

class Auth extends PluginBase implements Listener {
    public $file, $check;

    public function onEnable(){
   $this->getServer()->getPluginManager()->registerEvents($this, $this);
   if(!is_dir($this->getDataFolder())){     
   @mkdir($this->getDataFolder());
   }
   $this->file = new Config($this->getDataFolder()."accounts.pp", Config::YAML);
   
}
    public function onJoin(PlayerJoinEvent $e){
        $igrok = $e->getPlayer();
        $e->setJoinMessage("");
        $lname = strtolower($igrok->getName());
        if(!isset($this->check[$lname])){
            if(isset($this->file->get("players")[$lname])){
                if($this->file->getNested("players.".$lname.".ip") == $igrok->getAddress() && $this->file->getNested("players.".$lname.".cid") == $igrok->getClientId()){
                    $igrok->sendMessage("§6* §ePixelPals §a|§f Welcome back!");
                    $this->check[$lname] = true;
                } else {
                    $igrok->sendMessage("§6* §ePixelPals §c|§f You need to write your password into the chat!");
                }
            } else {
                $igrok->sendMessage("§6* §ePixelPals §b|§f Please type password into the chat for registration!");
            }
        }
}
    public function onQuit(PlayerQuitEvent $e){
        $igrok = $e->getPlayer();
        $e->setQuitMessage("");
        $lname = strtolower($igrok->getName());
        if(isset($this->check[$lname])){
        unset($this->check[$lname]);
        }
    }
    public function onCommand(CommandSender $s, Command $cmd, $label, array $args){
        $lname = strtolower($s->getName());
        switch($cmd->getName()){
            case "respass":
            if($s->hasPermission("res.pass")){
              if(isset($args[0]) && isset($args[1])){
                if(isset($this->file->get("players")[strtolower($args[0])])){
                  if(strlen($args[1]) > 5){
                      $this->file->setNested("players.".strtolower($args[0]).".pass", $this->pEncode($args[1]));
                      if(isset($this->check[strtolower($args[0])])){
                          unset($this->check[strtolower($args[0])]);
                      }
                      $this->file->save();
                      $s->sendMessage("§6* §ePixelPals §a|§b {$args[0]}§f's password is now §b{$args[1]}§f!");
                     }else{
                         $s->sendMessage("§6* §ePixelPals §c|§f Please set the password length to at least 6!");
                     }
                 }else{
                     $s->sendMessage("§6* §ePixelPals §c|§f This player is not registered!");
                 }
             }else{
                 $s->sendMessage("§6* §ePixelPals §c|§f Use: /respass <player> <newPassword>");
             }
         }else{
             $s->sendMessage("§6* §ePixelPals §c|§f You are not allowed to use this!");
         }
            break;
            case "seepass":
            if($s->hasPermission("see.pass")){
              if(isset($args[0])){
                if(isset($this->file->get("players")[strtolower($args[0])])){
                    $filepass = $this->file->getNested("players.".strtolower($args[0]).".pass");
                    $s->sendMessage("§6* §ePixelPals §a|§b {$args[0]}§f's password in the file is §b".$filepass);
                }else{
                    $s->sendMessage("§6* §ePixelPals §c|§f This player is not registered!");
                }
              }else{
                  $s->sendMessage("§6* §ePixelPals §c|§f Use: /seepass <player>");
              }
            }else{
                $s->sendMessage("§6* §ePixelPals §c|§f You are not allowed to use this!");
            }
            break;
            case "changepassword":
            case "chpass":
            if($s->hasPermission("ch.pass")){
                if(isset($args[0])){
                    if(strlen($args[0]) > 5){
                        $this->file->setNested("players.".$lname.".pass", $this->pEncode($args[0]));
                        $s->sendMessage("§6* §ePixelPals §a|§f Your new password has been changed successfully!");
                        $this->file->save();
                    }else{
                        $s->sendMessage("§6* §ePixelPals §c|§f Please make your password length at least 6!");
                    }
                }else{
                    $s->sendMessage("§6* §ePixelPals §c|§f Use: /chpass <newPassword>");
                }
            }else{
                $s->sendMessage("§6* §ePixelPals §c|§f You are not allowed to use this!");
            }
        }
        return true;
    }
    public function onChat(PlayerChatEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        $msg = $e->getMessage();
        if(!isset($this->check[$lname])){
          if(isset($this->file->get("players")[$lname])){
              if($this->pEncode($msg) == $this->file->getNested("players.".$lname.".pass")){
                  $this->check[$lname] = true;
                  $igrok->sendMessage("§6* §ePixelPals §a|§f Password is correct! Welcome back to the server!");
                  $this->file->setNested("players.".$lname.".cid", $igrok->getClientId());
                  $this->file->setNested("players.".$lname.".ip", $igrok->getAddress());
                  $e->setCancelled(true);
                  $this->file->save();
             }else{
                 $e->setCancelled(true);
                 $igrok->sendMessage("§6* §ePixelPals §c|§f Wrong password!");
             }
          }elseif(strlen($msg) > 5){
              $this->file->setNested("players.".$lname.".pass", $this->pEncode($msg));
              $this->file->setNested("players.".$lname.".cid", $igrok->getClientId());
              $this->file->setNested("players.".$lname.".ip", $igrok->getAddress());
              $this->check[$lname] = true;
              $igrok->sendMessage("§6* §ePixelPals §a|§f You have successfully registered!");
              $e->setCancelled(true);
              $this->file->save();
              }else{
               $igrok->sendMessage("§6* §ePixelPals §c|§f Please make your password length at least 6!");
               $e->setCancelled(true);
          }
       }
       return true;
    }
    public function onDrop(PlayerDropItemEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        if(!isset($this->check[$lname])){
            $e->setCancelled(true);
        }
        if(!isset($this->file->get("players")[$lname])){
            $e->setCancelled(true);
            $igrok->sendTip("§ePlease enter your password\n§6into the chat §fto register§7.");
        }
    }
    public function onMove(PlayerMoveEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        if(!isset($this->check[$lname])){
            $e->setCancelled(true);
            $igrok->sendTip("§ePlease enter your password\n§6into the chat §fto continue§7.");
        }
        if(!isset($this->file->get("players")[$lname])){
            $e->setCancelled(true);
            $igrok->sendTip("§ePlease enter your password\n§6into the chat §fto register§7.");
        }
    }
    public function onCommandPreprocess(PlayerCommandPreprocessEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        $msg = $e->getMessage();
        if(!isset($this->check[$lname])){
            if(strpos($msg, "/") === 0){
                $e->setCancelled(true);
            if(!isset($this->file->get("players")[$lname])){
                $e->setCancelled(true);
                $igrok->sendMessage("§6* §ePixelPals §c|§f Please enter your password into the chat to register.");
             }
          }
       }
    }
    public function onInteract(PlayerInteractEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        if(!isset($this->check[$lname])){
            $e->setCancelled(true);
            $igrok->sendTip("§ePlease enter your password\n§6into the chat §fto continue§7.");
        }
        if(!isset($this->file->get("players")[$lname])){
            $e->setCancelled(true);
        }
    }
    public function onBreakBlock(BlockBreakEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        if(!isset($this->check[$lname])){
            $e->setCancelled(true);
            $igrok->sendTip("§ePlease enter your password\n§6into the chat §fto continue§7.");
        }
        if(!isset($this->file->get("players")[$lname])){
            $e->setCancelled(true);
        }
    }
    public function onToggleSprint(PlayerToggleSprintEvent $e){
        $igrok = $e->getPlayer();
        $lname = strtolower($igrok->getName());
        if(!isset($this->check[$lname])){
            $e->setCancelled(true);
            $igrok->sendTip("§ePlease enter your password\n§6into the chat §fto continue§7.");
        }
        if(!isset($this->file->get("players")[$lname])){
            $e->setCancelled(true);
        }
    }
    public function pEncode($input, $klyuch = "PixelPalsIsGood"){
        $klyuch = str_pad($klyuch, strlen($input), $klyuch);
        $output = '';
        for ($a = 0; $a < strlen($input); $a++) {
        $output .= $input[$a] ^ $klyuch[$a]; 
        }
        return base64_encode($output);
    }
    public function pDecode($input, $klyuch = "PixelPalsIsGood"){
        $input = base64_decode($input);
        $klyuch = str_pad($klyuch, strlen($input), $klyuch);
        $output = '';
        for ($x = 0; $x < strlen($input); $x++) {
        $output .= $input[$x] ^ $klyuch[$x];
        }
        return $output;
    }
}




