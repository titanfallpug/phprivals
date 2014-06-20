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
 * Invite Members
 * Called from ucp_rivals with mode == 'invite_members'
 */
function ucp_rivals_invite_members($id, $mode, $u_action)
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
	}

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		$member_id	= utf8_normalize_nfc(request_var('member_id', '', true));

		// Invite user. Get the user's information.
		$sql		= "SELECT * FROM " . USERS_TABLE . " WHERE (username_clean = '" . utf8_clean_string($member_id) . "' OR user_id = " . intval($member_id) . ")";
		$result		= $db->sql_query($sql);
		$row		= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Check to see if the user can be invited
		if (validate_user4clan($row['user_id'], $user->data['group_session'], true) == 0)
		{
			// This user apparently does not exist.
			$urls1 = append_sid ( "{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=invite_members" );
			meta_refresh(3, $urls1);
			trigger_error('USER_NOT_FOUND');
			break;
		}

		// Check if they are inviting themselves.
		if ($row['user_id'] == $user->data['user_id'])
		{
			// They are :/. Kill it.
			$urls1 = append_sid ( "{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=invite_members" );
			meta_refresh(3, $urls1);
			trigger_error('CANT_INVITE_YOURSELF');
			break;
		}
		
		// Validate user for rivals
		if (validate_user($row['user_id'], $config['rivals_bannedgroup'], $config['rivals_minpost']) == false)
		{
			$url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id);
			meta_refresh(4, $url);
			trigger_error(sprintf($user->lang['SEL_USER_CANT_PLAY'], $config['rivals_minpost']));
			break;
		}
		
		// Send the PM.
		$id_relativa = $row['user_id'];
		
		$board   	= generate_board_url();
		$yes_url	= "/rivals.$phpEx?action=join_group&amp;group_id={$group->data['group_id']}&amp;type=2&amp;type_2=1&amp;id_r={$id_relativa}";
		$no_url		= "/rivals.$phpEx?action=join_group&amp;group_id={$group->data['group_id']}&amp;type=2&amp;type_2=2&amp;id_r={$id_relativa}";

		$subject	= $user->lang['PMINVITE'];
		$message	= sprintf($user->lang['PMINTVITETXT'], $group->data['group_name'], $board, $yes_url, $board, $no_url);
		insert_pm($row['user_id'], $user->data, $subject, $message);

		// Completed. Let the user know.
		$urls1 = append_sid ( "{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=invite_members" );
		meta_refresh(2, $urls1);
		trigger_error('USER_INVITED');
	}

	// Assign the other variables to the template.
	$template->assign_vars(array('U_ACTION' => $u_action));
}

?>