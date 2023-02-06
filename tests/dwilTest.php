<?php
/**
 * Class dwilTest
 *
 * @created      03.02.2023
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2023 smiley
 * @license      MIT
 */

namespace PHPTootBot\dwilTest;

use PHPTootBot\dwil\dwil;
use PHPTootBot\dwil\dwilOptions;
use PHPTootBot\PHPTootBot\TootBotInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class dwilTest extends TestCase{

	public function testInstance():void{
		$this::assertInstanceOf(TootBotInterface::class, new dwil(new dwilOptions(['dataDir' => __DIR__.'/../data'])));
	}

}
