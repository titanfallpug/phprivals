<?php
/**
*
* phpRivalsMOD [English]
*
* @package language
* @version $Id: info_acp_rivals.php 2.0 rev.003 $
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
	'acl_a_rivals'					=> array('lang' => 'Can view phpRivals Mod admin', 'cat' => 'misc'),
	'ACP_CAT_RIVALS'				=> 'phpRivals Mod',
	'ACP_RIVALS'					=> 'phpRivals Mod',
	'ACP_RIVALS_ADD_LADDER'			=> 'Add a Ladder',
	'ACP_RIVALS_ADD_SEASON'			=> 'Add a Season',
	'ACP_RIVALS_EDIT_SEASON'		=> 'Edit Season',
	'ACP_RIVALS_ADD_PLATFORM'		=> 'Add a Platform',
	'ACP_RIVALS_ADD_TOURNAMENT'		=> 'Add a Tournament',
	'ACP_RIVALS_CONFIGURE'			=> 'Configure',
	'ACP_RIVALS_MANAGE_SEASONS'		=> 'Menage Seasons',
	'ACP_RIVALS_EDIT_BRACKETS'		=> 'Edit Brackets',
	'ACP_RIVALS_EDIT_GROUPS'		=> 'Edit Clans',
	'ACP_RIVALS_EDIT_LADDERS'		=> 'Edit Ladders',
	'ACP_RIVALS_EDIT_LADDER'		=> 'Edit Ladder',
	'ACP_RIVALS_EDIT_SUBLADDER'		=> 'Edit Sub-Ladder',
	'ACP_RIVALS_EDIT_PLATFORMS'		=> 'Edit Platforms',
	'ACP_RIVALS_EDIT_TOURNAMENT'	=> 'Edit Tournament',
	'ACP_RIVALS_EDIT_TOURNAMENTS'	=> 'Edit Tournaments',
	'ACP_RIVALS_MAIN'				=> 'Main Page',
	'ACP_RIVALS_REPORT_MATCH'		=> 'Report a Match',
	'ACP_RIVALS_EDIT_MVP'           => 'Edit user MVPs e stats',
	'ACP_RIVALS_ADD_MVP_LIST'       => 'Add a MVP chart',
	'ACP_RIVALS_EDIT_MVP_LIST'      => 'Edit MVP chart',
	'ACP_RIVALS_EDIT_RANDOM'        => 'Edit day random map',
	'ACP_RIVALS_EDIT_RULES'         => 'Edit Ladder Rules',
	
	'ACP_RIVALS_CONFIG_DECERTO'			=> 'Set Decertos’s Ladder',
	'ACP_RIVALS_EDIT_MATCH'				=> 'Menage Clan Matches',
	'ACP_RIVALS_EDIT_MATCH_USER'		=> 'Menage User Matches',
	'ACP_RIVALS_EDIT_MATCH_TOURNAMENT'	=> 'Menage tournaments matches',
	'ACP_RIVALS_EDIT_DECERTO'			=> 'Menage Decerto e CPC',
	'ACP_RIVALS_SEED_TOURNAMENT'		=> 'Seed Tournament',
	
	'LOG_TOURNAMENT_MATCH_UP'		=> 'Assigned a Staff victory in %s tournament to %s',
	'LOG_RIVALS_MATCH_EDITED'		=> 'Edit the match <strong>%s</strong> vs <strong>%s</strong>',
	'LOG_RIVALS_MATCH_RESETTED'		=> 'Reset the match <strong>%s</strong> vs <strong>%s</strong>',
	'LOG_MATCH_EDITED'				=> '<strong>Match ID:%s edited.</strong><br />» %s vs %s',
));

$lang = array_merge($lang, array(
	'acl_m_rivals'	=> array('lang' => 'Can use the moderator panel of Rivals MOD', 'cat' => 'misc'),
	'acl_a_rivals'	=> array('lang' => 'Can manage Rivals MOD', 'cat' => 'misc')
));

?>