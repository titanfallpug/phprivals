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
 * Tournaments Sing UP
 * Called from rivals with action == 'tournaments_signup'
 */

$tournament		= new tournament();
$group			= new group();
$tournament_id	= (int) request_var('tournament_id', 0);
$removeU		= (int) request_var('remove', 0);
$rostersys		= (int) request_var('rostersys', 0);
$addroster		= (!empty($_POST['addroster'])) ? true : false;
$removeroster	= (!empty($_POST['removeroster'])) ? true : false;

// action add roster
if ($addroster)
{
	$roster_dix	= (int) request_var('roster_link', 0);
	
	// check if this roster is already joined
	$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE roster_id = {$roster_dix} AND group_tournament = " . $tournament_id;
	$result	= $db->sql_query_limit($sql,1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (empty($row['roster_id']))
	{
		$sql_array	= array(
			'group_tournament'	=> $tournament_id,
			'group_id'			=> $group->data['group_id'],
			'roster_id'			=> $roster_dix,
			'group_bracket'		=> 1,
			'group_position'	=> 0,
		);
		$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);

		// Add the tournament to the group's data.
		if (!empty($group->data['group_tournaments']))
		{
			$tournaments	= unserialize($group->data['group_tournaments']);
		}
		$tournaments[]	= $tournament_id;
		$tournaments	= array_unique($tournaments);
		$tournaments	= serialize($tournaments);

		$sql = "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group->data['group_id'];
		$db->sql_query($sql);
		
		// Completed. Let the user know.
		$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
		meta_refresh(2, $url);
		trigger_error('GROUP_SIGNED_UP');
		break;
	}
	else
	{
		$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
		meta_refresh(4, $url);
		trigger_error('ROSTER_ALREADY_IN');
		break;
	}
}

if ($removeroster)
{
	$roster_dix	= (int) request_var('roster_link', 0);
	
	// fix tournaments clan data
	$sql	= "SELECT count(group_id) AS numclan FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_id = " . $group->data['group_id'];
	$result	= $db->sql_query_limit($sql,1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if ($row['numclan'] == 1)
	{
		$tournaments	= unserialize($group->data['group_tournaments']);
		$tournaments	= array_merge(array_diff($tournaments, array($tournament_id)));
		$tournaments	= serialize($tournaments);
	}
			
	$sql = "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group->data['group_id'];
	$db->sql_query($sql);
	
	$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND roster_id = {$roster_dix} AND group_id = " . $group->data['group_id'];
	$db->sql_query($sql);
	
	$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
	meta_refresh(2, $redirect_url);
	trigger_error('REMOVED_FROM_TOURNAMENT');
	break;
}

if ($removeU == 1 && $rostersys == 0)
{
	// clan based
	if ($tournament->data('tournament_userbased', $tournament_id) == 0)
	{
		// check if are joined like main clan or like roster
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = {$group->data['group_id']} AND roster_id > 0 AND group_tournament = " . $tournament_id;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!empty($row['group_id'])) /* there are a roster! */
		{
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments_signup&amp;rostersys=1&amp;remove=1&amp;tournament_id=' . $tournament_id);
			redirect($url);
			break;
		}
		else
		{
			// fix tournaments clan data
			$tournaments	= unserialize($group->data['group_tournaments']);
			$tournaments	= array_merge(array_diff($tournaments, array($tournament_id)));
			$tournaments	= serialize($tournaments);
			
			$sql = "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group->data['group_id'];
			$db->sql_query($sql);
			
			$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_id = " . $group->data['group_id'];
			$db->sql_query($sql);
		}
	}
	else if ($tournament->data('tournament_userbased', $tournament_id) == 1)
	{
		// fix tournaments clan data
		$tournaments	= unserialize($user->data['user_tournaments']);
		$tournaments	= array_merge(array_diff($tournaments, array($tournament_id)));
		$tournaments	= serialize($tournaments);
		
		$sql = "UPDATE " . USERS_TABLE . " SET user_tournaments = '{$tournaments}' WHERE user_id = " . $user->data['user_id'];
		$db->sql_query($sql);
		
		$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_id = " . $user->data['user_id'];
		$db->sql_query($sql);
	}
	
	$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
	meta_refresh(2, $redirect_url);
	trigger_error('REMOVED_FROM_TOURNAMENT');
	break;
}
else if ($removeU == 1 && $rostersys == 1)
{
	// get roster list
	$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE clan_id = {$group->data['group_id']} ORDER BY roster_name ASC";
	$result	= $db->sql_query($sql);
	$q		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Get only roster with the correct members number for this tournament
		$roster_members	= get_roster_members($row['roster_id']);

		if (count($roster_members) >= $tournament->data('tournament_minuser', $tournament_id) && count($roster_members) <= $tournament->data('tournament_maxuser', $tournament_id))
		{
			$template->assign_block_vars('blocco_roster', array(
				'ROSTER_ID'		=> $row['roster_id'],
				'ROSTER_NAME'	=> $row['roster_name'],
				'ROSTER_EXP'	=> get_roster_exp($row['roster_id'])
			));
			$q++;
		}
	}
	$db->sql_freeresult($result);
	
	$template->assign_vars(array(
		'S_TUTTO_VUOTO'	=> ($q == 0) ? true : false,
		'MIN_OK'		=> $tournament->data('tournament_minuser', $tournament_id),
		'MAX_OK'		=> $tournament->data('tournament_maxuser', $tournament_id),
		'S_COMMAND'		=> 'removeroster'
	));
	
	$template->set_filenames(array('body' => 'rivals/tournaments_rosters.html'));
}
else if ($removeU == 0 && $rostersys == 0)
{
	// Check if the user is leader of a clan if the tournament is clan based.
	if ($tournament->data('tournament_userbased', $tournament_id) == 0 && empty($group->data['group_id']))
	{
		// They are not. Let the user know.
		$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
		meta_refresh(4, $url);
		trigger_error('GROUP_NOTSIGNED_UP_LADDER');
		break;
	}
	else if ($tournament->data('tournament_userbased', $tournament_id) == 1 && $user->data['user_id'] <= ANONYMOUS)
	{
		$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
		meta_refresh(4, $redirect_url);
		trigger_error('LICENZA_USER_INSUFFICIENTE');
		break;
	}

	// Check for licences
	if ($tournament->data('tournament_userbased', $tournament_id) == 0)
	{
		if ($tournament->data('tournament_licence', $tournament_id) == 1 && $group->data['clan_level'] == 0)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'B';
			$yourlicence	= 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
		if ($tournament->data('tournament_licence', $tournament_id) == 2 && $group->data['clan_level'] < 2)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'A';
			$yourlicence	= ($group->data['clan_level'] == 1) ? 'B' : 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
	}
	else
	{
		if ($tournament->data('tournament_licence', $tournament_id) == 1 && $user->data['user_ladder_level'] == 0)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'B';
			$yourlicence	= 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_USER_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
		if ($tournament->data('tournament_licence', $tournament_id) == 2 && $user->data['user_ladder_level'] < 2)
		{
			// Your clan have do not have a good licence.
			$wantlicence	= 'A';
			$yourlicence	= ($roww['clan_level'] == 1) ? 'B' : 'C';
			
			$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(5, $redirect_url);
			$messaggio	= sprintf($user->lang['LICENZA_USER_INSUFFICIENTE'], $wantlicence, $yourlicence, '<a href="' . $redirect_url . '">', '</a>');
			trigger_error("{$messaggio}");
			break;
		}
	}

	// Check if this is invitation only.
	if ($tournament->data('tournament_userbased', $tournament_id) == 0)
	{
		if ($tournament->data('tournament_type', $tournament_id) == 2 && !in_array($group->data['group_id'], explode("\n", unserialize($tournament->data('tournament_invite', $tournament_id)))))
		{
			// They are not invited. Let the user know.
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(4, $url);
			trigger_error('GROUP_NOTINVITED');
			break;
		}
	}
	else
	{
		if ($tournament->data('tournament_type', $tournament_id) == 2 && !in_array($user->data['user_id'], explode("\n", unserialize($tournament->data('tournament_invite', $tournament_id)))))
		{
			// They are not invited. Let the user know.
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(4, $url);
			trigger_error('USER_NOTINVITED');
			break;
		}
	}

	if ($tournament->data('tournament_userbased', $tournament_id) == 0)
	{
		// Get the list of tournaments the group is in. Works only with main clan.
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = {$group->data['group_id']} AND roster_id = 0 AND group_tournament = " . $tournament_id;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		// Are they already in this tournament?
		if (!empty($row['group_id']))
		{
			// They are. Let the user know.
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(4, $url);
			trigger_error('ALREADY_IN_TOURNAMENT');
			break;
		}
	}
	else
	{
		// Get the list of tournaments the group is in.
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = {$user->data['user_id']} AND group_tournament = " . $tournament_id;
		$result	= $db->sql_query_limit($sql,1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Are they already in this tournament?
		if (!empty($row['group_id']))
		{
			// They are. Let the user know.
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(4, $url);
			trigger_error('ALREADY_IN_TOURNAMENT');
			break;
		}
	}

	// Get the number of groups in the tournament.
	$sql	= "SELECT COUNT(*) AS num_groups FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = 1";
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if ($row['num_groups'] >= $tournament->data('tournament_brackets', $tournament_id))
	{
		// They can not join, the tournament is full. Let the user know.
		$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
		meta_refresh(4, $url);
		trigger_error('TOURNAMENT_FULL');
		break;
	}
	
	// Check for Min clan members limit
	if ($tournament->data('tournament_userbased', $tournament_id) == 0)
	{
		// Get the members joined to this group.
		$membersX	= $group->members('get_members', $group->data['group_id']);

		if (sizeof($membersX) < $tournament->data('tournament_minuser', $tournament_id))
		{
			// They do not have enough members. Let the user know.
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(4, $url);
			trigger_error(sprintf($user->lang['TOURNAMENT_MEMBERS_FAILED'], $tournament->data('tournament_minuser', $tournament_id)));
			break;
		}
	}

	/**********
	* If all checks are passed add the groups or the user to the tournament
	*/

	// For clan based tournament
	if ($tournament->data('tournament_userbased', $tournament_id) == 0)
	{
		// Check for stricted and max user - for rosters!
		if ($tournament->data('tournament_stricted', $tournament_id) == 1)
		{
			// Get the members joined to this group.
			$membersY	= $group->members('get_members', $group->data['group_id']);
			
			if (sizeof($membersY) > $tournament->data('tournament_maxuser', $tournament_id))
			{
				// check if clan have rosters
				$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE clan_id = " . (int) $group->data['group_id'];
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				if (empty($row['roster_id']))
				{
					$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
					meta_refresh(4, $url);
					trigger_error(sprintf($user->lang['NO_ROSTER_FOR_CLAN'], $tournament->data('tournament_minuser', $tournament_id), $tournament->data('tournament_maxuser', $tournament_id)));
					break;
				}
				else
				{
					$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments_signup&amp;rostersys=1&amp;tournament_id=' . $tournament_id);
					redirect($url);
					break;
				}
			}
			else if (sizeof($membersY) <= $tournament->data('tournament_maxuser', $tournament_id) && sizeof($membersY) >= $tournament->data('tournament_minuser', $tournament_id))
			{
				// The clan dot not have more users than requested but have the min user, so it will be added like main clan
				$sql_array	= array(
					'group_tournament'	=> $tournament_id,
					'group_id'			=> $group->data['group_id'],
					'group_bracket'		=> 1,
					'group_position'	=> 0,
				);
				$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);

				// Add the tournament to the group's data.
				if (!empty($group->data['group_tournaments']))
				{
					$tournaments	= unserialize($group->data['group_tournaments']);
				}
				$tournaments[]	= $tournament_id;
				$tournaments	= serialize($tournaments);

				$sql = "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group->data['group_id'];
				$db->sql_query($sql);
			}
		}
		else
		{
			// Everything is OK. Add them to the tournament.
			$sql_array	= array(
				'group_tournament'	=> $tournament_id,
				'group_id'			=> $group->data['group_id'],
				'group_bracket'		=> 1,
				'group_position'	=> 0,
			);
			$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);

			// Add the tournament to the group's data.
			if (!empty($group->data['group_tournaments']))
			{
				$tournaments	= unserialize($group->data['group_tournaments']);
			}
			$tournaments[]	= $tournament_id;
			$tournaments	= serialize($tournaments);

			$sql = "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group->data['group_id'];
			$db->sql_query($sql);
		}
	}
	else if ($tournament->data('tournament_userbased', $tournament_id) == 1) /* user based */
	{
		// Check if the user can join
		include($phpbb_root_path . 'rivals/classes/function_security.' . $phpEx);
		if (validate_user($user->data['user_id'], $config['rivals_bannedgroup'], $config['rivals_minpost']) == true)
		{
			$sql_array	= array(
				'group_tournament'	=> $tournament_id,
				'group_id'			=> $user->data['user_id'],
				'group_bracket'		=> 1,
				'group_position'	=> 0,
			);
			$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);
			
			// update user table ferecent
			if (!empty($user->data['user_tournaments']))
			{
				$tournaments	= unserialize($user->data['user_tournaments']);
			}
			$tournaments[]	= $tournament_id;
			$tournaments	= serialize($tournaments);

			$sql = "UPDATE " . USERS_TABLE . " SET user_tournaments = '{$tournaments}' WHERE user_id = " . $user->data['user_id'];
			$db->sql_query($sql);
		}
		else
		{
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
			meta_refresh(4, $url);
			trigger_error(sprintf($user->lang['USER_CANT_PLAY'], $config['rivals_minpost']));
			break;
		}
	}

	// Completed. Let the user know.
	$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
	meta_refresh(2, $url);
	trigger_error('GROUP_SIGNED_UP');
}
else if ($removeU == 0 && $rostersys == 1)
{
	// Now the roster system
	
	// get roster list
	$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE clan_id = {$group->data['group_id']} ORDER BY roster_name ASC";
	$result	= $db->sql_query($sql);
	$q		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Get only roster with the correct members number for this tournament
		$roster_members	= get_roster_members($row['roster_id']);

		if (count($roster_members) >= $tournament->data('tournament_minuser', $tournament_id) && count($roster_members) <= $tournament->data('tournament_maxuser', $tournament_id))
		{
			$template->assign_block_vars('blocco_roster', array(
				'ROSTER_ID'		=> $row['roster_id'],
				'ROSTER_NAME'	=> $row['roster_name'],
				'ROSTER_EXP'	=> get_roster_exp($row['roster_id'])
			));
			$q++;
		}
	}
	$db->sql_freeresult($result);
	
	$template->assign_vars(array(
		'S_TUTTO_VUOTO'	=> ($q == 0) ? true : false,
		'MIN_OK'		=> $tournament->data('tournament_minuser', $tournament_id),
		'MAX_OK'		=> $tournament->data('tournament_maxuser', $tournament_id),
		'S_COMMAND'		=> 'addroster'
	));
	
	$template->set_filenames(array('body' => 'rivals/tournaments_rosters.html')); 
}
?>