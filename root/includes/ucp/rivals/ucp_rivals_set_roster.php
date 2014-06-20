<?php
/**
*
* @package RivalsMod
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
 * Set up the Roster LineUP
 * Called from ucp_rivals with mode == 'set_roster'
 */
function ucp_rivals_set_roster($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$error		= array();
	$rostering	= array();
	$addroster	= (!empty($_POST['addroster'])) ? true : false;
	$adduser	= (!empty($_POST['adduser'])) ? true : false;
	$delroster	= (!empty($_POST['deleteroster'])) ? true : false;
	$editroster	= (!empty($_POST['editroster'])) ? true : false;
	$deletembrs	= (!empty($_POST['deletembrs'])) ? true : false;
	$setleader	= (!empty($_POST['setleader'])) ? true : false;
	
	// Check if you have a clan.
	if (empty($group->data['group_id']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
	}
	else
	{
		// get roster list
		$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE clan_id = {$group->data['group_id']} ORDER BY roster_name ASC";
		$result	= $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('blocco_roster', array(
				'ROSTER_ID'		=> $row['roster_id'],
				'ROSTER_NAME'	=> $row['roster_name'],
				'ROSTER_EXP'	=> get_roster_exp($row['roster_id'])
			));
			
			// get roster lineup members
			if (!empty($row['roster_members']))
			{
				$rostering	= explode('|', $row['roster_members']);

				if (sizeof($rostering) > 0)
				{
					foreach ($rostering AS $ilroster)
					{
						recalculate_totalEXP($ilroster);
						
						$template->assign_block_vars('blocco_roster.the_rosters', array(
							'USER_ID'	=> $ilroster,
							'USER_NAME'	=> getuserdata('username', $ilroster),
							'USER_EXP'	=> getuserdata('user_ladder_value', $ilroster),
							'THELEADER' => ($row['roster_leader'] == $ilroster) ? ' checked="checked"' : '',
							'GAMERNAME'	=> getuserdata('gamer_name', $ilroster),
						));
					}
				}
			}
		}
		$db->sql_freeresult($result);
		
		// get clan members
		$clanmembers	= $group->members('get_members', $group->data['group_id']);
		foreach ($clanmembers as $member)
		{
			$template->assign_block_vars('blocco_member_select', array(
				'USER_ID'	=> $member,
				'USERNAME'	=> getusername($member)
			));
		}
		
		
/**************************
*	ACTIONS
**************/
		// Add a new roster team
		if ($addroster)
		{
			$roster_name	= (string) utf8_normalize_nfc(request_var('roster_name', '', true));
			
			if (empty($roster_name))
			{
				$error[] = $user->lang['ROSTER_NAME_EMPTY'];
			}
			
			// Validate the name
			$sql_2_array = array(
				'UCASE(roster_name)'	=> strtoupper($roster_name),
			);
			$sqlS		= 'SELECT * FROM ' . RIVAL_ROSTERS . ' WHERE ' . $db->sql_build_array('SELECT', $sql_2_array) 
						. ' AND clan_id = ' . $group->data['group_id'];
			$resultS	= $db->sql_query_limit($sqlS,1);
			$rowS		= $db->sql_fetchrow($resultS);
			$db->sql_freeresult($resultS);
			
			if (!empty($rowS['roster_id']))
			{
				$error[] = $user->lang['ROSTER_NAME_USED'];
			}
			
			if (!sizeof($error))
			{
				$sql_array	= array(
					'clan_id'			=> $group->data['group_id'],
					'roster_name'		=> $roster_name,
					'roster_members'	=> ''
				);
				$sql	= "INSERT INTO " . RIVAL_ROSTERS . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster");
				meta_refresh(2, $redirect_url);
				trigger_error('ROSTER_ADDED');
			}
		}
		
		// Add user to roster team
		if ($adduser)
		{
			$user_r_id	= (int) request_var('roster_user', 0);
			$roster_id	= (int) request_var('roster_link', 0);
			
			$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			// Check if exist
			if (!empty($row['roster_id']))
			{
				// Check if this roster are in a ongoing tournament
				$sql_c		= "SELECT * FROM " . TGROUPS_TABLE . " AS tg LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tg.group_tournament = tt.tournament_id
							WHERE tg.roster_id = {$roster_id} AND tt.tournament_status = 2";
				$result_c	= $db->sql_query_limit($sql_c, 1);
				$row_c		= $db->sql_fetchrow($result_c);
				$db->sql_freeresult($result_c);
				
				/*// Check if this roster are in a ongoing league cycle (status = 2 for ongoin leagues, status = 1 for ogoing leagues but in cycle pause!)
				$sql_d		= "SELECT * FROM " . LEAGUES_CLAN_DATA . " AS ld LEFT JOIN " . LEAGUES_TABLE . " AS lt ON ld.league_id = lt.league_id
							WHERE ld.roster = {$roster_id} AND lt.league_status = 2";
				$result_d	= $db->sql_query_limit($sql_d, 1);
				$row_d		= $db->sql_fetchrow($result_d);
				$db->sql_freeresult($result_d);*/
				
				if (!empty($row_c['group_id']))/* || !empty($row_d['clan_id'])) /* this roster are in a ongoin competition */
				{
					$error[] = $user->lang['ROSTER_IN_COMPETITION'];
				}
				
				if (empty($row['roster_members']))
				{
					$totalmembers	= $user_r_id;
				}
				else
				{
					$oldmembers	= explode('|', $row['roster_members']);
					foreach ($oldmembers AS $value)
					{
						if ($value == $user_r_id)
						{
							$error[] = $user->lang['USER_ROSTER_ALREADY_IN'];
						}
					}
					$totalmembers	= $row['roster_members'] . '|' . $user_r_id;
				}
				
				if (!sizeof($error))
				{
					$sql_array	= array(
						'roster_members'	=> $totalmembers
					);
					$sql = "UPDATE " . RIVAL_ROSTERS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
					$db->sql_query($sql);
					
					// Completed. Let the user know.
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster");
					meta_refresh(2, $redirect_url);
					trigger_error('USER_ROSTER_ADDED');
				}
			}
		}
		
		// Delete roster
		if ($delroster)
		{
			$roster_id	= (int) request_var('rosteridx', 0);
			
			// Check if this roster are in a ongoing tournament
			$sql_c		= "SELECT * FROM " . TGROUPS_TABLE . " AS tg LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tg.group_tournament = tt.tournament_id
						WHERE tg.roster_id = {$roster_id} AND tt.tournament_status = 2";
			$result_c	= $db->sql_query_limit($sql_c, 1);
			$row_c		= $db->sql_fetchrow($result_c);
			$db->sql_freeresult($result_c);
			
			/*// Check if this roster are in a ongoing league cycle (status = 2 for ongoin leagues, status = 1 for ogoing leagues but in cycle pause!)
			$sql_d		= "SELECT * FROM " . LEAGUES_CLAN_DATA . " AS ld LEFT JOIN " . LEAGUES_TABLE . " AS lt ON ld.league_id = lt.league_id
						WHERE ld.roster = {$roster_id} AND lt.league_status = 2";
			$result_d	= $db->sql_query_limit($sql_d, 1);
			$row_d		= $db->sql_fetchrow($result_d);
			$db->sql_freeresult($result_d);*/
			
			if (!empty($row_c['group_id']))/* || !empty($row_d['clan_id'])) /* this roster are in a ongoin competition */
			{
				$error[] = $user->lang['ROSTER_IN_COMPETITION_DEL'];
			}
			else
			{
				$sql	= "DELETE FROM " . RIVAL_ROSTERS . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster");
				meta_refresh(2, $redirect_url);
				trigger_error('ROSTER_DELETED');
			}
		}
		
		// Sets roster leader
		if ($setleader)
		{
			$roster_id	= (int) request_var('rosteridx', 0);
			$leader_id	= (int) request_var('leader_id', 0);
			
			$sql_array	= array(
				'roster_leader'	=> $leader_id
			);
			$sql = "UPDATE " . RIVAL_ROSTERS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
			$db->sql_query($sql);
			
			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster");
			meta_refresh(2, $redirect_url);
			trigger_error('ROSTER_UPDATED');
		}
		
		// Edit roster name
		if ($editroster)
		{
			$roster_id		= (int) request_var('rosteridx', 0);
			$roster_name	= (string) utf8_normalize_nfc(request_var('rostername', '', true));
			
			if (empty($roster_name))
			{
				$error[] = $user->lang['ROSTER_NAME_EMPTY'];
			}
			
			// Validate the name
			$sql_2_array = array(
				'UCASE(roster_name)'	=> strtoupper($roster_name),
			);
			$sqlS		= 'SELECT * FROM ' . RIVAL_ROSTERS . ' WHERE ' . $db->sql_build_array('SELECT', $sql_2_array) 
						. ' AND clan_id = ' . $group->data['group_id'];
			$resultS	= $db->sql_query_limit($sqlS,1);
			$rowS		= $db->sql_fetchrow($resultS);
			$db->sql_freeresult($resultS);
			
			if (!empty($rowS['roster_id']))
			{
				$error[] = $user->lang['ROSTER_NAME_USED'];
			}
			
			if (!sizeof($error))
			{
				$sql_array	= array(
					'roster_name'	=> $roster_name
				);
				$sql = "UPDATE " . RIVAL_ROSTERS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster");
				meta_refresh(2, $redirect_url);
				trigger_error('ROSTER_UPDATED');
			}
		}
		
		// Remove members from roster
		if ($deletembrs)
		{
			$toberemoved	= request_var('toberemoved', array(0 => 0));
			$roster_id		= (int) request_var('rosteridx', 0);
			
			$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			// Check if exist
			if (!empty($row['roster_id']))
			{
				// Check if this roster are in a ongoing tournament
				$sql_c		= "SELECT * FROM " . TGROUPS_TABLE . " AS tg LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tg.group_tournament = tt.tournament_id
							WHERE tg.roster_id = {$roster_id} AND tt.tournament_status = 2";
				$result_c	= $db->sql_query_limit($sql_c, 1);
				$row_c		= $db->sql_fetchrow($result_c);
				$db->sql_freeresult($result_c);
				
				/*// Check if this roster are in a ongoing league cycle (status = 2 for ongoin leagues, status = 1 for ogoing leagues but in cycle pause!)
				$sql_d		= "SELECT * FROM " . LEAGUES_CLAN_DATA . " AS ld LEFT JOIN " . LEAGUES_TABLE . " AS lt ON ld.league_id = lt.league_id
							WHERE ld.roster = {$roster_id} AND lt.league_status = 2";
				$result_d	= $db->sql_query_limit($sql_d, 1);
				$row_d		= $db->sql_fetchrow($result_d);
				$db->sql_freeresult($result_d);*/
				
				if (!empty($row_c['group_id']))/* || !empty($row_d['clan_id'])) /* this roster are in a ongoin competition */
				{
					$error[] = $user->lang['ROSTER_IN_COMPETITION'];
				}
			
				$newmember = $row['roster_members'];
				
				foreach ($toberemoved AS $uremoved)
				{
					$newmember	= str_replace($uremoved, '', $row['roster_members']);
				}
				// Clean all
				$newmember	= str_replace('||', '|', $newmember);
				$newmember	= (substr($newmember, 0, 1) == '|') ? substr($newmember, 1) : $newmember;
				$newmember	= (substr($newmember, -1) == '|') ? substr($newmember, 0, -1) : $newmember;
				
				if (!sizeof($error))
				{
					$sql_array	= array(
						'roster_members'	=> $newmember
					);
					$sql = "UPDATE " . RIVAL_ROSTERS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE roster_id = {$roster_id} AND clan_id = " . $group->data['group_id'];
					$db->sql_query($sql);
					
					// Completed. Let the user know.
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster");
					meta_refresh(2, $redirect_url);
					trigger_error('ROSTER_UPDATED');
				}
			}
		}
	}
	// Assign the other variables to the template.
	$template->assign_vars(array(
		'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : '',
		'U_ACTION'	=> $u_action,
	));
}

?>