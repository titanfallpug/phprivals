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
 * User Subladder
 * Called from rivals with mode == 'userssubladder'
 */

$ladder		= new ladder();
$start		= (int) request_var('start', 0);
$ladder_id	= (int) request_var('ladder_id', 0);

// CHECK FOR ONEONE
if ($ladder->data['ladder_oneone'] == 0)
{
	$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $ladder_id);
	redirect($redirect_url);
}

// CHECK FOR RTH IF SOMEONE GOT 1000 POINTS
if ($ladder->data['ladder_ranking'] == 2)
{
	$sqlj		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} ORDER BY user_score DESC";
	$resultj	= $db->sql_query_limit($sqlj, 1);
	$rowj		= $db->sql_fetchrow($resultj);
	$db->sql_freeresult($resultj);
		
	if ($rowj['user_score'] >= 1000)
	{
		// block the join but do not set like locked (=1)
		$sql_array	= array(
			'ladder_locked'	=> 2
		);
		$sql = "UPDATE " . LADDERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = " . $ladder_id;
		$db->sql_query($sql);
		
		// the winner is
		$template->assign_vars(array(
			'LAD_WINNER'	=> getusername($rowj['user_id']),
			'U_LADWINNER'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $rowj['user_id']),
		));	
	}
}

// Order the users by their score.
switch ($ladder->data['ladder_ranking'])
{
	case 0: /* ELO */
	case 2: /* RTH */
		$order_by	= 'cdd.user_score';
		$sort		= 'DESC';
	break;
	case 1: /* SWAP */
		$order_by	= 'cdd.user_current_rank';
		$sort		= 'ASC';
	break;
}

if ($ladder->data['ladder_style'] == 3)
{
///////////// CALCOLO I VALORI MASSIMALI GOALS
	$sql_t		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " cdd, " . USERS_TABLE . " usd WHERE cdd.1vs1_ladder = {$ladder_id} AND usd.user_id = cdd.user_id ORDER BY {$order_by} {$sort}";
	$result_t	= $db->sql_query($sql_t);

	$gol_max = 0;
	$gol_min = 0;

	$it	= 0;
	while ($row_t = $db->sql_fetchrow($result_t))
	{
		$media_gol = (($row_t[ 'user_goals_fatti' ]) - ($row_t[ 'user_goals_subiti' ]));
		$gol_max = $media_gol > $gol_max ? $media_gol : $gol_max;
		$gol_min = $media_gol < $gol_min ? $media_gol : $gol_min;
	$it++;
	}
	$db->sql_freeresult( $result_t );

//////////////////////
}

$sql		= "SELECT cdd.*, usd.* FROM " . ONEVSONEDATA_TABLE . " cdd, " . USERS_TABLE . " usd WHERE cdd.1vs1_ladder = {$ladder_id} AND usd.user_id = cdd.user_id ORDER BY {$order_by} {$sort}";
$result		= $db->sql_query_limit($sql, 30, $start);

$i	= 0;
while($row = $db->sql_fetchrow($result))
{
	if ($order_by == 'cdd.user_score')
	{
		// Apply the proper ladder position to the user.
		// Offset it based on the page number.
		if ($start > 0)
		{
			$pos	= ordinal($start +($i + 1));
		}
		else
		{
			$pos	= ordinal($i + 1);
		}
	}
	else
	{
		// Use the ordinal current rank.
		$pos	= ordinal($row['user_current_rank']);
	}

	// Check if a challenge link is to be showen.
	if ($user->data['user_id'] != ANONYMOUS && $ladder->data['ladder_cl'] == 1 && $row['user_id'] != $user->data['user_id'] && $ladder->data['ladder_locked'] != 1)
	{
		// Show the challenge link.
		$challenge_link	= append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=add_challenge&amp;ulad=true&amp;user_id=' . $row['user_id'] . '&amp;ladder_id=' . $ladder_id);
	}
	else
	{
		// Don't show it.
		$challenge_link	= '';
	}

	// Get the up or down ranking image.
	if ($row['user_last_rank'] != $row['user_current_rank'] && $row['user_last_rank'] != 0)
	{
		if ($row['user_current_rank'] < $row['user_last_rank'])
		{
			// They have moved up.
			$up_down	= '<img src="' . $phpbb_root_path . 'rivals/images/rank_up.png" alt="rankup" />';
		}
		else
		{
			// They have moved down.
			$up_down	= '<img src="' . $phpbb_root_path . 'rivals/images/rank_down.png" alt="rankdown" />';
		}
	}
	else
	{
		// They are a new user.
		$up_down	= '';
	}

	// Get the hot or cold image.
	if ($row['user_streak'] >= 4)
	{
		// They are on a hot streak.
		$hot_cold	= '<img src="' . $phpbb_root_path . 'rivals/images/hot.png" class="status_icon" alt="hot" />';
	}
	else if ($row['user_streak'] <= -4)
	{
		// They are on a cold streak.
		$hot_cold	= '<img src="' . $phpbb_root_path . 'rivals/images/cold.png" class="status_icon" alt="cold" />';
	}
	else
	{
		// They are neutral.
		$hot_cold	= '';
	}
	
	/// TROFEI
	$sqlx		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} ORDER BY user_ratio DESC";
	$resultx	= $db->sql_query_limit($sqlx, 1);
	$rowx		= $db->sql_fetchrow($resultx);
	$db->sql_freeresult($resultx);
	
	$sqly		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} ORDER BY user_ratio ASC";
	$resulty	= $db->sql_query_limit($sqly, 1);
	$rowy		= $db->sql_fetchrow($resulty);
	$db->sql_freeresult($resulty);
	
	if ($row['user_id'] == $rowx['user_id'] && $row['user_wins'] >= 8)
	{
		$best = '<img src="' . $phpbb_root_path . 'rivals/images/crown.gif" class="status_icon" alt="crown" />';
	}
	else if ($row['user_id'] == $rowy['user_id'] && $row['user_losses'] >= 8)
	{
		$best = '<img src="' . $phpbb_root_path . 'rivals/images/tomb.png" class="status_icon" alt="tomb" />';
	}
	else
	{
		$best = '';
	}

////////////////////////// CAPOCANNONIERE
	if ($ladder->data['ladder_style'] == 3)
	{
		$goals =($row['user_goals_fatti'] - $row['user_goals_subiti']);
		if (($goals == $gol_max) && ($row['user_goals_fatti'] > 10))
		{
			$capoc = '<img src="' . $phpbb_root_path . 'rivals/images/scarpaoro.png" class="status_icon" alt="goleador" />';
		}
		else if (($goals == $gol_min) &&($row['user_goals_subiti'] > 10))
		{
			$capoc = '<img src="' . $phpbb_root_path . 'rivals/images/saponetta.png" class="status_icon" alt="soap" />';
		}
		else
		{
			$capoc = '';
		}
	}
	else
	{ 
		$capoc = '';
	}

	$piu = (is_numeric(substr($row['user_streak'], 0, 1))) ? '+' : '';
	
	// Assign the users to the template.
	$template->assign_block_vars('block_usersres', array(
		'U_ACTION'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
		'U_CHALLENGE_LINK' 	=> $challenge_link,
		'POS' 				=> $pos,
		'UP_DOWN' 			=> $up_down,
		'HOT_COLD' 			=> $hot_cold,
		'BEST' 				=> $best,
		'STATUS' 			=> getactivity_status($row['user_id'], $ladder_id, true),
		'CANNONIERE' 		=> $capoc,
		'USER_NAME'			=> $row['username'],
		'USER_ID' 			=> $row['user_id'],
		'USER_WINS' 		=> $row['user_wins'],
		'USER_LOSSES' 		=> $row['user_losses'],
		'USER_SCORE' 		=> $row['user_score'],
		'USER_STREAK' 		=> $piu . $row['user_streak'],
		'PAREGGI' 			=> $row['user_pari'],
		'SCORE_OK'	 		=> $row['user_goals_fatti'],
		'SCORE_BAD' 		=> $row['user_goals_subiti'],
		'RATIO'				=> ($row['user_goals_subiti'] == 0) ? 100 : round($row['user_goals_fatti'] / $row['user_goals_subiti'],3),
		'CALCIO'			=> ($ladder->data['ladder_style'] == 3) ? true : false,
		'BG_COLOR' 			=> ($i % 2) ? 'bg1' : 'bg2',
		'ROW_COLOR' 		=> ($i % 2) ? 'row1' : 'row2'
	));

	$i++;
}
$db->sql_freeresult($result);

// Setup the pagination.
$sql	= "SELECT COUNT(user_id) AS total FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . $ladder_id;
$result	= $db->sql_query($sql);
$total	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Generate the pagination.
$pagination	= generate_pagination(append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=userssubladder&amp;ladder_id=' . $ladder_id), $total['total'], 30, $start);

// Check if the user is in this sub-ladder or not.
if ($user->data['user_id'] >= ANONYMOUS)
{
	$sql9		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} AND user_id = " . $user->data['user_id'];
	$result9	= $db->sql_query($sql9);
	$row9		= $db->sql_fetchrow($result9);
	$db->sql_freeresult($result9);
	
	if (empty($row9['1vs1_ladder']))
	{
		// SE NON E' BLOCCATA
		if ($ladder->data['ladder_locked'] == 0)
		{
		// The user is not in this sub-ladder. Show join link.
			$membership			= 'JOIN_LADDER';
			$membership_action	= append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladder_membership&amp;type=1&amp;ladder_id=' . $ladder_id . '&amp;ulad=true');
			$jimages 			= 'join_ladder';
			$frost_action		= '';
			$hibernation		= '';
			$hibernation_img	= '';
			$iy					= 102;
		}
		else
		{
			$membership			= 'MEMBERSHIP_LOCKED';
			$membership_action	= '#';
			$jimages 			= 'join_ladder_closed';
			$frost_action		= '';
			$hibernation		= '';
			$hibernation_img	= '';
			$iy					= 102;
		}
	}
	else
	{
		if ($ladder->data['ladder_locked'] == 0)
		{
			// The user is already joined with this ladder. Show leave link.
			$membership			= 'LEAVE_LADDER';
			$membership_action	= append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladder_membership&amp;type=2&amp;ladder_id=' . $ladder_id . '&amp;ulad=true');
			$jimages 			= 'leave_ladder';
			$frost_action		= ($row9['user_frosted'] == 0) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=main&amp;user_id=' . $user->data['user_id'] . '&amp;ladder_id=' . $ladder_id . '&amp;frost=1') : '';
			$hibernation		= 'HIBERNATION';
			$hibernation_img	= 'hibernation';
			$iy					= 116;
		}
		else
		{
			$membership			= 'MEMBERSHIP_LOCKED';
			$membership_action	= '#';
			$jimages 			= 'join_ladder_closed';
			$frost_action		= '';
			$hibernation		= '';
			$hibernation_img	= '';
			$iy					= 116;
		}
	}
}
else
{
	$membership			= '';
	$membership_action	= '';
	$jimages 			= '';
	$frost_action		= '';
	$hibernation		= '';
	$hibernation_img	= '';
}

// CLANS INSCRITTI
		$sql_3		= "SELECT COUNT(*) AS num_users FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id}";
		$result_3	= $db->sql_query($sql_3);
		$row_3		= $db->sql_fetchrow($result_3);
		$db->sql_freeresult($result_3);
// TOTALE WARS LADDER
		$sql_4		= "SELECT COUNT(*) AS num_wars FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$ladder_id} AND end_time != 0 AND 1vs1_unranked = 0";
		$result_4	= $db->sql_query($sql_4);
		$row_4		= $db->sql_fetchrow($result_4);
		$db->sql_freeresult($result_4);
		

// Assign the other variables to the template.
$template->assign_vars(array(
	'U_MEMBERSHIP'	=> $membership_action,
	'U_RULES'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladder_rules&amp;rules_ladder=' . $ladder_id),
	'L_MEMBERSHIP'	=> $membership,
	'PAGINATION'	=> $pagination,
	'TOT_CLANS'		=> $row_3['num_users'],
	'TOT_WARS'		=> $row_4['num_wars'],
	'JIMAGES'		=> (!$membership_action) ? '' : getimg_button("{$jimages}", "{$membership}", $iy, 25),
	'RULESIMG'		=> getimg_button('ladder_rules', 'IMG_SUBLADDER_RULES', 116, 25),
	'HIBERNAT_IMG'	=> (!$frost_action) ? '' : getimg_button("{$hibernation_img}", "{$hibernation}", 102, 25),
	'U_HIBERNATION'	=> $frost_action,
	'SOCCER'		=> ($ladder->data['ladder_style'] == 3) ? true : false,
	'PAGE_NUMBER'	=> on_page($total['total'], 30, $start))
);

// Set up the breadcrumbs.
$ladder_data	= $ladder->get_roots($ladder_id);
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $ladder_data['PLATFORM_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $ladder_data['PLATFORM_ID']))
);
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $ladder_data['LADDER_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $ladder_data['PLATFORM_ID']))
);
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $ladder_data['SUBLADDER_NAME'] . $user->lang['SUBLADDER_1VS1_SHORT'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=userssubladder&amp;ladder_id=' . $ladder_data['SUBLADDER_ID']))
);

$template->set_filenames(array('body' => 'rivals/subladders_user.html')); 
?>