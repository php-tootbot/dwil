<?php
/**
 * common.php
 *
 * @created      03.12.2022
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2022 smiley
 * @license      MIT
 */

use chillerlan\DotEnv\DotEnv;
use PHPTootBot\dwil\dwil;
use PHPTootBot\dwil\dwilOptions;
use Psr\Log\LogLevel;

ini_set('date.timezone', 'UTC');
mb_internal_encoding('UTF-8');

require_once __DIR__.'/../vendor/autoload.php';

// if we're running on gh-actions, we're going to fetch the variables from gh.secrets,
// otherwise we'll load them from the local .env file
if(!isset($_SERVER['GITHUB_ACTIONS'])){
	(new DotEnv(__DIR__.'/../config', '.env', true))->load();
}

// invoke the options instance
// please excuse the IDE yelling: https://youtrack.jetbrains.com/issue/WI-66549
$options = new dwilOptions;

// HTTPOptionsTrait
$options->ca_info                  = realpath(__DIR__.'/../config/cacert.pem'); // https://curl.haxx.se/ca/cacert.pem
$options->user_agent               = 'phpTootBot/1.0 +https://github.com/php-tootbot/php-tootbot';
$options->retries                  = 3;

// OAuthOptionsTrait
// these settings are only required for authentication/remote token acquisition
#$options->key                      = getenv('MASTODON_KEY');
#$options->secret                   = getenv('MASTODON_SECRET');
#$options->callbackURL              = getenv('MASTODON_CALLBACK_URL');
#$options->sessionStart             = true;

// TootBotOptions
$options->instance                 = getenv('MASTODON_INSTANCE');
$options->apiToken                 = getenv('MASTODON_TOKEN');
$options->loglevel                 = LogLevel::INFO;
#$options->buildDir                 = __DIR__.'/../.build';
$options->dataDir                  = __DIR__.'/../data';
$options->tootVisibility           = 'public';

// dwilOptions
$options->topTweetProbability      = 15;
$options->topTweetLimit            = 100;

// UwuifyOptionsTrait
// all threshold values range [0-100], -1 to disable

// controls how much the text will be uwufied
$options->uwuModifier              = 70;

// these 6 options control the appearance of the several additional elements in spaces between words
// if the combined total value exceeds 100, each value will be adjusted to percentages ($val / $sum * 100)
$options->spaceModifierPunctuation = 5;
$options->spaceModifierEmoticon    = 5;
$options->spaceModifierEmoji       = 20;
$options->spaceModifierKaomoji     = -1;
$options->spaceModifierActions     = -1;
$options->spaceModifierStutter     = 5;

// these 3 options control text upper/lowercasing (same adjustment as above)
$options->lowercaseModifier        = 5;
$options->uppercaseModifier        = 5;
$options->mockingcaseModifier      = 5;

$options->mockingModifier          = 60;
$options->exclamationModifier      = 10;
$options->stutterEllipseModifier   = -1;

$options->exclamationMinLength     = 2;
$options->exclamationMaxLength     = 5;

$options->uwuMap = [
	'/(?:r|l)/'         => 'w',
	'/(?:R|L)/'         => 'W',
	'/^you\'?(r|w)e$/i' => 'ur', // before/after modification
	'/^you$/i'          => 'u',
	'/^a(r|w)e$/i'      => 'r',
	'/n([aeiou])/'      => 'ny$1',
	'/N([aeiou])/'      => 'Ny$1',
	'/N([AEIOU])/'      => 'NY$1',
	'/ove/i'            => 'uv',
#	'/their/i'          => 'deyw', // @todo
	'/^they$/i'         => 'dey',
	'/^the$/i'          => 'da',
	'/^this$/i'         => 'dis',
#	'/th/i'             => 'd',
	'/ou/'              => 'ow',
	'/Ou/'              => 'Ow',
	'/OU/'              => 'OW',
];

// invoke the bot instance and post
(new dwil($options))->post();

exit(0);
