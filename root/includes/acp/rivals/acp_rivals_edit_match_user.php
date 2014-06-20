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
 * Called from acp_rivals with mode == 'edit_match_user'
 */
function acp_rivals_edit_match_user($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;
	
	$ladder		= new ladder();
	$select		= (!empty($_POST['select'])) ? true : false;
	$submit		= (!empty($_POST['submit'])) ? true : false;
	$reset		= (!empty($_POST['resettable'])) ? true : false;
	$clan1		= (int) request_var('clan1', 0);
	$clan2		= (int) request_var('clan2', 0);
	$zlad		= (int) request_var('zonelad', 0);
	
	// LOAD CLANS FOR SELECT
	$sql	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " AS ut LEFT JOIN " . USERS_TABLE . " AS u ON ut.user_id = u.user_id WHERE u.user_id >0 GROUP BY ut.user_id ORDER BY UCASE(u.username) ASC";
	$result	= $db->sql_query($sql);
	$j 		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign it to the template.
		$template->assign_block_vars('clan_select', array(
			'CLAN_NAME'	=> $row['username'],
			'CLAN_ID'	=> $row['user_id']
		));
		$j++;
	}
	$db->sql_freeresult($result);
	
	// LOAD LADDERS FOR SELECT
	$template->assign_vars(array(
		'S_LADDER'	=> $ladder->make_ladder_select(true, false, false, false)
	));
	
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
			$sql4		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$zlad} AND ((1vs1_challanger = {$clan1} AND 1vs1_challangee = {$clan2}) 
						OR (1vs1_challanger = {$clan2} AND 1vs1_challangee = {$clan1})) AND 1vs1_unranked = 0 AND 1vs1_confirmer > 0 ORDER BY end_time DESC";
			$result4	= $db->sql_query_limit($sql4, 1);
			$row4		= $db->sql_fetchrow($result4);
			$db->sql_freeresult($result4);
			
			if (!empty($row4['1vs1_id']))
			{
				// GOT THE LAST MATCH REPORTED FOR THIS CLANS
				// Only if the match are the last one for challanger or challangee
				$sql5		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$zlad} AND (1vs1_challanger = {$clan1} OR 1vs1_challangee = {$clan1})
							ORDER BY end_time DESC";
				$result5	= $db->sql_query_limit($sql5, 1);
				$row5		= $db->sql_fetchrow($result5);
				$db->sql_freeresult($result5);
			
				$sql6		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$zlad} AND (1vs1_challanger = {$clan2} OR 1vs1_challangee = {$clan2})
							ORDER BY end_time DESC";
				$result6	= $db->sql_query_limit($sql6, 1);
				$row6		= $db->sql_fetchrow($result6);
				$db->sql_freeresult($result6);
			
				$template->assign_block_vars('block_recursive', array(
					'MATCH_ID'	 	=> (!empty($row4['1vs1_id'])) ? $row4['1vs1_id'] : false,
					'XLADDER'	 	=> $row4['1vs1_ladder'],
					'CLAN1'			=> $row4['1vs1_challanger'],
					'CLAN2'			=> $row4['1vs1_challangee'],
					'CHALLANGER' 	=> (!empty($row4['1vs1_challanger'])) ? getusername($row4['1vs1_challanger']) : '',
					'CHALLANGEE'	=> (!empty($row4['1vs1_challangee'])) ? getusername($row4['1vs1_challangee']) : '',
					'WINNER' 		=> ($row4['1vs1_winner'] == '9999999') ? $user->lang['PAREGGIO'] : getusername($row4['1vs1_winner']),
					'ER_POINT'		=> $row4['1vs1_challanger_score'],
					'EE_POINT'		=> $row4['1vs1_challangee_score'],
					'RESETTABLE1'	=> ($row4['1vs1_id'] == $row5['1vs1_id']) ? true : false,
					'RESETTABLE2'	=> ($row4['1vs1_id'] == $row6['1vs1_id']) ? true : false,
					'REFMATCH'		=> ($row4['1vs1_id'] == $row5['1vs1_id']) ? $clan1 : $clan2
				));
			}
			else
			{
				$template->assign_block_vars('block_recursive', array(
					'MATCH_ID'	 	=> false,
					'RESETTABLE1'	=> false,
					'RESETTABLE2'	=> false
				));
			}
			
			// MATCH UNREPORTED
			$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$zlad} AND ((1vs1_challanger = {$clan1} AND 1vs1_challangee = {$clan2}) 
						OR (1vs1_challanger = {$clan2} AND 1vs1_challangee = {$clan1})) AND end_time = 0 AND 1vs1_confirmer = 0 AND 1vs1_reporter > 0 AND 1vs1_accepted = 1";
			$result	= $db->sql_query($sql);
			$m 		= 0;
			while ($row = $db->sql_fetchrow($result))
			{
				//get ladder data
				$ladder_data	= $ladder->get_roots($row['1vs1_ladder']);
				
				// Assign it to the template.
				$template->assign_block_vars('match_unreported', array(
					'CHALLANGER'	=> getusername($row['1vs1_challanger']),
					'CHALLANGEE'	=> getusername($row['1vs1_challangee']),
					'WINNERER' 		=> ($row['1vs1_winner'] == $row['1vs1_challanger']) ? 'selected="selected"' : '',
					'WINNEREE' 		=> ($row['1vs1_winner'] == $row['1vs1_challangee']) ? 'selected="selected"' : '',
					'WINNERPP' 		=> ($row['1vs1_winner'] == '9999999') ? 'selected="selected"' : '',
					'ID_ER'			=> $row['1vs1_challanger'],
					'ID_EE'			=> $row['1vs1_challangee'],
					'ID_PP'			=> '9999999',
					'IP_ER'			=> $row['1vs1_challanger_ip'],
					'IP_EE'			=> $row['1vs1_challangee_ip'],
					'U_IP_ER'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row['1vs1_challanger_ip']),
					'U_IP_EE'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row['1vs1_challangee_ip']),
					'ER_POINT'		=> $row['1vs1_challanger_score'],
					'EE_POINT'		=> $row['1vs1_challangee_score'],
					'ER_POINT_M1'	=> $row['mode1_score_er'],
					'EE_POINT_M1'	=> $row['mode1_score_ee'],
					'ER_POINT_M2'	=> $row['mode2_score_er'],
					'EE_POINT_M2'	=> $row['mode2_score_ee'],
					'ER_POINT_M3'	=> $row['mode3_score_er'],
					'EE_POINT_M3'	=> $row['mode3_score_ee'],
					'REPORTER_NO'	=> ($row['1vs1_reporter'] == 0) ? 'selected="selected"' : '',
					'REPORTER_ER'	=> ($row['1vs1_reporter'] == $row['1vs1_challanger']) ? 'selected="selected"' : '',
					'REPORTER_EE'	=> ($row['1vs1_reporter'] == $row['1vs1_challangee']) ? 'selected="selected"' : '',
					'MATCHTIME'		=> $user->format_date($row['start_time']),
					'MATCH_ID'		=> $row['1vs1_id'],
					'MODE1'			=> $row['1vs1_mappa1'],
					'MODE2'			=> $row['1vs1_mappa2'],
					'MODE3'			=> $row['1vs1_mappa3'],
					'DECERTO'		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
					'POINTRESULT'	=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
					'RANKED'		=> ($row['1vs1_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA']
				));
				
				$m++;
			}
			$db->sql_freeresult($result);
			
			// ALL REPORTED MATCHES
			$sql_r		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$zlad} AND ((1vs1_challanger = {$clan1} AND 1vs1_challangee = {$clan2}) 
						OR (1vs1_challanger = {$clan2} AND 1vs1_challangee = {$clan1})) AND end_time > 0 AND 1vs1_confirmer > 0 AND 1vs1_reporter > 0 AND 1vs1_accepted = 1";
			$result_r	= $db->sql_query($sql_r);
			$r 			= 0;
			while ($row_r = $db->sql_fetchrow($result_r))
			{		
				$theladder	= $ladder->get_roots($row_r['1vs1_ladder']);
				
				// Assign it to the template.
				$template->assign_block_vars('match_reported', array(
					'CHALLANGER'	=> getusername($row_r['1vs1_challanger']),
					'CHALLANGEE'	=> getusername($row_r['1vs1_challangee']),
					'IP_ER'			=> $row_r['1vs1_challanger_ip'],
					'IP_EE'			=> $row_r['1vs1_challangee_ip'],
					'U_IP_ER'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row_r['1vs1_challanger_ip']),
					'U_IP_EE'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;mode=overview&amp;action=whois&amp;user_ip=" . $row_r['1vs1_challangee_ip']),
					'WINNER'		=> ($row_r['1vs1_winner'] == '9999999') ? $user->lang['PAREGGIO'] : getusername($row_r['1vs1_winner']),
					'MATCHTIME'		=> $user->format_date($row_r['end_time']),
					'RANKED'		=> ($row_r['1vs1_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
					'LADDER'		=> $theladder['PLATFORM_NAME'] . ', ' . $theladder['LADDER_NAME'] . ', ' . $theladder['SUBLADDER_NAME']
				));
				
				$r++;
			}
			$db->sql_freeresult($result_r);
		}	
	}

/**************************
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
		$sql_m		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = {$xmatch}";
		$result_m	= $db->sql_query($sql_m);
		$row_m		= $db->sql_fetchrow($result_m);
		$db->sql_freeresult($result_m);
		
		$winner		= $row_m['1vs1_winner'];
		$gugu		= ($winner == '9999999') ? 0 : 1;
		$ref_point	= ($referente == $clan1) ? $row_m['1vs1_challanger_score'] : $row_m['1vs1_challangee_score'];
		$alt_point	= ($referente == $clan2) ? $row_m['1vs1_challanger_score'] : $row_m['1vs1_challangee_score'];
		
		// get referente data
		$sql_g		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$referente} AND 1vs1_ladder = {$xladder}";
		$result_g	= $db->sql_query_limit($sql_g, 1);
		$row_g		= $db->sql_fetchrow($result_g);
		$db->sql_freeresult($result_g);
		
		$currentvalue	= $row_g['user_score'];
		$oldscore		= $row_g['user_lastscore'];
		$matchpoints	= ($currentvalue > $oldscore) ? ($currentvalue - $oldscore) : ($oldscore - $currentvalue);
		$currentrank	= $row_g['user_current_rank'];
		$lastrank		= $row_g['user_last_rank'];
		$altroclan		= ($referente == $clan1) ? $clan2 : $clan1;
		
		// get altro data
		$sql_a		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$altroclan} AND 1vs1_ladder = {$xladder}";
		$result_a	= $db->sql_query_limit($sql_a, 1);
		$row_a		= $db->sql_fetchrow($result_a);
		$db->sql_freeresult($result_a);
		
		//update referente
		$sql_array	= array(
			'user_wins'			=> ($winner == $referente) ? ($row_g['user_wins'] - $gugu) : $row_g['user_wins'],
			'user_losses'		=> ($winner == $referente) ? $row_g['user_losses'] : ($row_g['user_losses'] - $gugu),
			'user_score'		=> $oldscore,
			'user_streak'		=> ($winner == $referente) ? ($row_g['user_streak'] - $gugu) : ($row_g['user_streak'] + $gugu),
			'user_current_rank'	=> ($lastrank == 0) ? $row_g['user_current_rank'] : $lastrank,
			'user_pari'			=> ($winner == '9999999') ? $row_g['user_pari'] - 1 : $row_g['user_pari'],
			'user_goals_fatti'	=> $row_g['user_goals_fatti'] - $ref_point,
			'user_goals_subiti'	=> $row_g['user_goals_subiti'] - $alt_point,
		);
		$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE user_id = {$referente} AND 1vs1_ladder = {$xladder}";
		$db->sql_query($sql);
		
		//update altro clan
		$sql_array2	= array(
			'user_wins'			=> ($winner == $altroclan) ? ($row_a['user_wins'] - $gugu) : $row_a['user_wins'],
			'user_losses'		=> ($winner == $altroclan) ? $row_a['user_losses'] : ($row_a['user_losses'] - $gugu),
			'user_score'		=> ($currentvalue > $oldscore) ? $row_a['user_score'] + $matchpoints : $row_a['user_score'] - $matchpoints, // if referente was the one who increment the score
			'user_streak'		=> ($winner == $altroclan) ? ($row_a['user_streak'] - $gugu) : ($row_a['user_streak'] + $gugu),
			'user_current_rank'	=> $currentrank,
			'user_pari'			=> ($winner == '9999999') ? $row_a['user_pari'] - 1 : $row_a['user_pari'],
			'user_goals_fatti'	=> $row_a['user_goals_fatti'] - $alt_point,
			'user_goals_subiti'	=> $row_a['user_goals_subiti'] - $ref_point,
		);
		$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE user_id = {$altroclan} AND 1vs1_ladder = {$xladder}";
		$db->sql_query($sql);
		
		// unreport the match
		$sql_array3	= array(
			'1vs1_winner'			=> 0,
			'1vs1_challanger_score'	=> 0,
			'1vs1_challangee_score'	=> 0,
			'mode1_score_er'		=> 0,
			'mode1_score_ee'		=> 0,
			'mode2_score_er'		=> 0,
			'mode2_score_ee'		=> 0,
			'mode3_score_er'		=> 0,
			'mode3_score_ee'		=> 0,
			'1vs1_reporter'			=> 0,
			'1vs1_confirmer'		=> 0,
		);
		$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE 1vs1_id = {$xmatch}";
		$db->sql_query($sql);
		
		add_log('admin', 'LOG_RIVALS_MATCH_RESETTED', getusername($row_m['1vs1_challanger']), getusername($row_m['1vs1_challangee']));
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_match");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_RESETTATO');	
	}
	
	if ($submit)
	{
		$idmatch	= (int) request_var('edited1vs1_id', 0);
		$winner		= (int) request_var('winner', 0);
		$reporter	= (int) request_var('reporter', 0);
		$er_score	= (int) request_var('er_score', 0);
		$ee_score	= (int) request_var('ee_score', 0);
		$er_scorem1	= (int) request_var('er_scorem1', 0);
		$er_scorem2	= (int) request_var('er_scorem2', 0);
		$er_scorem3	= (int) request_var('er_scorem3', 0);
		$ee_scorem1	= (int) request_var('ee_scorem1', 0);
		$ee_scorem2	= (int) request_var('ee_scorem2', 0);
		$ee_scorem3	= (int) request_var('ee_scorem3', 0);
		$er_id		= (int) request_var('er_id', 0);
		$ee_id		= (int) request_var('ee_id', 0);
		
		$sql_array3	= array(
			'1vs1_winner'			=> $winner,
			'1vs1_reporter'			=> $reporter,
			'1vs1_challanger_score'	=> $er_score,
			'1vs1_challangee_score'	=> $ee_score,
			'mode1_score_er'		=> $er_scorem1,
			'mode1_score_ee'		=> $ee_scorem1,
			'mode2_score_er'		=> $er_scorem2,
			'mode2_score_ee'		=> $ee_scorem2,
			'mode3_score_er'		=> $er_scorem3,
			'mode3_score_ee'		=> $ee_scorem3,
		);
		$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE 1vs1_id = {$idmatch}";
		$db->sql_query($sql);
		
		// FINISH ALL 
		$sql_m		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = {$idmatch}";
		$result_m	= $db->sql_query($sql_m);
		$row_m		= $db->sql_fetchrow($result_m);
		$db->sql_freeresult($result_m);
		
		add_log('admin', 'LOG_RIVALS_MATCH_EDITED', getusername($row_m['1vs1_challanger']), getusername($row_m['1vs1_challangee']));
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_match_user");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_UPDATED');		
	}
	
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action
	));
}
?>