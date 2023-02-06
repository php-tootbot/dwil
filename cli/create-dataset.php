<?php
/**
 * create-dataset.php
 *
 * @created      04.02.2023
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2023 smiley
 * @license      MIT
 */

use PHPTootBot\PHPTootBot\Util;

require_once __DIR__.'/../vendor/autoload.php';

// https://github.com/codemasher/dril-archive
$json = Util::loadJSON(__DIR__.'/../data/dril.json');
$data = [];

array_multisort(array_column($json->tweets, 'retweet_count'), SORT_DESC, SORT_NUMERIC, $json->tweets);

foreach($json->tweets as $tweet){

	if(
		$tweet->user_id !== 16298441
		|| $tweet->in_reply_to_status_id !== null
		|| isset($tweet->retweeted_status)
		|| isset($tweet->quoted_status)
		|| !empty($tweet->media)
		|| preg_match('#https?://#i', $tweet->text)
		|| preg_match('#@[a-z\d]+#i', $tweet->text)
	){
		continue;
	}

	$data[$tweet->id] = str_replace(['&amp;', '&gt;', '&lt;'], ['&', '>', '<'], $tweet->text);
}

Util::saveJSON(__DIR__.'/../data/dwil.json', $data);
