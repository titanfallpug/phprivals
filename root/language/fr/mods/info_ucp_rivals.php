<?php
/**
*
* phpRivalsMOD [French]
*
* @package language
* @version $Id: info_ucp_rivals.php 2.0 rev.003 $
* @copyright (c) 2012 toxic
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
	'UCP_CAT_RIVALS'					=> 'Panel Joueur',
	'UCP_RIVALS_ADD_CHALLENGE'			=> 'Ajouter un défi',
	'UCP_RIVALS_CHALLENGES'				=> 'Gérer les défis',
	'UCP_RIVALS_GROUP_MEMBERS'			=> 'Gérer les membres',
	'UCP_RIVALS_INVITE_MEMBERS'			=> 'Invitez des membres',
	'UCP_RIVALS_PENDING_MEMBERS'		=> 'Gérer les membres en attente',
	'UCP_RIVALS_EDIT_GROUP'				=> 'Editer l’équipe',
	'UCP_RIVALS_FIND_GROUP'				=> 'Rechercher une équipe',
	'UCP_RIVALS_MAIN'					=> 'Page d’accueil',
	'UCP_RIVALS_MATCHCOMM'				=> 'Messages de l’équipe',
	'UCP_RIVALS_MATCHES'				=> 'Rapport du Matchs',
	'UCP_RIVALS_MATCHES_CONFIRM'		=> 'Confirmer les résultat du match',
	'UCP_RIVALS_MATCHES_MVP'			=> 'Réglez les résultat stats avancées',
	'UCP_RIVALS_MATCH_FINDER'			=> 'Rechercher un Match',
	'UCP_RIVALS_TICKET'					=> 'Envoyer un Ticket',
	'UCP_RIVALS_ADD_CHALLENGE_ONEONE'	=> 'Ajouter un défis 1vs1',
	'UCP_RIVALS_MATCHES_ONEONE'			=> 'Gérer un match 1vs1',
	'UCP_RIVALS_TOURNAMENTS'			=> 'Gérer un match de tournoi' ,
	'UCP_RIVALS_TOURNAMENTS_ONEONE'		=> 'Gérer un match de tournoi 1vs1',
	'UCP_RIVALS_MATCH_CHAT'				=> 'Match chat',
	'UCP_RIVALS_SET_ROSTER'				=> 'Gérer Roster LineUP'
	)
);

?>