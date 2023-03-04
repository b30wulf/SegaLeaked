<?php

namespace SegaCore\Core\bot\FistBot;

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\entity\Attribute;
use pocketmine\block\Liquid;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\Flowable;
use pocketmine\color\Color;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\utils\TextFormat;
use SegaCore\Core\EventListener;

class FistMediumBot extends Human
{
    const ATTACK_COOLDOWN = 6;
    const REACH_DISTANCE = 3.5;
    const LOW_REACH_DISTANCE = 0.6;
    const ACCURACY = 50;

    public $name = "§bFistBot§f(§6Medium§f)";
    /** @var Player */
    public $target = null;
    public $duel = null;
    public $deactivated = false;
    public $refilling = false;
    public $refillTicks = 0;
    public $randomPosition = null;
    public $newLocTicks = 60;
    public $gravity = 0.0072;
    public $jumpTicks = 10;
    public $attackcooldown = self::ATTACK_COOLDOWN;
    public $reachDistance = self::REACH_DISTANCE;
    public $safeDistance = 2.5;
    public $attackDamage = 2;
    public $speed = 0.60;
    public $startingHealth = 20;

    public function __construct(Location $position, Skin $skin, Player $target)
    {
        parent::__construct($position, $skin);
        $this->setTarget($target);
        $this->setMaxHealth($this->startingHealth);
        $this->setHealth($this->startingHealth);
        $this->setNametag($this->name);
        $this->generateRandomPosition();
        $this->giveItems();
    }

    public function getType()
    {
        return "FistMediumBot";
    }

    public function setTarget($player)
    {
        $target = $player;
        $this->target = $target;
    }

    public function hasTarget(): bool
    {
        if ($this->target === null) return false;
        $target = $this->getTarget();
        if ($target === null) return false;
        $player = $this->getTarget();
        return !$player->isSpectator();
    }

    public function getTarget()
    {
        return Server::getInstance()->getPlayerExact($this->target->getName());
    }

    private function isDeactivated(): bool
    {
        return $this->deactivated === true;
    }

    public function setDeactivated(bool $result = true)
    {
        $this->deactivated = $result;
    }

    private function isRefilling(): bool
    {
        return $this->refilling === true;
    }

    public function setRefilling(bool $result = true)
    {
        $this->refilling = $result;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameTag(): string
    {
        return $this->name;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        parent::entityBaseTick($tickDiff);
        if ($this->isDeactivated()) return false;
        if (!$this->isAlive()) {
            if (!$this->closed) $this->flagForDespawn();
            return false;
        }
        $this->setNametag($this->getNameTag() . "\n" . " §7[§l§a" . round($this->getHealth(), 1) . "§r§7]");
        if ($this->hasTarget()) {
         /*   if($this->getPosition()->asVector3()->distance($this->target->getPosition()->asVector3()) > 20) {
                $this->pearl();
            }*/
            if ($this->getWorld()->getFolderName() == $this->getTarget()->getWorld()->getFolderName()) {
                return $this->attackTarget();
            } else {
                $this->setDeactivated();
                if (!$this->closed) $this->flagForDespawn();
            }
        } else {
            $this->setDeactivated();
            if (!$this->closed) $this->flagForDespawn();
            return false;
        }
        if ($this->jumpTicks > 0) $this->jumpTicks--;
        if ($this->newLocTicks > 0) $this->newLocTicks--;
        if (!$this->isOnGround()) {
            if ($this->motion->y > -$this->gravity * 1) { //default is 4
                $this->motion->y = -$this->gravity * 1;
            } else {
                $this->motion->y += $this->isUnderwater() ? $this->gravity : -$this->gravity;
            }
        } else {
            $this->motion->y -= $this->gravity;
        }
        if ($this->isAlive() and !$this->isClosed()) $this->move($this->motion->x, $this->motion->y, $this->motion->z);
        if ($this->shouldJump()) $this->jump();
        if ($this->atRandomPosition() or $this->newLocTicks === 0) {
            $this->generateRandomPosition();
            $this->newLocTicks = 60;
            return true;
        }
        $position = $this->getRandomPosition();
        $x = $position->x - $this->getPosition()->getX();
        $y = $position->y - $this->getPosition()->getY();
        $z = $position->z - $this->getPosition()->getZ();
        if ($x * $x + $z * $z < 4 + $this->getScale()) {
            $this->motion->x = 0;
            $this->motion->z = 0;
        } else {
            $this->motion->x = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
            $this->motion->z = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
        }
        $this->getLocation()->yaw = rad2deg(atan2(-$x, $z));
        $this->getLocation()->pitch = 0;
        if ($this->isAlive() and !$this->isClosed()) $this->move($this->motion->x, $this->motion->y, $this->motion->z);
        if ($this->shouldJump()) $this->jump();
        if ($this->isAlive()) $this->updateMovement();
        return $this->isAlive();
    }

    public function attackTarget()
    {
        if ($this->isDeactivated()) return;
        if (!$this->isAlive()) {
            if (!$this->closed) $this->flagForDespawn();
            return;
        }
        $target = $this->getTarget();
        if ($target === null) {
            $this->target = null;
            return true;
        }
        if ($this->getWorld()->getFolderName() != $target->getWorld()->getFolderName()) {
            $this->setDeactivated();
            if (!$this->closed) $this->flagForDespawn();
        }
        $x = $target->getPosition()->getX() - $this->getPosition()->getX();
        $y = $target->getPosition()->getY() - $this->getPosition()->getY();
        $z = $target->getPosition()->getZ() - $this->getPosition()->getZ();
        if ($this->jumpTicks > 0) $this->jumpTicks--;
        if (!$this->isOnGround()) {
            $this->reachDistance = self::LOW_REACH_DISTANCE;
            if ($this->getPosition()->asVector3()->distance($target->getPosition()->asVector3()) <= 5) {
                $this->motion->x = $this->getSpeed() * 0.15 * -$x;
                $this->motion->z = $this->getSpeed() * 0.15 * -$z;
            }
            if ($this->motion->y > -$this->gravity * 1) { //default is 4
                $this->motion->y = -$this->gravity * 1;
            } else {
                $this->motion->y += $this->isUnderwater() ? $this->gravity : -$this->gravity;
            }
        } else {
            $this->reachDistance = self::REACH_DISTANCE;
            $this->motion->y -= $this->gravity;
        }
        if ($this->isAlive() and !$this->isClosed()) $this->move($this->motion->x, $this->motion->y, $this->motion->z);
        if ($this->shouldJump()) $this->jump();
        if ($this->getPosition()->asVector3()->distance($target->getPosition()->asVector3()) <= $this->safeDistance) {
            $this->motion->x = 0;
            $this->motion->z = 0;
        } else {
            if ($target->isSprinting()) {
                $this->motion->x = $this->getSpeed() * 0.20 * ($x / (abs($x) + abs($z)));
                $this->motion->z = $this->getSpeed() * 0.20 * ($z / (abs($x) + abs($z)));
            } else {
                $this->motion->x = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
                $this->motion->z = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
            }
        }
        $this->getLocation()->yaw = rad2deg(atan2(-$x, $z));
        $this->getLocation()->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
        if ($this->shouldJump()) $this->jump();
        if ($this->isAlive() and !$this->isClosed()) $this->move($this->motion->x, $this->motion->y, $this->motion->z);
        if (0 >= $this->attackcooldown) {
            if ($this->getPosition()->asVector3()->distance($target->getPosition()->asVector3()) <= $this->reachDistance) {
                $event = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getBaseAttackDamage());
                if (mt_rand(0, 100) <= self::ACCURACY) {
                    $target->attack($event);
                    $packet = new AnimatePacket();
                    $packet->action = AnimatePacket::ACTION_SWING_ARM;
                    $packet->actorRuntimeId = $this->getId();
                    $this->target->getNetworkSession()->sendDataPacket($packet);
                } else {
                    $volume = 0x10000000 * (min(30, 10) / 5);
                }
                $this->attackcooldown = self::ATTACK_COOLDOWN;
            }
        }
        if ($this->isAlive()) $this->updateMovement();
        $this->attackcooldown--;
        $this->lookAt($target->getPosition()->asVector3());
        return $this->isAlive();
    }

    public function attack($source): void
    {
        parent::attack($source);
        if ($source->isCancelled()) {
            $source->cancel();
            return;
        }
        if ($source instanceof EntityDamageByEntityEvent) {
            $killer = $source->getDamager();
            if ($killer instanceof Player) {
                if ($killer->isSpectator()) {
                    $source->cancel();
                    return;
                }
                $deltaX = $this->getPosition()->getX() - $killer->getPosition()->getX();
                $deltaZ = $this->getPosition()->getZ() - $killer->getPosition()->getZ();
                $this->knockBack($deltaX, $deltaZ);
                $this->lookAt($this->target->getPosition()->asVector3());
            }
        }
    }

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        $xzKB = 0.341;
        $yKb = 0.386;
        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > $this->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;
            $motion = clone $this->motion;
            $motion->x /= 2;
            $motion->y /= 2;
            $motion->z /= 2;
            $motion->x += $x * $f * $xzKB;
            $motion->y += $yKb;
            $motion->z += $z * $f * $xzKB;
            if ($motion->y > $yKb) {
                $motion->y = $yKb;
            }
            if ($this->isAlive() and !$this->isClosed()) $this->move($motion->x * 1.60, $motion->y * 1.80, $motion->z * 1.60);
        }
    }

    public function kill(): void
    {
        $this->getArmorInventory()->clearAll();
        $this->getInventory()->clearAll();
        $player = $this->target;
        $player->sendMessage("" . "\n" . TextFormat::GREEN . "Winner: " . TextFormat::RESET . $player->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $this->getName() . "\n". "");
        EventListener::teleportLobby($player);
        parent::kill();
    }

    public function atRandomPosition()
    {
        return $this->getRandomPosition() == null or $this->getPosition()->asVector3()->distance($this->getRandomPosition()) <= 2;
    }

    public function getRandomPosition()
    {
        return $this->randomPosition;
    }

    public function generateRandomPosition()
    {
        $minX = $this->getPosition()->getFloorX() - 8;
        $minY = $this->getPosition()->getFloorY() - 8;
        $minZ = $this->getPosition()->getFloorZ() - 8;
        $maxX = $minX + 16;
        $maxY = $minY + 16;
        $maxZ = $minZ + 16;
        $level = $this->getWorld();
        for ($attempts = 0; $attempts < 16; ++$attempts) {
            $x = mt_rand($minX, $maxX);
            $y = mt_rand($minY, $maxY);
            $z = mt_rand($minZ, $maxZ);
            while ($y >= 0 and !$level->getBlockAt($x, $y, $z)->isSolid()) {
                $y--;
            }
            if ($y < 0) {
                continue;
            }
            $blockUp = $level->getBlockAt($x, $y + 1, $z);
            $blockUp2 = $level->getBlockAt($x, $y + 2, $z);
            if ($blockUp->isSolid() or $blockUp instanceof Liquid or $blockUp2->isSolid() or $blockUp2 instanceof Liquid) {
                continue;
            }
            break;
        }
        $this->randomPosition = new Vector3($x, $y + 1, $z);
    }

    public function getSpeed()
    {
        return ($this->isUnderwater() ? $this->speed / 2 : $this->speed);
    }

    public function getBaseAttackDamage()
    {
        return $this->attackDamage;
    }

    public function getFrontBlock($y = 0)
    {
        $dv = $this->getDirectionVector();
        $pos = $this->getPosition()->asVector3()->add($dv->x * $this->getScale(), $y + 1, $dv->z * $this->getScale())->round();
        return $this->getWorld()->getBlock($pos);
    }

    public function shouldJump()
    {
        if ($this->jumpTicks > 0) return false;
        if (!$this->isOnGround()) return false;
        return $this->isCollidedHorizontally or
            ($this->getFrontBlock()->getId() != 0 or $this->getFrontBlock(-1) instanceof Stair) or
            ($this->getWorld()->getBlock($this->getPosition()->asVector3()->add(0, -0, 5)) instanceof Slab and
                (!$this->getFrontBlock(-0.5) instanceof Slab and $this->getFrontBlock(-0.5)->getId() != 0)) and
            $this->getFrontBlock(1)->getId() == 0 and
            $this->getFrontBlock(2)->getId() == 0 and
            !$this->getFrontBlock() instanceof Flowable and
            $this->jumpTicks == 0;
    }

    public function getJumpMultiplier()
    {
        return 64;
        if ($this->getFrontBlock() instanceof Slab or $this->getFrontBlock() instanceof Stair or $this->getWorld()->getBlock($this->getPosition()->asVector3()->subtract(0, 0.5, 0)->round()) instanceof Slab and $this->getFrontBlock()->getId() != 0) {
            $fb = $this->getFrontBlock();
            if ($fb instanceof Slab and $fb->getDamage() & 0x08 > 0) return 8;
            if ($fb instanceof Stair and $fb->getDamage() & 0x04 > 0) return 8;
            return 16;
        }
        return 32;
    }

    public function jump(): void
    {
        if ($this->jumpTicks > 0) return;
        $this->motion->y = $this->gravity * $this->getJumpMultiplier();
        if ($this->isAlive() and !$this->isClosed()) $this->move($this->motion->x * 1.15, $this->motion->y, $this->motion->z * 1.15);
        $this->jumpTicks = 10; //($this->getJumpMultiplier()==4 ? 2:5);
    }

    public function giveItems(): void
    {
        $item = new ItemFactory();
        $this->getInventory()->clearAll();
        $food = $item->get(ItemIds::STEAK, 0, 1);
        $this->getInventory()->addItem($food);
        $this->getInventory()->setHeldItemIndex(0);
    }
}
