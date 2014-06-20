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


$group		= new group();
include($phpbb_root_path . 'rivals/classes/function_security.' . $phpEx);

$type		= (int) request_var('type', 0);
$group_id	= (int) request_var('group_id', 0);
$group_data	= $group->data('*', $group_id);
$user_id	= (int) $user->data['user_id'];
$num_sid	= (int) request_var('sid', 0);

define('USERS_REQUEST', 1);
define('PM_REQUEST', 2);

// before all check if clan exist
if (clan_check($group_id) == 0)
{
	$url = append_sid("{$phpbb_root_path}index.$phpEx");
	meta_refresh(4, $url);
	trigger_error('CHEATER');
	break;
}

// Validate user
if (validate_user($user->data['user_id'], $config['rivals_bannedgroup'], $config['rivals_minpost']) == false)
{
	$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
	meta_refresh(4, $url);
	trigger_error(sprintf($user->lang['USER_CANT_PLAY'], $config['rivals_minpost']));
	break;
}

// Check if there is a request to join.
if ($type == USERS_REQUEST)
{	
	// check if the user can join
	if (validate_user4clan($user_id, $group_id, true) == 0)
	{
		$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
		meta_refresh(4, $url);
		trigger_error('YOUR_APART_OF_GROUP');
	} 
	else
	{
		// CARICO I DATI.
		$sql_array	= array(
			'group_id'		=> $group_id,
			'user_id'		=> $user_id,
			'mvp_utente'	=> 0,
			'group_leader'	=> 0,
			'user_pending'	=> 1
		);
		$sql		= "INSERT INTO " . USER_CLAN_TABLE . "" . $db->sql_build_array ('INSERT', $sql_array);
		$db->sql_query($sql);

		// Send a PM to the group leader.
		$subject	= $user->lang['PMPENDINGMEMBER'];
		$message	= sprintf($user->lang['PMPENDINGMEMBERTXT'], $user->data['username'], $group->data('group_name', $group_id));
		insert_pm($group->data('user_id', $group_id), $user->data, $subject, $message);

		// Completed. Let the user know.
		$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
		meta_refresh(2, $url);
		trigger_error('REQUEST_SENT');
	}
}
else if ($type == PM_REQUEST)
{
	// This is linked from the PM when a group leader sends a invite.
	$type_2	= (int) request_var('type_2', 0);
	$tuo_id = (int) request_var('id_r', 0);

	if ($tuo_id != $user->data['user_id'])
	{
  		trigger_error('CHEATER');
	}
	else
	{
		// accept
		if ($type_2 == 1)
		{
			if (validate_user4clan($tuo_id, $group_id, true) == 0)
			{
				$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
				meta_refresh(4, $url);
				trigger_error('YOUR_APART_OF_GROUP');
			}
			else
			{
				$sql_array	= array(
					'group_id'		=> $group_id,
					'user_id'		=> $tuo_id,
					'mvp_utente'	=> 0,
					'group_leader'	=> 0,
					'user_pending'	=> 0
				);
				$sql		= "INSERT INTO " . USER_CLAN_TABLE . "" . $db->sql_build_array ('INSERT', $sql_array);
				$db->sql_query($sql);
				
				$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
				meta_refresh(2, $url);
				trigger_error('JOINED_GROUP');
			}
		}
		// decline
		else if ($type_2 == 2)
		{
			if (validate_user4clan($tuo_id, $group_id, true) == 0)
			{
				$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
				meta_refresh(4, $url);
				trigger_error('YOUR_APART_OF_GROUP');
			}
			else
			{
				// Send a PM to the group leader.
				$subject	= $user->lang['PMPENDINGMEMBER'];
				$message	= sprintf($user->lang['PMPENDINGMEMBERTXT_DECLINED'], $user->data['username']);
				insert_pm($group->data('user_id', $group_id), $user->data, $subject, $message);
				
				// Completed. Let the user know.
				$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
				meta_refresh(2, $url);
				trigger_error('REQUEST_DECLINED');
			}
		}
	}

}

?>