<?php
/**
*
* phpRivalsMOD [French]
*
* @package language
* @version $Id: info_acp_rivals.php 2.0 rev.003 $
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
	'acl_a_rivals'                      => array('lang' => 'Peut afficher phpRivals administrateur Mod', 'cat' => 'misc'),
	'ACP_CAT_RIVALS'                    => 'phpRivals Mod',
	'ACP_RIVALS'                        => 'phpRivals Mod',
	'ACP_RIVALS_ADD_LADDER'             => 'Ajouter un Ladder',
	'ACP_RIVALS_ADD_SEASON'             => 'Ajouter une ligue',
	'ACP_RIVALS_EDIT_SEASON'            => 'Editer une ligue',
	'ACP_RIVALS_ADD_PLATFORM'           => 'Ajouter une ligue',
	'ACP_RIVALS_ADD_TOURNAMENT'         => 'Ajouter un tournoi',
	'ACP_RIVALS_CONFIGURE'              => 'Configure',
	'ACP_RIVALS_MANAGE_SEASONS'         => 'Gérer ligue',
	'ACP_RIVALS_EDIT_BRACKETS'          => 'Editer Brackets',
	'ACP_RIVALS_EDIT_GROUPS'            => 'Editer Clans',
	'ACP_RIVALS_EDIT_LADDERS'           => 'Editer les Ladders',
	'ACP_RIVALS_EDIT_LADDER'            => 'Editer le Ladder',
	'ACP_RIVALS_EDIT_SUBLADDER'         => 'Editer Sub-Ladder',
	'ACP_RIVALS_EDIT_PLATFORMS'         => 'Editer ligue',
	'ACP_RIVALS_EDIT_TOURNAMENT'        => 'Editer Tournoi',
	'ACP_RIVALS_EDIT_TOURNAMENTS'       => 'Editer Tournois',
	'ACP_RIVALS_MAIN'                   => 'page d\'accueil',
	'ACP_RIVALS_REPORT_MATCH'           => 'Signaler un match',
	'ACP_RIVALS_EDIT_MVP'               => 'Modifier les stats utilisateur MVP ',
	'ACP_RIVALS_ADD_MVP_LIST'           => 'Ajouter un tableau MVP',
	'ACP_RIVALS_EDIT_MVP_LIST'          => 'Editer un tableau MVP',
	'ACP_RIVALS_EDIT_RANDOM'            => 'Editer la map aléatoire du jour',
	'ACP_RIVALS_EDIT_RULES'             => 'Editer les règles du Ladder',
       
	'ACP_RIVALS_CONFIG_DECERTO'         => 'Set Decertos’s Ladder',
	'ACP_RIVALS_EDIT_MATCH'             => 'Gérer matches de clan',
	'ACP_RIVALS_EDIT_MATCH_USER'        => 'Gérer matches de l’utilisateur',
	'ACP_RIVALS_EDIT_MATCH_TOURNAMENT'  => 'Gérer matches du tournois',
	'ACP_RIVALS_EDIT_DECERTO'           => 'Manage Decerto e CPC',
	'ACP_RIVALS_SEED_TOURNAMENT'        => 'Seed Tournament',
       
	'LOG_TOURNAMENT_MATCH_UP'           => 'A accordé une victoire acquise dans le tournoi <strong>%s</strong> au <strong>%s</strong>',
	'LOG_RIVALS_MATCH_EDITED'           => 'modification de le match <strong>%s</strong> vs <strong>%s</strong>',
	'LOG_RIVALS_MATCH_RESETTED'         => 'rétablir le match <strong>%s</strong> vs <strong>%s</strong>',
	'LOG_MATCH_EDITED'					=> '<strong>Match ID:%s édité.</strong><br />» %s vs %s',
));

$lang = array_merge($lang, array(
	'acl_m_rivals'	=> array('lang' => 'Può accedere al pannello moderatore Rivals MOD', 'cat' => 'misc'),
	'acl_a_rivals'	=> array('lang' => 'Può gestire Rivals MOD', 'cat' => 'misc')
));

?>