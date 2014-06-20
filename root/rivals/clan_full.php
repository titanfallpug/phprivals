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
if(!defined('IN_PHPBB'))
{
	exit;
}

$group		= new group();
$start		= (int) request_var('start', 0);
$alpha		= request_var('alpha', '');

if(!empty($alpha) && $alpha != '09')
{
	// Filtering for name.
	$where	= " AND group_name " . $db->sql_like_expression(substr($alpha, 0, 1) . $db->any_char) . " OR group_name " . $db->sql_like_expression(substr(strtoupper($alpha), 0, 1) . $db->any_char) . " ORDER BY group_name ASC";
}
else if(!empty($alpha) && $alpha == '09')
{
	$where	= " AND ucase(substring(group_name,1,1)) NOT BETWEEN 'A' AND 'Z' ORDER BY group_name ASC";
}
else
{
	$where	= ' ORDER BY group_id DESC';
}

// Prima pagina, lista dei gruppi
$sql	= " SELECT * FROM " . CLANS_TABLE . " WHERE clan_closed = 0 " . $where;
$result = $db->sql_query_limit($sql, 30, $start);
$i		= 0;
while($row = $db->sql_fetchrow($result))
{	
	if ($group->active($row['group_id']) == true)
	{
		$status		= $user->lang['ATTIVO'];
		$warlink	= append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=add_challenge&amp;group_id=' . $row['group_id']);
		$war		= $user->lang['CHALLENGE']; 
	}
	else
	{
		$status		= $user->lang['NON_ATTIVO']; 
		$war		= '-';
		$warlink	= '#';
	}

	$template->assign_block_vars('clan_list', array(
		'GROUP_ID'		=> $row['group_id'],
		'GROUP_NAME'	=> $row['group_name'],
		'STATUS'		=> $status,
		'WAR'			=> $war,
		'U_CHALLENGE'	=> $warlink,
		'U_ACTION'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['group_id']),
		'ROW_COLOR'		=>($i % 2) ? 'row1' : 'row2'
	));
}

// Setup the alphas.
$alphas	= range('a', 'z');
foreach($alphas AS $value)
{
	// Show the A B Cs!
	$template->assign_block_vars('block_alphas', array(
		'U_ALPHA' => append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=clan_full&amp;alpha=' . $value),
		'ALPHA' => $value
	));
}
$template->assign_block_vars('block_tutti', array(
	'U_TUTTI'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=clan_full'),
	'U_09'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=clan_full&amp;alpha=09")
));

// Setup the pagination.
$sql	= " SELECT * FROM " . CLANS_TABLE . " WHERE clan_closed = 0 " . $where;
$result	= $db->sql_query($sql);
$total	= sizeof($db->sql_fetchrowset($result));
$db->sql_freeresult($result);

// Generate the pagination.
$pagination	= generate_pagination(append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=clan_full' . ((!empty($alpha)) ? "&amp;alpha={$alpha}" : '')), $total, 30, $start);

// Assign the other variables to the template.
$template->assign_vars(array(
	'CLANACTIVEPG'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_list'),
	'PAGINATION'	=> $pagination,
	'PAGE_NUMBER'	=> on_page($total, 30, $start)
));

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['CLAN_FULL'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=clan_full'))
);

$template->set_filenames(array('body' => 'rivals/clan_full.html'));
?>