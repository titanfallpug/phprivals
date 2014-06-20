<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_rivals_info
{
	function module()
	{
		return	array(
			'filename'	=> 'acp_rivals',
			'title'		=> 'ACP_CAT_RIVALS',
			'version'	=> '2.0',
			'modes' 	=> array(
				'main' 	=> array('title' => 'ACP_RIVALS_MAIN', 'auth' => 'acl_a_rivals', 'enable' => true, 'display' => true, 'cat' => array('ACP_RIVALS')),
				'add_season'		=> array('title' => 'ACP_RIVALS_ADD_SEASON', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS')),
				'add_platform'		=> array('title' => 'ACP_RIVALS_ADD_PLATFORM', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'add_ladder'		=> array('title' => 'ACP_RIVALS_ADD_LADDER', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'add_tournament'	=> array('title' => 'ACP_RIVALS_ADD_TOURNAMENT', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'configure'			=> array('title' => 'ACP_RIVALS_CONFIGURE', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'manage_seasons'	=> array('title' => 'ACP_RIVALS_MANAGE_SEASONS', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_season'		=> array('title' => 'ACP_RIVALS_EDIT_SEASON', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS')),
				'edit_brackets'		=> array('title' => 'ACP_RIVALS_EDIT_BRACKETS', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS')),
				'edit_platforms'	=> array('title' => 'ACP_RIVALS_EDIT_PLATFORMS', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_ladders'		=> array('title' => 'ACP_RIVALS_EDIT_LADDERS', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_ladder'		=> array('title' => 'ACP_RIVALS_EDIT_LADDER', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS')),
				'edit_subladder'	=> array('title' => 'ACP_RIVALS_EDIT_SUBLADDER', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS')),
				'edit_groups'		=> array('title' => 'ACP_RIVALS_EDIT_GROUPS', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_tournaments'	=> array('title' => 'ACP_RIVALS_EDIT_TOURNAMENTS', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				//'report_match'		=> array('title' => 'ACP_RIVALS_REPORT_MATCH', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_rules'		=> array('title' => 'ACP_RIVALS_EDIT_RULES', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_match'		=> array('title' => 'ACP_RIVALS_EDIT_MATCH', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_match_user'	=> array('title' => 'ACP_RIVALS_EDIT_MATCH_USER', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_match_tourn'	=> array('title' => 'ACP_RIVALS_EDIT_MATCH_TOURNAMENT', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'add_mvp_list'		=> array('title' => 'ACP_RIVALS_ADD_MVP_LIST', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_mvp_list'		=> array('title' => 'ACP_RIVALS_EDIT_MVP_LIST', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_mvp'			=> array('title' => 'ACP_RIVALS_EDIT_MVP', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_random'		=> array('title' => 'ACP_RIVALS_EDIT_RANDOM', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_decerto'		=> array('title' => 'ACP_RIVALS_EDIT_DECERTO', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => true, 'cat' => array('ACP_RIVALS')),
				'edit_tournament'	=> array('title' => 'ACP_RIVALS_EDIT_TOURNAMENT', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS')),
				'seed_tournament'	=> array('title' => 'ACP_RIVALS_SEED_TOURNAMENT', 'enable' => true, 'auth' => 'acl_a_rivals', 'display' => false, 'cat' => array('ACP_RIVALS'))
				
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