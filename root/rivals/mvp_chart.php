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
* Show MVP chart
*/
$group		= new group();
$valuemvp	= (int) request_var('ladder_mvp', 0);
		
// Load user data
	
$sql	= "SELECT ls.*, ut.user_id, ut.username, ut.gamer_name, ut.user_regdate FROM " . USER_LADDER_STATS . " AS ls LEFT JOIN " . USERS_TABLE . " AS ut ON ls.user_id = ut.user_id 
		WHERE ls.ladder_id = {$valuemvp} AND ls.mvps > 0 GROUP BY ls.user_id ORDER BY ls.mvps DESC";
$result	= $db->sql_query_limit($sql, 50);		
$i = 0; 
while ($row = $db->sql_fetchrow($result))
{
	$numero = $i + 1;
	$originalclan	= getoriginalclan_id($row['user_id'], $row['ladder_id']);

	// Assign the member's data to the template.
	$template->assign_block_vars('block_members', array(
		'U_MEMBERPROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
		'U_CLAN'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $originalclan),
		'MEMBER_NAME'		=> $row['username'],
		'MEMBER_MVP'		=> $row['mvps'],
		'GAMERNAME'			=> (!empty($row['gamer_name'])) ? $row['gamer_name'] : 'na',
		'NUM'				=> $numero,
		'MEMBER_CLAN'		=> $group->data('group_name', $originalclan),
		'ONLINE_DA'			=> $user->format_date($row['user_regdate']),
		'ROW_COLOR' 		=> ($i % 2) ? 'row1' : 'row2'
	));
	$i++;
}
$db->sql_freeresult($result);

// Ladder data loads
$sql_t		= "SELECT * FROM " . RIVAL_MVP . " WHERE ladder_mvp = " . $valuemvp;
$result_t	= $db->sql_query_limit($sql_t, 1);
$row_t		= $db->sql_fetchrow($result_t);
$db->sql_freeresult($result_t);
		 
$template->assign_block_vars('block_ladder', array(
	'U_LADDERURL' => append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $row_t['ladder_mvp']),
	'NOME_LADDER' => $row_t['nome_mvp']
));

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['MVP'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=mvp'))
);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $row_t['nome_mvp'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $row_t['ladder_mvp'])
));

$template->set_filenames(array('body' => 'rivals/mvp_chart.html'));
?>