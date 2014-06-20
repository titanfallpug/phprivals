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

$ladder		= new ladder();
$rules		= (int) request_var('rules', 0);
$platform	= (int) request_var('platform', 0);

// Are we going to show the rules?
if(!empty($rules))
{
	// Setup the BBcode for the ladder rules.
	$rules	= nl2br(generate_text_for_display($ladder->data['ladder_rules'], $ladder->data['bbcode_uid'], $ladder->data['bbcode_bitfield'], $ladder->data['bbcode_options']));
	trigger_error($rules);
}

// Get the parent ladders and order them.
$sql	= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = 0 AND ladder_platform = {$platform} ORDER BY ladder_order ASC";
$result	= $db->sql_query($sql);

while($row = $db->sql_fetchrow($result))
{
	// Assign the ladders to the template.
	$template->assign_block_vars('block_ladders', array(
		'U_LADDERRULES' => append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;rules=1&amp;ladder_id=' . $row['ladder_id']),
		'LADDER_LOGO'	=> $row['ladder_logo'],
		'LADDER_NAME'	=> $row['ladder_name']
	));

	// Get the sub-ladders and order them.
	$sql_2		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = {$row['ladder_id']} ORDER BY subladder_order ASC";
	$result_2	= $db->sql_query($sql_2);

	while($row_2 = $db->sql_fetchrow($result_2))
	{
		if ($row_2['ladder_oneone'] == 1)
		{ 
			// Count the number of users in the sub-ladder.
			$totalinladder	= get_totaluserladder($row_2['ladder_id'], true);
		}
		else
		{
			// Count the number of groups in the sub-ladder.
			$totalinladder	= get_totaluserladder($row_2['ladder_id'], false);
		}

		// Setup the BBcode for the ladder description.
		$desc	= nl2br(generate_text_for_display($row_2['ladder_desc'], $row_2['bbcode_uid'], $row_2['bbcode_bitfield'], $row_2['bbcode_options']));

		// DIVISIONE LADDER CHIUSE
		if($row_2['ladder_locked'] != 1)
		{
			$xplay = $row_2['ladder_rm'] + 1;
		// Assign the sub-ladders to the template.
			$template->assign_block_vars('block_ladders.block_subladders', array(
				'U_ACTION'		=> ($row_2['ladder_oneone'] == 1) ? append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=userssubladder&amp;ladder_id=' . $row_2['ladder_id']) : append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $row_2['ladder_id']),
				'LADDER_NAME'	=> $row_2['ladder_name'],
				'NUM_GROUPS'	=> $totalinladder,
				'ONEONE'		=> ($row_2['ladder_oneone'] == 1) ? true : false,
				'DECERTO_ICON'	=> ($row_2['ladder_style'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/decerto_si.jpg" alt="' . $user->lang['ICON_DECERTO'] . '" title="' . $user->lang['ICON_DECERTO'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/decerto_no.jpg" alt="' . $user->lang['ICON_DECERTO_NO'] . '" title="' . $user->lang['ICON_DECERTO_NO'] . '" />',
				'CPC_ICON'		=> ($row_2['ladder_style'] == 2) ? '<img src="' . $phpbb_root_path .'rivals/images/cpc_si.jpg" alt="' . $user->lang['ICON_CPC'] . '" title="' . $user->lang['ICON_CPC'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/cpc_no.jpg" alt="' . $user->lang['ICON_CPC_NO'] . '" title="' . $user->lang['ICON_CPC_NO'] . '" />',
				'CALCIO_ICON'	=> ($row_2['ladder_style'] == 3) ? '<img src="' . $phpbb_root_path .'rivals/images/soccer_yes.jpg" alt="' . $user->lang['ICON_SOCCER'] . '" title="' . $user->lang['ICON_SOCCER'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/soccer_no.jpg" alt="' . $user->lang['ICON_SOCCER_NO'] . '" title="' . $user->lang['ICON_SOCCER_NO'] . '" />',
				'IMG_ADVSTATS'	=> ($row_2['ladder_advstat'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/advstat_yes.jpg" alt="' . $user->lang['ICON_ADVSTATS'] . '" title="' . $user->lang['ICON_ADVSTATS'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/advstat_no.jpg" alt="' . $user->lang['ICON_ADVSTATS_NO'] . '" title="' . $user->lang['ICON_ADVSTATS_NO'] . '" />',
				'IMG_MVP'		=> ($row_2['ladder_mvp'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/mvp_si.jpg" alt="' . $user->lang['ICON_MVP'] . '" title="' . $user->lang['ICON_MVP'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/mvp_no.jpg" alt="' . $user->lang['ICON_MVP_NO'] . '" title="' . $user->lang['ICON_MVP_NO'] . '" />',
				'IMG_1VS1'		=> ($row_2['ladder_oneone'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/1vs1_si.jpg" alt="' . $user->lang['1VS1LADDER'] . '" title="' . $user->lang['1VS1LADDER'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/1vs1_no.jpg" alt="' . $user->lang['1VS1LADDER_NO'] . '" title="' . $user->lang['1VS1LADDER_NO'] . '" />',
				'IMG_RTH'		=> ($row_2['ladder_ranking'] == 2) ? '<img src="' . $phpbb_root_path .'rivals/images/rth_si.jpg" alt="' . $user->lang['RTH_LADDER'] . '" title="' . $user->lang['RTH_LADDER'] . '" />' : '<img src="' . $phpbb_root_path .'rivals/images/rth_no.jpg" alt="' . $user->lang['RTH_LADDER_NO'] . '" title="' . $user->lang['RTH_LADDER_NO'] . '" />',
				'LADDER_DESC'	=> (!empty($row_2['ladder_desc'])) ? "> {$desc}" : '',
				'LADDER_LIMIT'	=> ($row_2['ladder_rm'] == 0) ? '' : "({$xplay}{$user->lang['VS']}{$xplay})"
			));
		}
		else
		{
			$template->assign_block_vars('block_ladders.block_subladders_locked', array(
				'U_ACTION'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $row_2['ladder_id']),
				'LADDER_NAME'	=> $row_2['ladder_name']
			));
		}
	
	}
	$db->sql_freeresult($result_2);
}

$db->sql_freeresult($result);

// Get the platform's data.
$sql	= "SELECT * FROM " . PLATFORMS_TABLE . " WHERE platform_id = " . $platform;
$result	= $db->sql_query($sql);
$row	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['PLATFORM'] . ': ' . $row['platform_name'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $platform))
);

$template->set_filenames(array('body' => 'rivals/ladders.html'));

?>