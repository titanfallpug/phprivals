<?php
/**
*
* phpRivalsMOD [Portuguese]
*
* @package language
* @version $Id: info_ucp_rivals.php 2.0 rev.003 $
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

$lang = array_merge($lang, array(
	'UCP_CAT_RIVALS'					=> 'Painel de controlo do Clan',
	'UCP_RIVALS_ADD_CHALLENGE'			=> 'Criar/adicionar uma partida',
	'UCP_RIVALS_CHALLENGES'				=> 'Gerir desafios',
	'UCP_RIVALS_GROUP_MEMBERS'			=> 'Gerir membros do clan',
    'UCP_RIVALS_INVITE_MEMBERS'			=> 'Convidar um membro para o clan',
	'UCP_RIVALS_PENDING_MEMBERS'		=> 'Gerir partidas pendentes',
	'UCP_RIVALS_EDIT_GROUP'				=> 'Editar clan',
	'UCP_RIVALS_FIND_GROUP'				=> 'Procurar clan',
	'UCP_RIVALS_MAIN'					=> 'Página inicial',
	'UCP_RIVALS_MATCHCOMM'				=> 'Mensagem curta do Clan',
	'UCP_RIVALS_MATCHES'				=> 'Reportar uma partida',
	'UCP_RIVALS_MATCHES_CONFIRM'    	=> 'Confirmar o resultado da partida',
	'UCP_RIVALS_MATCHES_MVP'       		=> 'Definir as estatisticas avançadas da partida',
	'UCP_RIVALS_MATCH_FINDER'			=> 'Partida Rápida/ Procurar Partida',
	'UCP_RIVALS_TICKET'					=> 'Emitir um Ticket',
	'UCP_RIVALS_ADD_CHALLENGE_ONEONE'	=> 'Criar/adicionar um desafio 1vs1',
	'UCP_RIVALS_MATCHES_ONEONE'			=> 'Gerir partidas 1vs1',
	'UCP_RIVALS_TOURNAMENTS'			=> 'Gerir partidas do torneio' ,
	'UCP_RIVALS_TOURNAMENTS_ONEONE'		=> 'Manage Tournaments matches 1vs1',
	'UCP_RIVALS_MATCH_CHAT'				=> 'Chat das partidas',
	'UCP_RIVALS_SET_ROSTER'				=> 'Gerir o Lineup da equipa'
	)
);

?>