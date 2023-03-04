<?php

declare(strict_types=1);

namespace juqn\pots\entity;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\InstantEffect;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\PotionType;
use pocketmine\player\Player;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\world\sound\PotionSplashSound;

class SplashPotion extends \pocketmine\entity\projectile\SplashPotion
{
    
    /** @var float */
    protected $gravity = 0.08;
    /** @var float */
    protected $drag = 0.028;
    
    protected function onHit(ProjectileHitEvent $event): void
    {
        $owning = $this->getOwningEntity();
        $effects = $this->getPotionEffects();
        $hasEffects = true;

        if (count($effects) === 0) {
            $particle = new PotionSplashParticle(PotionSplashParticle::DEFAULT_COLOR());
            $hasEffects = false;
        } else {
            $colors = [];

            foreach ($effects as $effect) {
                $level = $effect->getEffectLevel();
				
                for ($j = 0; $j < $level; ++$j)
                    $colors[] = $effect->getColor();
            }
            $particle = new PotionSplashParticle(Color::mix(...$colors));
        }
        $this->getWorld()->addParticle($this->location, $particle);
        $this->broadcastSound(new PotionSplashSound());
		
        if (!$hasEffects) {
            if ($event instanceof ProjectileHitBlockEvent and $this->getPotionType()->equals(PotionType::WATER())) {
                $blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());

                if ($blockIn->getId() === BlockLegacyIds::FIRE) 
                    $this->getWorld()->setBlock($blockIn->getPosition(), VanillaBlocks::AIR());
				
                foreach ($blockIn->getHorizontalSides() as $horizontalSide) {
                    if ($horizontalSide->getId() === BlockLegacyIds::FIRE)
					    $this->getWorld()->setBlock($horizontalSide->getPosition(), VanillaBlocks::AIR());
                }
            }
            return;
	    }
		
        if ($this->willLinger())
            return;
		
        foreach ($this->getWorld()->getNearbyEntities($this->boundingBox->expandedCopy(3, 3, 3), $this) as $entity) {
            if ($entity instanceof Player and $entity->isAlive()) {
                $distanceMultiplier = 0.580 * 1.75;
				
                foreach ($this->getPotionEffects() as $effect) {
                    if (!($effect->getType() instanceof InstantEffect)) {
                        $newDuration = (int) round($effect->getDuration() * 0.75 * $distanceMultiplier);
						
                        if ($newDuration < 20)
                            continue;
                        $effect->setDuration($newDuration);
                        $entity->getEffects()->add($effect);
                    } else
                        $effect->getType()->applyEffect($entity, $effect, $distanceMultiplier, $this);
                }
            }
        }
    }
    public function onBlockHit(ProjectileHitBlockEvent $event): void{
        $projectile = $event->getEntity();

        if($projectile instanceof SplashPotion && $projectile->getPotionType()->equals(PotionType::getAll())){
            $player = $projectile->getOwningEntity();

            if($player instanceof Player){
                $distance = $projectile->getPosition()->distance($player->getPosition());

                if($distance <= 3.5 && $player->isAlive()){
                    $health = $player->getHealth() + 4.5;
                    $player->setHealth($health > $player->getMaxHealth() ? $player->getMaxHealth() : $health);
                }
            }
        }
    }
}
