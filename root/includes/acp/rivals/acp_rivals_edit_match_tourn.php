<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * EDIT MATCHES TOURNAMENTS
 * Called from acp_rivals with mode == 'edit_match_tourn'
 */
function acp_rivals_edit_match_tourn($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;
	
	$group		= new group();
	$tournament	= new tournament();
	$select		= (!empty($_POST['select'])) ? true : false;
	$submit		= (!empty($_POST['submit'])) ? true : false;
	$clan1		= (int) request_var('clan1', 0);
	$clan2		= (int) request_var('clan2', 0);
	$tournmt	= (int) request_var('tournament_id', 0);
	$round		= (int) request_var('round', 0);
	$error		= array();
	
	// LOAD TOURNAMENTS FOR SELECT
	$sql	= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_status = 2 ORDER BY tournament_name ASC";
	$result	= $db->sql_query($sql);
	$s 		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign it to the template.
		$template->assign_block_vars('tourn_select', array(
			'TNAME'	=> $row['tournament_name'],
			'TID'	=> $row['tournament_id'],
			'TONE'	=> ($row['tournament_userbased'] == 1) ? true : false
		));
		$s++;
	}
	$db->sql_freeresult($result);
	
	if ($select)
	{
		// Check for tournament match
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournmt} AND group_bracket = {$round} AND group_id = " . $clan1;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!empty($row['group_id']))
		{
			$opponent = $tournament->get_vsclan($tournmt, $clan1, $round, false);
			
			if ($opponent != $clan2)
			{
				$error[] = $user->lang['TOURN_CLAN_NOT_GOOD'];
			}
			else
			{
				// All it's ok, so parse data for template
				$template->assign_block_vars('tourn_match', array(
					'GROUP1_NAME'	=> ($tournament->data('tournament_userbased', $tournmt) == 0) ? $group->data('group_name', $clan1) : getusername($clan1),
					'GROUP2_NAME'	=> ($tournament->data('tournament_userbased', $tournmt) == 0) ? $group->data('group_name', $clan2) : getusername($clan2),
					'GROUP1'		=> $clan1,
					'GROUP2'		=> $clan2,
					'TORNEO'		=> $tournament->data('tournament_name', $tournmt),
					'TOURN_ID'		=> $tournmt,
					'ROUND'			=> $round,
					'TTIME'			=> ($row['group_time'] == 0) ? $user->lang['UNREPORTED'] : $user->format_date($row['group_time']) . $user->lang['DA'] . ': ' . (($tournament->data('tournament_userbased', $tournmt) == 0) ? $group->data('group_name', $row['group_reported']) : getusername($row['group_reported']))
				));

				// Get chat data
				$xflag		= ($tournament->data('tournament_userbased', $tournmt) == 0) ? 2 : 3;
				$xoppon		= ($row['group_position'] & 1) ? $row['group_position'] + 1 : $row['group_position'] - 1;
				$modtpos	= $clan1 . $clan2 . '_mod';
				$modtpos2	= $clan2 . $clan1 . '_mod';
				
				$sql7		= "SELECT * FROM " . RIVALS_MATCH_CHAT . " WHERE id_match = {$tournmt} AND 
							(tposition = {$row['group_position']} OR tposition = {$xoppon} OR tposition = '{$modtpos}' OR tposition = '{$modtpos2}') 
							AND chat_flag = {$xflag} AND tround = {$round} ORDER BY chat_time DESC";
				$result7	= $db->sql_query($sql7);
				$i7	= 0;
				while($row7 = $db->sql_fetchrow($result7))
				{
					$template->assign_block_vars('block_match_chat', array(
						'POSTER_URL'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row7['id_writer']),
						'POSTER_NAME'	=> getusername($row7['id_writer']),
						'CLAN_NAME'		=> ($tournament->data('tournament_userbased', $tournmt) == 0) ? (($row7['id_clan'] > 0) ? $group->data('group_name', $row7['id_clan']) : $user->lang['LADDER_STAFF']) : ((strpos($row7['tposition'], '_mod') !== false) ? $user->lang['LADDER_STAFF'] : ''),
						'CLAN_URL'		=> ($row7['id_clan'] > 0) ? append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $row7['id_clan']) : '#',
						'CHAT_TIME'		=> $user->format_date($row7['chat_time']),
						'CHAT_TEXT'		=> generate_text_for_display($row7['chat_text'], $row7['bbcode_uid'], $row7['bbcode_bitfield'], $row7['bbcode_options'])
					));
					$i7++;
				}
				$db->sql_freeresult($result7);
			}
		}
		else
		{
			$error[] = $user->lang['TOURN_CLAN_NOT_GOOD'];
		}
	}
	
/**********************************************************
*	SET THE WINNER BY STAFF ACTION
**************************************/
	
	if ($submit)
	{
		$winner	= (int) request_var('winner', 0);
		
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournmt} AND group_bracket = {$round} AND group_id = {$winner} AND loser_confirm = 0 AND group_loser = 0";
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		// Get the opponend ID
		$vsID	= $tournament->get_vsclan($tournmt, $winner, $round, false);
		$vsPOS	= ($row['group_position'] & 1) ? $row['group_position'] + 1 : $row['group_position'] - 1; /* this is if we have a bye clan/user */
		
		// We will matchup the winner so for first thing update for the current round
		$sql_array	= array(
			'group_uid'			=> 69, /* standard uid for match up */
			'group_reported'	=> $winner,
			'group_loser'		=> 0,
			'group_time'		=> time(),
			'loser_confirm'		=> 1
		);
		$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_tournament = {$tournmt} AND group_bracket = {$round} AND group_position = {$row['group_position']} AND group_id = " . $winner;
		$db->sql_query($sql);
		
		$sql_array7	= array(
			'group_uid'			=> 69, /* standard uid for match up */
			'group_reported'	=> $winner,
			'group_loser'		=> 1,
			'group_time'		=> time(),
			'loser_confirm'		=> 1
		);
		$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array7) . " WHERE group_tournament = {$tournmt} AND group_bracket = {$round} AND group_position = {$vsPOS} AND group_id = " . $vsID;
		$db->sql_query($sql);
		
		// Ok now insert new data for the winner in the next bracket
		$sql_array3	= array(
			'group_tournament'		=> $tournmt,
			'group_id'				=> $winner,
			'group_bracket'			=> $round + 1,
			'group_position'		=> ($row['group_position'] & 1) ? ($row['group_position'] + 1) / 2 : $row['group_position'] / 2,
			'group_loser'			=> 0,
			'group_position_temp'	=> 0,
			'group_reported'		=> 0,
			'loser_confirm'			=> 0,
			'group_uid'				=> 0,
			'group_time'			=> 0
		);
		$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array ('INSERT', $sql_array3);
		$db->sql_query($sql);
		
		// New add to the log this action and finish all
		$winnername	= ($tournament->data('tournament_userbased', $tournmt) == 0) ? $group->data('group_name', $winner) : getusername($winner);
		add_log('admin', 'LOG_TOURNAMENT_MATCH_UP', $tournament->data('tournament_name', $tournmt), $winnername);
		trigger_error($user->lang['MATCH_UP_DONE'] . adm_back_link($u_action));
	}
	
	$template->assign_vars(array(
		'SELECTOR'	=> ($tournmt == 0 || sizeof($error)) ? true : false,
		'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : '',
		'U_ACTION'	=> $u_action
	));
		
}
?>