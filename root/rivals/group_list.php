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
if(!defined('IN_PHPBB'))
{
	exit;
}

$group		= new group();
$ladder		= new ladder();
$start		= (int) request_var('start', 0);
$ladder_id	= (int) request_var('ladder_id', 0);
$alpha		= request_var('alpha', '');
$ladderids	= array();

// Get the group's data.
$sql	= "SELECT cl.*, cd.*, ug.user_id FROM " . CLANS_TABLE . " cl, " . GROUPDATA_TABLE . " cd, " . USER_CLAN_TABLE . " ug WHERE cd.group_id = cl.group_id AND ug.group_id = cl.group_id AND ug.group_leader = 1 ";
if ($ladder_id != 0)
{
	// Filtering for ladder.
	$where2	= " AND cd.group_ladder = {$ladder_id} ";
	$pilu	= "&amp;ladder_id={$ladder_id}";
}
else
{
	$where2	= "";
	$pilu	= "";
}

if(!empty($alpha) && $alpha != '09')
{
	// Filtering for name.
	$where	= " AND (cl.group_name " . $db->sql_like_expression(substr($alpha, 0, 1) . $db->any_char) . " OR cl.group_name " . $db->sql_like_expression(substr(strtoupper($alpha), 0, 1) . $db->any_char) . ") ";
}
else if(!empty($alpha) && $alpha == '09')
{
	$where	= " AND ucase(substring(cl.group_name,1,1)) NOT BETWEEN 'A' AND 'Z' ";
}
else
{
	$where = "";
}

// Combine the SQL and the WHERE.
$sql	.= $where . $where2 . "GROUP BY cd.group_id ORDER BY cl.group_name ASC";
$result	= $db->sql_query_limit($sql, 30, $start);
$i	= 0;
while($row = $db->sql_fetchrow($result))
{
	// Assign the groups to the template.
	$template->assign_block_vars('block_groups', array(
		'U_ACTION'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['group_id']),
		'GROUP_NAME'	=> $row['group_name'],
		'GROUP_MEMBERS'	=> sizeof($group->members('get_members', $row['group_id'])),
		'BG_COLOR'		=>($i % 2) ? 'bg1' : 'bg2',
		'ROW_COLOR'		=>($i % 2) ? 'row1' : 'row2'
	));

	// Check if a challenge link is to be showen.
	if($row['user_id'] != $user->data['user_id'] && $user->data['user_id'] != ANONYMOUS)
	{
		// Show the challenge link.
		$template->assign_block_vars('block_groups.block_challenge', array(
			'U_CHALLENGE' => append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=add_challenge&amp;group_id=' . $row['group_id'])
		));
	}
	$i++;
}
$db->sql_freeresult($result);

// Setup the alphas.
$alphas	= range('a', 'z');
foreach($alphas AS $value)
{
	// Show the A B Cs!
	$template->assign_block_vars('block_alphas', array(
		'U_ALPHA'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list&amp;alpha=' . $value . $pilu),
		'ALPHA'		=> $value
	));
}
$template->assign_block_vars('block_tutti', array(
	'U_TUTTI'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list' . $pilu),
	'U_09'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list&amp;alpha=09' . $pilu),
));

// Loop through the ladder list.
$sql	= "SELECT l.*, p.* FROM " . LADDERS_TABLE . " l, " . PLATFORMS_TABLE . " p WHERE l.ladder_platform = p.platform_id AND l.ladder_oneone = 0";
$result	= $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result))
{
	$ladderids[] = array($row['ladder_id'], $row['ladder_name'], $row['platform_name']);		
}
$db->sql_freeresult($result);

foreach ($ladderids AS $theladder)
{
	// Assign it to the template.
	$template->assign_block_vars('block_ladders', array(
		'LADDER_NAME'	=> $theladder[1],
		'PLATFORM'		=> $theladder[2]
	));
	
	$sql_2		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = " . $theladder[0] . " AND ladder_oneone = 0";
	$result_2	= $db->sql_query($sql_2);
	while ($row_2 = $db->sql_fetchrow($result_2))
	{
		// Assign them to the template.
		$template->assign_block_vars('block_ladders.block_subladders', array(
			'LADDER_ID'		=> $row_2['ladder_id'],
			'LADDER_NAME'	=> $row_2['ladder_name'],
			'LADDER_SELECT'	=> ($row_2['ladder_id'] == $ladder_id) ? 'selected="selected"' : ''
		));
	}
$db->sql_freeresult($result_2);
}

// Setup the pagination.
$sql	= "SELECT cl.*, cd.* AS total FROM " . CLANS_TABLE . " cl, " . GROUPDATA_TABLE . " cd WHERE cl.group_id = cd.group_id {$where} GROUP BY cd.group_id";
$result	= $db->sql_query($sql);
$total	= sizeof($db->sql_fetchrowset($result));
$db->sql_freeresult($result);

// Generate the pagination.
$pagination	= generate_pagination(append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list' . ((!empty($ladder_id)) ? "&amp;ladder_id={$ladder_id}" : '') . ((!empty($alpha)) ? "&amp;alpha={$alpha}" : '')), $total, 30, $start);

// Assign the other variables to the template.
$template->assign_vars(array(
	'U_ACTION'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list'),
	'PAGINATION'	=> $pagination,
	'PAGE_NUMBER'	=> on_page($total, 30, $start)
));

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['CLAN_FILTERED'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list'))
);

$template->set_filenames(array('body' => 'rivals/group_list.html'));

?>