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
 * MatchComm
 * Called from ucp_rivals with mode == 'matchcomm'
 */
function ucp_rivals_matchcomm($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group	= new group();
	$error	= array();
	$submit	= (!empty($_POST['submit'])) ? true : false;
	$delete	= (int) request_var('del', 0);
	
	// Check if you have a clan.
	if (empty($user->data['group_session']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
		
		break;
	}
	
	//ADD short msg to your clan
	if ($submit)
	{
		$smsg_text	= substr(utf8_normalize_nfc(request_var('smsg_text', '', true)), 0, 251);
	
		if (empty($smsg_text))
		{
			$error[] = $user->lang['ENTER_SMSG_TEXT'];
		}
		else
		{
			$uid = $bitfield = $options = '';
			$allow_bbcode = $allow_urls = $allow_smilies = true;
			generate_text_for_storage($smsg_text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
	
			$sql_array	= array(
				'group_id'			=> $group->data['group_id'],
				'matchcomm_message'	=> $smsg_text,
				'matchcomm_time'	=> time(),
				'bbcode_uid'		=> $uid,
				'bbcode_bitfield'	=> $bitfield,
				'bbcode_options'	=> $options			
			);
			$sql	= "INSERT INTO " . CLANSMSG_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);
		
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchcomm");
			meta_refresh(2, $redirect_url);
			trigger_error('SMSG_INSERTED');
		}
	}
	
	// delete a smsg
	if (!empty($delete))
	{
		//check for hack attempt
		$sql1		= "SELECT * FROM " . CLANSMSG_TABLE . " WHERE smsg_id = " . (int) $delete;
		$result1	= $db->sql_query_limit($sql1, 1);
		$row1		= $db->sql_fetchrow($result1);
		$db->sql_freeresult($result1);
		
		if ($row1['group_id'] != $group->data['group_id'] || empty($row1['group_id']))
		{
			$url = append_sid("{$phpbb_root_path}index.$phpEx");
			meta_refresh(4, $url);
			trigger_error('CHEATER');
	
			break;
		}
		else
		{
			$sql	= "DELETE FROM " . CLANSMSG_TABLE . " WHERE smsg_id = " . (int) $delete;
			$db->sql_query($sql);
			
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchcomm");
			redirect($redirect_url);
		}
	}
	
	
	// Get lastest 30 messages
	$sql 	= " SELECT * FROM " . CLANSMSG_TABLE . " WHERE group_id = {$group->data['group_id']} ORDER BY matchcomm_time DESC ";
	$result = $db->sql_query_limit($sql, 30);
	$i		= 0;
	while ($row = $db->sql_fetchrow($result))
	{		
		$template->assign_block_vars('block_smsg', array(
			'SMSG_ID'	=> $row['smsg_id'],
			'SMSG_TEXT'	=> generate_text_for_display($row['matchcomm_message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']),
			'DEL_WORK'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchcomm&amp;del=" . $row['smsg_id']),
			'SMSG_TIME'	=> $user->format_date($row['matchcomm_time'])
		));
		$i++;
	}
	$db->sql_freeresult($result);
	
	// Assign the other variables to the template.
	$template->assign_vars(array(
		'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : '',
		'U_ACTION'	=> $u_action
	));
}

?>