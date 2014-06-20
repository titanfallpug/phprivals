<?php
/**
*
* @package acp
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
 * EDIT MATCHES
 * Called from acp_rivals with mode == 'edit_match'
 */
function acp_rivals_edit_match($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;
	
	$group	= new group();
	$ladder	= new ladder();
	$select	= (!empty($_POST['select'])) ? true : false;
	$submit	= (!empty($_POST['submit'])) ? true : false;
	$reset	= (!empty($_POST['resettable'])) ? true : false;
	$clan1	= (int) request_var('clan1', 0);
	$clan2	= (int) request_var('clan2', 0);
	$zlad	= (int) request_var('zonelad', 0);
	
	// LOAD CLANS FOR SELECT
	$sql	= "SELECT * FROM " . CLANS_TABLE . " ORDER BY UCASE(group_name) ASC";
	$result	= $db->sql_query($sql);
	$j 		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign it to the template.
		$template->assign_block_vars('clan_select', array(
			'CLAN_NAME'	=> $row['group_name'],
			'CLAN_ID'	=> $row['group_id']
		));
		$j++;
	}
	$db->sql_freeresult($result);
	
	// LOAD LADDERS FOR SELECT
	$template->assign_vars(array(
		'S_LADDER'	=> $ladder->make_ladder_select(false, true, false, false)
	));	
	
	// Checker
	if (empty($clan1) && empty($clan2))
	{
		$template->assign_vars(array(
			'SELECTOR'	=> true,
		));
	}

if ($select)
{
	if (empty($clan1) || empty($clan2) || empty($zlad))
	{
		$template->assign_vars(array(
			'SELECTOR'	=> true,
			'DETAILS'	=> false,
			'NOTWO'		=> (empty($clan1) || empty($clan2)) ? $user->lang['EDITMATCH_NO_CLAN'] : true,
			'XZONELAD'	=> (empty($zlad)) ? $user->lang['SPECIFICA_LADDER'] : ''
		));
	}
	else if ($clan1 == $clan2 && $clan2 > 0 && !empty($zlad))
	{
		$template->assign_vars(array(
			'SELECTOR'	=> true,
			'DETAILS'	=> false,
			'NOTWO'		=> $user->lang['EDITMATCH_SAME_CLAN'],
			'XZONELAD'	=> false,
		));
	}
	else
	{
		$template->assign_vars(array(
			'SELECTOR'	=> false,
			'DETAILS'	=> true,
			'NOTWO'		=> false,
			'XZONELAD'	=> false
		));
	
		// THE LATEST MATCH RECURSIVE
		$sql4		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_ladder = {$zlad} AND ((match_challenger = {$clan1} AND match_challengee = {$clan2}) 
					OR (match_challenger = {$clan2} AND match_challengee = {$clan1})) AND match_unranked = 0 AND match_confirmed > 0 ORDER BY match_finishtime DESC";
		$result4	= $db->sql_query_limit($sql4, 1);
        $row4		= $db->sql_fetchrow($result4);
	    $db->sql_freeresult($result4);
		
		// GOT THE LAST MATCH REPORTED FOR THIS CLANS
		// Only if the match are the last one for challanger or challangee
		$sql5		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_ladder = {$zlad} AND (match_challenger = {$clan1} OR match_challengee = {$clan1})
					ORDER BY match_finishtime DESC";
		$result5	= $db->sql_query_limit($sql5, 1);
        $row5		= $db->sql_fetchrow($result5);
	    $db->sql_freeresult($result5);
		
		$sql6		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_ladder = {$zlad} AND (match_challenger = {$clan2} OR match_challengee = {$clan2})
					ORDER BY match_finishtime DESC";
		$result6	= $db->sql_query_limit($sql6, 1);
        $row6		= $db->sql_fetchrow($result6);
	    $db->sql_freeresult($result6);
		
		$template->assign_block_vars('block_recursive', array(
			'MATCH_ID'	 	=> (!empty($row4['match_id'])) ? $row4['match_id'] : false,
			'XLADDER'	 	=> $row4['match_ladder'],
			'CLAN1'			=> $row4['match_challenger'],
			'CLAN2'			=> $row4['match_challengee'],
			'CHALLANGER' 	=> $group->data('group_name', $row4['match_challenger']),
			'CHALLANGEE'	=> $group->data('group_name', $row4['match_challengee']),
			'WINNER' 		=> ($row4['match_winner'] == '9999999') ? $user->lang['PAREGGIO'] : $group->data('group_name', $row4['match_winner']),
			'ER_POINT'		=> $row4['match_challanger_score'],
			'EE_POINT'		=> $row4['match_challangee_score'],
			'RESETTABLE1'	=> ($row4['match_id'] == $row5['match_id']) ? true : false,
			'RESETTABLE2'	=> ($row4['match_id'] == $row6['match_id']) ? true : false,
			'REFMATCH'		=> ($row4['match_id'] == $row5['match_id']) ? $clan1 : $clan2
		));
		
		// MATCH UNREPORTED
		$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_ladder = {$zlad} AND ((match_challenger = {$clan1} AND match_challengee = {$clan2}) 
				OR (match_challenger = {$clan2} AND match_challengee = {$clan1})) AND match_finishtime = 0 AND match_confirmed = 0 AND match_reported > 0";
		$result	= $db->sql_query($sql);
		$m 		= 0;
		while ($row = $db->sql_fetchrow($result))
		{
			//get ladder data
			$ladder_data	= $ladder->get_roots($row['match_ladder']);
			
			// Assign it to the template.
			$template->assign_block_vars('match_unreported', array(
				'CHALLANGER'	=> $group->data('group_name', $row['match_challenger']),
				'CHALLANGEE'	=> $group->data('group_name', $row['match_challengee']),
				'WINNERER' 		=> ($row['match_winner'] == $row['match_challenger']) ? 'selected="selected"' : '',
				'WINNEREE' 		=> ($row['match_winner'] == $row['match_challengee']) ? 'selected="selected"' : '',
				'WINNERPP' 		=> ($row['match_winner'] == '9999999') ? 'selected="selected"' : '',
				'ID_ER'			=> $row['match_challenger'],
				'ID_EE'			=> $row['match_challengee'],
				'ID_PP'			=> '9999999',
				'IP_ER'			=> $row['match_challenger_ip'],
				'IP_EE'			=> $row['match_challengee_ip'],
				'U_IP_ER'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row['match_challenger_ip']),
				'U_IP_EE'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row['match_challengee_ip']),
				'ER_POINT'		=> $row['match_challanger_score'],
				'EE_POINT'		=> $row['match_challangee_score'],
				'ER_POINT_M1'	=> $row['match_challanger_score_mode1'],
				'EE_POINT_M1'	=> $row['match_challangee_score_mode1'],
				'ER_POINT_M2'	=> $row['match_challanger_score_mode2'],
				'EE_POINT_M2'	=> $row['match_challangee_score_mode2'],
				'ER_POINT_M3'	=> $row['match_challanger_score_mode3'],
				'EE_POINT_M3'	=> $row['match_challangee_score_mode3'],
				'REPORTER_NO'	=> ($row['match_reported'] == 0) ? 'selected="selected"' : '',
				'REPORTER_ER'	=> ($row['match_reported'] == $row['match_challenger']) ? 'selected="selected"' : '',
				'REPORTER_EE'	=> ($row['match_reported'] == $row['match_challengee']) ? 'selected="selected"' : '',
				'MVP1'			=> $row['mvp1'],
				'MVP2'			=> $row['mvp2'],
				'MVP3'			=> $row['mvp3'],
				'MATCHTIME'		=> $user->format_date($row['match_posttime']),
				'MATCH_ID'		=> $row['match_id'],
				'MODE1'			=> $row['mappa_mode1'],
				'MODE2'			=> $row['mappa_mode2'],
				'MODE3'			=> $row['mappa_mode3'],
				'ADVSTATS'		=> ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? true : false,
				'DECERTO'		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
				'POINTRESULT'	=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
				'CALCIO'		=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? true : false,
				'RANKED'		=> ($row['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA']
			));
			
			// userstats for match
			if ($ladder_data['SUBLADDER_ADVSTAT'] == 1)
			{
				$sqlu		= "SELECT * FROM " . MATCH_TEMP_USTATS . " WHERE id_ladder = {$zlad} AND id_match = " . $row['match_id'];
				$resultu	= $db->sql_query($sqlu);
				$u 			= 0;
				while ($rowu = $db->sql_fetchrow($resultu))
				{
					$template->assign_block_vars('match_unreported.ustats', array(
						'USERNAME'	=> getusername($rowu['user_id']),
						'USER_ID'	=> $rowu['user_id'],
						'KILL'		=> $rowu['kills'],
						'DEAD'		=> $rowu['deads'],
						'ASSIST'	=> $rowu['assists'],
						'GOAL_F'	=> $rowu['goal_f'],
						'GOAL_A'	=> $rowu['goal_a']
					));
					$u++;
				}
				$db->sql_freeresult($result);
			}
			// end userstats	
			$m++;
		}
		$db->sql_freeresult($result);
		
		// ALL REPORTED MATCHES
		$sql_r		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_ladder = {$zlad} AND ((match_challenger = {$clan1} AND match_challengee = {$clan2}) 
					OR (match_challenger = {$clan2} AND match_challengee = {$clan1})) AND match_finishtime > 0 AND match_confirmed > 0 AND match_reported > 0";
		$result_r	= $db->sql_query($sql_r);
		$r 			= 0;
		while ($row_r = $db->sql_fetchrow($result_r))
		{		
			$theladder	= $ladder->get_roots($row_r['match_ladder']);
			
			// Assign it to the template.
			$template->assign_block_vars('match_reported', array(
				'CHALLANGER'	=> $group->data('group_name', $row_r['match_challenger']),
				'CHALLANGEE'	=> $group->data('group_name', $row_r['match_challengee']),
				'IP_ER'			=> $row_r['match_challenger_ip'],
				'IP_EE'			=> $row_r['match_challengee_ip'],
				'U_IP_ER'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row_r['match_challenger_ip']),
				'U_IP_EE'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row_r['match_challengee_ip']),
				'WINNER'		=> ($row_r['match_winner'] == '9999999') ? $user->lang['PAREGGIO'] : $group->data('group_name', $row_r['match_winner']),
				'MATCHTIME'		=> $user->format_date($row_r['match_finishtime']),
				'RANKED'		=> ($row_r['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
				'LADDER'		=> $theladder['PLATFORM_NAME'] . ', ' . $theladder['LADDER_NAME'] . ', ' . $theladder['SUBLADDER_NAME']
			));
			
			$r++;
		}
		$db->sql_freeresult($result_r);
	}	
}

/*******************************
*	ACTIONS
************/
	if ($reset)
	{
		$referente	= (int) request_var('referente', 0);
		$clan1		= (int) request_var('clan1', 0);
		$clan2		= (int) request_var('clan2', 0);
		$xladder	= (int) request_var('xladder', 0);
		$xmatch		= (int) request_var('xmatch', 0);
		
		// get match data
		$sql_m		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = {$xmatch}";
		$result_m	= $db->sql_query($sql_m);
		$row_m		= $db->sql_fetchrow($result_m);
		$db->sql_freeresult($result_m);
		
		$winner		= $row_m['match_winner'];
		$gugu		= ($winner == '9999999') ? 0 : 1;
		$ref_point	= ($referente == $clan1) ? $row_m['match_challanger_score'] : $row_m['match_challangee_score'];
		$alt_point	= ($referente == $clan2) ? $row_m['match_challanger_score'] : $row_m['match_challangee_score'];
		$mvp1		= $row_m['mvp1'];
		$mvp2		= $row_m['mvp2'];
		$mvp3		= $row_m['mvp3'];
		
		// get referente data
		$sql_g		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$referente} AND group_ladder = {$xladder}";
		$result_g	= $db->sql_query_limit($sql_g, 1);
		$row_g		= $db->sql_fetchrow($result_g);
		$db->sql_freeresult($result_g);
		
		$currentvalue	= $row_g['group_score'];
		$oldscore		= $row_g['group_lastscore'];
		$matchpoints	= ($currentvalue > $oldscore) ? ($currentvalue - $oldscore) : ($oldscore - $currentvalue);
		
		$currentrank	= $row_g['group_current_rank'];
		$lastrank		= $row_g['group_last_rank'];
		
		$altroclan		= ($referente == $clan1) ? $clan2 : $clan1;
		
		// get altro data
		$sql_a		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$altroclan} AND group_ladder = {$xladder}";
		$result_a	= $db->sql_query_limit($sql_a, 1);
		$row_a		= $db->sql_fetchrow($result_a);
		$db->sql_freeresult($result_a);
		
		//update referente
		$sql_array	= array(
			'group_wins'			=> ($winner == $referente) ? ($row_g['group_wins'] - $gugu) : $row_g['group_wins'],
			'group_losses'			=> ($winner == $referente) ? $row_g['group_losses'] : ($row_g['group_losses'] - $gugu),
			'group_score'			=> $oldscore,
			'group_streak'			=> ($winner == $referente) ? ($row_g['group_streak'] - $gugu) : ($row_g['group_streak'] + $gugu),
			'group_current_rank'	=> ($lastrank == 0) ? $row_g['group_current_rank'] : $lastrank,
			'group_pari'			=> ($winner == '9999999') ? $row_g['group_pari'] - 1 : $row_g['group_pari'],
			'group_goals_fatti'		=> $row_g['group_goals_fatti'] - $ref_point,
			'group_goals_subiti'	=> $row_g['group_goals_subiti'] - $alt_point,
		);
		$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = {$referente} AND group_ladder = {$xladder}";
		$db->sql_query($sql);
		
		//update altro clan
		$sql_array2	= array(
			'group_wins'			=> ($winner == $altroclan) ? ($row_a['group_wins'] - $gugu) : $row_a['group_wins'],
			'group_losses'			=> ($winner == $altroclan) ? $row_a['group_losses'] : ($row_a['group_losses'] - $gugu),
			'group_score'			=> ($currentvalue > $oldscore) ? $row_a['group_score'] + $matchpoints : $row_a['group_score'] - $matchpoints, // if referente was the one who increment the score
			'group_streak'			=> ($winner == $altroclan) ? ($row_a['group_streak'] - $gugu) : ($row_a['group_streak'] + $gugu),
			'group_current_rank'	=> $currentrank,
			'group_pari'			=> ($winner == '9999999') ? $row_a['group_pari'] - 1 : $row_a['group_pari'],
			'group_goals_fatti'		=> $row_a['group_goals_fatti'] - $alt_point,
			'group_goals_subiti'	=> $row_a['group_goals_subiti'] - $ref_point,
		);
		$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = {$altroclan} AND group_ladder = {$xladder}";
		$db->sql_query($sql);
		
		// update mvps if exist
		if ($mvp1 > 0)
		{
			$sql	= "UPDATE " . USER_LADDER_STATS . " SET mvps = mvps - 1 WHERE ladder_id = {$xladder} AND user_id = " . $mvp1;
			$db->sql_query($sql);
				
			$sql	= "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp - 1 WHERE user_id = " . $mvp1;
			$db->sql_query($sql);
			
			$sql	= "UPDATE " . USER_CLAN_TABLE . " SET mvp_utente = mvp_utente - 1 WHERE (group_id = {$referente} OR group_id = {$altroclan}) AND user_id = " . $mvp1;
			$db->sql_query($sql);
		}
		if ($mvp2 > 0)
		{
			$sql	= "UPDATE " . USER_LADDER_STATS . " SET mvps = mvps - 1 WHERE ladder_id = {$xladder} AND user_id = " . $mvp2;
			$db->sql_query($sql);
				
			$sql	= "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp - 1 WHERE user_id = " . $mvp2;
			$db->sql_query($sql);
			
			$sql	= "UPDATE " . USER_CLAN_TABLE . " SET mvp_utente = mvp_utente - 1 WHERE (group_id = {$referente} OR group_id = {$altroclan}) AND user_id = " . $mvp2;
			$db->sql_query($sql);
		}
		if ($mvp3 > 0)
		{
			$sql	= "UPDATE " . USER_LADDER_STATS . " SET mvps = mvps - 1 WHERE ladder_id = {$xladder} AND user_id = " . $mvp3;
			$db->sql_query($sql);
				
			$sql	= "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp - 1 WHERE user_id = " . $mvp3;
			$db->sql_query($sql);
			
			$sql	= "UPDATE " . USER_CLAN_TABLE . " SET mvp_utente = mvp_utente - 1 WHERE (group_id = {$referente} OR group_id = {$altroclan}) AND user_id = " . $mvp3;
			$db->sql_query($sql);
		}
		
		// unreport the match
		$sql_array3	= array(
		'match_winner'					=> 0,
		'match_loser'					=> 0,
		'match_challanger_score'		=> 0,
		'match_challangee_score'		=> 0,
		'match_challanger_score_mode1'	=> 0,
		'match_challangee_score_mode1'	=> 0,
		'match_challanger_score_mode2'	=> 0,
		'match_challangee_score_mode2'	=> 0,
		'match_challanger_score_mode3'	=> 0,
		'match_challangee_score_mode3'	=> 0,
		'match_reported'				=> 0,
		'match_confirmed'				=> 0,
		'mvp1'							=> 0,
		'mvp2'							=> 0,
		'mvp3'							=> 0,
		);
		$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE match_id = {$xmatch}";
		$db->sql_query($sql);
		
		add_log('admin', 'LOG_RIVALS_MATCH_RESETTED', $group->data('group_name', $row_m['match_challenger']), $group->data('group_name', $row_m['match_challengee']));
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_match");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_RESETTATO');	
	}
	
	if ($submit)
	{
		$idmatch	= (int) request_var('editedmatch_id', 0);
		$winner		= (int) request_var('winner', 0);
		$reportet	= (int) request_var('reporter', 0);
		$er_score	= (int) request_var('er_score', 0);
		$ee_score	= (int) request_var('ee_score', 0);
		$er_scorem1	= (int) request_var('er_scorem1', 0);
		$er_scorem2	= (int) request_var('er_scorem2', 0);
		$er_scorem3	= (int) request_var('er_scorem3', 0);
		$ee_scorem1	= (int) request_var('ee_scorem1', 0);
		$ee_scorem2	= (int) request_var('ee_scorem2', 0);
		$ee_scorem3	= (int) request_var('ee_scorem3', 0);
		$mvp1		= (int) request_var('mvp1', 0);
		$mvp2		= (int) request_var('mvp2', 0);
		$mvp3		= (int) request_var('mvp3', 0);
		$er_id		= (int) request_var('er_id', 0);
		$ee_id		= (int) request_var('ee_id', 0);
		
		if ($winner == '9999999')
		{
			$loser	= '9999999';
		}
		else if ($winner == $er_id)
		{
			$loser	= $ee_id;
		}
		else if ($winner == $ee_id)
		{
			$loser	= $er_id;
		}
		
		$sql_array3	= array(
			'match_winner'					=> $winner,
			'match_reported'				=> $reportet,
			'match_loser'					=> $loser,
			'match_challanger_score'		=> $er_score,
			'match_challangee_score'		=> $ee_score,
			'match_challanger_score_mode1'	=> $er_scorem1,
			'match_challangee_score_mode1'	=> $ee_scorem1,
			'match_challanger_score_mode2'	=> $er_scorem2,
			'match_challangee_score_mode2'	=> $ee_scorem2,
			'match_challanger_score_mode3'	=> $er_scorem3,
			'match_challangee_score_mode3'	=> $ee_scorem3,
			'mvp1'							=> $mvp1,
			'mvp2'							=> $mvp2,
			'mvp3'							=> $mvp3,
		);
		$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE match_id = {$idmatch}";
		$db->sql_query($sql);
		
		// check for adv stats
		$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = {$idmatch}";
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		//get ladder datas
		$ladder_data	= $ladder->get_roots($row['match_ladder']);
		
		// advanced stats
		if ($ladder_data['SUBLADDER_ADVSTAT'] == 1)
		{
			$advstats	= isset($_POST['stats']) ? $_POST['stats'] : array();
			foreach ($advstats as $ID_utente => $values)
			{
				$xkill	= (!empty($values['kills'])) ? $values['kills'] : 0;
				$xdeads	= (!empty($values['morti'])) ? $values['morti'] : 0;
				$xasist = (!empty($values['assist'])) ? $values['assist'] : 0;
				$xgoalf = (!empty($values['goalf'])) ? $values['goalf'] : 0;
				$xgoala = (!empty($values['goals'])) ? $values['goals'] : 0;
				
				//CHECK FOR NUMBERS ENTRIES
				if (!is_numeric($xkill)
				|| !is_numeric($xdeads)
				|| !is_numeric($xasist)
				|| !is_numeric($xgoalf)
				|| !is_numeric($xgoala))
				{
					$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&&amp;mode=edit_match");
					meta_refresh(4, $redirect_url);
					trigger_error(sprintf($user->lang['STATS_SOLO_NUMERI'], '<a href="' . $redirect_url . '">', '</a>'));
				}
				
				$sql_array4	= array(
					'kills'		=> $xkill,
					'deads'		=> $xdeads,
					'assists'	=> $xasist,
					'goal_f'	=> $xgoalf,
					'goal_a'	=> $xgoala
				);
				$sql = "UPDATE " . MATCH_TEMP_USTATS. " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE id_match = {$idmatch} AND user_id = {$ID_utente}";
				$db->sql_query($sql);
			}
		}
		
		// FINISH ALL
		add_log('admin', 'LOG_RIVALS_MATCH_EDITED', $group->data('group_name', $winner), $group->data('group_name', $loser));
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_match");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_UPDATED');		
	}
	
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action
	));
}
?>