<?php
/**
*
* phpRivalsMOD [Italian]
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
    'acl_a_rivals'					=> array('lang' => 'Può gestire phpRivals Mod', 'cat' => 'misc'),
	'ACP_CAT_RIVALS'				=> 'phpRivals Mod',
	'ACP_RIVALS'					=> 'phpRivals Mod',
	'ACP_RIVALS_ADD_LADDER'			=> 'Aggiungi Ladder',
	'ACP_RIVALS_ADD_SEASON'			=> 'Aggiungi Stagione',
	'ACP_RIVALS_EDIT_SEASON'		=> 'Edita Stagione',
	'ACP_RIVALS_ADD_PLATFORM'		=> 'Aggiungi una piattaforma',
	'ACP_RIVALS_ADD_TOURNAMENT'		=> 'Aggiungi Torneo',
	'ACP_RIVALS_CONFIGURE'			=> 'Configura',
	'ACP_RIVALS_MANAGE_SEASONS'		=> 'Amministra Stagione',
	'ACP_RIVALS_EDIT_BRACKETS'		=> 'Edita Tebellone',
	'ACP_RIVALS_EDIT_GROUPS'		=> 'Edita Clans',
	'ACP_RIVALS_EDIT_LADDERS'		=> 'Edita Ladders',
	'ACP_RIVALS_EDIT_LADDER'		=> 'Edita Ladder',
	'ACP_RIVALS_EDIT_SUBLADDER'		=> 'Edita Sub-Ladder',
	'ACP_RIVALS_EDIT_PLATFORMS'		=> 'Edita una piattaforma',
	'ACP_RIVALS_EDIT_TOURNAMENT'	=> 'Edita Torneo',
	'ACP_RIVALS_EDIT_TOURNAMENTS'	=> 'Edita Tornei',
	'ACP_RIVALS_MAIN'				=> 'Pagina principale',
	'ACP_RIVALS_REPORT_MATCH'		=> 'Riporta un match',
	'ACP_RIVALS_EDIT_MVP'           => 'Edita MVPs e Statistiche Utenti',
	'ACP_RIVALS_EDIT_RANDOM'        => 'Edita Mappe del giorno random',
	'ACP_RIVALS_ADD_MVP_LIST'       => 'Aggiungi una classifica MVP',
	'ACP_RIVALS_EDIT_MVP_LIST'      => 'Edita le classifiche MVP',
	'ACP_RIVALS_EDIT_RULES'         => 'Modifica Regole Ladder',
	
	'ACP_RIVALS_CONFIG_DECERTO'   		=> 'Imposta ladder Decerto',
	'ACP_RIVALS_EDIT_MATCH'				=> 'Amministra matches Clan',
	'ACP_RIVALS_EDIT_MATCH_USER'		=> 'Amministra matches User',
	'ACP_RIVALS_EDIT_MATCH_TOURNAMENT'	=> 'Amministra matches Torneo',
	'ACP_RIVALS_EDIT_DECERTO'			=> 'Amministra Decerto e CPC',
	'ACP_RIVALS_SEED_TOURNAMENT'		=> 'Invia Torneo',

	'LOG_TOURNAMENT_MATCH_UP'		=> 'Ha assegnato una vittoria a tavolino nel torneo <strong>%s</strong> a <strong>%s</strong>',
	'LOG_RIVALS_MATCH_EDITED'		=> 'Ha editato il match <strong>%s</strong> contro <strong>%s</strong>',
	'LOG_RIVALS_MATCH_RESETTED'		=> 'Ha resettato il match <strong>%s</strong> contro <strong>%s</strong>',
	'LOG_MATCH_EDITED'				=> '<strong>Match ID:%s editato.</strong><br />» %s vs %s',
));

$lang = array_merge($lang, array(
	'acl_m_rivals'	=> array('lang' => 'Può accedere al pannello moderatore Rivals MOD', 'cat' => 'misc'),
	'acl_a_rivals'	=> array('lang' => 'Può gestire Rivals MOD', 'cat' => 'misc')
));

?>