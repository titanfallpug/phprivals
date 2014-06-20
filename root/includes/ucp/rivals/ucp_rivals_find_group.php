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
 * Find a Group
 * Called from ucp_rivals with mode == 'find_group'
 */
function ucp_rivals_find_group($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$userlad	= request_var('user', 'false');

	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		$thefind		= utf8_normalize_nfc(request_var('group_search', '', true));
		$group_search	= ($thefind) ? ("UCASE(c.group_name) " . $db->sql_like_expression($db->any_char . strtoupper($thefind) . $db->any_char) . ' AND ') : ' ';

		$sql	= "SELECT c.*, cl.*, ud.* FROM " . CLANS_TABLE . " c, " . GROUPDATA_TABLE . " cl, " . USER_CLAN_TABLE . " ud WHERE {$group_search}
				c.clan_closed = 0 AND c.group_id = cl.group_id AND c.group_id = ud.group_id AND ud.group_leader = 1 AND ud.user_id != {$user->data['user_id']}
				AND cl.group_ladder IN(" . implode(',', $group->data['group_ladders']) . ")";
		$result	= $db->sql_query($sql);
		$i	= 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$ladder_data	= $ladder->get_roots($row['group_ladder']);
			
			// Assign each group to the template.
			$template->assign_block_vars('block_groups', array(
				'U_GROUP'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['group_id']),
				'ROW_COLOR'		=> ($i % 2) ? 'row1' : 'row2',
				'GROUP_NAME' 	=> $row['group_name'],
				'GROUP_ID'		=> $row['group_id'],
				'PLATFORM' 		=> $ladder_data['PLATFORM_NAME'],
				'LADDER' 		=> $ladder_data['LADDER_NAME'],
				'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME']
			));

			$i++;
		}
		$db->sql_freeresult($result);
	}

	// Assign the other variables to the template.
	$template->assign_vars(array('U_ACTION' => $u_action));
}

?>