<?php
/**
*
* @package acp
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
 * Edit Ladders
 * Called from acp_rivals with mode == 'edit_ladders'
 */
function acp_rivals_edit_ladders($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$move		= (!empty($_POST['move'])) ? true : false;
	$ladder_id	= (int) request_var('ladder_id', 0);

	// Moving a ladder position.
	if (!empty($move))
	{
		re_order($move, $ladder_id);
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
		meta_refresh(2, $redirect_url);
		trigger_error('LADDER_MOVED');
	}

	// Get the parent ladders and order them.
	$sql	= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = 0 ORDER BY ladder_order ASC";
	$result	= $db->sql_query($sql);
	$i		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Get the platform this ladder is joined to.
		$sql_2		= "SELECT * FROM " . PLATFORMS_TABLE . " WHERE platform_id = " . $row['ladder_platform'];
		$result_2	= $db->sql_query($sql_2);
		$row_2		= $db->sql_fetchrow($result_2);
		$db->sql_freeresult($result_2);

		// Assign each ladder to the template.
		$template->assign_block_vars('block_ladders', array(
			'U_ACTION'		=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=rivals&amp;mode=edit_ladder&amp;ladder_id=' . $row['ladder_id']),
			'U_MOVEUP'		=> $u_action . '&amp;move=up&amp;ladder_id=' . $row['ladder_id'],
			'U_MOVEDOWN'	=> $u_action . '&amp;move=down&amp;ladder_id=' . $row['ladder_id'],
			'LADDER_NAME'	=> $row['ladder_name'],
			'PLATFORM_NAME'	=> $row_2['platform_name'])
		);

		// Get the sub-ladders for this ladder.
		$sql_3		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = {$row['ladder_id']} ORDER BY subladder_order ASC";
		$result_3	= $db->sql_query($sql_3);
		while ($row_3 = $db->sql_fetchrow($result_3))
		{
			// Assign each sub-ladder to the template.
			$template->assign_block_vars('block_ladders.block_subladders', array(
				'U_ACTION'		=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=rivals&amp;mode=edit_subladder&amp;ladder_id=' . $row_3['ladder_id']),
				'U_MOVEUP'		=> $u_action . '&amp;move=up&amp;ladder_id=' . $row_3['ladder_id'],
				'U_MOVEDOWN'	=> $u_action . '&amp;move=down&amp;ladder_id=' . $row_3['ladder_id'],
				'LADDER_LOCKED'	=> ($row_3['ladder_locked'] == 1) ? '<img src="' . $phpbb_root_path . 'rivals/images/lucchetto.png" alt="locked" style="vertical-align:bottom;" />' : '',
				'1VS1_IMG'		=> ($row_3['ladder_oneone'] == 1) ? '<img src="' . $phpbb_root_path . 'rivals/images/1vs1.gif" alt="' . $user->lang['1VS1LADDER'] . '" style="vertical-align:middle;" />' : '',
				'LADDER_NAME'	=> $row_3['ladder_name'])
			);
		}
		$db->sql_freeresult($result_3);
		
		$i++;
	}
	$db->sql_freeresult($result);
	
	// If empty
	if ($i == 0)
	{
		// They have not added a platform.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_ladder");
		meta_refresh(8, $redirect_url);
		trigger_error($user->lang['MUST_ADD_LADDER'] . adm_back_link($redirect_url));
	}
}

?>