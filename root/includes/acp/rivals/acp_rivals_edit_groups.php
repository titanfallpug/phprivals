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
 * Edit Groups
 * Called from acp_rivals with mode == 'edit_groups'
 */
function acp_rivals_edit_groups($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$tournament	= new tournament();

	$submit		= (!empty($_POST['submit'])) ? true : false;
	$resume		= (!empty($_POST['resume'])) ? true : false;
	$group_id	= (int) request_var('group_id', 0);
	$onlyclos	= (int) request_var('closed', 0);
	
	// Resume action
	if ($resume)
	{
		$newfounder	= (int) request_var('newfounder', 0);
		
		// reset users level
		$members	= $group->members('get_members', $group_id);
				
		foreach ($members AS $member)
		{			
			if ($member == $newfounder)
			{
				$sql_array	= array(
					'group_leader'	=> 1
				);
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = {$group_id} AND user_id = " . $newfounder;
				$db->sql_query($sql);
			}
			else
			{
				$sql_array	= array(
					'group_leader'	=> 0
				);
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = {$group_id} AND user_id = " . $member;
				$db->sql_query($sql);
			}
			
			// Now resume the team
			$sql_array2	= array(
				'clan_closed'	=> 0
			);
			$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = " . $group_id;
			$db->sql_query($sql);
			
			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_groups");
			meta_refresh(2, $redirect_url);
			trigger_error('GROUP_UPDATED');
		}
	}
	
	// Are we submitting a form?
	if ($submit && $onlyclos == 0 && $group_id > 0)
	{
		// Yes, handle the form.
		$group_delete	= (int) request_var('group_delete', 0);
		if ($group_delete != 0)
		{
			// Try to set the user's group session to another group they own.
			$sql	= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_leader > 0 AND group_id = {$group_id}";
			$result	= $db->sql_query($sql);
			$i	= 0;
			while ($row = $db->sql_fetchrow($result))
			{
				// CHECK IF THEY HAVE A ANOTHER CLAN
				$sqlj		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE user_id = {$row['user_id']} AND group_leader > 0 AND group_id != {$group_id}";
				$resultj	= $db->sql_query_limit($sqlj, 1);
				$rowj		= $db->sql_fetchrow($resultj);
				$db->sql_freeresult($resultj);
				
				if (!empty($rowj['user_id']))
				{
					$sql	= "UPDATE " . USERS_TABLE . " SET group_session = {$rowj['group_id']} WHERE user_id = " . $row['user_id'];
					$db->sql_query($sql);
				}
				else
				{
					$sql	= "UPDATE " . USERS_TABLE . " SET group_session = 0 WHERE user_id = " . $row['user_id'];
					$db->sql_query($sql);
				}
				$i++;
			}
			// Before delete the clan check if it plays matches
			$sqly		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_challenger = {$group_id} OR match_challengee = {$group_id}";
			$resulty	= $db->sql_query_limit($sqly, 1);
			$rowy		= $db->sql_fetchrow($resulty);
			$db->sql_freeresult($resulty);
			
			// if yes close clan instad delete clan
			if (!empty($rowy['match_id']))
			{
				$sql	= "UPDATE " . CLANS_TABLE . " SET clan_closed = 1 WHERE group_id = " . $group_id;
				$db->sql_query($sql);
			}
			else
			{
				// Delete the group.
				$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_id = " . $group_id;
				$db->sql_query($sql);
			
				$sql	= "DELETE FROM " . CLANS_TABLE . " WHERE group_id = " . $group_id;
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenger = {$group_id} OR challengee = {$group_id}";
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . USER_CLAN_TABLE . " WHERE group_id = {$group_id}";
				$db->sql_query($sql);
			}

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_groups");
			meta_refresh(2, $redirect_url);
			trigger_error('GROUP_UPDATED');
		}
		
		// BLOCK TEAM
		$group_ban	= (int) request_var('group_ban', 0);
		if ($group_ban != 0)
		{
			$sql	= "UPDATE " . CLANS_TABLE . " SET clan_closed = 1 WHERE group_id = " . $group_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_groups");
			meta_refresh(2, $redirect_url);
			trigger_error('GROUP_UPDATED');
		}

		// Check to see if they are adding to a ladder.
		$add_to_ladder	= (int) request_var('add_to_ladder', 0);
		if ($add_to_ladder > 0)
		{
			// Add the group to the ladder.
			$sql		= "SELECT MAX(group_current_rank) AS current_rank FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . $add_to_ladder;
			$result		= $db->sql_query($sql);
			$row		= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			$new_rank	= $row['current_rank'] + 1;
			
			//CHECK LADDER TYPE FOR STATS POINTS (rth mod)
			$sql7		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = {$add_to_ladder}";
			$result7	= $db->sql_query($sql7);
			$row7		= $db->sql_fetchrow($result7);
			$db->sql_freeresult($result7);
			
			$basepoints = ($row7['ladder_ranking'] == 2) ? 50 : 1200;

			$sql_array	= array(
				'group_id' 				=> $group_id,
				'group_ladder' 			=> $add_to_ladder,
				'group_score' 			=> $basepoints,
				'group_streak' 			=> 0,
				'group_current_rank' 	=> $new_rank,
				'group_best_rank' 		=> $new_rank,
				'group_worst_rank' 		=> 0,
				'group_last_rank' 		=> 0
			);
			$sql		= "INSERT INTO " . GROUPDATA_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);
		}

		// Check to see if they are adding to a tournament.
		$add_to_tournament	= (int) request_var('add_to_tournament', 0);
		if ($add_to_tournament > 0)
		{
			// Check if the group is joined to the ladder the tournament is in.
			$group_data	= $group->data('*', $group_id);

			// Add them to the tournament.
			$sql_array	= array(
				'group_tournament'	=> $add_to_tournament,
				'group_id' 			=> $group_id,
				'group_bracket' 	=> 1,
				'group_position'	=> 0
			);
			$sql		= "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);

			// Add the tournament to the group's data.
			if (!empty($group->data['group_tournaments']))
			{
				$tournaments	= unserialize($group->data('group_tournaments', $group_id));
			}
			$tournaments[]	= $add_to_tournament;
			$tournaments	= serialize($tournaments);

			$sql	= "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group_id;
			$db->sql_query($sql);
		}

		// Check to see if they are moving the group.
		$move_ladder		= (int) request_var('move_ladder', 0);
		$move_from_ladder	= (int) request_var('move_from_ladder', 0);
		if ($move_ladder > 0)
		{
			// They are moving. Stats too?
			$move_stats	= (int) request_var('move_stats', 0);
			if ($move_stats != 0)
			{
				// Yes, move the stats.
				$sql_array	= array(
					'group_ladder'	=> $move_ladder
				);
				$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_ladder = {$move_from_ladder} AND group_id = " . $group_id;
				$db->sql_query($sql);
			}
			else
			{
				//CHECK LADDER TYPE FOR STATS POINTS (rth mod)
				$sql7		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = {$move_from_ladder}";
				$result7	= $db->sql_query($sql7);
				$row7		= $db->sql_fetchrow($result7);
				$db->sql_freeresult($result7);
			
				$basepoints = ($row7['ladder_ranking'] == 2) ? 50 : 1200;
				
				// No, delete the stats.
				$sql_array	= array(
					'group_ladder'		=> $move_ladder,
					'group_wins'		=> 0,
					'group_losses'		=> 0,
					'group_score'		=> $basepoints,
					'group_lastscore'	=> 0,
					'group_streak'		=> 0
				);
				$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_ladder = {$move_from_ladder} AND group_id = " . $group_id;
				$db->sql_query($sql);
			}
		}

		// Get and update the group's data.
		$group_name		= (string) utf8_normalize_nfc(request_var('group_name', '', true));
		$group_desc		= (string) utf8_normalize_nfc(request_var('group_desc', '', true));
		$group_sito		= (string) utf8_normalize_nfc(request_var('group_sito', '', true));
		$favmap			= (string) utf8_normalize_nfc(request_var('clan_favouritemap', '', true));
		$favteam		= (string) utf8_normalize_nfc(request_var('clan_favouriteteam', '', true));
		$group_level	= (int) request_var('group_level', 0);
		$logo_delete	= (int) request_var('logo_delete', 0);

		// Edit the group.
		$sql_array	= array(
			'group_name' 			=> $group_name,
			'group_sito' 			=> $group_sito,
			'group_desc' 			=> $group_desc,
			'clan_favouritemap'		=> $favmap,
			'clan_favouriteteam'	=> $favteam,
			'clan_level'			=> $group_level
		);
		$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = " . $group_id;
		$db->sql_query($sql);
		
		// reset logo
		if ($logo_delete)
		{			
			$sql_array8	= array(
				'clan_logo_name' 	=> 'nologo.jpg',
				'clan_logo_ext' 	=> 'jpg',
				'clan_logo_width' 	=> 100,
				'clan_logo_height'	=> 100
			);
			$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE group_id = " . $group_id;
			$db->sql_query($sql);
		}

		// Get and update the group's ladder data.
		$group_ladder	= request_var('group_ladder', array(0 => 0));
		$delete_ladder	= request_var('delete_ladder', array(0 => 0));
		$delete_frost	= request_var('delete_frost', array(0 => 0));
		$group_wins		= request_var('group_wins', array(0 => 0));
		$group_losses	= request_var('group_losses', array(0 => 0));
		$group_score	= request_var('group_score', array(0 => 0));
		$group_streak	= request_var('group_streak', array(0 => 0));

		// deleting step
		foreach ($delete_ladder AS $idremoved)
		{
			if (isset($delete_ladder))
			{
				$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$idremoved} AND group_id = " . $group_id;
				$db->sql_query($sql);
			}
		}

		// remove hibernating status
		foreach ($delete_frost AS $iddefrosted)
		{
			if (isset($delete_frost))
			{
				$sql_array	= array(
					'group_frosted' 		=> 0,
					'group_frosted_time' 	=> 0,
				);
				$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_ladder = {$iddefrosted} AND group_id = " . $group_id;
				$db->sql_query($sql);
			}
		}
		
		// editing step
		$idel	= 0;
		foreach ($group_ladder AS $value)
		{
			// Update the group's ladder data.	
			$sql_array	= array(
				'group_wins' 	=> $group_wins[$idel],
				'group_losses' 	=> $group_losses[$idel],
				'group_score' 	=> $group_score[$idel],
				'group_streak'	=> $group_streak[$idel]
			);
			$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_ladder = {$value} AND group_id = " . $group_id;
			$db->sql_query($sql);
			$idel++;
		}

		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_groups");
		meta_refresh(2, $redirect_url);
		trigger_error('GROUP_UPDATED');
	}
	else if ($submit && $onlyclos == 1 && $group_id == 0)
	{
		redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_groups&amp;closed=1"));
	}
	else
	{
		// Check if a group ID was set.
		if (!empty($group_id))
		{
			// Check if the group exists.
			$group_leader	= $group->data('user_id', $group_id);
			if (empty($group_leader))
			{
				// The group does not exist.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_groups");
				meta_refresh(4, $redirect_url);
				trigger_error('NONEXISTANT_GROUP');
			}
			
			// Check for clan closed
			if ($group->data('clan_closed', $group_id) == 1)
			{
				$members	= $group->members('get_members', $group_id);
				
				foreach ($members AS $member)
				{
					$sql	= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_id = {$group_id} AND user_id = " . $member;
					$result	= $db->sql_query($sql);
					$row	= $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					
					switch ($row['group_leader'])
					{
						case 2:
							$livello	= $user->lang['COF_SI'];
						break;
						case 1:
							$livello	= $user->lang['FOUNDER'];
						break;
						case 0:
							$livello	= $user->lang['COF_NO'];
						break;
					}
					
					$template->assign_block_vars('block_members', array(
						'USERNAME'		=> getuserdata('username', $member),
						'GAMERNAME'		=> getuserdata('gamer_name', $member),
						'USER_ID'		=> $member,
						'LEVEL'			=> $livello
					));
				}
				
				
				$template->assign_vars(array(
					'GROUP_LEADER' 	=> true,
					'CLAN_CLOSED'	=> true,
					'GROUP_ID' 		=> $group_id,
					'GROUP_NAME' 	=> $group->data('group_name', $group_id)
				));
			}
			else
			{
				// get the ladder where the groups is joined
				$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = " . $group_id;
				$result	= $db->sql_query($sql);
				$ladderjoined	= array();
				while ($row = $db->sql_fetchrow($result))
				{
					$ladderjoined[]	= $row['group_ladder'];
				}
				$db->sql_freeresult($result);
				
				
				// Loop through the ladders the group is joined to.
				$group_data	= $group->data('*', $group_id);
				if (sizeof($group_data['group_ladders']) > 0)
				{
					foreach ($group_data['group_ladders'] AS $value)
					{
						// Get the ladder's roots.
						$ladder_data	= $ladder->get_roots($value);

						// Get the group's data for this ladder.
						$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$group_id} AND group_ladder = " . $value;
						$result	= $db->sql_query($sql);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						// Assign each ladder to the template.
						$template->assign_block_vars('block_ladders', array(
							'LADDER_ID' 	=> $value,
							'FROST'			=> ($row['group_frosted'] == 1) ? true : false,
							'PLATFORM' 		=> $ladder_data['PLATFORM_NAME'],
							'LADDER' 		=> $ladder_data['LADDER_NAME'],
							'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME'],
							'GROUP_WINS' 	=> $row['group_wins'],
							'GROUP_LOSSES'	=> $row['group_losses'],
							'GROUP_SCORE' 	=> $row['group_score'],
							'GROUP_ID' 		=> $group_id,
							'GROUP_STREAK' 	=> $row['group_streak'])
						);
					}
				}

				// Loop through the group's ladder list where group login.
				if (sizeof($group_data['group_ladders']) > 0)
				{
					foreach ($group_data['group_ladders'] AS $value)
					{
						if (in_array($value, $ladderjoined))
						{
							// Get the ladder's roots.
							$ladder_data	= $ladder->get_roots($value);

							// Assign each ladder to the template.
							$template->assign_block_vars('block_ladders2', array(
								'LADDER_ID' => $value,
								'PLATFORM'	=> $ladder_data['PLATFORM_NAME'],
								'LADDER' 	=> $ladder_data['LADDER_NAME'],
								'SUBLADDER' => $ladder_data['SUBLADDER_NAME']
							));
						}
					}
				}

				// Loop through the ladders where the groups isn't.
				$sql	= "SELECT l.*, p.* FROM " . LADDERS_TABLE . " l, " . PLATFORMS_TABLE . " p WHERE l.ladder_platform = p.platform_id";
				$result	= $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$ladderids[] = array($row['ladder_id'], $row['ladder_name'], $row['platform_name']);		
				}
				$db->sql_freeresult($result);
				
				foreach ($ladderids AS $theladder)
				{
					// Assign it to the template.
					$template->assign_block_vars('block_ladders3', array(
						'LADDER_NAME'	=> $theladder[1],
						'PLATFORM'		=> $theladder[2]
					));
					
					$sql_2		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_oneone = 0 AND ladder_parent = " . $theladder[0];
					$result_2	= $db->sql_query($sql_2);
					while ($row_2 = $db->sql_fetchrow($result_2))
					{
						if (!in_array($row_2['ladder_id'], $ladderjoined))
						{
							// Assign them to the template.
							$template->assign_block_vars('block_ladders3.block_subladders3', array(
								'LADDER_ID'		=> $row_2['ladder_id'],
								'LADDER_NAME'	=> $row_2['ladder_name']
							));
						}
					}
					$db->sql_freeresult($result_2);
				}

				// Get the tournaments where the clan joined
				$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = " . $group_id;
				$result	= $db->sql_query($sql);
				$tournjoined	= array();
				while ($row = $db->sql_fetchrow($result))
				{
					$tournjoined[]	= $row['group_tournament'];
				}
				$db->sql_freeresult($result);
				
				// Get the tournaments where the groups isn't joined.
				$sql	= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_status <> 3 AND tournament_userbased = 0 ORDER BY tournament_time DESC";
				$result	= $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					if (!in_array($row['tournament_id'], $tournjoined))
					{
						// Assign the tournaments to the template.
						$template->assign_block_vars('block_tournaments', array(
							'TOURNAMENT_ID' 	=> $row['tournament_id'],
							'TOURNAMENT_NAME' 	=> $row['tournament_name']
						));
					}
				}

				$db->sql_freeresult($result);
				
				$template->assign_vars(array(
					'GROUP_LEADER' 	=> $group_data['user_id'],
					'CLAN_CLOSED'	=> false,
					'GROUP_ID' 		=> $group_id,
					'GROUP_DESC' 	=> $group_data['group_desc'],
					'GROUP_SITO' 	=> $group_data['group_sito'],
					'GROUP_NAME' 	=> $group_data['group_name'],
					'FAVMAP'	 	=> $group_data['clan_favouritemap'],
					'FAVTEAM' 		=> $group_data['clan_favouriteteam'],
					'LICENCEA'		=> ($group_data['clan_level'] == 2) ? 'selected="selected"' : '',
					'LICENCEB'		=> ($group_data['clan_level'] == 1) ? 'selected="selected"' : '',
					'LICENCEC'		=> ($group_data['clan_level'] == 0) ? 'selected="selected"' : '',
					'CLOSED'		=> ($group_data['clan_closed'] == 1) ? 'selected="selected"' : '',
					'AVATAR'		=> (!empty($group_data['clan_logo_name'])) ? $group_data['clan_logo_name'] : '',
					'IMGPATH'		=> "{$phpbb_root_path}images/rivals/clanlogo/"
				));
			}
		}	
	}
	
	// LOAD CLANS FOR SELECT
	$xwhere	= (!empty($onlyclos)) ? " WHERE clan_closed = 1 ORDER BY group_name ASC" : " ORDER BY group_name ASC";
	
	$sql	= "SELECT * FROM " . CLANS_TABLE . " {$xwhere}";
	$result	= $db->sql_query($sql);
	$j 		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign it to the template.
		$template->assign_block_vars('clan_select', array(
			'CLAN_NAME'	=> $row['group_name'],
			'CLAN_ID'	=> $row['group_id']
		));
	$j++;
	}
	
	// Assign the other variables to the template.
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action,
		'ONLYCCK'	=> ($onlyclos == 1) ? 'checked="checked"' : ''
	));
}
?>