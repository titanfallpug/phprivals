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

	$group		= new group();
	$ladder	    = new ladder();
	
/***************
* CLAN WAR
***************/	

$sql	= "SELECT m.*, l.* FROM " . MATCHES_TABLE . " m, " . LADDERS_TABLE . " l WHERE m.match_ladder = l.ladder_id AND m.match_confirmed > 0 AND m.match_finishtime > 0 ORDER BY m.match_finishtime DESC";
$result	= $db->sql_query_limit($sql, 30);
$i	= 0;
while($row = $db->sql_fetchrow($result))
{
	// Get the ladder's roots.
	$ladder_data	= $ladder->get_roots($row['match_ladder']);
	
	// CARICO LADDER E LA DISCRIMINO
	$ladder_kind = $row['match_ladder'];

	// Assign each match to the template.
	$template->assign_block_vars('block_matchhistory', array(
		'MATCH_ID'		=> $row['match_id'],
		'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
		'LADDER' 		=> $ladder_data['LADDER_NAME'],
		'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME'],
		'U_LADDER'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_kind}"),
		'ROW_COLOR' 	=> ($i % 2) ? 'row1' : 'row2',
		'DATE' 			=> ($row['match_finishtime'] > 0) ? $user->format_date($row['match_finishtime']) : $user->format_date($row['match_posttime']),
		'MAP1'			=> $row['mappa_mode1'],
		'MAP2'			=> $row['mappa_mode2'],
		'MAP3'			=> $row['mappa_mode3'],
		'DECERTO'		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
		'MODE1'			=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode1'] : '',
		'MODE2'			=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode2'] : '',
		'MODE3'			=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode3'] : '',
		'CHALLANGER' 	=> ($row['match_winner'] == $row['match_challenger']) ? '<span class="rivalwinner">' . $group->data('group_name', $row['match_challenger']) . '</span>' : $group->data('group_name', $row['match_challenger']),
		'CHALLANGEE'	=> ($row['match_winner'] == $row['match_challengee']) ? '<span class="rivalwinner">' . $group->data('group_name', $row['match_challengee']) . '</span>' : $group->data('group_name', $row['match_challengee']),
		'U_ER'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_challenger']),
		'U_EE'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_challengee']),
		'CLASSIFICATA'	=> ($row['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
		'MVP'			=> ($ladder_data['SUBLADDER_MVP'] == 1) ? true : false,
		'MVP1'			=> ($row['mvp1'] == 0) ? $user->lang['NO_MVP'] : getusername($row['mvp1']),
		'MVP2'			=> ($row['mvp2'] == 0) ? $user->lang['NO_MVP'] : getusername($row['mvp2']),
		'MVP3'			=> ($row['mvp3'] == 0) ? $user->lang['NO_MVP'] : getusername($row['mvp3']),
		'ER_SCORE'		=> $row['match_challanger_score'],
		'EE_SCORE'		=> $row['match_challangee_score'],
		'ER_TEAM'		=> $row['challenger_team'],
		'EE_TEAM'		=> $row['challengee_team'],
		'MODE1_ER_SCOR' => $row['match_challanger_score_mode1'],
		'MODE1_EE_SCOR' => $row['match_challangee_score_mode1'],
		'MODE2_ER_SCOR' => $row['match_challanger_score_mode2'],
		'MODE2_EE_SCOR' => $row['match_challangee_score_mode2'],
		'MODE3_ER_SCOR' => $row['match_challanger_score_mode3'],
		'MODE3_EE_SCOR' => $row['match_challangee_score_mode3'],
		'THEWINNER'		=> ($row['match_winner'] == '9999999') ? $user->lang['PAREGGIO'] : "{$user->lang['WINNERIS']} {$group->data('group_name', $row['match_winner'])}",
		'SCORE'			=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
		'CALCIO'		=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? true : false,
		'ADVSTATS'      => ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? true : false
	));
	
	// ADVANCED STATS
	if ($ladder_data['SUBLADDER_ADVSTAT'] == 1 || $ladder_data['SUBLADDER_MVP'] == 1)
	{
		$sql_adv	= "SELECT * FROM " . MATCH_TEMP_USTATS . " AS adv LEFT JOIN " . USERS_TABLE . " AS u ON u.user_id = adv.user_id
					WHERE adv.id_match = {$row['match_id']} ORDER BY u.username ASC";
		$result_adv	= $db->sql_query($sql_adv);
		$ist	= 0;
		while ($row_adv = $db->sql_fetchrow($result_adv))
		{
			$template->assign_block_vars('block_matchhistory.block_advstats', array(
				'USERNAME'	=> $row_adv['username'],
				'USERGT'	=> $row_adv['gamer_name'],
				'RATIO'		=> ($row_adv['deads'] == 0) ? 100 : round(($row_adv['kills'] / $row_adv['deads']),2),
				'KILLS'		=> $row_adv['kills'],
				'DEADS'		=> $row_adv['deads'],
				'ASSISTS'	=> $row_adv['assists'],
				'GOALS_F'	=> $row_adv['goal_f'],
				'GOALS_S'	=> $row_adv['goal_a'],
				'RATIOC'	=> ($row_adv['goal_a'] == 0) ? 100 : round(($row_adv['goal_f'] / $row_adv['goal_a']),2)
			));
		$ist++;
		}
		$db->sql_freeresult($result_adv);
	}
	$i++;
}
$db->sql_freeresult($result);

/***************
* USERS WAR
***************/	

$sqlu		= "SELECT m.*, l.* FROM " . ONEVSONE_MATCH_DATA . " m, " . LADDERS_TABLE . " l WHERE m.1vs1_ladder = l.ladder_id AND m.1vs1_confirmer > 0 AND m.end_time > 0 ORDER BY m.end_time DESC";
$resultu	= $db->sql_query_limit($sqlu, 30, 0);
$iu			= 0;
while($rowu = $db->sql_fetchrow($resultu))
{
	// Get the ladder's roots.
	$ladder_datau	= $ladder->get_roots($rowu['1vs1_ladder']);
	$ladder_kindu	= $rowu['1vs1_ladder'];

	// Assign each match to the template.
	$template->assign_block_vars('block_matchhistory_user', array(
		'MATCH_ID'		=> $rowu['1vs1_id'],
		'PLATFORM'		=> $ladder_datau['PLATFORM_NAME'],
		'LADDER' 		=> $ladder_datau['LADDER_NAME'],
		'SUBLADDER' 	=> $ladder_datau['SUBLADDER_NAME'],
		'U_LADDER'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=userssubladder&amp;ladder_id={$ladder_kindu}"),
		'ROW_COLOR' 	=> ($iu % 2) ? 'row1' : 'row2',
		'DATE' 			=> ($rowu['end_time'] > 0) ? $user->format_date($rowu['end_time']) : $user->format_date($rowu['start_time']),
		'MAP1'			=> $rowu['1vs1_mappa1'],
		'MAP2'			=> $rowu['1vs1_mappa2'],
		'MAP3'			=> $rowu['1vs1_mappa3'],
		'DECERTO'		=> ($ladder_datau['SUBLADDER_STYLE'] == 1) ? true : false,
		'MODE1'			=> ($ladder_datau['SUBLADDER_STYLE'] == 1) ? $rowu['mode1'] : '',
		'MODE2'			=> ($ladder_datau['SUBLADDER_STYLE'] == 1) ? $rowu['mode2'] : '',
		'MODE3'			=> ($ladder_datau['SUBLADDER_STYLE'] == 1) ? $rowu['mode3'] : '',
		'CHALLANGER' 	=> ($rowu['1vs1_winner'] == $rowu['1vs1_challanger']) ? '<span class="rivalwinner">' . getusername($rowu['1vs1_challanger']) . '</span>' : getusername($rowu['1vs1_challanger']),
		'CHALLANGEE'	=> ($rowu['1vs1_winner'] == $rowu['1vs1_challangee']) ? '<span class="rivalwinner">' . getusername($rowu['1vs1_challangee']) . '</span>' : getusername($rowu['1vs1_challangee']),
		'U_ER'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $rowu['1vs1_challanger']),
		'U_EE'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $rowu['1vs1_challangee']),
		'CLASSIFICATA'	=> ($rowu['1vs1_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
		'ER_SCORE'		=> $rowu['1vs1_challanger_score'],
		'EE_SCORE'		=> $rowu['1vs1_challangee_score'],
		'ER_TEAM'		=> $rowu['1vs1_challanger_team'],
		'EE_TEAM'		=> $rowu['1vs1_challangee_team'],
		'MODE1_ER_SCOR' => $rowu['mode1_score_er'],
		'MODE1_EE_SCOR' => $rowu['mode1_score_ee'],
		'MODE2_ER_SCOR' => $rowu['mode2_score_er'],
		'MODE2_EE_SCOR' => $rowu['mode2_score_ee'],
		'MODE3_ER_SCOR' => $rowu['mode3_score_er'],
		'MODE3_EE_SCOR' => $rowu['mode3_score_ee'],
		'THEWINNER'		=> ($rowu['1vs1_winner'] == '9999999') ? $user->lang['PAREGGIO'] : "{$user->lang['WINNERIS']} " . getusername($rowu['1vs1_winner']),
		'SCORE'			=> ($rowu['ladder_win_system'] == 0) ? true : false,
		'CALCIO'		=> ($rowu['ladder_style'] == 3) ? true : false
	));
	$iu++;
}
$db->sql_freeresult($resultu);

/*$template->assign_vars(array(
	'IMG_CLANCHART'	=> getimg_button('clan_latest_matches', 'IMG_CLAN_CHART', 128, 25),
	'IMG_USERCHART'	=> getimg_button('users_latest_matches', 'IMG_USER_CHART', 128, 25)
));*/

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['LATEST_WAR'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=latest_war'))
);

$template->set_filenames(array('body' => 'rivals/latest_war.html'));
?>