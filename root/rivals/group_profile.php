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
 * Group Profile
 * Called from rivals with mode == 'group_profile'
 */

$group		= new group();
$ladder		= new ladder();
$tournament	= new tournament();
include($phpbb_root_path . 'rivals/classes/funzioni_numeriche.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
$group_id		= (int) request_var('group_id', 0);
$season_id		= (int) request_var('season_id', 0);
$season_ladder	= (int) request_var('season_ladder', 0);
$group_data		= $group->data('*', $group_id);

// Get the leader's data.
$sql	= "SELECT * FROM " . USERS_TABLE . " WHERE user_id = " . $group_data['user_id'];
$result	= $db->sql_query($sql);
$row	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Assign the leader's data to the template.
$template->assign_vars(array(
	'U_LEADERPROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
	'U_CHALLENGE'		=>($group_data['user_id'] != $user->data['user_id']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=rivals&amp;mode=add_challenge&amp;group_id=' . $group_id) : '',
	'LEADER_NAME'		=> $row['username']
));

// Get the list of group members for the group.
$members	= (array) $group->members('get_members', $group_id);
$i			= 0;
if(sizeof($members) > 0)
{
	foreach($members AS $value)
	{
		// Get the member's data.
		$sql		= "SELECT * FROM " . USERS_TABLE . " AS U INNER JOIN " . USER_CLAN_TABLE . " AS G
					ON G.user_id = U.user_id WHERE G.group_id = {$group_id} AND U.user_id = " . $value . "";
		$result		= $db->sql_query($sql);
		$row		= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
				
		if ($row['group_leader'] == 2)
		{
			$livello = $user->lang['COF_SI_SHORT'];
		}
		else if ($row['group_leader'] == 1)
		{
			$livello = $user->lang['FONDATORE_SHORT'];
		}
		else
		{
			$livello = $user->lang['COF_NO_SHORT'];
		}

		// Assign the member's data to the template.
		$template->assign_block_vars('block_members', array(
			'U_MEMBERPROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
			'MEMBER_NAME'		=> $row['username'],
			'GAMER_NAME'		=> $row['gamer_name'],
			'MEMBER_LEVEL'		=> $row['user_ladder_value'],
			'MEMBER_MVP'		=> $row['mvp_utente'],
			'MEMBER_KILLS'		=> $row['kills'],
			'MEMBER_DEADS'		=> $row['deads'],
			'MEMBER_RATIO'		=> ($row['deads'] == 0) ? $row['kills'] : round($row['kills'] / $row['deads'], 2),
			'MEMBER_ASSISTS'	=> $row['assists'],
			'MEMBER_GOALA'		=> $row['agoals'],
			'MEMBER_GOALF'		=> $row['fgoals'],
			'MEMBER_COF'		=> $livello,
			'ROW_COLOR'			=>($i % 2) ? 'row bg1' : 'row bg2'
		));
		$i++;
	}
}

// Get the on-going and finished matches from the database.
$sql	= "SELECT m.*, l.* FROM " . MATCHES_TABLE . " m, " . LADDERS_TABLE . " l WHERE (m.match_challenger = {$group_id} OR m.match_challengee = {$group_id})
		AND m.match_ladder = l.ladder_id AND m.match_confirmed > 0 AND m.match_finishtime > 0 ORDER BY m.match_finishtime DESC";
$result	= $db->sql_query_limit($sql, 30);

$i	= 0;
while($row = $db->sql_fetchrow($result))
{
	// Get the ladder's roots.
	$ladder_data	= $ladder->get_roots($row['match_ladder']);
	
	$ladder_kind = $row['match_ladder'];
	$tipo_ladder = $row['ladder_style'];

	// Assign each match to the template.
	$template->assign_block_vars('block_matchhistory', array(
		'MATCH_ID'		=> $row['match_id'],
		'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
		'LADDER' 		=> $ladder_data['LADDER_NAME'],
		'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME'],
		'U_LADDER'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id={$ladder_kind}"),
		'ROW_COLOR' 	=> ($i % 2) ? 'row bg1' : 'row bg2',
		'DATE' 			=> $user->format_date($row['match_finishtime']),
		'MAP1'			=> $row['mappa_mode1'],
		'MAP2'			=> $row['mappa_mode2'],
		'MAP3'			=> $row['mappa_mode3'],
		'DECERTO'		=> ($tipo_ladder == 1) ? true : false,
		'MODE1'			=> ($tipo_ladder == 1) ? $row['mode1'] : '',
		'MODE2'			=> ($tipo_ladder == 1) ? $row['mode2'] : '',
		'MODE3'			=> ($tipo_ladder == 1) ? $row['mode3'] : '',
		'CHALLANGER' 	=> ($row['match_winner'] == $row['match_challenger']) ? '<span class="rivalwinner">' . $group->data('group_name', $row['match_challenger']) . '</span>' : $group->data('group_name', $row['match_challenger']),
		'CHALLANGEE'	=> ($row['match_winner'] == $row['match_challengee']) ? '<span class="rivalwinner">' . $group->data('group_name', $row['match_challengee']) . '</span>' : $group->data('group_name', $row['match_challengee']),
		'U_ER'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_challenger']),
		'U_EE'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_challengee']),
		'CLASSIFICATA'	=> ($row['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
		'MVP'			=> ($row['ladder_mvp'] == 1) ? true : false,
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
		'SCORE'			=> ($row['ladder_win_system'] == 0) ? true : false,
		'CALCIO'		=> ($row['ladder_style'] == 3) ? true : false,
		'ADVSTATS'      => ($row['ladder_advstat'] == 1) ? true : false
	));
	
	// ADVANCED STATS
		if ($row['ladder_advstat'] == 1 || $row['ladder_mvp'] == 1)
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

// Check if we need to show the "Request to Join".
if(!in_array($user->data['user_id'], $members))
{
	// The user is not in the group so show the message.
	$template->assign_block_vars('block_request', array('U_REQUEST' => append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=join_group&amp;type=1&amp;group_id=' . $group_id)));
}

// Get the ladders the group is joined to.
$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = " . $group_id;
$result	= $db->sql_query($sql);

while($row = $db->sql_fetchrow($result))
{
	// Get the ladder's roots.
	$ladder_data	= $ladder->get_roots($row['group_ladder']);

	// Get the season for this ladder active.
	$sql_22		= "SELECT * FROM " . SEASONS_TABLE . " WHERE season_ladder = {$row['group_ladder']} AND season_status = 1";
	$result_22	= $db->sql_query($sql_22);
	$row_22		= $db->sql_fetchrow($result_22);
	$db->sql_freeresult($result_22);

	// Check where to get the data.
	if($season_id != 0 && $season_ladder == $row['group_ladder'] && $season_id != $row_22['season_id'])
	{
		// User selected to see archived season data.
		$sql_2		= "SELECT * FROM " . SEASONS_TABLE . " WHERE season_id = " . $season_id;
		$result_2	= $db->sql_query($sql_2);
		$row_2		= $db->sql_fetchrow($result_2);
		$db->sql_freeresult($result_2);

		$sql_3		= "SELECT * FROM " . SEASONDATA_TABLE . " WHERE season_id = $season_id AND group_id = " . $group_id;
		$result_3	= $db->sql_query($sql_3);
		$data		= $db->sql_fetchrow($result_3);
		$db->sql_freeresult($result_3);
	}
	else
	{
		// User needs to see the current season.
		$data	= $row;
	}


	// Assign the ladder and the stats to the template.
	$template->assign_block_vars('block_ladders', array(
		'U_ACTION'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id),
		'SEASON_NAME'	=>(!empty($row_2['season_name'])) ? $row_2['season_name'] : $user->lang['CURRENT_SEASON'],
		'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
		'LADDER'		=> $ladder_data['LADDER_NAME'],
		'SUBLADDER_ID'	=> $ladder_data['SUBLADDER_ID'],
		'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME'],
		'GROUP_WINS'	=> $data['group_wins'],
		'GROUP_LOSSES'	=> $data['group_losses'],
		'PAREGGI'		=> $data['group_pari'],
		'GROUP_STREAK'	=> $data['group_streak'],
		'GROUP_SCORE'	=> $data['group_score'],
		'CURRENT_RANK'	=> $data['group_current_rank'],
		'LAST_RANK'		=> $data['group_last_rank'],
		'BEST_RANK'		=> $data['group_best_rank'],
		'WORST_RANK'	=> $data['group_worst_rank']
	));

	// Get the current seasons data
	$sql_21		= "SELECT * FROM " . SEASONS_TABLE . " WHERE season_ladder = {$row['group_ladder']} AND season_status = 1";
	$result_21	= $db->sql_query($sql_21);
	$row_21		= $db->sql_fetchrow($result_21);
	$db->sql_freeresult($result_21);
	$template->assign_block_vars('block_ladders.block_seasons_current', array(
		'SEASON_ID' 	=> $row_21['season_id'],
		'SEASON_NAME'	=> $row_21['season_name'])
	);
	
	// Get all the OLD seasons for this ladder.
	$sql_4		= "SELECT * FROM " . SEASONS_TABLE . " WHERE season_ladder = {$row['group_ladder']} AND season_status = 0";
	$result_4	= $db->sql_query($sql_4);
	while($row_4 = $db->sql_fetchrow($result_4))
	{
		// Assign each season to the template.
		$template->assign_block_vars('block_ladders.block_seasons', array(
			'SEASON_ID' 	=> $row_4['season_id'],
			'SEASON_NAME'	=> $row_4['season_name'])
		);
	}
	$db->sql_freeresult($result_4);
}
$db->sql_freeresult($result);

// STATS TOURNAMENTS
$playedtournament	= array();
if (!empty($group_data['group_tournaments']) && $group_data['group_tournaments'] != 'N/A')
{
	$playedtournament	= unserialize($group_data['group_tournaments']);

	foreach ($playedtournament AS $tourid)
	{
		if ($tournament->data('tournament_status', $tourid) >= 2)
		{
			$sql_tt		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tourid} AND group_id = {$group_id} ORDER BY group_bracket DESC";
			$result_tt	= $db->sql_query_limit($sql_tt, 1);
			$row_tt		= $db->sql_fetchrow($result_tt);
			$db->sql_freeresult($result_tt);
			
			// find the best placement
			if ($tournament->data('tournament_brackets', $tourid) > 2)
			{
				$rounds	= ($tournament->data('tournament_brackets', $tourid) / 2);
				
				if ($row_tt['group_bracket'] == $rounds)
				{
					$tresult	= $user->lang['WINNER_ROUND'];
				}
				else if ($row_tt['group_bracket'] == ($rounds-1))
				{
					$tresult	= $user->lang['FINAL_ROUND'];
				}
				else if ($row_tt['group_bracket'] == ($rounds-2))
				{
					$tresult	= $user->lang['SEMIFINAL_ROUND'];
				}
				else
				{
					$tresult	= sprintf($user->lang['ROUND'], $row_tt['group_bracket']);
				}
			}
			else
			{
				if ($row_tt['group_bracket'] == 2)
				{
					$tresult	= $user->lang['WINNER_ROUND'];
				}
				else if ($row_tt['group_bracket'] == 1)
				{
					$tresult	= sprintf($user->lang['ROUND'], 1);
				}
			}
			
			switch ($tournament->data('tournament_status', $tourid))
			{
				case 2:
					$tstatus	= $user->lang['INIZIATO'];
				break;
				case 3:
					$tstatus	= $user->lang['CHIUSO'];
				break;
				default:
					$tstatus	= $user->lang['PRONTO_A_PARTIRE'];
				break;
			}
			
			$template->assign_block_vars('block_tourstats', array(
				'TOUR_NAME'		=> $tournament->data('tournament_name', $tourid),
				'TOUR_STATUS'	=> $tstatus,
				'TOUR_URL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=tournaments_brackets&amp;tournament_id=" . $tourid),
				'TOUR_POSITION'	=> $tresult
			));
		}
	}
}

//GET CHAT MESSAGES
$sql 	= " SELECT * FROM " . CLANSMSG_TABLE . " WHERE group_id = {$group_id} ORDER BY matchcomm_time DESC ";
$result = $db->sql_query_limit($sql, 10);
$i		= 0;
while ($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('block_smsg', array(
		'SMSG_ID'	=> $row['smsg_id'],
		'SMSG_TEXT'	=> generate_text_for_display($row['matchcomm_message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']),
		'DEL_WORK'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=main&amp;delsmsg=" . $row['smsg_id']),
		'SMSG_TIME'	=> $user->format_date($row['matchcomm_time'])
	));
$i++;
}
$db->sql_freeresult($result);
	
// FAVOURITE MAP
$malist = explode(",", $group_data['clan_favouritemap']);
foreach($malist AS $maps)
{
	$template->assign_block_vars('block_favmaps', array (
	'MAP' => $maps)
	);
}

// FAVOURITE TEAM
$telist = explode(",", $group_data['clan_favouriteteam']);
foreach($telist AS $teams)
{
	$template->assign_block_vars('block_favteams', array(
		'TEAMS' => $teams)
	);
}
	
// CLAN LEVEL
if ($group_data['clan_level'] == 2)
{
	$patente = "patenteA.gif";
}
else if ($group_data['clan_level'] == 1)
{
	$patente = "patenteB.gif";
}
else
{
	$patente = "patenteC.gif";
}

/******************************************************
*	Total clan members level
*********************************/
$clanmembers	= $group->members('get_members', $group_id);
$totalpos		= 0;
$totalneg		= 0;
foreach ($clanmembers AS $member1)
{
	$user_value		= getuserdata('user_ladder_value', $member1);
	$userlevenint	= abs(getuserdata('user_ladder_value', $member1));
	
	if (substr($user_value, 0, 1) == '-')
	{
		$totalneg	= $totalneg + $userlevenint;
		$totalpos	= $totalpos + 0;
	}
	else
	{
		$totalneg	= $totalneg + 0;
		$totalpos	= $totalpos + $userlevenint;
	}
}
$clan_level	= $totalpos - $totalneg;

/******************************************************
*	All time stats definition
*********************************/
$total_matchs	= 0;
$total_wins		= 0;
$total_losses	= 0;
$total_draws	= 0;

// total matches played
$result			= $db->sql_query("SELECT COUNT(match_id) as total_matches FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_confirmed > 0");
$total_matchsA	= (int) $db->sql_fetchfield('total_matches');
$db->sql_freeresult($result);

$result			= $db->sql_query("SELECT COUNT(tm.group_uid) as total_matchesB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo WHERE (tm.group1 = {$group_id} OR tm.group2 = {$group_id}) AND tm.conferma2 = 1 AND tt.tournament_userbased = 0");
$total_matchsB	= (int) $db->sql_fetchfield('total_matchesB');
$db->sql_freeresult($result);

$total_matchs	= ($total_matchsA + $total_matchsB == 0) ? 0 : $total_matchsA + $total_matchsB;
$totm_dividendo	= ($total_matchs == 0) ? 1 : $total_matchs;

// total wins
$result			= $db->sql_query("SELECT COUNT(match_id) as total_winsA FROM " . MATCHES_TABLE . " WHERE match_winner = {$group_id} AND match_confirmed > 0");
$total_winsA	= (int) $db->sql_fetchfield('total_winsA');
$db->sql_freeresult($result);

$result			= $db->sql_query("SELECT COUNT(tm.group_uid) as total_winsB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo WHERE tm.vincitore = {$group_id} AND tm.conferma2 = 1 AND tt.tournament_userbased = 0");
$total_winsB	= (int) $db->sql_fetchfield('total_winsB');
$db->sql_freeresult($result);

$total_wins		= ($total_winsA + $total_winsB == 0) ? 1 : $total_winsA + $total_winsB;
$total_winsGR	= ($total_winsA + $total_winsB == 0) ? 0 : $total_winsA + $total_winsB;

// total losses
$result			= $db->sql_query("SELECT COUNT(match_id) as total_lossesA FROM " . MATCHES_TABLE . " WHERE match_loser = {$group_id} AND match_confirmed > 0");
$total_lossesA	= (int) $db->sql_fetchfield('total_lossesA');
$db->sql_freeresult($result);

$result			= $db->sql_query("SELECT COUNT(tm.group_uid) as total_lossesB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo WHERE (tm.group1 = {$group_id} OR tm.group2 = {$group_id}) AND tm.vincitore <> {$group_id} AND tm.conferma2 = 1 AND tt.tournament_userbased = 0");
$total_lossesB	= (int) $db->sql_fetchfield('total_lossesB');
$db->sql_freeresult($result);

$total_losses	= ($total_lossesA + $total_lossesB == 0) ? 1 : $total_lossesA + $total_lossesB;
$total_lossesGR	= ($total_lossesA + $total_lossesB == 0) ? 0 : $total_lossesA + $total_lossesB;

// total draws
$result			= $db->sql_query("SELECT COUNT(match_id) as total_drawsA FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_winner = '9999999' AND match_confirmed > 0");
$total_drawsA	= (int) $db->sql_fetchfield('total_drawsA');
$db->sql_freeresult($result);

$result2		= $db->sql_query("SELECT COUNT(tm.group_uid) as total_drawsB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo WHERE tm.vincitore = '9999999' AND tm.conferma2 = 1 AND tt.tournament_userbased = 0");
$total_drawsB	= (int) $db->sql_fetchfield('total_drawsB');
$db->sql_freeresult($result2);

$total_draws	= ($total_drawsA + $total_drawsB == 0) ? 1 : $total_drawsA + $total_drawsB;
$total_drawsGR	= ($total_drawsA + $total_drawsB == 0) ? 0 : $total_drawsA + $total_drawsB;

$alltimeratio	= round_up(($total_wins / $total_losses) / ($totm_dividendo / $total_wins), 5);
$experience		= ($total_winsGR*3) + $total_drawsGR - ($total_lossesGR*3);

/*****************************
* CHART ADDON
***************/
$latesttime	= get_lastmatchtime($group_id, false);
// BAR
$datag	= array();
$res	= $db->sql_query("SELECT match_id, match_challenger, match_challengee, match_finishtime, match_confirmed FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_confirmed > 0 ORDER BY match_id ASC");
while($roww = $db->sql_fetchrow($res))
{
  $datag[] = (int) get_totalmath4graph($roww['match_id'], $latesttime, $group_id, false);
}
$db->sql_freeresult($res);

$winsg	= array();
$intwin	= array();
$res2	= $db->sql_query("SELECT match_id, match_challenger, match_challengee, match_finishtime, match_confirmed FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_confirmed > 0 ORDER BY match_id ASC");
while($roww2 = $db->sql_fetchrow($res2))
{
  $winsg[]	= round_up(get_totalmath4graph($roww2['match_id'], $latesttime, $group_id, true),3);
  $intwin[]	= ceil(get_totalmath4graph($roww2['match_id'], $latesttime, $group_id, true));
}
$db->sql_freeresult($res2);

include_once("{$phpbb_root_path}rivals/graph/open-flash-chart.php");
$bar = new line_hollow(2, 4, '0x80a033', 'Winner Graph', 10);
$bar->key( '% Complete', 10 );
$g = new graph();
$g->line_dot(3, 6, '#d01f3c', '', 18);
$g->title("{$user->lang['CLAN_WINS_RATIO']}", '{font-size: 18px; color: #000000;}' );
$g->set_tool_tip( 'Ratio: #val#' );
$g->bg_colour = '#ECF1F3';
$g->set_x_labels($datag);
$g->set_data($winsg);
$g->set_x_label_style(10, '#000000', 0, 1);
$g->set_y_label_style(10, '#000000');
$g->x_axis_colour('#000000', '#A0A0A0');
$g->set_x_legend("{$user->lang['PLAYED']}", 12, '#000000');
$g->y_axis_colour('#000000', '#A0A0A0');
$g->set_y_legend("{$user->lang['RATIO']}", 12, '#000000');
$gmaxfx	= (empty($intwin)) ? array(1,1) : $intwin;
$ymax	= max($gmaxfx);
$g->set_y_min(0);
$g->set_y_max($ymax);
$g->set_x_min(0);
$g->set_x_max($total_matchs);
$g->y_label_steps(1);
$g->set_width(650);
$g->set_height(200);
$g->set_js_path("{$phpbb_root_path}rivals/graph/js/");
$g->set_output_type('js');

// PIE
$p = new graph();
$p->pie(60,'#505050','{font-size: 12px; color: #404040;');
$datap	= array($total_winsGR, $total_lossesGR, $total_drawsGR);
$p->pie_values($datap, array("{$user->lang['WINS']}","{$user->lang['LOSSES']}", "{$user->lang['LADDER_GROUP_PARI']}"));
$p->pie_slice_colours( array('#d01f3c','#356aa0','#C79810') );
$p->bg_colour = '#ECF1F3';
$p->set_tool_tip('Matches: #val#');
$p->title("{$user->lang['MATCH_PLAYED_AT_TODAY']} {$total_matchs}", '{font-size:18px; color: #d01f3c; margin-bottom: 16px;}');
$p->set_width(400);
$p->set_height(220);
$p->set_js_path("{$phpbb_root_path}rivals/graph/js/");
$p->set_output_type('js');

/******************************
*	Clan Reputation addon
****************************/
$therepint	= (int) ceil($group_data['clan_rep_value'] / $group_data['clan_rep_time']);
$therepval	= round_up(($group_data['clan_rep_value'] / $group_data['clan_rep_time']),3);

switch ($therepint)
{
	case 5:
		$repimg	= $phpbb_root_path . "rivals/images/rep5.png";
	break;
	case 4:
		$repimg	= $phpbb_root_path . "rivals/images/rep4.png";
	break;
	case 3:
		$repimg	= $phpbb_root_path . "rivals/images/rep3.png";
	break;
	case 2:
		$repimg	= $phpbb_root_path . "rivals/images/rep2.png";
	break;
	case 1:
	case 0:
		$repimg	= $phpbb_root_path . "rivals/images/rep1.png";
	break;
}

// fix site url
$urlcheck	= strpos($group_data['group_sito'], 'http://');
$fixedurl	= ($urlcheck === false) ? 'http://' . $group_data['group_sito'] : $group_data['group_sito'];

// Assign the other variables to the template.
$template->assign_vars(array(
	'GROUP_ID'		=> $group_id,
	'GROUP_NAME'	=> $group_data['group_name'],
	'REQJOIN_IMG'	=> getimg_button('request_to_join', 'IMG_REQUEST_JOIN', 128, 25),
	'CHALLANGE_IMG'	=> getimg_button('challenge', 'IMG_CHALLANGE', 102, 25),
	'U_CLANFEED'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=rssexporter&amp;clan=' . $group_id),
	'GROUP_SITO'	=> (!empty($group_data['group_sito'])) ? $fixedurl : '',
	'GUID'			=> (!empty($group_data['guid'])) ? $group_data['guid'] : '',
	'UAC'			=> (!empty($group_data['uac'])) ? $group_data['uac'] : '',
	'GROUP_LOGO'	=> $group_data['clan_logo_name'],
	'GROUP_LOGOW'	=> $group_data['clan_logo_width'],
	'GROUP_LOGOH'	=> $group_data['clan_logo_height'],
	'TOTALMATCHES'	=> $total_matchs,
	'TOTALWINS'		=> $total_wins,
	'TOTALLOSSES'	=> $total_losses,
	'TOTALDRAWS'	=> $total_draws,
	'TOTAL_RATIO'	=> $alltimeratio,
	'TOTAL_XP'		=> $experience,
	'CLAN_LEVEL'	=> $clan_level,
	'GRAPH'			=> $g->render(),
	'TORTA'			=> $p->render(),
	'REPUTATION'	=> $repimg,
	'REPVALUE'		=> $therepval,
	'POLLO'			=> ($group_data['rth_chicken'] >= 3) ? true : false,
	'POWNER'		=> ($group_data['rth_powner'] > 0) ? $group_data['rth_powner'] : false,
	'STRAK10'		=> ($group_data['clan_target_10streak'] >= 1) ? $group_data['clan_target_10streak'] : false,
	'LADDERWIN'     => ($group_data['clan_target_ladderwin'] >= 1) ? $group_data['clan_target_ladderwin'] : false,
	'CHICKEN'		=> floor($group_data['rth_chicken'] / 3),
	'PATENTE'		=> $patente,
	'ADMIN'			=> ($auth->acl_getf_global('m_') || $auth->acl_get('a_')) ? true : false,
	'GROUP_DESC'	=> nl2br($group_data['group_desc'])
));

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['GROUP'] . ': ' . $group_data['group_name'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $group_id))
);

$template->set_filenames(array('body' => 'rivals/group_profile.html'));

?>