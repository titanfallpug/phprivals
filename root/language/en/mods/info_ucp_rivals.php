<?php
/**
*
* phpRivalsMOD [English]
*
* @package language
* @version $Id: info_ucp_rivals.php 2.0 rev.003 $
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
   exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …

$lang = array_merge($lang, array(
	'UCP_CAT_RIVALS'					=> 'Clan Control Panel',
	'UCP_RIVALS_ADD_CHALLENGE'			=> 'Add a Challenge',
	'UCP_RIVALS_CHALLENGES'				=> 'Menage Challenges',
	'UCP_RIVALS_GROUP_MEMBERS'			=> 'Menage Members',
    'UCP_RIVALS_INVITE_MEMBERS'			=> 'Invite members',
	'UCP_RIVALS_PENDING_MEMBERS'		=> 'Menage pending members',
	'UCP_RIVALS_EDIT_GROUP'				=> 'Edit Clan',
	'UCP_RIVALS_FIND_GROUP'				=> 'Find a Clan',
	'UCP_RIVALS_MAIN'					=> 'Main Page',
	'UCP_RIVALS_MATCHCOMM'				=> 'Clan Short Messages',
	'UCP_RIVALS_MATCHES'				=> 'Report Matches',
	'UCP_RIVALS_MATCHES_CONFIRM'    	=> 'Confirm Matches result',
	'UCP_RIVALS_MATCHES_MVP'       		=> 'Sets matches advanced stats',
	'UCP_RIVALS_MATCH_FINDER'			=> 'Match Finder',
	'UCP_RIVALS_TICKET'					=> 'Issue a Ticket',
	'UCP_RIVALS_ADD_CHALLENGE_ONEONE'	=> 'Add a 1vs1 Challenge',
	'UCP_RIVALS_MATCHES_ONEONE'			=> 'Menage 1vs1 matches',
	'UCP_RIVALS_TOURNAMENTS'			=> 'Menage Tournaments matches' ,
	'UCP_RIVALS_TOURNAMENTS_ONEONE'		=> 'Menage Tournaments matches 1vs1',
	'UCP_RIVALS_MATCH_CHAT'				=> 'Matches chat',
	'UCP_RIVALS_SET_ROSTER'				=> 'Menage Roster LineUP'
	)
);

?>
