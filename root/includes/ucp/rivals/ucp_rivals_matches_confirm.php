<?php
/**
*
* @package ucp
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
 * Confirm Match Result
 * Called from ucp_rivals with mode == 'matches_confirm'
 */ 
function ucp_rivals_matches_confirm($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$ladderv	= new ladder();
	
	$submit	= (!empty($_POST['submit'])) ? true : false;
	
	if (empty($user->data['group_session']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
	}
	else if (empty($group->data['group_ladders']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['GROUP_NOTIN_LADDER'], '<a href="' . $redirect_url . '">', '</a>'));
	}

/*********************************************************
*	ACTION SUBMIT
************************/
	if($submit)
	{
		$confirmed	= request_var('confirmed', array(0 => 0));
		$contested	= request_var('contested', array(0 => 0));
		nodouble_check($confirmed, $contested, 'i=rivals&amp;mode=matches_confirm');
		
		if (empty($confirmed) && empty($contested))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_confirm");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		// confirm action
		if (!empty($confirmed))
		{
			foreach ($confirmed AS $cvalue)
			{
				// Get the match information.
				$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $cvalue;
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
	
				// Confirm that this is their match.
				validate_opponents($row['match_challenger'], $row['match_challengee']);
				
				if ($row['match_confirmed'] == 0) /* check for trick */
				{
					$opponent	= ($row['match_challenger'] == $group->data['group_id']) ? $row['match_challengee'] : $row['match_challenger'];
					$reporter	= $row['match_reported'];
					$challanger = $row['match_challenger'];
					$challangee = $row['match_challengee'];
					
					if ($group->data['group_id'] == $reporter)
					{
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_confirm");
						meta_refresh(4, $redirect_url);
						trigger_error(sprintf($user->lang['REPORTER_SAME_YOU'], '<a href="' . $redirect_url . '">', '</a>'));
					}	
							
					// Set value for match table
					$sql_array	= array(
						'match_confirmed'	=> $group->data['group_id'],
						'match_finishtime'	=> time(),
						'match_status'		=> 1
					);
					$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE match_id = " . $cvalue;
					$db->sql_query($sql);

					// From now only if the match is ranked
					if ($row['match_unranked'] == 0)
					{
						////////// Sets mvps.
						$mvp1	= $row['mvp1'];
						$mvp2	= $row['mvp2'];
						$mvp3	= $row['mvp3'];
						$ladder	= $row['match_ladder'];
					
						// Get ladder information.
						$getladder	= $ladderv->get_roots($ladder);
					
						if ($mvp1 != 0)
						{
							$sql_m1		= "SELECT * FROM " . USER_LADDER_STATS . " WHERE ladder_id = {$ladder} AND user_id = {$mvp1}";
							$result_m1	= $db->sql_query_limit($sql_m1, 1);
							$row_m1		= $db->sql_fetchrow($result_m1);
							$db->sql_freeresult($result_m1);
						
							if (!empty($row_m1['user_id']))
							{
								$sql_array	= array(
									'mvps'	=> $row_m1['mvps'] + 1
								);
								$sql = "UPDATE " . USER_LADDER_STATS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = {$ladder} AND user_id = {$mvp1}";
								$db->sql_query($sql);						
							}
							else
							{
								$sql_array	= array(
									'ladder_id'		=> $ladder,
									'user_id'		=> $mvp1,
									'ranking'		=> 0,
									'kills'			=> 0,
									'deads'			=> 0,
									'assists'		=> 0,
									'goalf'			=> 0,
									'goala'			=> 0,
									'mvps'			=> 1,
									'match_played'	=> 0,
								);
								$sql		= "INSERT INTO " . USER_LADDER_STATS . " " . $db->sql_build_array ('INSERT', $sql_array);
								$db->sql_query($sql);
							}
						
							// users table
							$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + 1 WHERE user_id = {$mvp1}";
							$db->sql_query($sql);
							
							// user-clan table
							$sql_u1		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$mvp1} AND user_pending = 0";
							$result_u1	= $db->sql_query($sql_u1);
							$row_u1		= $db->sql_fetchrow($result_u1);
							$db->sql_freeresult($result_u1);
					
							$sql_array3	= array(
								'mvp_utente'	=> $row_u1['mvp_utente'] + 1
							);
							$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$mvp1} AND user_pending = 0";
							$db->sql_query($sql);
							
							// Recalculate exp user
							recalculate_totalEXP($mvp1);
						}
					
						if ($mvp2 != 0)
						{
							$sql_m2		= "SELECT * FROM " . USER_LADDER_STATS . " WHERE ladder_id = {$ladder} AND user_id = {$mvp2}";
							$result_m2	= $db->sql_query_limit($sql_m2, 1);
							$row_m2		= $db->sql_fetchrow($result_m2);
							$db->sql_freeresult($result_m2);
						
							if (!empty($row_m2['user_id']))
							{
								$sql_array	= array(
									'mvps'	=> $row_m2['mvps'] + 1
								);
								$sql = "UPDATE " . USER_LADDER_STATS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = {$ladder} AND user_id = {$mvp2}";
								$db->sql_query($sql);
							}
							else
							{
								$sql_array	= array(
									'ladder_id'		=> $ladder,
									'user_id'		=> $mvp2,
									'ranking'		=> 0,
									'kills'			=> 0,
									'deads'			=> 0,
									'assists'		=> 0,
									'goalf'			=> 0,
									'goala'			=> 0,
									'mvps'			=> 1,
									'match_played'	=> 0,
								);
								$sql = "INSERT INTO " . USER_LADDER_STATS . " " . $db->sql_build_array ('INSERT', $sql_array);
								$db->sql_query($sql);
							}
						
							// user table
							$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + 1 WHERE user_id = {$mvp2}";
							$db->sql_query($sql);
							
							// user-clan table
							$sql_u2		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$mvp2} AND user_pending = 0";
							$result_u2	= $db->sql_query($sql_u2);
							$row_u2		= $db->sql_fetchrow($result_u2);
							$db->sql_freeresult($result_u2);
					
							$sql_array3	= array(
								'mvp_utente'	=> $row_u2['mvp_utente'] + 1
							);
							$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$mvp2} AND user_pending = 0";
							$db->sql_query($sql);
							
							// Recalculate exp user
							recalculate_totalEXP($mvp2);
						}
					
						if ($mvp3 != 0)
						{
							$sql_m3		= "SELECT * FROM " . USER_LADDER_STATS . " WHERE ladder_id = {$ladder} AND user_id = {$mvp3}";
							$result_m3	= $db->sql_query_limit($sql_m3, 1);
							$row_m3		= $db->sql_fetchrow($result_m3);
							$db->sql_freeresult($result_m3);
						
							if (!empty($row_m3['user_id']))
							{
								$sql_array	= array(
									'mvps'	=> $row_m3['mvps'] + 1
								);
								$sql = "UPDATE " . USER_LADDER_STATS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = {$ladder} AND user_id = {$mvp3}";
								$db->sql_query($sql);
							}
							else
							{
								$sql_array	= array(
									'ladder_id'		=> $ladder,
									'user_id'		=> $mvp3,
									'ranking'		=> 0,
									'kills'			=> 0,
									'deads'			=> 0,
									'assists'		=> 0,
									'goalf'			=> 0,
									'goala'			=> 0,
									'mvps'			=> 1,
									'match_played'	=> 0,
								);
								$sql = "INSERT INTO " . USER_LADDER_STATS . " " . $db->sql_build_array ('INSERT', $sql_array);
								$db->sql_query($sql);
							}
							
							// user table
							$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + 1 WHERE user_id = {$mvp3}";
							$db->sql_query($sql);
							
							// user-clan table
							$sql_u3		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$mvp3} AND user_pending = 0";
							$result_u3	= $db->sql_query($sql_u3);
							$row_u3		= $db->sql_fetchrow($result_u3);
							$db->sql_freeresult($result_u3);
						
							$sql_array3	= array(
								'mvp_utente'	=> $row_u3['mvp_utente'] + 1
							);
							$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$mvp3} AND user_pending = 0";
							$db->sql_query($sql);
							
							// Recalculate exp user
							recalculate_totalEXP($mvp3);
						}
					
						////////// Sets advanced stats if need.
						if ($getladder['SUBLADDER_ADVSTAT'] == 1)
						{
							$sql1		= "SELECT * FROM " . MATCH_TEMP_USTATS . " WHERE id_match = {$cvalue} AND id_ladder = {$ladder}";
							$result1	= $db->sql_query($sql1);
							while ($row1 = $db->sql_fetchrow($result1))
							{
								$tempuser	= $row1['user_id'];
												
								/* i know that's a loop but it's needed */							
								$sql2		= "SELECT * FROM " . USER_LADDER_STATS . " WHERE ladder_id = {$ladder} AND user_id = {$tempuser}";
								$result2	= $db->sql_query_limit($sql2, 1);
								$row2		= $db->sql_fetchrow($result2);
								$db->sql_freeresult($result2);
								
								if (!empty($row2['user_id'])) // if there are user datas.
								{			
									$positivep	= $row1['kills'] + $row1['goal_f'] + $row2['kills'] + $row2['goalf'];
									$negativep	= $row1['deads'] + $row1['goal_a'] + $row2['deads'] + $row2['goala'];
									$played		= $row2['match_played'] + 1;
									
									$sql_array	= array(
										'ranking'		=> getuser_rank($played, $positivep, $negativep),
										'kills'			=> $row2['kills'] + $row1['kills'],
										'deads'			=> $row2['deads'] + $row1['deads'],
										'assists'		=> $row2['assists'] + $row1['assists'],
										'goalf'			=> $row2['goalf'] + $row1['goal_f'],
										'goala'			=> $row2['goala'] + $row1['goal_a'],
										'match_played'	=> $played
									);
									$sql = "UPDATE " . USER_LADDER_STATS . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = {$ladder} AND user_id = {$tempuser}";
									$db->sql_query($sql);
								}
								else // insert new one
								{
									$positivep	= $row1['kills'] + $row1['goal_f'];
									$negativep	= $row1['deads'] + $row1['goal_a'];
									
									$sql_array	= array(
										'ladder_id'		=> $ladder,
										'user_id'		=> $tempuser,
										'ranking'		=> getuser_rank(1, $positivep, $negativep),
										'kills'			=> $row1['kills'],
										'deads'			=> $row1['deads'],
										'assists'		=> $row1['assists'],
										'goalf'			=> $row1['goal_f'],
										'goala'			=> $row1['goal_a'],
										'mvps'			=> 0,
										'match_played'	=> 1
									);
									$sql = "INSERT INTO " . USER_LADDER_STATS . " " . $db->sql_build_array ('INSERT', $sql_array);
									$db->sql_query($sql);
								}
								
								// SAVE DATA FOR USER CLAN GENERAL INFO (repopulate)
								$sql3		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$tempuser} AND user_pending = 0";
								$result3	= $db->sql_query_limit($sql3, 1);
								$row3		= $db->sql_fetchrow($result3);
								$db->sql_freeresult($result3);
								
								$newkill	= $row1['kills'] + $row3['kills'];
								$newdeads	= $row1['deads'] + $row3['deads'];
								$newassist	= $row1['assists'] + $row3['assists'];
								$newgoalf	= $row1['goal_f'] + $row3['fgoals'];
								$newgoala	= $row1['goal_a'] + $row3['agoals'];
								
								$sql_array3	= array(
									'kills'		=> $newkill,
									'deads'		=> $newdeads,
									'assists'	=> $newassist,
									'fgoals'	=> $newgoalf,
									'agoals'	=> $newgoala
								);
								$sql = "UPDATE " . USER_CLAN_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE (group_id = {$challanger} OR group_id = {$challangee}) AND user_id = {$tempuser} AND user_pending = 0";
								$db->sql_query($sql);

								// Recalculate eser stats
								recalculate_totalEXP($tempuser);
							}
							$db->sql_freeresult($result1);
						}	
		////////		// NOW DEFINE THE MATCH PLAYERS SCORE FOR LADDER
						$tipoladder		= $getladder['SUBLADDER_STYLE'];
						$match_winner	= $row['match_winner'];
						$match_loser	= $row['match_loser'];
						$ladder_rank	= $getladder['SUBLADDER_RAKING'];
								
						// FOOTBALL ladder
						switch ($tipoladder)
						{		
							case FOOTBALL_LADDER:
							
								$winner_goals	= ($match_winner == $row['match_challenger']) ? $row['match_challanger_score'] : $row['match_challangee_score'];
								$loser_goals	= ($match_loser == $row['match_challenger']) ? $row['match_challanger_score'] : $row['match_challangee_score']; /** with a draw result the loser will be always the challangee */

								// If the result is a draw i use ER e EE
								if ($match_winner == '9999999')
								{
									// ER score.
									$sql_er		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$row['match_challenger']} AND group_ladder = {$ladder}";
									$result_er	= $db->sql_query_limit($sql_er, 1);
									$row_er		= $db->sql_fetchrow($result_er);
									$db->sql_freeresult($result_er);
									$er_score	= $row_er['group_score'];
								
									// EE score.
									$sql_ee		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$row['match_challengee']} AND group_ladder = {$ladder}";
									$result_ee	= $db->sql_query_limit($sql_ee, 1);
									$row_ee		= $db->sql_fetchrow($result_ee);
									$db->sql_freeresult($result_ee);
									$ee_score	= $row_ee['group_score'];
									
									// RTH MOD
									if ($ladder_rank == 2)
									{
										$er_punti	= $er_score + 10;
										$ee_punti	= $ee_score + 10;
									}
									else
									{
										// Calculate the score using the ladder position. 
										if ($er_score > $ee_score) // ER > EE
										{
											$er_punti	= $er_score + (10 + ceil(($er_score - $ee_score)/100));
											$ee_punti	= $ee_score + (10 + ceil(($er_score - $ee_score)/50));
										}
										else if ($er_score < $ee_score) // ER < EE
										{
											$er_punti	= $er_score + (10 + ceil(($ee_score - $er_score)/50));
											$ee_punti	= $ee_score + (10 + ceil(($ee_score - $er_score)/100));
										}
										else // ER = EE
										{
											$er_punti	= $er_score + 10;
											$ee_punti	= $ee_score + 10;
										}
									}
									
									// Update challanger
									$sql_array1	= array(
										'group_score'			=> $er_punti,
										'group_streak'			=> 0,
										'group_lastscore'		=> $er_score,
										'group_pari'			=> $row_er['group_pari'] + 1,
										'group_goals_fatti'		=> $row_er['group_goals_fatti'] + $row['match_challanger_score'],
										'group_goals_subiti'	=> $row_er['group_goals_subiti'] + $row['match_challangee_score']
									);
									$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE group_id = {$row['match_challenger']} AND group_ladder = {$ladder}";
									$db->sql_query($sql);
									
									// Update challanger
									$sql_array2	= array(
										'group_score'			=> $ee_punti,
										'group_streak'			=> 0,
										'group_lastscore'		=> $ee_score,
										'group_pari'			=> $row_ee['group_pari'] + 1,
										'group_goals_fatti'		=> $row_ee['group_goals_fatti'] + $row['match_challangee_score'],
										'group_goals_subiti'	=> $row_ee['group_goals_subiti'] + $row['match_challanger_score']
									);
									$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = {$row['match_challengee']} AND group_ladder = {$ladder}";
									$db->sql_query($sql);
									
									// Now, update the ranks. Swap if needed.
									$xladder	= new ladder();
									$xladder->update_ranks($row['match_challenger'], $row['match_challengee'], $ladder);
								}
								else /* NOW DRAW RESULT WE HAVE A WINNER */
								{
									// Winner score.
									$sql_er		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
									$result_er	= $db->sql_query_limit($sql_er, 1);
									$row_er		= $db->sql_fetchrow($result_er);
									$db->sql_freeresult($result_er);
									$w_score	= $row_er['group_score'];
								
									// Loser score.
									$sql_ee		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$match_loser} AND group_ladder = {$ladder}";
									$result_ee	= $db->sql_query_limit($sql_ee, 1);
									$row_ee		= $db->sql_fetchrow($result_ee);
									$db->sql_freeresult($result_ee);
									$l_score	= $row_ee['group_score'];
									
									// RTH MOD
									if ($ladder_rank == 2)
									{
										// Calculate the score using the ladder position. 
										if ($w_score > $l_score) // winner > loser
										{
											$w_punti	= $w_score + ceil($l_score / 5); //win 20% of loser point
											$l_punti_t	= $l_score - ceil($l_score / 5);
										}
										else if ($w_score < $l_score) // loser > winner
										{
											$w_punti	= $w_score + ceil($l_score / 2); //win 50% of loser point
											$l_punti_t	= $l_score - ceil($l_score / 2);
										}
										else // winner = loser
										{
											$w_punti	= $w_score + ceil($l_score * 35 / 100); // win 35% of loser point
											$l_punti_t	= $l_score - ceil($l_score * 35 / 100);
										}
										
										// do not allow point under 50.
										if ($l_punti_t < 50)
										{
											$l_punti = 50;
										}
										else
										{
											$l_punti = $l_punti_t;
										}
										
										// CHECK FOR POWNS! AWARDS
										if (($winner_goals - $loser_goals) >= 3)
										{
											if ($row_er['powns_award'] == 9)
											{
												// reset the powner count
												$sql_array8	= array(
													'powns_award'	=> 0,
												);
												$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
												$db->sql_query($sql);
												
												// update powener clan list
												$sql = "UPDATE " . CLANS_TABLE . " SET rth_powner = rth_powner + 1 WHERE group_id = {$match_winner}";
												$db->sql_query($sql);
												
												//assign extra points
												$powner	= 75;
											}
											else
											{
												// add new powner award to list
												$sql_array8	= array(
													'powns_award'	=> $row_er['powns_award'] + 1,
												);
												$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
												$db->sql_query($sql);
												
												//assign extra points
												$powner	= 0;
											}
										}
										else
										{
											$powner	= 0;
										}
										
										$price 	= $powner;
										$price2 = 0;
									}
									else
									{
										// Calculate the score using the ladder position. WE are in football ladder remember it!
										if ($w_score > $l_score) // winner > loser
										{
											$w_punti	= $w_score + (30 + ceil(($w_score - $l_score)/100) + ($winner_goals - $loser_goals));
											$l_punti	= $l_score - (30 + ($winner_goals - $loser_goals) + ceil(($w_score - $l_score)/100));
										}
										else if ($w_score < $l_score) // loser > winner
										{										
											$w_punti	= $w_score + (30 + ceil(($l_score - $w_score)/20) + ($winner_goals - $loser_goals));
											$l_punti	= $l_score - (30 + ($winner_goals - $loser_goals) + ceil(($l_score - $w_score)/20));
										}
										else // winner = loser
										{
											$w_punti	= $w_score + 30;
											$l_punti	= $l_score - 30;
										}
										
										// Check if the loser are the clan with best win/loss ration for extra points
										$sqlx		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder} ORDER BY group_ratio DESC";
										$resultx	= $db->sql_query_limit($sqlx, 1);
										$rowx		= $db->sql_fetchrow($resultx);
										$db->sql_freeresult($resultx);
									
										if ($match_loser == $rowx['group_id'] && $row_er['group_wins'] >= 8)
										{
											$price = 5;
										}
										else
										{
											$price = 0;
										}
									
										$price2 = ($row_ee['group_streak'] >= 4) ? 5 : 0;
									}
									
									// Update the winner
									$streak_w	= ($row_er['group_streak'] >= 0) ? $row_er['group_streak'] + 1 : 0;
									$w_ratio	= ($row_er['group_losses'] == 0) ? '1.00' : round(($row_er['group_wins'] + 1) / $row_er['group_losses'], 2);
									$sql_array1	= array(
										'group_score'			=> $w_punti + $price + $price2,
										'group_streak'			=> $streak_w,
										'group_lastscore'		=> $w_score,
										'group_wins'			=> $row_er['group_wins'] + 1,
										'group_goals_fatti'		=> $row_er['group_goals_fatti'] + $winner_goals,
										'group_goals_subiti'	=> $row_er['group_goals_subiti'] + $loser_goals,
										'group_ratio'			=> $w_ratio
									);
									$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
									$db->sql_query($sql);
									
									// AGGIORNO LOSER
									$streak_l	= ($row_ee['group_streak'] <= 0) ? $row_ee['group_streak'] - 1 : 0;
									$l_ratio	= ($row_ee['group_wins'] == 0) ? '1.00' : round($row_ee['group_wins'] / ($row_ee['group_losses'] + 1), 2);
									$sql_array2	= array(
										'group_score'			=> $l_punti,
										'group_streak'			=> $streak_l,
										'group_lastscore'		=> $l_score,
										'group_losses'			=> $row_ee['group_losses'] + 1,
										'group_goals_fatti'		=> $row_ee['group_goals_fatti'] + $loser_goals,
										'group_goals_subiti'	=> $row_ee['group_goals_subiti'] + $winner_goals,
										'group_ratio'			=> $l_ratio
									);
									$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = {$match_loser} AND group_ladder = {$ladder}";
									$db->sql_query($sql);
									
									// Now, update the ranks. Swap if needed.
									$xladder	= new ladder();
									$xladder->update_ranks($match_winner, $match_loser, $ladder);
								}
							break;
							case STANDARD_LADDER:
							case DECERTO_LADDER:
							case CPC_LADDER:
								// Get both group's data.
								$sql_2		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
								$result_2	= $db->sql_query_limit($sql_2, 1);
								$row_2		= $db->sql_fetchrow($result_2);
								$db->sql_freeresult($result_2);

								$sql_3		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$match_loser} AND group_ladder = {$ladder}";
								$result_3	= $db->sql_query_limit($sql_3, 1);
								$row_3		= $db->sql_fetchrow($result_3);
								$db->sql_freeresult($result_3);
								
								$winner_goals	= ($match_winner == $row['match_challenger']) ? $row['match_challanger_score'] : $row['match_challangee_score'];
								$loser_goals	= ($match_loser == $row['match_challenger']) ? $row['match_challanger_score'] : $row['match_challangee_score'];
								
								// RTH MOD
								if ($ladder_rank == 2)
								{
									// Calculate the score using the ladder position.
									if ($row_2['group_score'] > $row_3['group_score']) // winner > loser
									{
										$w_punti	= $row_2['group_score'] + ceil($row_3['group_score'] / 5); // win 20% of loser point
										$l_punti_t	= $row_3['group_score'] - ceil($row_3['group_score'] / 5);
									}
									else if ($row_2['group_score'] < $row_3['group_score']) // loser > winner
									{
										$w_punti	= $row_2['group_score'] + ceil($row_3['group_score'] / 2); // win 50% of loser point
										$l_punti_t	= $row_3['group_score'] - ceil($row_3['group_score'] / 2);
									}
									else // winner = loser
									{
										$w_punti	= $row_2['group_score'] + ceil($row_3['group_score'] * 35 / 100); //win 35% of loser point
										$l_punti_t	= $row_3['group_score'] - ceil($row_3['group_score'] * 35 / 100);
									}
										
									// do not allow point under 50.
									if ($l_punti_t < 50)
									{
										$l_punti = 50;
									}
									else
									{
										$l_punti = $l_punti_t;
									}
									
									// CHECK FOR POWNS! AWARDS, IN NOT FOOTBALL LADDER GIVE EXTRA POINT FOR THOSE WO WIN 3-0 INSTEAD 2-1
									if ($winner_goals == 3 && $loser_goals == 0)
									{
										if ($row_2['powns_award'] == 9)
										{
											// reset the powner count
											$sql_array8	= array(
												'powns_award'	=> 0,
											);
											$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
											$db->sql_query($sql);
											
											// update powener clan list
											$sql = "UPDATE " . CLANS_TABLE . " SET rth_powner = rth_powner + 1 WHERE group_id = {$match_winner}";
											$db->sql_query($sql);
											
											//assign extra points
											$powner	= 75;
										}
										else
										{
											// add new powner award to list
											$sql_array8	= array(
												'powns_award'	=> $row_2['powns_award'] + 1,
											);
											$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
											$db->sql_query($sql);
											
											//assign extra points
											$powner	= 0;
										}
									}
									else
									{
										$powner	= 0;
									}

									$w_score	= $w_punti + $powner;
									$l_score	= $l_punti;
									$price		= 0;
									$price2 	= 0;
									
								}
								else // for elo ladder
								{
									// CHECK if the loser is the clan with best win/loss ratio
									$sqlx		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder} ORDER BY group_ratio DESC";
									$resultx	= $db->sql_query_limit($sqlx, 1);
									$rowx		= $db->sql_fetchrow($resultx);
									$db->sql_freeresult($resultx);
								
									if ($match_loser == $rowx['group_id'] && $row_2['group_wins'] >= 8)
									{
										$price = 10;
									}
									else
									{
										$price = 0;
									}
								
									$price2 = ($row_3['group_streak'] >= 4) ? 5 : 0;

									// Calculate the new group stats for the winning group.
									// ELO scoring system.
									$w_score	= calculate_elo($row_2['group_score'], $row_3['group_score'], true);
									$l_score	= calculate_elo($row_3['group_score'], $row_2['group_score'], false);
								}
								// winner update
								$w_streak	= ($row_2['group_streak'] >= 0) ? $row_2['group_streak'] + 1 : 0;
								$w_ratio	= ($row_2['group_losses'] == 0) ? 100 : round(($row_2['group_wins'] + 1) / $row_2['group_losses'],2);
								$sql_array1	= array(
									'group_score'			=> $w_score + $price + $price2,
									'group_streak'			=> $w_streak,
									'group_lastscore'		=> $row_2['group_score'],
									'group_wins'			=> $row_2['group_wins'] + 1,
									'group_goals_fatti'		=> $row_2['group_goals_fatti'] + $winner_goals,
									'group_goals_subiti'	=> $row_2['group_goals_subiti'] + $loser_goals,
									'group_ratio'			=> $w_ratio
								);
								$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE group_id = {$match_winner} AND group_ladder = {$ladder}";
								$db->sql_query($sql);
								
								// loser update
								$l_streak	= ($row_3['group_streak'] <= 0) ? $row_3['group_streak'] - 1 : 0;
								$l_ratio	= ($row_3['group_wins'] == 0) ? 1 : round($row_3['group_wins'] / ($row_3['group_losses'] + 1),2);	
								$sql_array2	= array(
									'group_score'			=> $l_score,
									'group_streak'			=> $l_streak,
									'group_lastscore'		=> $row_3['group_score'],
									'group_losses'			=> $row_3['group_losses'] + 1,
									'group_goals_fatti'		=> $row_3['group_goals_fatti'] + $loser_goals,
									'group_goals_subiti'	=> $row_3['group_goals_subiti'] + $winner_goals,
									'group_ratio'			=> $l_ratio
								);
								$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = {$match_loser} AND group_ladder = {$ladder}";
								$db->sql_query($sql);


								// Get the match information (repopulate).
								$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $cvalue;
								$result	= $db->sql_query_limit($sql, 1);
								$row	= $db->sql_fetchrow($result);
								$db->sql_freeresult($result);

								// Now, update the ranks. Swap if needed.
								$ladderv = new ladder();
								$ladderv->update_ranks($row['match_winner'], $row['match_loser'], $row['match_ladder']);
							break;
						} /* end switch */
					} // chiusura if se classificata
					
			////	// Feedbacks
					$frep	= (int) request_var("vsrep_{$cvalue}", 5);
					$fieldK	= ($row['match_challenger'] == $group->data['group_id']) ? 'challangee_rep' : 'challanger_rep';
									
					$sql_array7	= array(
						"{$fieldK}" => $frep
					);
					$sql = "UPDATE " . MATCHES_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array7) . " WHERE match_id = {$cvalue}";
					$db->sql_query($sql);
					
					// make the new general reputation value for reporter * repopulate
					$sql_R		= "SELECT match_id, challanger_rep, challangee_rep FROM " . MATCHES_TABLE . " WHERE match_id = {$cvalue}";
					$result_R	= $db->sql_query_limit($sql_R, 1);
					$row_R		= $db->sql_fetchrow($result_R);
					$db->sql_freeresult($result_R);
					
					// load right value for each clan				
					$sql_array9	= array(
						'clan_rep_value'	=> $group->data('clan_rep_value', $row['match_challenger']) + $row_R['challanger_rep'], /* sum the reputation gived */
						'clan_rep_time'		=> $group->data('clan_rep_time', $row['match_challenger']) + 1
					);
					$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array9) . " WHERE group_id = " . $row['match_challenger'];
					$db->sql_query($sql);
					
					$sql_array10	= array(
						'clan_rep_value'	=> $group->data('clan_rep_value', $row['match_challengee']) + $row_R['challangee_rep'], /* sum the reputation gived */
						'clan_rep_time'		=> $group->data('clan_rep_time', $row['match_challengee']) + 1
					);
					$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array10) . " WHERE group_id = " . $row['match_challengee'];
					$db->sql_query($sql);
						
					// Send a PM to the winner to tell them that it was confirmed
					$destinatario	= $group->data('user_id', $reporter);
					$subject		= $user->lang['PMWINCONFIRMED'];
					$message		= sprintf($user->lang['PMWINCONFIRMEDTXT'], $group->data['group_name']);
					insert_pm($destinatario, $user->data, $subject, $message);
				}
			} /* end for each reported */
		}
		
		// CONTEST ACTION
		if (!empty($contested))
		{
			foreach ($contested AS $xvalue)
			{
				// Get the match information.
				$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $xvalue;
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their match.
				validate_opponents($row['match_challenger'], $row['match_challengee']);
				
				$sql_array	= array(
					'match_status'	=> 2
				);
				$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE match_id = " . $xvalue;
				$db->sql_query($sql);
			}
		}
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_confirm");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_REPORTED');
	} /* END SUBMIT */

/******************************************************************
*	TEMPLATE DEFINITION
*********************************/

	// Get all the unreported and reported matches.
	$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE (match_challengee = {$group->data['group_id']} OR match_challenger = {$group->data['group_id']})
			AND match_reported != 0 AND match_reported != {$group->data['group_id']} AND match_confirmed = 0";
	$result	= $db->sql_query($sql);
    $i		= 0;
		
	while ($row = $db->sql_fetchrow($result))
	{
		$other	= ($row['match_challenger'] == $group->data['group_id']) ? $row['match_challengee'] : $row['match_challenger'];
			
		// CARICO INFO LADDER
		$ladder_data	= $ladderv->get_roots($row['match_ladder']);
				
		// TIPI LADDER DEF
		switch ($ladder_data['SUBLADDER_STYLE'] == 3)
		{
			case FOOTBALL_LADDER;
				$tipos	= $user->lang['CALCIO_LADDER'];
			break;
			case CPC_LADDER;
				$tipos	= $user->lang['CPC_LADDER'];
			break;
			case DECERTO_LADDER;
				$tipos	= $user->lang['DECERTO_LADDER'];
			break;
			case STANDARD_LADDER;
				$tipos	= $user->lang['STANDARD_LADDER'];
			break;
		}
		
		$classificata	= ($row['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'];
		
		// STARDARD E CPC.
		$template->assign_block_vars('block_reported', array(
			'REPOTER_TXT'	=> sprintf($user->lang['REPORTED_TEXT'], '<a href="' . append_sid ("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_reported']) . '">', $group->data ('group_name', $row['match_reported']), '</a>', $user->format_date($row['match_posttime']), $classificata),
			'U_WINNER'		=> ($row['match_winner'] !== '9999999') ? '' : append_sid ("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_winner']),
			'ER_CLAN'		=> $group->data('group_name', $row['match_challenger']),
			'U_ER'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_challenger']),
			'EE_CLAN'		=> $group->data('group_name', $row['match_challengee']),
			'U_EE'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['match_challengee']),
			'WINNER'		=> ($row['match_winner'] !== '9999999') ? $group->data('group_name', $row['match_winner']) : $user->lang['PAREGGIO'],
			'MATCH_ID'		=> $row['match_id'],
			'MATCHDESC'		=> $row['match_details'],
			'MAP1'			=> $row['mappa_mode1'],
			'MAP2'			=> $row['mappa_mode2'],
			'MAP3'			=> $row['mappa_mode3'],
			'DECERTO'		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
			'MODE1'			=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode1'] : '',
			'MODE2'			=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode2'] : '',
			'MODE3'			=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode3'] : '',
			'CALCIO'		=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? true : false,
			'SCORE'			=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
			'ADVSTATS'      => ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? true : false,
			'ER_TEAM'		=> $row['challenger_team'],
			'EE_TEAM'		=> $row['challengee_team'],
			'TIPO'			=> $tipos,
			'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
			'LADDER'		=> $ladder_data['LADDER_NAME'],
			'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME'],
			'ER_SCORE'		=> $row['match_challanger_score'],
			'EE_SCORE'		=> $row['match_challangee_score'],
			'MODE1_ER_SCOR' => $row['match_challanger_score_mode1'],
			'MODE1_EE_SCOR' => $row['match_challangee_score_mode1'],
			'MODE2_ER_SCOR' => $row['match_challanger_score_mode2'],
			'MODE2_EE_SCOR' => $row['match_challangee_score_mode2'],
			'MODE3_ER_SCOR' => $row['match_challanger_score_mode3'],
			'MODE3_EE_SCOR' => $row['match_challangee_score_mode3'],
			'MVP'			=> ($ladder_data['SUBLADDER_MVP'] == 1) ? true : false,
			'MVP1'			=> ($row['mvp1'] == 0) ? $user->lang['NO_MVP'] : getusername($row['mvp1']),
			'MVP2'			=> ($row['mvp2'] == 0) ? $user->lang['NO_MVP'] : getusername($row['mvp2']),
			'MVP3'			=> ($row['mvp3'] == 0) ? $user->lang['NO_MVP'] : getusername($row['mvp3']),
			'MATCH_CHAT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;mid={$row['match_id']}&amp;lb=1"),
			'BG_COLOR'		=> ($i % 2) ? 'bg1' : 'bg2',
			'ROW_COLOR'		=> ($i % 2) ? 'row1' : 'row2'
		));
						
		// ADVANCED STATS /* yeah is a loop but is needed */
		if ($ladder_data['SUBLADDER_ADVSTAT'] == 1)
		{
			$sql_adv	= "SELECT * FROM " . MATCH_TEMP_USTATS . " AS adv LEFT JOIN " . USERS_TABLE . " AS u ON u.user_id = adv.user_id
						WHERE adv.id_match = {$row['match_id']} AND adv.id_ladder = {$row['match_ladder']} ORDER BY u.username ASC";
			$result_adv	= $db->sql_query($sql_adv);
			$ist	= 0;
			while ($row_adv = $db->sql_fetchrow($result_adv))
			{
				$template->assign_block_vars('block_reported.block_advstats', array(
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
	}
	$db->sql_freeresult($result);
	
	// Assign the other variables to the template.
	$template->assign_vars(array(
		'ADVSTATIMG'	=> getimg_button('advancedstat', 'ADVANC_STATS', 128, 25),
		'U_ACTION'		=> $u_action,
		// SAFEGT addon
		'S_SAFE_GT_TOPIC'	=> true
	));
}
?>