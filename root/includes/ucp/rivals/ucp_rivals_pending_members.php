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

/**
 * Manage Pending Members
 * Called from ucp_rivals with mode == 'pending_members'
 */
function ucp_rivals_pending_members($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpEx;

	$group	= new group();
	
	// Check if the group is apart of a ladder yet.
	if (empty($user->data['group_session']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
		break;
	}

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		$accept		= request_var('accept', array(0 => 0));
		$decline	= request_var('decline', array(0 => 0));
		nodouble_check($accept, $decline, 'i=rivals&amp;mode=pending_members');
		
		if (!empty($accept))
		{
			foreach ($accept AS $value)
			{
				// Validate user for rivals
				if (validate_user($value, $config['rivals_bannedgroup'], $config['rivals_minpost']) == false)
				{
					$url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=pending_members");
					meta_refresh(4, $url);
					trigger_error(sprintf($user->lang['SEL_USER_CANT_PLAY'], $config['rivals_minpost']));
					break;
				}
				else
				{
					// Add the user to the group.
					$sql_array	= array(
						'user_pending' => 0
					);
					$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = {$group->data['group_id']} AND user_id = {$value}";
					$db->sql_query($sql);

					// Send a PM to the member telling them they have been approved.
					$subject	= $user->lang['PMREQUEST_APPROVED'];
					$message	= sprintf($user->lang['PMREQUEST_APPROVEDTXT'], $group->data['group_name']);
					insert_pm($value, $user->data, $subject, $message);
				}
			}
		}
		
		if (!empty($decline))
		{
			foreach ($decline AS $value)
			{
				// Remove the pending member.
				$sql	= "DELETE FROM " . USER_CLAN_TABLE . " WHERE group_id = {$group->data['group_id']} AND user_id = {$value}";
				$db->sql_query($sql);

				// Send a PM to the member telling them they have been declined.
				$subject	= $user->lang['PMREQUEST_DECLINED'];
				$message	= sprintf($user->lang['PMREQUEST_DECLINEDTXT'], $group->data['group_name']);
				insert_pm($value, $user->data, $subject, $message);
			}
		}
		
		// Completed. Let the user know.
		$urls1 = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=pending_members");
		meta_refresh(2, $urls1);
		trigger_error('USER_PENDING_COMPLETE');
	}

	// Get pending members.
	$members	= (array) $group->members('get_pending', $group->data['group_id']);
	$i			= 0;
	foreach($members AS $value)
	{
		// Assign each member to the template.
		$template->assign_block_vars('block_pendingmembers', array(
			'U_PROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $value),
			'MEMBER_ID'		=> getuserdata('user_id', $value),
			'MEMBER_NAME'	=> getuserdata('username', $value),
			'BG_COLOR'		=>($i % 2) ? 'bg1' : 'bg2',
			'ROW_COLOR'		=>($i % 2) ? 'row1' : 'row2'
		));
		$i++;
	}

	// Assign the other variables to the template.
	$template->assign_vars(array('U_ACTION' => $u_action));
}

?>