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
 * Subladder
 * Called from rivals with mode == 'subladder'
 */

$group		= new group();
$ladder		= new ladder();
$start		= (int) request_var('start', 0);
$ladder_id	= (int) request_var('ladder_id', 0);

// CHECK FOR ONEONE
if ($ladder->data['ladder_oneone'] == 1)
{
	$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=userssubladder&amp;ladder_id=' . $ladder_id);
	redirect($redirect_url);
}

// CHECK FOR RTH IF SOMEONE GOT 1000 POINTS
if ($ladder->data['ladder_ranking'] == 2)
{
	$sqlj		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} ORDER BY group_score DESC";
	$resultj	= $db->sql_query_limit($sqlj, 1);
	$rowj		= $db->sql_fetchrow($resultj);
	$db->sql_freeresult($resultj);
	
	if ($rowj['group_score'] >= 1000)
	{
		// block the join but do not set like locked (=1)
		$sql_array	= array(
			'ladder_locked'	=> 2
		);
		$sql = "UPDATE " . LADDERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = " . $ladder_id;
		$db->sql_query($sql);
		
		// the winner is
		$template->assign_vars(array(
			'LAD_WINNER'	=> $group->data('group_name', $rowj['group_id']),
			'U_LADWINNER'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $rowj['group_id'])
		));
	}
}

// Order the groups by their score.
switch ($ladder->data['ladder_ranking'])
{
	case 0: /* ELO */
	case 2: /* RTH */
		$order_by	= 'cdd.group_score';
		$sort		= 'DESC';
	break;
	case 1: /* SWAP */
		$order_by	= 'cdd.group_current_rank';
		$sort		= 'ASC';
	break;
}

if ($ladder->data['ladder_style'] == 3)
{
///////////// CALCOLO I VALORI MASSIMALI GOALS
	$sql_t		= "SELECT * FROM " . GROUPDATA_TABLE . " cdd, " . CLANS_TABLE . " csd WHERE csd.clan_closed = 0 AND cdd.group_ladder = {$ladder_id} AND csd.group_id = cdd.group_id ORDER BY {$order_by} {$sort}";
	$result_t	= $db->sql_query($sql_t);

	$gol_max = 0;
	$gol_min = 0;

	$it	= 0;
	while ($row_t = $db->sql_fetchrow($result_t))
	{
		$media_gol = (($row_t[ 'group_goals_fatti' ]) - ($row_t[ 'group_goals_subiti' ]));
		$gol_max = $media_gol > $gol_max ? $media_gol : $gol_max;
		$gol_min = $media_gol < $gol_min ? $media_gol : $gol_min;
		$it++;
	}
	$db->sql_freeresult($result_t);

//////////////////////
}

$sql	= "SELECT cdd.*, csd.* FROM " . GROUPDATA_TABLE . " cdd, " . CLANS_TABLE . " csd WHERE csd.clan_closed = 0 AND cdd.group_ladder = {$ladder_id} AND csd.group_id = cdd.group_id ORDER BY {$order_by} {$sort}";
$result	= $db->sql_query_limit($sql, 30, $start);
$i	= 0;
while($row = $db->sql_fetchrow($result))
{	
	if ($order_by == 'cdd.group_score')
	{
		// Apply the proper ladder position to the group.
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
		$pos	= ordinal($row['group_current_rank']);
	}

	// Check if a challenge link is to be showen.
	if ($user->data['user_id'] != ANONYMOUS)
	{
		if (!empty($group->data['group_id']))
		{
			if ($ladder->data['ladder_cl'] == 1 && $row['group_id'] != $group->data['group_id'] && $ladder->data['ladder_locked'] != 1 && in_array($ladder_id, $group->data['group_ladders']))
			{
				// Show the challenge link.
				$challenge_link	= append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=add_challenge&amp;group_id=' . $row['group_id'] . '&amp;ladder_id=' . $ladder_id);
			}
			else
			{
				// Don't show it.
				$challenge_link	= '';
			}
		}
		else
		{
			// Don't show it.
			$challenge_link	= '';
		}
	}
	else
	{
		$challenge_link	= '';
	}

	// Get the up or down ranking image.
	if ($row['group_last_rank'] != $row['group_current_rank'] && $row['group_last_rank'] != 0)
	{
		if ($row['group_current_rank'] < $row['group_last_rank'])
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
		// They are a new group.
		$up_down	= '';
	}

	// Get the hot or cold image.
	if ($row['group_streak'] >= 4)
	{
		// They are on a hot streak.
		$hot_cold	= '<img src="' . $phpbb_root_path . 'rivals/images/hot.png" class="rvmiddle" alt="hot" />';
	}
	else if ($row['group_streak'] <= -4)
	{
		// They are on a cold streak.
		$hot_cold	= '<img src="' . $phpbb_root_path . 'rivals/images/cold.png" class="rvmiddle" alt="cold" />';
	}
	else
	{
		// They are neutral.
		$hot_cold	= '';
	}
	
	/// TROFEI
	$sqlx		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} ORDER BY group_ratio DESC";
	$resultx	= $db->sql_query_limit($sqlx, 1);
	$rowx		= $db->sql_fetchrow($resultx);
	$db->sql_freeresult($resultx);
	
	$sqly		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} ORDER BY group_ratio ASC";
	$resulty	= $db->sql_query_limit($sqly, 1);
	$rowy		= $db->sql_fetchrow($resulty);
	$db->sql_freeresult($resulty);
	
	if ($row['group_id'] == $rowx['group_id'] && $row['group_wins'] >= 8)
	{
		$best = '<img src="' . $phpbb_root_path . 'rivals/images/crown.gif" class="rvmiddle" alt="crown" />';
	}
	else if ($row['group_id'] == $rowy['group_id'] && $row['group_losses'] >= 8)
	{
		$best = '<img src="' . $phpbb_root_path . 'rivals/images/tomb.png" class="rvmiddle" alt="tomb" />';
	}
	else
	{
		$best = '';
	}
	
////////////////////////// CAPOCANNONIERE
	if ($ladder->data['ladder_style'] == 3)
	{
		$goals =($row['group_goals_fatti'] - $row['group_goals_subiti']);
		if (($goals == $gol_max) && ($row['group_goals_fatti'] > 10))
		{
			$capoc = '<img src="' . $phpbb_root_path . 'rivals/images/scarpaoro.png" class="rvmiddle" alt="goleador" />';
		}
		else if (($goals == $gol_min) &&($row['group_goals_subiti'] > 10))
		{
			$capoc = '<img src="' . $phpbb_root_path . 'rivals/images/saponetta.png" class="rvmiddle" alt="soap" />';
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
	
// RTH addon
	if ($ladder->data['ladder_ranking'] == 2)
	{
		$pollo = ($row['rth_chicken'] >= 3) ? '<img src="' . $phpbb_root_path . 'rivals/images/pollosmall.png" class="rvmiddle" alt="chicken" />' : '';
	}
	else
	{
		$pollo = '';
	}

	$piu = (is_numeric(substr($row['group_streak'], 0, 1))) ? '+' : '';
	
	// Assign the groups to the template.
	$template->assign_block_vars('block_groups', array(
		'U_ACTION' 			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['group_id']),
		'U_CHALLENGE_LINK' 	=> $challenge_link,
		'POS' 				=> $pos,
		'UP_DOWN' 			=> $up_down,
		'HOT_COLD' 			=> $hot_cold,
		'BEST' 				=> $best,
		'STATUS' 			=> getactivity_status($row['group_id'], $ladder_id, false),
		'CANNONIERE' 		=> $capoc,
		'POLLO'				=> ($ladder->data['ladder_ranking'] == 2) ? true : false,
		'CHICKEN'			=> $pollo,
		'GROUP_NAME' 		=> $row['group_name'],
		'GROUP_ID' 			=> $row['group_id'],
		'GROUP_WINS' 		=> $row['group_wins'],
		'GROUP_LOSSES' 		=> $row['group_losses'],
		'GROUP_SCORE' 		=> $row['group_score'],
		'GROUP_RATIO'		=> $row['group_ratio'],
		'GROUP_STREAK' 		=> $piu . $row['group_streak'],
		'PAREGGI' 			=> $row['group_pari'],
		'GOL_FATTI' 		=> $row['group_goals_fatti'],
		'GOL_SUBITI' 		=> $row['group_goals_subiti'],
		'BG_COLOR' 			=> ($i % 2) ? 'bg1' : 'bg2',
		'ROW_COLOR' 		=> ($i % 2) ? 'row1' : 'row2')
	);

	$i++;
}

$db->sql_freeresult($result);

// Setup the pagination.
$sql	= "SELECT COUNT(group_id) AS total FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . $ladder_id;
$result	= $db->sql_query($sql);
$total	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Generate the pagination.
$pagination	= generate_pagination(append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $ladder_id), $total['total'], 30, $start);

// Check if the group is in this sub-ladder or not.
if ($user->data['is_registered'] && !empty($group->data['group_id']))
{
	if (!in_array($ladder_id, $group->data['group_ladders']))
	{
		// SE NON E' BLOCCATA
		if ($ladder->data['ladder_locked'] == 0)
		{
		// The group is not in this sub-ladder. Show join link.
			$membership			= 'JOIN_LADDER';
			$membership_action	= append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladder_membership&amp;type=1&amp;ladder_id=' . $ladder_id);
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
			// The group is already joined with this ladder. Show leave link.
			$membership			= 'LEAVE_LADDER';
			$membership_action	= append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladder_membership&amp;type=2&amp;ladder_id=' . $ladder_id);
			$jimages 			= 'leave_ladder';
			$frost_action		= (!in_array($ladder_id, $group->data['group_frosteds'])) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=main&amp;clan_id=' . $group->data['group_id'] . '&amp;ladder_id=' . $ladder_id . '&amp;frost=1') : '';
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
		$sql_3		= "SELECT COUNT(*) AS num_groups FROM " . GROUPDATA_TABLE . " WHERE group_ladder = $ladder_id";
		$result_3	= $db->sql_query($sql_3);
		$row_3		= $db->sql_fetchrow($result_3);
		$db->sql_freeresult($result_3);
// TOTALE WARS LADDER
		$sql_4		= "SELECT COUNT(*) AS num_wars FROM " . MATCHES_TABLE . " WHERE match_ladder = $ladder_id AND match_finishtime != 0 AND match_unranked = 0";
		$result_4	= $db->sql_query($sql_4);
		$row_4		= $db->sql_fetchrow($result_4);
		$db->sql_freeresult($result_4);
		

// Assign the other variables to the template.
$template->assign_vars(array(
	'U_MEMBERSHIP'	=> $membership_action,
	'U_RULES'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladder_rules&amp;rules_ladder=' . $ladder_id),
	'PAGINATION'	=> $pagination,
	'TOT_CLANS'		=> $row_3['num_groups'],
	'TOT_WARS'		=> $row_4['num_wars'],
	'JIMAGES'		=> (!$membership_action) ? '' : getimg_button("{$jimages}", "{$membership}", $iy, 25),
	'RULESIMG'		=> getimg_button('ladder_rules', 'IMG_SUBLADDER_RULES', 116, 25),
	'HIBERNAT_IMG'	=> (!$frost_action) ? '' : getimg_button("{$hibernation_img}", "{$hibernation}", 102, 25),
	'U_HIBERNATION'	=> $frost_action,
	'ADVSTATS'		=> ($ladder->data['ladder_advstat'] == 1) ? true : false,
	'SOCCER'		=> ($ladder->data['ladder_style'] == 3) ? true : false,
	'MVP'			=> ($ladder->data['ladder_mvp'] == 1) ? true : false,
	'POLLO'			=> ($ladder->data['ladder_ranking'] == 2) ? true : false,
	'PAGE_NUMBER'	=> on_page($total['total'], 30, $start))
);

// Set up the breadcrumbs.
$ladder_data	= $ladder->get_roots($ladder_id);
$template->assign_block_vars('navlinks', array (
	'FORUM_NAME'	=> $ladder_data['PLATFORM_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $ladder_data['PLATFORM_ID']))
);
$template->assign_block_vars('navlinks', array (
	'FORUM_NAME'	=> $ladder_data['LADDER_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $ladder_data['PLATFORM_ID']))
);
$template->assign_block_vars('navlinks', array (
	'FORUM_NAME'	=> $ladder_data['SUBLADDER_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $ladder_data['SUBLADDER_ID']))
);

///////////////
//ADVANCED STATS
if ($ladder->data['ladder_advstat'] == 1)
{
	include ($phpbb_root_path . 'rivals/subladders_advstats.' . $phpEx);
}


if ($ladder->data['ladder_style'] == 3)
{
	$template->set_filenames(array('body' => 'rivals/subladders_calcio.html')); 
}
else
{
	$template->set_filenames(array('body' => 'rivals/subladders.html')); 
}
?>