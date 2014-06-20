<?php
/**
*
* phpRivalsMOD [Portuguese]
*
* @package language
* @version $Id: info_mcp_rivals.php 2.0 rev.003 $
* @copyright (c) 2012 nenuc0
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

$lang	= array_merge($lang, array(
	'acl_m_rivals'			=> array('lang' => 'Só pode modificar partidas<br /><em>Se o phpRivalsMOD estiver ativo.</em>', 'cat' => 'misc'),
	'MCP_RIVALS'			=> 'phpRivalsMOD',
	'MCP_RIVALS_MAIN'		=> 'Página inicial',
	'MCP_RIVALS_EDIT_MATCH'	=> 'Editar uma partida disputada',
	'MCP_RIVALS_IP_WHOIS'	=> 'Mostrar IP whois',
	
	'LOG_MATCH_EDITED'		=> '<strong>Match ID:%s editado.</strong><br />» %s vs %s',
));

?>