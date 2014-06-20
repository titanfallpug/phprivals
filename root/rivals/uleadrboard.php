<?php
/**
*
* @package RivalsMod
* @version $Id$
* @copyright (c) 2012 Soshen <nipponart.org>
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
* Show User Leaderboard. The user score are getted from user 1vs1 match and when a user play in a clan.
*/
$start	= request_var('start', 0);
$alpha	= request_var('alpha', '');
$start	= (int) $start;
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
		
// Load user data
$sql	= "SELECT user_id, username, user_avatar, user_avatar_type, user_colour, user_regdate, 
			gamer_name, user_mvp, user_ladder_value, user_exp, user_round_wins, user_round_losses 
		FROM " . USERS_TABLE . " WHERE user_type <> 2 AND user_ladder_value <> 0 
			ORDER BY (100000 + user_ladder_value) DESC";
$result	= $db->sql_query_limit($sql, 50, $start);	
$i = 0; 
$row_number = $start;
if ($row = $db->sql_fetchrow($result))
{
	do
	{
		$row_number++;
		$s_avatar = get_user_avatar($row['user_avatar'], $row['user_avatar_type'], 20, 20);
		// Assign the member's data to the template.
		$template->assign_block_vars('block_members', array(
			'MEMBER_NAME'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'GAMERNAME'			=> (!empty($row['gamer_name'])) ? $row['gamer_name'] : '',
			'NUM'				=> $row_number,
			'MVPS'				=> $row['user_mvp'],
			'SCORE'				=> $row['user_ladder_value'],
			'XP'				=> $row['user_exp'],
			'WINS'				=> $row['user_round_wins'],
			'LOSSES'			=> $row['user_round_losses'],
			'RATIO'				=> ($row['user_round_losses'] == 0) ? 100 : round($row['user_round_wins'] / $row['user_round_losses'],2),
			'ONLINE_DA'			=> $user->format_date($row['user_regdate']),
			'AVATAR'			=> (!$s_avatar) ? '' : $s_avatar,
			'IMGALT'			=> $row['username'],
			'ROW_COLOR' 		=> ($i % 2) ? 'row1' : 'row2'
		));
		$i++;
	}
	while ($row = $db->sql_fetchrow($result));
}
$db->sql_freeresult($result);

// Pagination
$total_items = 0;
$result = $db->sql_query("SELECT COUNT(user_id) as total_items FROM " . USERS_TABLE . " WHERE user_type <> 2 AND user_ladder_value > 0 GROUP BY user_id");
$total_items = (int) $db->sql_fetchfield('total_items');
$db->sql_freeresult($result);

$pagination_url	= append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=uleadrboard');
$pagination		= generate_pagination($pagination_url, $total_items, 50, $start);

// Assign the other variables to the template.
$template->assign_vars(array(
	'PAGINATION'	=> $pagination,
	'PAGE_NUMBER'	=> on_page($total_items, 50, $start),
	'TOTALS'		=> $total_items
));


// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['USER_LEADERBOARD'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=uleadrboard'))
);

$template->set_filenames(array('body' => 'rivals/uleadrboard.html'));
?>