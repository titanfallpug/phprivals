<?php
/**
*
* @package ucp
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
 * Add a Challenge
 * Called from ucp_rivals with mode == 'add_challenge'
 */
function ucp_rivals_add_challenge($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$ulad		= (string) request_var('ulad', 'false');
	$ladder_id	= (int) request_var('ladder_id', 0);
	$error		= array();
	
	if ($ulad == 'true')
	{
		if (!empty($ladder_id))
		{	
			$sql9		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} AND user_id = " . $user->data['user_id'];
			$result9	= $db->sql_query($sql9);
			$row9		= $db->sql_fetchrow($result9);
			$db->sql_freeresult($result9);
		
			if (empty($row9['user_id']))
			{
				// They are not apart of a ladder. Deny them.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
				meta_refresh(4, $redirect_url);
				trigger_error(sprintf($user->lang['USER_NOT_IN_LADDER'], '<a href="' . $redirect_url . '">', '</a>'));
			}
		}
		else
		{
			$sql9		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$user->data['user_id']}";
			$result9	= $db->sql_query_limit($sql9, 1);
			$row9		= $db->sql_fetchrow($result9);
			$db->sql_freeresult($result9);
			
			if (empty($row9['user_id']))
			{
				// They are not apart of a ladder. Deny them.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
				meta_refresh(4, $redirect_url);
				trigger_error(sprintf($user->lang['USER_NOT_IN_LADDER'], '<a href="' . $redirect_url . '">', '</a>'));
			}
		}
		
	}
	else
	{	
		if (empty($user->data['group_session']))
		{
			// They are not apart of a ladder. Deny them.
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(4, $redirect_url);
			trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
		}
		else if (empty($group->data['group_ladders']))
		{
			// They are not apart of a ladder. Deny them.
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(4, $redirect_url);
			trigger_error(sprintf($user->lang['GROUP_NOTIN_LADDER'], '<a href="' . $redirect_url . '">', '</a>'));
		}
	}

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if($submit)
	{
		
		$challengee			= utf8_normalize_nfc(request_var('challengee', '', true));
		$challenge_details	= utf8_normalize_nfc(request_var('extra', '', true));
		$challenge_unranked	= (int) request_var('challenge_unranked', 0);
		$ladder_id_sel		= (int) request_var('ladder_id_sel', 0);
		$xulad				= (string) request_var('ulad', 'false');

/// BEGIN USER LADDER
		if ($xulad == 'true')
		{
			// Get the challengee's users data. I split the fint for fix the bug when a username is a numeric one like a user_id of other one.
			// First check the ID
			$challengeeINT = (int) $challengee;
			$sql_3_array = array(
				'user_id'	=> intval($challengeeINT),
			);
			$sql		= 'SELECT user_id, user_type FROM '
						. USERS_TABLE . ' WHERE ' . $db->sql_build_array('SELECT', $sql_3_array)
						. ' AND user_type <> 2';
			$result	= $db->sql_query_limit($sql,1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			if (empty($row['user_id']))
			{
				// No corrispondence with id try the unsername
				$sql_2_array = array(
					'UCASE(username)'	=> strtoupper($challengee),
				);
				$sqlS		= 'SELECT user_id, username, user_type FROM '
							. USERS_TABLE . ' WHERE ' . $db->sql_build_array('SELECT', $sql_2_array) 
							. ' AND user_type <> 2';
				$resultS	= $db->sql_query_limit($sqlS,1);
				$rowS		= $db->sql_fetchrow($resultS);
				$db->sql_freeresult($resultS);
				
				// Again no result
				if (empty($rowS['user_id']))
				{
					$error [] = $user->lang['NONEXISTANT_USER'];
				}
				else
				{
					// Chellengee is in the database. Get their record.
					$challengee	= $rowS['user_id'];
				}
			}
			else
			{
				// Chellengee is in the database. Get their record.
				$challengee	= $row['user_id'];
			}

			
			// Check if the user is on the selected ladder.
			if (!sizeof($error))
			{
				$sql	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id_sel} AND user_id = " . $challengee;
				$result	= $db->sql_query_limit($sql,1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (sizeof($row) == 0)
				{
					// Challengee is not in the same ladder as the logged-in group.
					$error [] = $user->lang['NONEXISTANT_USER2'];
				}
			}
			
			// ANTI AUTOCHALLANGER
			if ($challengee == $user->data['user_id'])
			{
				// They are trying to challenge themself. Warn them.
				$error [] = $user->lang['CHEATER'];
			}
			
			if (!sizeof($error))
			{
				// Finally. Insert the challenge.
				$sql_array	= array(
					'1vs1_challanger'		=> $user->data['user_id'],
					'1vs1_challangee' 		=> $challengee,
					'1vs1_challanger_ip'	=> (!empty($user->data['user_ip'])) ? $user->data['user_ip'] : $_SERVER['REMOTE_ADDR'],
					'1vs1_unranked' 		=> $challenge_unranked,
					'1vs1_details' 			=> $challenge_details,
					'start_time' 			=> time(),
					'1vs1_ladder' 			=> $ladder_id_sel
				);
				$sql	= "INSERT INTO " . ONEVSONE_MATCH_DATA . " " . $db->sql_build_array ('INSERT', $sql_array);
				$db->sql_query($sql);

				// Get all the ladder's root details for the PM.
				$ladder_data	= $ladder->get_roots($ladder_id_sel);
		
				// Send a PM to the challengee.
				$subject	= sprintf($user->lang['PM_CHALLENGE'], $user->data['username']);
				$message	= sprintf(($challenge_unranked == 0) ? $user->lang['PM_CHALLENGETXT_USER'] : $user->lang['PM_CHALLENGETXT2_USER'], $user->data['username'], $ladder_data['PLATFORM_NAME'], $ladder_data['LADDER_NAME'], $ladder_data['SUBLADDER_NAME']);
				insert_pm($challengee, $user->data, $subject, $message);
			}
		}
// END USER LADDER
		else
		{
			// Get the challengee's group data.
			$challengeeINT = (int) $challengee;
			$sql_3_array = array(
				'group_id'	=> intval($challengeeINT),
			);
			$sql		= 'SELECT group_id FROM '
						. CLANS_TABLE . ' WHERE ' . $db->sql_build_array('SELECT', $sql_3_array)
						. ' AND clan_closed = 0';
			$result	= $db->sql_query_limit($sql,1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (empty($row['group_id']))
			{
				// Challengee is not in the database.
				$error [] = $user->lang['NONEXISTANT_GROUP'];
			}

			// Chellengee is in the database. Update the ID.
			$challengee	= $challengeeINT;

			// Check if the opponent is in the selected ladder.
			if (!sizeof($error))
			{
				$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id_sel} AND group_id = " . $challengee;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (sizeof($row) == 0)
				{
					// Challengee is not in the same ladder as the logged-in group.
					$error [] = $user->lang['NONEXISTANT_GROUP2'];
				}
			}

			// If Swap is on, then check for 3 ranks above or below.
			if ($ladder->data('ladder_ranking',  $ladder_id_sel) == 1)
			{
				// Swap is on, check ranking.
				$challenger_rank	= $group->data('group_current_rank', $group->data['group_id'], $ladder_id_sel);
				$rank				= $group->data('group_current_rank', $challengee, $ladder_id_sel);

				if ((($rank - $challenger_rank) >= -3 && ($rank - $challenger_rank) <= 0) || ($challenger_rank - $rank) <= 3)
				{
					// Everything is OK.
				}
				else
				{
					// They are out of the ranking range. Let the user know.
					$error [] = $user->lang['RANKING_RANGE'];
				}
			}

			// Get all the groups owned by the user.
			$sql	= "SELECT gd.*, ud.user_id FROM " . CLANS_TABLE . " gd, " . USER_CLAN_TABLE . " ud WHERE ud.group_leader = 1 AND ud.user_id = {$user->data['user_id']} AND gd.group_id = ud.group_id";
			$result	= $db->sql_query ($sql);
			while ($row = $db->sql_fetchrow ($result))
			{
				if ($challengee == $row['group_id'])
				{
					// They are trying to challenge themself. Warn them.
					$error [] = $user->lang['CHEATER'];
				}
			}
			$db->sql_freeresult($result);

			// Finally. Insert the challenge.
			if (!sizeof($error))
			{
				$sql_array	= array(
					'challenger' 			=> $group->data['group_id'],
					'challengee' 			=> $challengee,
					'challenger_ip'			=> (!empty($user->data['user_ip'])) ? $user->data['user_ip'] : $_SERVER['REMOTE_ADDR'],
					'challenge_unranked' 	=> $challenge_unranked,
					'challenge_details' 	=> $challenge_details,
					'challenge_posttime'	=> time(),
					'challenge_ladder' 		=> $ladder_id_sel
				);
				$sql	= "INSERT INTO " . CHALLENGES_TABLE . " " . $db->sql_build_array ('INSERT', $sql_array);
				$db->sql_query($sql);

				// Get all the ladder's root details for the PM.
				$ladder_data	= $ladder->get_roots($ladder_id_sel);
			
				// Send a PM to the challengee.
				$subject	= sprintf($user->lang['PM_CHALLENGE'], $group->data['group_name']);
				$message	= sprintf(($challenge_unranked == 0) ? $user->lang['PM_CHALLENGETXT'] : $user->lang['PM_CHALLENGETXT2'], $group->data['group_name'], $ladder_data['PLATFORM_NAME'], $ladder_data['LADDER_NAME'], $ladder_data['SUBLADDER_NAME']);
				insert_pm($group->data('user_id', $challengee), $user->data, $subject, $message);
			}
		}
		
		// Assign the other variables to the template.
		$template->assign_vars(array(
			'ERROR'	=> (sizeof($error)) ? implode('<br />', $error) : '',
		));
			
		// Completed. Let the user know.
		if (!sizeof($error))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(2, $redirect_url);
			trigger_error('CHALLENGE_ADDED');
		}
	}

	/*********
	*	Template
	********************/
	if ($ulad == 'true')
	{
		$ladder_selezionata	= $ladder_id;
		$user_id			= (int) request_var('user_id', 0);
		
		$sql	= "SELECT * FROM " . LADDERS_TABLE . " AS lt LEFT JOIN " . ONEVSONEDATA_TABLE . " as one ON lt.ladder_id = one.1vs1_ladder
				WHERE lt.ladder_oneone = 1 AND one.user_id = {$user->data['user_id']} ORDER BY lt.ladder_id ASC ";
		$result	= $db->sql_query($sql);	
		while ($row = $db->sql_fetchrow($result))
		{
			$ladder_data	= $ladder->get_roots($row['ladder_id']);
			if ($ladder_data['SUBLADDER_LOCKED'] == 0)
			{
				// Assign each ladder to the template.
				$template->assign_block_vars('block_ladders', array(
					'LADDER_ID' 		=> $row['ladder_id'],
					'LADDER_SELECTED' 	=> ($ladder_selezionata == $row['ladder_id']) ? 'selected="selected"' : '',
					'PLATFORM' 			=> $ladder_data['PLATFORM_NAME'],
					'LADDER' 			=> $ladder_data['LADDER_NAME'],
					'SUBLADDER' 		=> $ladder_data['SUBLADDER_NAME']
				));
			}
		}
		$db->sql_freeresult($result);
		
		// Assign the other variables to the template.
		$template->assign_vars(array(
			'U_ACTION' 		=> $u_action,
			'USERLADDER'	=> true,
			'ULAD'			=> $ulad,
			'GROUP_ID' 		=> ($user_id != 0) ? $user_id : ''
		));
	}
	else
	{
		$group_id			= (int) request_var('group_id', 0);
		$ladder_selezionata	= $ladder_id;
	
		foreach ($group->data['group_ladders'] AS $value)
		{
			// Get the ladder's roots to show.
			$ladder_data	= $ladder->get_roots($value);

			// Check to see if the ladder is locked.
			if ($ladder_data['SUBLADDER_LOCKED'] == 0)
			{
				// Assign each ladder to the template.
				$template->assign_block_vars('block_ladders', array(
					'LADDER_ID' 		=> $value,
					'LADDER_SELECTED' 	=> ($ladder_selezionata == $value) ? 'selected="selected"' : '',
					'PLATFORM' 			=> $ladder_data['PLATFORM_NAME'],
					'LADDER' 			=> $ladder_data['LADDER_NAME'],
					'SUBLADDER' 		=> $ladder_data['SUBLADDER_NAME']
				));
			}
		}
		// Assign the other variables to the template.
		$template->assign_vars(array(
			'U_ACTION' 		=> $u_action,
			'ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : '',
			'U_FIND_GROUP' 	=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=find_group'),
			'USERLADDER'	=> false,
			'ULAD'			=> $ulad,
			'GROUP_ID' 		=> ($group_id != 0) ? $group_id : ''
		));
	}
}
?>