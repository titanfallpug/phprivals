<?php
/**
*
* @package RivalsMod
* @version $Id$
* @copyright (c) 2011 Soshen <nipponart.org> based on Rivals by Tyler N. King <aibotca@yahoo.ca>
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
 * Manage ladder join/leave
 * Called from rivals with action == 'ladder_membership'
 */

global	$phpbb_root_path;

$group		= new group();
$ladder		= new ladder();
$type		= (int) request_var('type', 0);
$ladder_id	= (int) request_var('ladder_id', 0);
$ulad		= request_var('ulad', 'false');

if ($type == 1)
{
	if ($ladder->data['ladder_locked'] == 1)
	{
		// Ladder is locked. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
		meta_refresh(2, $redirect_url);
		trigger_error('LADDER_JOIN_LOCKED');
		break;
	}
	
	if ($ulad == 'true')
	{
		// Check for licence user level
		if ($user->data['user_id'] <= ANONYMOUS || $ladder->data['ladder_limit'] == 1 && $user->data['user_ladder_level'] == 0)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'B';
			$yourlicence	= 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_USER_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
		if ($ladder->data['ladder_limit'] == 2 && $user->data['user_ladder_level'] < 2)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'A';
			$yourlicence	= ($roww['clan_level'] == 1) ? 'B' : 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_USER_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
		
		// Validate user
		include($phpbb_root_path . 'rivals/classes/function_security.' . $phpEx);
		if (validate_user($user->data['user_id'], $config['rivals_bannedgroup'], $config['rivals_minpost']) == false)
		{
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
			meta_refresh(4, $url);
			trigger_error(sprintf($user->lang['USER_CANT_PLAY'], $config['rivals_minpost']));
			break;
		}
		
		// The group is wishing to join the ladder. Do some checking!
		$sql	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} AND user_id = " . $user->data['user_id'];
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);

		if (!empty($row['user_id']))
		{
			// They are already joined with the ladder. Let the user know.
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
			meta_refresh(2, $redirect_url);
			trigger_error('ALREADY_IN_LADDER');
			break;
		}
		
		// Everything seems OK. Join them to the ladder.
		$sql		= "SELECT MAX(user_current_rank) AS current_rank FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . $ladder_id;
		$result		= $db->sql_query($sql);
		$row		= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$new_rank	= $row['current_rank'] + 1;
		
		// ready for rth ladder
		$sqlf		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
		$resultf	= $db->sql_query($sqlf);
		$rowf		= $db->sql_fetchrow($resultf);
		$db->sql_freeresult($resultf);
		
		// ready for rth ladder
		if ($rowf['ladder_ranking'] == 2)
		{
			$sql_array	= array(
				'user_id'			=> $user->data['user_id'],
				'1vs1_ladder'		=> $ladder_id,
				'user_score'		=> 50,
				'user_current_rank'	=> $new_rank,
				'user_streak'		=> 0,
				'user_best_rank'	=> $new_rank,
				'user_worst_rank'	=> $new_rank,
				'user_last_rank'	=> 0
			);
		}
		else
		{
			$sql_array	= array(
				'user_id'			=> $user->data['user_id'],
				'1vs1_ladder'		=> $ladder_id,
				'user_score'		=> 1200,
				'user_current_rank'	=> $new_rank,
				'user_streak'		=> 0,
				'user_best_rank'	=> $new_rank,
				'user_worst_rank'	=> $new_rank,
				'user_last_rank'	=> 0
			);
		}
		$sql		= "INSERT INTO " . ONEVSONEDATA_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
		meta_refresh(2, $redirect_url);
		trigger_error('JOINED_WITH_LADDER_USER');
	}
	else
	{
		//check for the limit
		$sqlw		= "SELECT * FROM " . CLANS_TABLE . " WHERE group_id = " . $group->data['group_id'];
		$resultw	= $db->sql_query($sqlw);
		$roww		= $db->sql_fetchrow($resultw);
		$db->sql_freeresult($resultw);
	
		if ($ladder->data['ladder_limit'] == 1 && $roww['clan_level'] == 0)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'B';
			$yourlicence	= 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
		if ($ladder->data['ladder_limit'] == 2 && $roww['clan_level'] < 2)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'A';
			$yourlicence	= ($roww['clan_level'] == 1) ? 'B' : 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
		
		
		// The group is wishing to join the ladder. Do some checking!
		$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} AND group_id = " . $group->data['group_id'];
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);

		if (!empty($row['group_id']))
		{
			// They are already joined with the ladder. Let the user know.
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
			meta_refresh(2, $redirect_url);
			trigger_error('ALREADY_IN_LADDER');
			break;
		}

		// Check if there are required members needed.
		if ($ladder->data['ladder_rm'] != 0)
		{
			// Get the members joined to this group.
			$members	= $group->members('get_members', $group->data['group_id']);

			if (sizeof($members) <= $ladder->data['ladder_rm'])
			{
				// They do not have enough members. Let the user know.
				$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
				meta_refresh(5, $redirect_url);
				trigger_error(sprintf($user->lang['REQUIRED_MEMBERS_FAILED'], $ladder->data['ladder_rm']));
				break;
			}
		}
				
		// Everything seems OK. Join them to the ladder.
		$sql		= "SELECT MAX(group_current_rank) AS current_rank FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . $ladder_id;
		$result		= $db->sql_query($sql);
		$row		= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$new_rank	= $row['current_rank'] + 1;
		
		$sqlf		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
		$resultf	= $db->sql_query($sqlf);
		$rowf		= $db->sql_fetchrow($resultf);
		$db->sql_freeresult($resultf);
		
		// ready for rth ladder
		if ($rowf['ladder_ranking'] == 2)
		{
			$sql_array	= array(
				'group_id' 				=> $group->data['group_id'],
				'group_ladder' 			=> $ladder_id,
				'group_score' 			=> 50,
				'group_current_rank'	=> $new_rank,
				'group_streak' 			=> 0,
				'group_best_rank' 		=> $new_rank,
				'group_worst_rank' 		=> $new_rank,
				'group_last_rank' 		=> 0
			);
		}
		else
		{
			$sql_array	= array(
				'group_id' 				=> $group->data['group_id'],
				'group_ladder' 			=> $ladder_id,
				'group_score' 			=> 1200,
				'group_current_rank'	=> $new_rank,
				'group_streak' 			=> 0,
				'group_best_rank' 		=> $new_rank,
				'group_worst_rank' 		=> $new_rank,
				'group_last_rank' 		=> 0
			);
		}
		$sql		= "INSERT INTO " . GROUPDATA_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
		meta_refresh(2, $redirect_url);
		trigger_error('JOINED_WITH_LADDER');
	}
}
else if ($type == 2)
{
	// Check to make sure the ladder is not locked.
	if ($ladder->data['ladder_locked'] == 1)
	{
		// Ladder is locked. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
		meta_refresh(2, $redirect_url);
		trigger_error('LADDER_JOIN_LOCKED');
		break;
	}
	
	if ($ulad == 'true')
	{
		// Anti dashboard :D
		$sqlw		= "SELECT COUNT(1vs1_id) AS played FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = " . $user->data['user_id'];
		$resultw	= $db->sql_query($sqlw);
		$roww		= $db->sql_fetchrow($resultw);
		$db->sql_freeresult($resultw);
		
		if ($roww['played'] > 1)
		{
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
			meta_refresh(2, $redirect_url);
			trigger_error('NO_LEFT_LADDER');
			break;
		}
		
		// The user is wishing to leave the ladder.
		$sql	= "DELETE FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} AND user_id = " . $user->data['user_id'];
		$db->sql_query($sql);
		
		// Remove pending matches
		$sql	= "DELETE FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$ladder_id} AND (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']}) AND 1vs1_confirmer = 0";
		$db->sql_query($sql);
		
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_id}");
		meta_refresh(2, $redirect_url);
		trigger_error('LEFT_LADDER_USER');
	}
	else
	{
		// Anti dashboard :D
		$sqlw		= "SELECT COUNT(match_id) AS played FROM " . MATCHES_TABLE . " WHERE match_challenger = {$group->data['group_id']} OR match_challenger = " . $group->data['group_id'];
		$resultw	= $db->sql_query($sqlw);
		$roww		= $db->sql_fetchrow($resultw);
		$db->sql_freeresult($resultw);
		
		if ($roww['played'] > 1)
		{
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
			meta_refresh(2, $redirect_url);
			trigger_error('NO_LEFT_LADDER');
			break;
		}
		
		// The group is wishing to leave the ladder.
		$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} AND group_id = " . $group->data['group_id'];
		$db->sql_query($sql);
		
		// Remove pending matches
		$sql	= "DELETE FROM " . MATCHES_TABLE . " WHERE match_ladder = {$ladder_id} AND (match_challenger = {$group->data['group_id']} OR match_challengee = {$group->data['group_id']}) AND match_confirmed = 0";
		$db->sql_query($sql);
		
		$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_ladder = {$ladder_id} AND (challenger = {$group->data['group_id']} OR challengee = {$group->data['group_id']})";
		$db->sql_query($sql);
		
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_id}");
		meta_refresh(2, $redirect_url);
		trigger_error('LEFT_LADDER');
	}
}

?>
