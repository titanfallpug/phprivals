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
if(!defined('IN_PHPBB'))
{
	exit;
}

define('DELETE_MEMBER', 1);
define('UPDATE_FOUNDER', 2);

/**
 * Manage Members
 * Called from ucp_rivals with mode == 'group_members'
 */
function ucp_rivals_group_members($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group	= new group();
	
	// Check if the group is apart of a ladder yet.
	if (empty($user->data['group_session']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
	}

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		$member_id	= request_var('member_id', array(0 => 0));
		$members	= request_var('members', array(0 => 0));

		foreach($member_id AS $user_id)
		{
			if ($members[$user_id] == DELETE_MEMBER)
			{
				// Remove the member.		
				$sql8		= "SELECT * FROM " . USERS_TABLE . " WHERE user_id = " . $user_id;
			    $result8		= $db->sql_query($sql8);
			    $row8		= $db->sql_fetchrow($result8);
			    $db->sql_freeresult($result8);
				
				// if are the user a cofounder
				if ($row8['group_session'] == $group->data['group_id'])
				{
					// check if the user is founder of other clan
					$sql8		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE user_id = " . $user_id . " AND group_leader > 0 AND group_id != " . $group->data['group_id'];
					$result8	= $db->sql_query_limit($sql8, 1);
					$row8		= $db->sql_fetchrow($result8);
					$newsession = $row8['group_id'];
					$db->sql_freeresult($result8);
					
					if (!empty($newsession))
					{
						// if have it
						$sql	= "UPDATE " . USERS_TABLE . " SET group_session = {$newsession} WHERE user_id = {$user_id}";
						$db->sql_query($sql);
					}
					else
					{
						$sql	= "UPDATE " . USERS_TABLE . " SET group_session = 0 WHERE user_id = {$user_id}";
						$db->sql_query($sql);
					}
					// now remove from clan
					$sql	= "DELETE FROM " . USER_CLAN_TABLE . " WHERE user_id = {$user_id} AND group_id = " . $group->data['group_id'];
					$db->sql_query($sql);
				}
				else
				{
					 // se non ha in sessione il gruppo in questione segalo e basta
					$sql = "DELETE FROM " . USER_CLAN_TABLE . " WHERE user_id = {$user_id} AND group_id = " . $group->data['group_id'];
					$db->sql_query($sql);
				}

//				// Completed. Let the user know.
				$urls1 = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=group_members");
				meta_refresh(2, $urls1);
                trigger_error('MEMBER_REMOVED');
			}
			else if ($members[$user_id] == UPDATE_FOUNDER)
			{
				// Update member like cofounder.
				$sql	= "UPDATE " . USERS_TABLE . " SET group_session = " . $group->data['group_id'] . " WHERE user_id = " . $user_id;
			    $db->sql_query($sql);
				
                $sql2	= "UPDATE " . USER_CLAN_TABLE . " SET group_leader = 2 WHERE user_id = {$user_id} AND group_id =" . $group->data['group_id'];
			    $db->sql_query($sql2);
				
			}
		}
		// Completed. Let the user know.
		$urls1 = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=group_members");
		meta_refresh(2, $urls1);
		trigger_error('MEMBER_UPDATED');
	}
	else
	{
		// Get members of the group.
		$members	= (array) $group->members('get_members', $group->data['group_id']);
		$i			= 0;
		foreach ($members AS $value)
		{
			$sql	= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_id = {$group->data['group_id']} AND user_id = " . $value;
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
				
			if ($row['group_leader'] != 1)
			{
				// Assign each member to the template.
				$template->assign_block_vars('block_members', array(
					'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $value),
					'MEMBER_ID'		=> $value,
					'MEMBER_NAME'	=> getuserdata('username', $value),
					'MEMBER_COF'	=> $livello,
					'BG_COLOR'		=> ($i % 2) ? 'bg1' : 'bg2',
					'ROW_COLOR'		=> ($i % 2) ? 'row1' : 'row2'
				));
			}
			$i++;
		}

		// Assign the other variables to the template.
		$template->assign_vars(array(
			'U_ACTION'			=> $u_action,
			'INVITE_IMG'		=> getimg_button('invite_members', 'INVITEMEMBR', 128, 25),
			'PENDING_IMG'		=> getimg_button('pending_members', 'PENDINGMEMBER', 128, 25),
			'U_INVITE_MEMBERS'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=invite_members'),
			'U_PENDING_MEMBERS'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=pending_members'),
		));
	}
}

?>