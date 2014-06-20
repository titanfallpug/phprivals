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

/**
 * Manage Matches Advanced Stats
 * Called from ucp_rivals with mode == 'matchmvp'
 */

function ucp_rivals_matches_mvp($id, $mode, $u_action)
{
	global	$config, $db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$error 		= array();
	$matchid	= (int) request_var('mid', 0);
	$group_id	= (int) request_var('group_id', 0);
	$group_data	= $group->data('*', $group_id);
	$submit = (!empty($_POST['submit'])) ? true : false;
	
	
/************************************************************
*	Action report match result
*********************************/
	if ($submit)
	{
		$match_id	= (int) request_var('matchid', 0);
		$ladder_id	= (int) request_var('ladderid', 0);
			
	    // check the there are already stats reported for this match... anti back button.
		$sql_c		= "SELECT * FROM " . MATCH_TEMP_USTATS . " WHERE id_match = {$match_id}";
		$result_c	= $db->sql_query_limit($sql_c, 1);
		$row_c		= $db->sql_fetchrow($result_c);
		$db->sql_freeresult($result_c);
	
		if (!empty($row_c['id_match']))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches");
			meta_refresh(4, $redirect_url);
			trigger_error(sprintf($user->lang['RISULTATO_GIA_RIPORTATO'], '<a href="' . $redirect_url . '">', '</a>'));
		}
		
		// Get the match information.
		$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $match_id;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		// Confirm that this is their match.
		validate_opponents($row['match_challenger'], $row['match_challengee']);
		
		
		// SETS MVPS IF NEED
		$ladder_data	= $ladder->get_roots($ladder_id);

		if ($ladder_data['SUBLADDER_MVP'] == 1)
		{
			$mvp1	= (int) request_var('mvp1', 0);
			$mvp2	= (int) request_var('mvp2', 0);
			$mvp3	= (int) request_var('mvp3', 0);
			
			$sql_array	= array(
				'mvp1'	=> $mvp1,
				'mvp2'	=> $mvp2,
				'mvp3'	=> $mvp3
			);
			$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE match_id = " . $match_id;
			$db->sql_query($sql);
		}
		
		// SETS ADVANCED STATS IF NEED
		if ($ladder_data['SUBLADDER_ADVSTAT'] == 1)
		{
			// SETS ADVANCED STATS IN TEMP DIRECTORY
			$advstats	= isset($_POST['stats']) ? $_POST['stats'] : array();
			
			// CHECK IF AT LEAST 2 PLAYERS PLAY THAT MATCH
			$haigiocato = 0;
			foreach ($advstats as $values)
			{
				if (isset($values['haigiocato']) && $values['haigiocato'] == 1)
				{
					$haigiocato++;
				}
				if ( $haigiocato >= 2 )
				{
					break;
				}
			}
			
			if ($haigiocato < 2)
			{
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchmvp&amp;mid={$match_id}");
				meta_refresh(4, $redirect_url);
				trigger_error(sprintf($user->lang['ADVSTATS_AT_LEAST_2'], '<a href="' . $redirect_url . '">', '</a>'));
			}
			else
			{
				foreach ($advstats as $ID_utente => $values)
				{
					if (isset($values['haigiocato']))
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
							$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchmvp&amp;mid={$match_id}");
							meta_refresh(4, $redirect_url);
							trigger_error(sprintf($user->lang['STATS_SOLO_NUMERI'], '<a href="' . $redirect_url . '">', '</a>'));
						}
					
						$sql_array3	= array(
							'id_match'	=> $match_id,
							'id_ladder'	=> $ladder_id,
							'user_id'	=> $ID_utente,
							'kills'		=> $xkill,
							'deads'		=> $xdeads,
							'assists'	=> $xasist,
							'goal_f'	=> $xgoalf,
							'goal_a'	=> $xgoala
						);
						$sql	= "INSERT INTO " . MATCH_TEMP_USTATS . " " . $db->sql_build_array('INSERT', $sql_array3);
						$db->sql_query($sql);
					}
				}
			} // if at least 2 members plays
		} // If advanced stats
		
		//ALL GOES WELL UPDATE THE MATCH WITH RESULT
		$winner			= (int) request_var('winner_clan', 0);
		$thefeedback	= (int) request_var('vsrep', 0);
		$reporter		= $group->data['group_id'];
				
		$tipo_ladder	= $ladder_data['SUBLADDER_STYLE'];
		$ladder_mvp		= $ladder_data['SUBLADDER_MVP'];
		$ladder_advst	= $ladder_data['SUBLADDER_ADVSTAT'];
				
		$other			= ($row['match_challenger'] == $group->data['group_id']) ? $row['match_challengee'] : $row['match_challenger'];
		$opponent		= $group->data('user_id', $other);
		
		$challangee_rep	= ($row['match_challenger'] == $group->data['group_id']) ? $thefeedback : 5;
		$challanger_rep	= ($row['match_challengee'] == $group->data['group_id']) ? $thefeedback : 5;
	
		if ($winner == 0) // risultato inviato con punteggio
		{
			$yourpoint 		= (int) request_var('your_point', 0);
			$opponentpoint	= (int) request_var('other_point', 0);
		// DECERTO INFOS ADDON:
			$your_score_mode1		= ($tipo_ladder == 1) ? (int) request_var('your_mode1_score', 0) : 0;
			$opponent_score_mode1	= ($tipo_ladder == 1) ? (int) request_var('opponent_mode1_score', 0) : 0;
			$your_score_mode2		= ($tipo_ladder == 1) ? (int) request_var('your_mode2_score', 0) : 0;
			$opponent_score_mode2	= ($tipo_ladder == 1) ? (int) request_var('opponent_mode2_score', 0) : 0;
			$your_score_mode3 		= ($tipo_ladder == 1) ? (int) request_var('your_mode3_score', 0) : 0;
			$opponent_score_mode3	= ($tipo_ladder == 1) ? (int) request_var('opponent_mode3_score', 0) : 0;
		// CALCIO TEAMS USED ADDON
			$your_team				= ($tipo_ladder == 3) ? (string) utf8_normalize_nfc(request_var('your_team_used', '', true)) : '';
			$opponent_team			= ($tipo_ladder == 3) ? (string) utf8_normalize_nfc(request_var('opponent_team_used', '', true)) : '';
			
			if (!is_numeric($yourpoint)
			|| !is_numeric($opponentpoint)
			|| !is_numeric($your_score_mode1)
			|| !is_numeric($opponent_score_mode1)
			|| !is_numeric($your_score_mode2)
			|| !is_numeric($opponent_score_mode2)
			|| !is_numeric($your_score_mode3)
			|| !is_numeric($opponent_score_mode3)
			)
			{
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchmvp&amp;mid={$match_id}");
				meta_refresh(4, $redirect_url);
				trigger_error(sprintf($user->lang['RISULTATO_NO_SCORE'], '<a href="' . $redirect_url . '">', '</a>'));
			}
			
			$challangerscore	= ($row['match_challenger'] == $group->data['group_id']) ? $yourpoint : $opponentpoint;
			$challangeescore	= ($row['match_challengee'] == $group->data['group_id']) ? $yourpoint : $opponentpoint;
			
			$challangerscore_mode1	= ($row['match_challenger'] == $group->data['group_id']) ? $your_score_mode1 : $opponent_score_mode1;
			$challangeescore_mode1	= ($row['match_challengee'] == $group->data['group_id']) ? $your_score_mode1 : $opponent_score_mode1;
			$challangerscore_mode2	= ($row['match_challenger'] == $group->data['group_id']) ? $your_score_mode2 : $opponent_score_mode2;
			$challangeescore_mode2	= ($row['match_challengee'] == $group->data['group_id']) ? $your_score_mode2 : $opponent_score_mode2;
			$challangerscore_mode3	= ($row['match_challenger'] == $group->data['group_id']) ? $your_score_mode3 : $opponent_score_mode3;
			$challangeescore_mode3	= ($row['match_challengee'] == $group->data['group_id']) ? $your_score_mode3 : $opponent_score_mode3;
			
			$challanger_team		= ($row['match_challenger'] == $group->data['group_id']) ? $your_team : $opponent_team;
			$challangee_team		= ($row['match_challengee'] == $group->data['group_id']) ? $your_team : $opponent_team;
			
			if ($yourpoint > $opponentpoint)
			{
				$vincitore	= $group->data['group_id'];
				$perdente	= $other;
			}
			else if ($yourpoint < $opponentpoint)
			{
				$vincitore	= $other;
				$perdente	= $group->data['group_id'];
			}
			else if ($yourpoint == $opponentpoint)
			{
				if ($tipo_ladder == 3) // la ladder calcio accetta il pareggio
				{
					$vincitore	= '9999999';
					$perdente	= '9999999';
				}
				else
				{
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchmvp&amp;mid={$match_id}");
					meta_refresh(4, $redirect_url);
					trigger_error(sprintf($user->lang['LADDER_NOT_ALLOW_DRAW'], '<a href="' . $redirect_url . '">', '</a>'));
				}
				
			}
			
			$sql_array	= array(
				'match_reptime'					=> time(),
				'match_winner'					=> $vincitore,
				'match_loser'					=> $perdente,
				'match_challanger_score'		=> $challangerscore,
				'match_challangee_score'		=> $challangeescore,
				'match_challanger_score_mode1'	=> $challangerscore_mode1,
				'match_challangee_score_mode1'	=> $challangeescore_mode1,
				'match_challanger_score_mode2'	=> $challangerscore_mode2,
				'match_challangee_score_mode2'	=> $challangeescore_mode2,
				'match_challanger_score_mode3'	=> $challangerscore_mode3,
				'match_challangee_score_mode3'	=> $challangeescore_mode3,
				'challenger_team'				=> $challanger_team,
				'challengee_team'				=> $challangee_team,
				'match_reported'				=> $reporter,
				'match_confirmed'				=> 0,
				'challanger_rep'				=> $challanger_rep,
				'challangee_rep'				=> $challangee_rep
			);
			$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE match_id = " . $match_id;
			$db->sql_query($sql);			
		}
		else
		{
			$vincitore	= $winner;
			$perdente	= ($row['match_challenger'] == $winner) ? $row['match_challengee'] : $row['match_challenger'];
			
			$sql_array	= array(
				'match_reptime'					=> time(),
				'match_winner'					=> $vincitore,
				'match_loser'					=> $perdente,
				'match_challanger_score'		=> 0,
				'match_challangee_score'		=> 0,
				'match_challanger_score_mode1'	=> 0,
				'match_challangee_score_mode1'	=> 0,
				'match_challanger_score_mode2'	=> 0,
				'match_challangee_score_mode2'	=> 0,
				'match_challanger_score_mode3'	=> 0,
				'match_challangee_score_mode3'	=> 0,
				'challenger_team'				=> '',
				'challengee_team'				=> '',
				'match_reported'				=> $reporter,
				'match_confirmed'				=> 0,
				'challanger_rep'				=> $challanger_rep,
				'challangee_rep'				=> $challangee_rep
			);
			$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE match_id = " . $match_id;
			$db->sql_query($sql);
		}
		
		
		// SEND THE PM
		//repopulate match info
		$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $match_id;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$other		= ($row['match_challenger'] == $group->data['group_id']) ? $row['match_challengee'] : $row['match_challenger'];
		$opponent	= $group->data('user_id', $other);
		
		// Send a PM to the loser to tell them to confirm the win...
		$subject	= $user->lang['PMCONFIRMWIN'];
		$message	= sprintf($user->lang['PMCONFIRMWINTXT'], $group->data['group_name']);
		insert_pm($opponent, $user->data, $subject, $message);
		
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_REPORTED');		
	}

/***********************************************
*	TEMPLATE DEFINITION
***************************/
	
	// Check if this match are already reported
	$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $matchid;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	// Confirm that this is their match.
	validate_opponents($row['match_challenger'], $row['match_challengee']);
	
	if ($row['match_finishtime'] > 0 || $row['match_confirmed'] > 0)
	{
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['RISULTATO_GIA_RIPORTATO'], '<a href="' . $redirect_url . '">', '</a>'));
	}
	else
	{		
		// delete temp stats this is for those form that output errors
		$sql = "DELETE FROM " . MATCH_TEMP_USTATS . " WHERE id_match = " . $matchid;
		$db->sql_query($sql);
		
		// get ladder info
		$ladder_data	= $ladder->get_roots($row['match_ladder']);
		
		// populate basic match data
		$challanger = $row['match_challenger'];
		$challangee	= $row['match_challengee'];
		$er_name	= $group->data('group_name', $challanger);
		$ee_name	= $group->data('group_name', $challangee);
		
		$other		= ($row['match_challenger'] == $group->data['group_id']) ? $row['match_challengee'] : $row['match_challenger'];
		
		$template->assign_vars(array(
            'CHALLANGER'	=> $er_name,
			'CHALLANGEE'	=> $ee_name,
			'MATCHID'		=> $matchid,
			'PLATFORM' 		=> $ladder_data['PLATFORM_NAME'],
			'LADDER' 		=> $ladder_data['LADDER_NAME'],
			'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME'],
			'LADDERID'		=> $row['match_ladder'],
			'MVP'			=> ($ladder_data['SUBLADDER_MVP'] == 1) ? true : false,
			'ADVSTATS'		=> ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? true : false,
			'DECERTO' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
			'MODE1' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode1'] : '',
			'MODE2' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode2'] : '',
			'MODE3' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode3'] : '',
			'CALCIO' 		=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? true : false,
			'LADDER_ICON'	=> ($ladder_data['SUBLADDER_STYLE'] > 0) ? '<img src="' . $phpbb_root_path .'rivals/images/iconlad' . $ladder_data['SUBLADDER_STYLE'] . '.gif" alt="' . $user->lang['ICON_LADDER'] . '" title="' . $user->lang["{$ladder_data['SUBLADDER_STYLE']}ICON_LADDER"] . '" />' : '',
			'IMG_ADVSTATS'	=> ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/statsicon.gif" alt="' . $user->lang['ICON_ADVSTATS'] . '" title="' . $user->lang['ICON_ADVSTATS'] . '" />' : '',
			'IMG_MVP'		=> ($ladder_data['SUBLADDER_MVP'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/mvpicon.gif" alt="' . $user->lang['ICON_MVP'] . '" title="' . $user->lang['ICON_MVP'] . '" />' : '',
			'IMG_RTH'		=> ($ladder_data['SUBLADDER_RAKING'] == 2) ? '<img src="' . $phpbb_root_path .'rivals/images/rth.gif" alt="' . $user->lang['RTH_LADDER'] . '" title="' . $user->lang['RTH_LADDER'] . '" /> ' : '',
			'SCORE_RELATED'	=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
			'TUOGRUPPO'		=> $group->data['group_name'],
			'TUAID'			=> $group->data['group_id'],
			'CLASSIFICATA'	=> ($row['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
			'MATCHDESC' 	=> $row['match_details'],
			'MAP1' 			=> $row['mappa_mode1'],
			'MAP2' 			=> $row['mappa_mode2'],
			'MAP3' 			=> $row['mappa_mode3'],
			'U_OPPONENT' 	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $other),
			'OPPONENT' 		=> $group->data('group_name', $other),
			'OPPONENT_ID' 	=> $other,
			'TIME' 			=> $user->format_date($row['match_posttime']),		
			'TPL'			=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? 2 : 1
		));
		
		// Populate challanger clan members list
		$clan1members	= $group->members('get_members', $challanger);
		foreach ($clan1members as $value1)
		{
			$template->assign_block_vars('blocco_er_memb', array(
				'USER_ID'	=> $value1,
				'USERNAME'	=> getusername($value1),
				'GT'		=> getgamername($value1)
			));
		}
		
		// Populate challangee clan members list
		$clan2members	= $group->members('get_members', $challangee);
		foreach ($clan2members as $value2)
		{
			$template->assign_block_vars('blocco_ee_memb', array(
				'USER_ID'	=> $value2,
				'USERNAME'	=> getusername($value2),
				'GT'		=> getgamername($value2)
			));
		}
	}
		
	// Assign the other variables to the template.
	$template->assign_vars(array('U_ACTION' => $u_action));
}

?>