<?php
/**
*
* phpRivalsMOD [Portuguese]
*
* @package language
* @version $Id: info_acp_rivals.php 2.0 rev.003 $
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
	'acl_a_rivals'					=> array('lang' => 'Consegue ver a administração de phpRivals Mod', 'cat' => 'misc'),
	'ACP_CAT_RIVALS'				=> 'phpRivals Mod',
	'ACP_RIVALS'					=> 'phpRivals Mod',
	'ACP_RIVALS_ADD_LADDER'			=> 'Adicionar uma ladder',
	'ACP_RIVALS_ADD_SEASON'			=> 'Adicionar uma época',
	'ACP_RIVALS_EDIT_SEASON'		=> 'Editar época',
	'ACP_RIVALS_ADD_PLATFORM'		=> 'Adicionar uma plataforma',
	'ACP_RIVALS_ADD_TOURNAMENT'		=> 'Adicionar um torneio',
	'ACP_RIVALS_CONFIGURE'			=> 'Configurar',
	'ACP_RIVALS_MANAGE_SEASONS'		=> 'Gerir épocas',
	'ACP_RIVALS_EDIT_BRACKETS'		=> 'Editar brackets',
	'ACP_RIVALS_EDIT_GROUPS'		=> 'Editar clans',
	'ACP_RIVALS_EDIT_LADDERS'		=> 'Editar ladders',
	'ACP_RIVALS_EDIT_LADDER'		=> 'Editar ladder',
	'ACP_RIVALS_EDIT_SUBLADDER'		=> 'Editar sub-Ladder',
	'ACP_RIVALS_EDIT_PLATFORMS'		=> 'Editar platforms',
	'ACP_RIVALS_EDIT_TOURNAMENT'	=> 'Editar torneio',
	'ACP_RIVALS_EDIT_TOURNAMENTS'	=> 'Editar torneios',
	'ACP_RIVALS_MAIN'				=> 'Página inicial',
	'ACP_RIVALS_REPORT_MATCH'		=> 'Reportar uma partida',
	'ACP_RIVALS_EDIT_MVP'           => 'Editar membros MVPs e estatísticas',
	'ACP_RIVALS_ADD_MVP_LIST'       => 'Adicionar uma tabela de MVP',
	'ACP_RIVALS_EDIT_MVP_LIST'      => 'Editar tabela de MVP',
	'ACP_RIVALS_EDIT_RANDOM'        => 'Editar o mapa aliatório dário',
	'ACP_RIVALS_EDIT_RULES'         => 'Editar regras da ladder',
	
	'ACP_RIVALS_CONFIG_DECERTO'			=> 'Adionar modo Decertos’s à ladder',
	'ACP_RIVALS_EDIT_MATCH'				=> 'Gerir partidas de clans',
	'ACP_RIVALS_EDIT_MATCH_USER'		=> 'Gerir partidas de membros',
	'ACP_RIVALS_EDIT_MATCH_TOURNAMENT'	=> 'Gerir partidas dos torneios',
	'ACP_RIVALS_EDIT_DECERTO'			=> 'Gerir Decerto e CPC',
	'ACP_RIVALS_SEED_TOURNAMENT'		=> 'Começar torneio',
	
	'LOG_TOURNAMENT_MATCH_UP'		=> 'Assign a Staff victory in %s tournament to %s',
	'LOG_RIVALS_MATCH_EDITED'		=> 'Editar partida <strong>%s</strong> vs <strong>%s</strong>',
	'LOG_RIVALS_MATCH_RESETTED'		=> 'Resetar a partida <strong>%s</strong> vs <strong>%s</strong>',
	'LOG_MATCH_EDITED'				=> '<strong>Match ID:%s editado.</strong><br />» %s vs %s',
));

$lang = array_merge($lang, array(
	'acl_m_rivals'	=> array('lang' => 'Pode acessar o painel de moderador MOD Rivals', 'cat' => 'misc'),
	'acl_a_rivals'	=> array('lang' => 'Ele pode lidar com Rivals MOD', 'cat' => 'misc')
));

?>