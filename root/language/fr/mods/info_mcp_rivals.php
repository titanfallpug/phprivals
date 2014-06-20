<?php
/**
*
* phpRivalsMOD [French]
*
* @package language
* @version $Id: info_mcp_rivals.php 2.0 rev.003 $
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
	'acl_m_rivals'			=> array('lang' => 'Peut gérer les matchs contestées<br /><em>Cela ne fonctionne que si phpRivalsMOD est active.</em>', 'cat' => 'misc'),
	'MCP_RIVALS'  			=> 'phpRivalsMOD',
	'MCP_RIVALS_MAIN' 		=> 'Page d’accueil',
	'MCP_RIVALS_EDIT_MATCH'	=> 'Modifier un match disputé',
	'MCP_RIVALS_IP_WHOIS'	=> 'Afficher whois IP',
   
	'LOG_MATCH_EDITED'		=> '<strong>Match ID:%s édité.</strong><br />» %s vs %s',
));

?>