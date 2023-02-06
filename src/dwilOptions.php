<?php
/**
 * Class dwilOptions
 *
 * @created      04.02.2023
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2023 smiley
 * @license      MIT
 */

namespace PHPTootBot\dwil;

use codemasher\Uwuify\UwuifyOptionsTrait;
use PHPTootBot\PHPTootBot\TootBotOptions;

/**
 *
 */
class dwilOptions extends TootBotOptions{
	use UwuifyOptionsTrait;

	protected int $topTweetProbability = 10;
	protected int $topTweetLimit       = 100;
}
