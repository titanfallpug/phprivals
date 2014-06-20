<?php
/**
*
* phpRivalsMOD [Italian]
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
	'UCP_CAT_RIVALS'					=> 'Pannello di Controllo Clan',
	'UCP_RIVALS_ADD_CHALLENGE'			=> 'Sfida un altro clan',
	'UCP_RIVALS_CHALLENGES'				=> 'Accetta/Declina sfide ricevute',
	'UCP_RIVALS_GROUP_MEMBERS'			=> 'Gestisci membri del clan',
	'UCP_RIVALS_INVITE_MEMBERS'			=> 'Invita utente nel clan',
	'UCP_RIVALS_PENDING_MEMBERS'		=> 'Amministra utenti in attesa',
	'UCP_RIVALS_EDIT_GROUP'				=> 'Edita dati del clan',
	'UCP_RIVALS_FIND_GROUP'				=> 'Trova un clan',
	'UCP_RIVALS_MAIN'					=> 'Pagina principale',
	'UCP_RIVALS_MATCHCOMM'				=> 'Clan Short Messages',
	'UCP_RIVALS_MATCHES'				=> 'Riporta risultati sfide',
	'UCP_RIVALS_MATCHES_CONFIRM'  		=> 'Conferma risultati sfide',
	'UCP_RIVALS_MATCHES_MVP'       		=> 'Imposta dettaglio del match che si riporta',
	'UCP_RIVALS_MATCH_FINDER'			=> 'Cerca una sfida al volo',
	'UCP_RIVALS_TICKET'					=> 'Invia un ticket',
	'UCP_RIVALS_ADD_CHALLENGE_ONEONE'	=> 'Sfida un altro utente (1vs1)',
	'UCP_RIVALS_MATCHES_ONEONE'			=> 'Gestisci sfide 1vs1',
	'UCP_RIVALS_TOURNAMENTS'			=> 'Gestisci sfide tornei',
	'UCP_RIVALS_TOURNAMENTS_ONEONE'		=> 'Gestisci sfide tornei 1vs1',
	'UCP_RIVALS_MATCH_CHAT'				=> 'Match Chat',
	'UCP_RIVALS_SET_ROSTER'				=> 'Gestisci Roster LineUP'
	)
);

?>