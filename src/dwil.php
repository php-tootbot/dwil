<?php
/**
 * Class dwil
 *
 * @created      03.02.2023
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2023 smiley
 * @license      MIT
 */

namespace PHPTootBot\dwil;

use chillerlan\HTTP\Utils\MessageUtil;
use codemasher\Uwuify\Uwuify;
use PHPTootBot\PHPTootBot\TootBot;
use PHPTootBot\PHPTootBot\TootBotInterface;
use PHPTootBot\PHPTootBot\Util;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use function array_rand;
use function is_object;
use function mb_strlen;
use function sprintf;
use function strtotime;

/**
 *
 */
class dwil extends TootBot{

	protected Uwuify $uwuifier;
	protected array  $tweets;
	protected array  $posted;
	protected int    $currentTweetID;

	/**
	 * dwil constwuctow ÚwÚ
	 */
	public function __construct(dwilOptions $options){
		parent::__construct($options);

		$this->uwuifier = new Uwuify($this->options);
		$this->tweets   = Util::loadJSON($this->options->dataDir.'/dwil.json', true);
		$this->posted   = Util::loadJSON($this->options->dataDir.'/posted.json');
	}

	/**
	 * save the posted list on exit
	 */
	public function __destruct(){
		Util::saveJSON($this->options->dataDir.'/posted.json', $this->posted);
	}

	/**
	 * remove used lines from the data pool
	 */
	protected function updatePool():void{
		foreach($this->posted as $toot){
			unset($this->tweets[$toot->tweetID]);
		}
	}

	/**
	 * fetch an item from the data pool
	 *
	 * @throws \RuntimeException
	 */
	protected function getPoolItem():string{
		$this->updatePool();

		if(empty($this->tweets)){
			throw new RuntimeException('data pool is empty');
		}

		$this->currentTweetID = (int)array_rand($this->tweets);

		return $this->tweets[$this->currentTweetID];
	}

	/**
	 * fetch a random line from the pool
	 *
	 * @throws \RuntimeException
	 */
	protected function getUwuifiedLine():string{
		$tweet = $this->getPoolItem();
		$i     = 0;
		$retry = 0;

		// try to stay under the character limit (500 multibyte characters)
		while($retry < 3){
			$line = $this->uwuifier->uwuify($tweet);

			// yay, we're below the character limit! (also after some extra loop runs for entropy™)
			if(mb_strlen($line) < 500 && $i > 10){
				$this->logger->info($tweet);
				$this->logger->info($line);

				return $line;
			}

			// ok, this line probably can't fit, try another one
			if($i > 1000){
				$tweet = $this->getPoolItem();
				$i     = 0;

				$retry++;
				continue;
			}

			$i++;
		}

		// this is taking too long now...
		throw new RuntimeException('could not generate a line that fits the character limit');
	}

	/**
	 * @inheritDoc
	 */
	public function post():TootBotInterface{

		$params = [
			'status'     => $this->getUwuifiedLine(),
			'visibility' => $this->options->tootVisibility,
			'language'   => 'en',
		];

		$this->submitToot($params);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function submitTootSuccess(ResponseInterface $response):void{
		$json = MessageUtil::decodeJSON($response);

		$this->posted[] = [
			'tootID'     => (int)$json->id,
			'created_at' => strtotime($json->created_at),
			'tweetID'    => $this->currentTweetID,
		];

		$this->logger->info(sprintf('posted: %s/@dwil/%s', rtrim($this->options->instance, '/'), $json->id));

		exit(0);
	}

	/**
	 * @inheritDoc
	 */
	protected function submitTootFailure(ResponseInterface $response):void{
		$json = MessageUtil::decodeJSON($response);

		if(is_object($json) && isset($json->error)){
			$this->logger->error($json->error);
		}

		exit(255);
	}

}
