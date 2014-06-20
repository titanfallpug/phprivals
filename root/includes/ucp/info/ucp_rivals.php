<?php
/**
*
* @package ucp
* @version $Id$
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class ucp_rivals_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_rivals',
			'title'		=> 'UCP_CAT_RIVALS',
			'modes'		=> array(
				'main' 					=> array('title' => 'UCP_RIVALS_MAIN', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'edit_group'			=> array('title' => 'UCP_RIVALS_EDIT_GROUP', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'matchcomm'				=> array('title' => 'UCP_RIVALS_MATCHCOMM', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'group_members'			=> array('title' => 'UCP_RIVALS_GROUP_MEMBERS', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'pending_members'		=> array('title' => 'UCP_RIVALS_PENDING_MEMBERS', 'auth' => '', 'display' => false, 'cat' => array('UCP_CAT_RIVALS')),
				'invite_members'		=> array('title' => 'UCP_RIVALS_INVITE_MEMBERS', 'auth' => '', 'display' => false, 'cat' => array('UCP_CAT_RIVALS')),
				'set_roster'			=> array('title' => 'UCP_RIVALS_SET_ROSTER', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'add_challenge'			=> array('title' => 'UCP_RIVALS_ADD_CHALLENGE', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'find_group'			=> array('title' => 'UCP_RIVALS_FIND_GROUP', 'auth' => '', 'display' => false, 'cat' => array('UCP_CAT_RIVALS')),
				'challenges'			=> array('title' => 'UCP_RIVALS_CHALLENGES', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'matches'				=> array('title' => 'UCP_RIVALS_MATCHES', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'matchmvp'				=> array('title' => 'UCP_RIVALS_MATCHES_MVP', 'auth' => '', 'display' => false, 'cat' => array('UCP_CAT_RIVALS')),
				'matches_confirm'		=> array('title' => 'UCP_RIVALS_MATCHES_CONFIRM', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'match_finder'			=> array('title' => 'UCP_RIVALS_MATCH_FINDER', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'tournaments'			=> array('title' => 'UCP_RIVALS_TOURNAMENTS', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'ticket'				=> array('title' => 'UCP_RIVALS_TICKET', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'add_challenge_oneone'	=> array('title' => 'UCP_RIVALS_ADD_CHALLENGE_ONEONE', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'matches_oneone'		=> array('title' => 'UCP_RIVALS_MATCHES_ONEONE', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'tournaments_oneone'	=> array('title' => 'UCP_RIVALS_TOURNAMENTS_ONEONE', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS')),
				'match_chat'			=> array('title' => 'UCP_RIVALS_MATCH_CHAT', 'auth' => '', 'display' => true, 'cat' => array('UCP_CAT_RIVALS'))
			)
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>