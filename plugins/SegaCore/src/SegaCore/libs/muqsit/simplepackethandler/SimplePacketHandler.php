<?php

declare(strict_types=1);

namespace SegaCore\libs\muqsit\simplepackethandler;

use InvalidArgumentException;
use SegaCore\libs\muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use SegaCore\libs\muqsit\simplepackethandler\interceptor\PacketInterceptor;
use SegaCore\libs\muqsit\simplepackethandler\monitor\IPacketMonitor;
use SegaCore\libs\muqsit\simplepackethandler\monitor\PacketMonitor;
use pocketmine\event\EventPriority;
use pocketmine\plugin\Plugin;

final class SimplePacketHandler{

	public static function createInterceptor(Plugin $registerer, int $priority = EventPriority::NORMAL, bool $handleCancelled = false) : IPacketInterceptor{
		if($priority === EventPriority::MONITOR){
			throw new InvalidArgumentException("Cannot intercept packets at MONITOR priority");
		}
		return new PacketInterceptor($registerer, $priority, $handleCancelled);
	}

	public static function createMonitor(Plugin $registerer, bool $handleCancelled = false) : IPacketMonitor{
		return new PacketMonitor($registerer, $handleCancelled);
	}
}