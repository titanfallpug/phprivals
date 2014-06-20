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
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * Manage Matches 1vs1
 * Called from ucp_rivals with mode == 'matches_oneone'
 */
function ucp_rivals_matches_oneone($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$ladder		= new ladder();
	$submit		= (!empty($_POST['submit'])) ? true : false;
	$report		= (!empty($_POST['riportamelo'])) ? true : false;
	$confirm	= (!empty($_POST['confermamelo'])) ? true : false;
	
	////////////////// TEMPLATE WAITING
	$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_challangee = {$user->data['user_id']} AND 1vs1_accepted = 0 ORDER BY start_time DESC";
	$result	= $db->sql_query($sql);
	$i		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$ladder_data	= $ladder->get_roots($row['1vs1_ladder']);
		
		// Assign each challenge to the template.
		$template->assign_block_vars('block_challenges', array(
			'U_CHALLENGER'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['1vs1_challanger']),
			'CHALLENGER'	=> getusername($row['1vs1_challanger']),
			'TIME' 			=> $user->format_date($row['start_time']),
			'DETAILS' 		=> (!empty($row['1vs1_details'])) ? nl2br($row['1vs1_details']) : $user->lang['NO_DETTAGLIO'],
			'CHALLENGE_ID' 	=> $row['1vs1_id'],
			'LADDER_ICON'	=> ($ladder_data['SUBLADDER_STYLE'] > 0) ? '<img src="' . $phpbb_root_path .'rivals/images/iconlad' . $ladder_data['SUBLADDER_STYLE'] . '.gif" alt="' . $user->lang['ICON_LADDER'] . '" title="' . $user->lang["{$ladder_data['SUBLADDER_STYLE']}ICON_LADDER"] . '" />' : '',
			'CLASSIFICATA' 	=> ($row['1vs1_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
			'PLATFORM' 		=> $ladder_data['PLATFORM_NAME'],
			'LADDER' 		=> $ladder_data['LADDER_NAME'],
			'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME'],
			'BG_COLOR' 		=> ($i % 2) ? 'bg1' : 'bg2',
			'ROW_COLOR' 	=> ($i % 2) ? 'row1' : 'row2')
		);
	$i++;
	}
	$db->sql_freeresult($result);
	
////////////////// TEMPLATE ACCEPTED
	$sql_ac		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
				AND 1vs1_accepted = 1 AND 1vs1_reporter = 0 AND 1vs1_confirmer = 0 ORDER BY start_time DESC";
	$result_ac	= $db->sql_query($sql_ac);
	$i_ac		= 0;
	while ($row_ac = $db->sql_fetchrow($result_ac))
	{
		$ladder_data_ac	= $ladder->get_roots($row_ac['1vs1_ladder']);
		
		// Assign each challenge to the template.
		$template->assign_block_vars('block_accepted', array(
			'U_CHALLENGER'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row_ac['1vs1_challanger']),
			'CHALLENGER'	=> getusername($row_ac['1vs1_challanger']),
			'U_CHALLENGEE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row_ac['1vs1_challangee']),
			'CHALLENGEE'	=> getusername($row_ac['1vs1_challangee']),
			'TIME' 			=> $user->format_date($row_ac['start_time']),
			'DETAILS' 		=> (!empty($row_ac['1vs1_details'])) ? nl2br($row_ac['1vs1_details']) : $user->lang['NO_DETTAGLIO'],
			'MATCH_ID' 		=> $row_ac['1vs1_id'],
			'CLASSIFICATA' 	=> ($row_ac['1vs1_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
			'PLATFORM' 		=> $ladder_data_ac['PLATFORM_NAME'],
			'LADDER' 		=> $ladder_data_ac['LADDER_NAME'],
			'SUBLADDER' 	=> $ladder_data_ac['SUBLADDER_NAME'],
			'WINBASED'		=> ($ladder_data_ac['SUBLADDER_WINSYS'] == 1) ? true : false,
			'DECERTO'		=> ($ladder_data_ac['SUBLADDER_STYLE'] == 1) ? true : false,
			'CPC'			=> ($ladder_data_ac['SUBLADDER_STYLE'] == 2) ? true : false,
			'CALCIO'		=> ($ladder_data_ac['SUBLADDER_STYLE'] == 3) ? true : false,
			'MAP1'			=> $row_ac['1vs1_mappa1'],
			'MAP2'			=> $row_ac['1vs1_mappa2'],
			'MAP3'			=> $row_ac['1vs1_mappa3'],
			'MODE1'			=> $row_ac['mode1'],
			'MODE2'			=> $row_ac['mode2'],
			'MODE3'			=> $row_ac['mode3'],
			'MATCH_CHAT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;uwar=1&amp;mid={$row_ac['1vs1_id']}&amp;lb=1"),
			'LADDER_ICON'	=> ($ladder_data_ac['SUBLADDER_STYLE'] > 0) ? '<img src="' . $phpbb_root_path .'rivals/images/iconlad' . $ladder_data_ac['SUBLADDER_STYLE'] . '.gif" alt="' . $user->lang['ICON_LADDER'] . '" title="' . $user->lang["{$ladder_data_ac['SUBLADDER_STYLE']}ICON_LADDER"] . '" />' : '',
			'BG_COLOR' 		=> ($i_ac % 2) ? 'bg1' : 'bg2',
			'ROW_COLOR' 	=> ($i_ac % 2) ? 'row1' : 'row2')
		);
	$i_ac++;
	}
	$db->sql_freeresult($result_ac);
	
////////////////// TEMPLATE REPORTED FOR CONFIRM
	$sql_cf		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
				AND 1vs1_accepted = 1 AND 1vs1_reporter != 0 AND 1vs1_reporter != {$user->data['user_id']} AND 1vs1_confirmer = 0 ORDER BY start_time DESC";
	$result_cf	= $db->sql_query($sql_cf);
	$i_cf		= 0;
	while ($row_cf = $db->sql_fetchrow($result_cf))
	{
		$ladder_data_cf	= $ladder->get_roots($row_cf['1vs1_ladder']);
		
		// Assign each challenge to the template.
		$template->assign_block_vars('block_reported', array(
			'U_CHALLENGER'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row_cf['1vs1_challanger']),
			'CHALLENGER'	=> getusername($row_cf['1vs1_challanger']),
			'U_CHALLENGEE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row_cf['1vs1_challangee']),
			'CHALLENGEE'	=> getusername($row_cf['1vs1_challangee']),
			'TIME' 			=> $user->format_date($row_cf['start_time']),
			'DETAILS' 		=> (!empty($row_cf['1vs1_details'])) ? nl2br($row_cf['1vs1_details']) : $user->lang['NO_DETTAGLIO'],
			'MATCH_ID' 		=> $row_cf['1vs1_id'],
			'CLASSIFICATA' 	=> ($row_cf['1vs1_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
			'PLATFORM' 		=> $ladder_data_cf['PLATFORM_NAME'],
			'LADDER' 		=> $ladder_data_cf['LADDER_NAME'],
			'SUBLADDER' 	=> $ladder_data_cf['SUBLADDER_NAME'],
			'SCORE'			=> ($ladder_data_cf['SUBLADDER_WINSYS'] == 0) ? true : false,
			'DECERTO'		=> ($ladder_data_cf['SUBLADDER_STYLE'] == 1) ? true : false,
			'CPC'			=> ($ladder_data_cf['SUBLADDER_STYLE'] == 2) ? true : false,
			'CALCIO'		=> ($ladder_data_cf['SUBLADDER_STYLE'] == 3) ? true : false,
			'MAP1'			=> $row_cf['1vs1_mappa1'],
			'MAP2'			=> $row_cf['1vs1_mappa2'],
			'MAP3'			=> $row_cf['1vs1_mappa3'],
			'MODE1'			=> $row_cf['mode1'],
			'MODE2'			=> $row_cf['mode2'],
			'MODE3'			=> $row_cf['mode3'],
			'WINNER'		=> getusername($row_cf['1vs1_winner']),
			'U_WINNER'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row_cf['1vs1_winner']),
			'ER_SCORE'		=> $row_cf['1vs1_challanger_score'],
			'EE_SCORE'		=> $row_cf['1vs1_challangee_score'],
			'MODE1_ER_SCOR'	=> $row_cf['mode1_score_er'],
			'MODE2_ER_SCOR'	=> $row_cf['mode2_score_er'],
			'MODE3_ER_SCOR'	=> $row_cf['mode3_score_er'],
			'MODE1_EE_SCOR'	=> $row_cf['mode1_score_ee'],
			'MODE2_EE_SCOR'	=> $row_cf['mode2_score_ee'],
			'MODE3_EE_SCOR'	=> $row_cf['mode3_score_ee'],
			'ER_TEAM'		=> $row_cf['1vs1_challanger_team'],
			'EE_TEAM'		=> $row_cf['1vs1_challangee_team'],
			'MATCH_CHAT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;uwar=1&amp;mid={$row_cf['1vs1_id']}&amp;lb=1"),
			'LADDER_ICON'	=> ($ladder_data_cf['SUBLADDER_STYLE'] > 0) ? '<img src="' . $phpbb_root_path .'rivals/images/iconlad' . $ladder_data_cf['SUBLADDER_STYLE'] . '.gif" alt="' . $user->lang['ICON_LADDER'] . '" title="' . $user->lang["{$ladder_data_cf['SUBLADDER_STYLE']}ICON_LADDER"] . '" />' : '',
			'BG_COLOR' 		=> ($i_cf % 2) ? 'bg1' : 'bg2',
			'ROW_COLOR' 	=> ($i_cf % 2) ? 'row1' : 'row2')
		);
	$i_cf++;
	}
	$db->sql_freeresult($result_cf);


/****************************************
*      ACCEPT
************************/
	if($submit)
	{
		$accept		= request_var('accept', array(0 => 0));
		$decline	= request_var('decline', array(0 => 0));
		nodouble_check($accept, $decline, 'i=rivals&amp;mode=matches_oneone');
		
		if (empty($accept) && empty($decline))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		if (!empty($accept))
		{
			foreach ($accept AS $value)
			{
				$matchid	= (int) $value;
				// Get the challenge detials.
				$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $matchid;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their match.
				validate_opponents($row['1vs1_challanger'], $row['1vs1_challangee'], true);
				
				$getladder	= $ladder->get_roots($row['1vs1_ladder']);
				$nome_corto = $getladder['SUBLADDER_SHORTNM'];
				$tipoladder = $getladder['SUBLADDER_STYLE'];
				$tiporank	= $getladder['SUBLADDER_RAKING'];
				
				if ($tipoladder == 1)
				{
					// modi
					$sql2	= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = {$tipoladder} AND nome_corto = '{$nome_corto}' ORDER BY RAND()";
					$result2	= $db->sql_query_limit($sql2, 1);
					$row2	= $db->sql_fetchrow($result2);
					$inter1 = $row2['decerto_interid'];
					$mode1	= $row2['decerto_mode'];
					$db->sql_freeresult($result2);
						
					$sql3	= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = {$tipoladder} AND nome_corto = '{$nome_corto}' AND decerto_interid != {$inter1} ORDER BY RAND()";
					$result3	= $db->sql_query_limit($sql3, 1);
					$row3	= $db->sql_fetchrow($result3);
					$inter2 = $row3['decerto_interid'];
					$mode2	= $row3['decerto_mode'];
					$db->sql_freeresult($result3);
						
					$sql4	= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = {$tipoladder} AND nome_corto = '{$nome_corto}' AND decerto_interid != {$inter1} AND decerto_interid != {$inter2} ORDER BY RAND()";
					$result4	= $db->sql_query_limit($sql4, 1);
					$row4	= $db->sql_fetchrow($result4);
					$mode3	= $row4['decerto_mode'];
					$db->sql_freeresult($result4);
						
					// mappa 1
					$sql_a	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 ORDER BY RAND()";
					$result_a	= $db->sql_query_limit($sql_a, 1);
					$row_a	= $db->sql_fetchrow($result_a);
					$mappa1 = $row_a['decerto_mappa']; //////////////
					$db->sql_freeresult($result_a);
					// mappa 2
					$sql_b	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 2 ORDER BY RAND()";
					$result_b	= $db->sql_query_limit($sql_b, 1);
					$row_b	= $db->sql_fetchrow($result_b);
					$mappa2 = $row_b['decerto_mappa']; /////////////////
					$db->sql_freeresult($result_b);
					// mappa 3
					$sql_c	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 3 ORDER BY RAND()";
					$result_c	= $db->sql_query_limit($sql_c, 1);
					$row_c	= $db->sql_fetchrow($result_c);
					$mappa3 = $row_c['decerto_mappa']; ///////////////
					$db->sql_freeresult($result_c);	
				}
				else if ($tipoladder == 2)
				{
					// mappa 1
					$sql_a	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 ORDER BY RAND()";
					$result_a	= $db->sql_query_limit($sql_a, 1);
					$row_a	= $db->sql_fetchrow($result_a);
					$mappa1 = $row_a['decerto_mappa']; //////////////
					$db->sql_freeresult($result_a);
					// mappa 2
					$sql_b	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 2 ORDER BY RAND()";
					$result_b	= $db->sql_query_limit($sql_b, 1);
					$row_b	= $db->sql_fetchrow($result_b);
					$mappa2 = $row_b['decerto_mappa']; /////////////////
					$db->sql_freeresult($result_b);
					// mappa 3
					$sql_c	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 3 ORDER BY RAND()";
					$result_c	= $db->sql_query_limit($sql_c, 1);
					$row_c	= $db->sql_fetchrow($result_c);
					$mappa3 = $row_c['decerto_mappa']; ///////////////
					$db->sql_freeresult($result_c);
						
					$mode1 = "-";
					$mode2 = "-";
					$mode3 = "-";
				} 
				else
				{
					$mappa1 = "-";
					$mappa2 = "-";
					$mappa3 = "-";
					$mode1	= "-";
					$mode2	= "-";
					$mode3	= "-";
				}
				
				// CHECK WAR RIPETUTE
				$superdata	= time();
				$mindata	= ($superdata - 259200);
				 
				$sql_rip	= "SELECT COUNT(1vs1_id) AS checkers FROM " . ONEVSONE_MATCH_DATA . " WHERE (start_time BETWEEN {$mindata} AND {$superdata}) AND (1vs1_challanger = " . $row['1vs1_challanger'] ." AND 1vs1_challangee = " . $row['1vs1_challangee'] .")
							OR (1vs1_challanger = " . $row['1vs1_challangee'] ." AND 1vs1_challangee = " . $row['1vs1_challanger'] .") ";
				$result_rip	= $db->sql_query($sql_rip);
				$row_rip	= $db->sql_fetchrow($result_rip);
				$sborro		= $row_rip['checkers'];
				$db->sql_freeresult($result_rip);
				 
				if ($sborro > 3)
				{ 
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
					meta_refresh(6, $redirect_url);
					trigger_error('SFIDATO_TROPPO');
				}
				
				// Do not allow up to 6 war in any case
				$sql_rip	= "SELECT COUNT(1vs1_id) AS checkers1 FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = " . $row['1vs1_challanger'] ." AND 1vs1_challangee = " . $row['1vs1_challangee'] .")
							OR (1vs1_challanger = " . $row['1vs1_challangee'] ." AND 1vs1_challangee = " . $row['1vs1_challanger'] .")";
				$result_rip	= $db->sql_query($sql_rip);
				$row_rip	= $db->sql_fetchrow($result_rip);
				$sborro1	= $row_rip['checkers1'];
				$db->sql_freeresult($result_rip);
				
				if ($sborro1 > 6)
				{ 
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
					meta_refresh(6, $redirect_url);
					trigger_error('SFIDATO_TROPPO');
				}
				
				// Finally edit the match.
				$sql_array	= array(
					'1vs1_challangee_ip'	=> (!empty($user->data['user_ip'])) ? $user->data['user_ip'] : $_SERVER['REMOTE_ADDR'],
					'1vs1_mappa1' 			=> $mappa1,
					'1vs1_mappa2' 			=> $mappa2,
					'1vs1_mappa3' 			=> $mappa3,
					'mode1' 				=> $mode1,
					'mode2' 				=> $mode2,
					'mode3' 				=> $mode3,
					'1vs1_accepted' 		=> 1
				);
				$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_id = " . $matchid;
				$db->sql_query($sql);
				
				// Delete clan entries if are on rth chicken risk list
				if ($tiporank == 2)
				{
					$sql	= "DELETE FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$row['1vs1_challanger']} AND oneone = 1 AND ladder_id = " . $row['1vs1_ladder'];
					$db->sql_query($sql);
				}
				
				// Reset hibernated status
				$sql_array5	= array(
					'user_frosted'	=> 0,
					'frosted_time'	=> 0,
				);
				$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array5) . " WHERE user_id = {$row['1vs1_challangee']} AND 1vs1_ladder = {$row['1vs1_ladder']}";
				$db->sql_query($sql);

				// Send a PM to the challenger's group leader telling them it was accepted.
				$subject	= $user->lang['PM_CHALLENGEACCEPTED'];
				$message	= sprintf ($user->lang['PM_CHALLENGEACCEPTEDTXT'], $user->data['username']);
				insert_pm($row['1vs1_challanger'], $user->data, $subject, $message);
			}
		}
		
		if (!empty($decline))
		{
			foreach ($decline AS $value)
			{
				$matchid	= (int) $value;
				// Get the challenge details.
				$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $matchid;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their match.
				validate_opponents($row['1vs1_challanger'], $row['1vs1_challangee'], true);
				
				$getladder	= $ladder->get_roots($row['1vs1_ladder']);
				
				// ADDON FOR RTH
				// we must count the number of declination		
				if ($getladder['SUBLADDER_RAKING'] == 2 && user_frosted($row['1vs1_challangee'], $row['1vs1_ladder']) == 0)
				{
					$sql_c		= "SELECT * FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$user->data['user_id']} AND oneone = 1 AND ladder_id = " . $row['1vs1_ladder'];
					$result_c	= $db->sql_query($sql_c);
					$row_c		= $db->sql_fetchrow($result_c);
					$db->sql_freeresult($result_c);
					
					if (!empty($row_c['group_id']))
					// Your clan just have done a decline so i set +1 chicken to it
					{
						$sql = "UPDATE " . USERS_TABLE . " SET user_chicken = user_chicken + 1 WHERE user_id = {$row_c['group_id']}";
						$db->sql_query($sql);
								
					// remove 25% of clan points
						$sql_v		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$user->data['user_id']} AND 1vs1_ladder = " . $row['1vs1_ladder'];
						$result_v	= $db->sql_query($sql_v);
						$row_v		= $db->sql_fetchrow($result_v);
						$db->sql_freeresult($result_v);
						
						if ($row_v['user_score'] >= 200)
						{
							$nuovopunteggio = ceil($row_v['user_score'] / 4);
						}
						else
						{
							$nuovopunteggio = 50;
						}
								
						$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET user_score = {$nuovopunteggio} WHERE user_id = {$user->data['user_id']} AND 1vs1_ladder = " . $row['1vs1_ladder'];
						$db->sql_query($sql);
						
						// now clean datas stored
						$sql	= "DELETE FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$user->data['user_id']} AND oneone = 1 AND ladder_id = " . $row['1vs1_ladder'];
						$db->sql_query($sql);
					}
					else
					{
						// this clan dont have declined chain so add it for future combo
						$sql_array	= array(
							'group_id' 	=> $user->data['user_id'],
							'ladder_id'	=> $row['1vs1_ladder'],
							'oneone'	=> 1
						);
						$sql		= "INSERT INTO " . RTH_CHECK_TABLE . " " . $db->sql_build_array ('INSERT', $sql_array);
						$db->sql_query($sql);
					}
				}
				// Send a PM to the challenger's group leader and to the logged in group.
				$subject	= $user->lang['PM_CHALLENGEDECLINED'];
				$message	= sprintf ($user->lang['PM_CHALLENGEDECLINEDTXT'], $user->data['username']);
				insert_pm($row['1vs1_challanger'], $user->data, $subject, $message);
				
				// Decline the challenge. Delete it.
				$sql	= "DELETE FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $matchid;
				$db->sql_query ($sql);
			}
		}
		
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
		meta_refresh(2, $redirect_url);
		trigger_error('CHALLENGES_UPDATED');
	}

/********************************************************
*      REPORT
***********************/
	if($report)
	{
		$matchRef	= isset($_POST['match']) ? $_POST['match'] : array();	
		$matchid	= 0;
		$reporter	= $user->data['user_id'];
		
		if (empty($matchRef))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		// Menage match reported data
		foreach ($matchRef as $matchid => $values)
		{			
			if (isset($values['matchrep']) && $values['matchrep'] == 1) /* go on only for checked matches */
			{
				$ERteam		= (string) (!empty($values['er_team_used'])) ? $values['er_team_used'] : '';
				$EEteam		= (string) (!empty($values['ee_team_used'])) ? $values['ee_team_used'] : '';
				
				$winnerclan	= (int) (!empty($values['winner_clan'])) ? $values['winner_clan'] : 0;
				$feedback	= (int) (!empty($values['feedback'])) ? $values['feedback'] : 5;
				
				// Get the challenge detials.
				$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $matchid;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their match.
				validate_opponents($row['1vs1_challanger'], $row['1vs1_challangee'], true);
				
				// Get ladder infos
				$getladder		= $ladder->get_roots($row['1vs1_ladder']);
				$tipo_ladder	= $getladder['SUBLADDER_STYLE'];
				$ladder_mvp		= $getladder['SUBLADDER_MVP'];
				$ladder_advst	= $getladder['SUBLADDER_ADVSTAT'];
				
				$other			= ($row['1vs1_challanger'] == $user->data['user_id']) ? $row['1vs1_challangee'] : $row['1vs1_challanger'];
				$rightfeedfield	= ($row['1vs1_challanger'] == $user->data['user_id']) ? 'ee_feedback' : 'er_feedback';
				
				//result sended by score
				if ($winnerclan == 0)
				{
					// challanger
					$ERpoint	= (int) (!empty($values['er_point'])) ? $values['er_point'] : 0;
					$ERmode1	= (int) (!empty($values['er_mode1_score'])) ? $values['er_mode1_score'] : 0; /* decerto if need */
					$ERmode2	= (int) (!empty($values['er_mode2_score'])) ? $values['er_mode2_score'] : 0;
					$ERmode3	= (int) (!empty($values['er_mode3_score'])) ? $values['er_mode3_score'] : 0;
					
					// challangee
					$EEpoint	= (int) (!empty($values['ee_point'])) ? $values['ee_point'] : 0;
					$EEmode1	= (int) (!empty($values['ee_mode1_score'])) ? $values['ee_mode1_score'] : 0; /* decerto if need */
					$EEmode2	= (int) (!empty($values['ee_mode2_score'])) ? $values['ee_mode2_score'] : 0;
					$EEmode3	= (int) (!empty($values['ee_mode3_score'])) ? $values['ee_mode3_score'] : 0;
					
					// validate integer score
					if (!is_numeric($ERpoint)
						|| !is_numeric($EEpoint)
						|| !is_numeric($ERmode1)
						|| !is_numeric($EEmode1)
						|| !is_numeric($ERmode2)
						|| !is_numeric($EEmode2)
						|| !is_numeric($ERmode3)
						|| !is_numeric($EEmode3)
						)
					{
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
						meta_refresh(4, $redirect_url);
						trigger_error(sprintf($user->lang['RISULTATO_NO_SCORE'], '<a href="' . $redirect_url . '">', '</a>'));
					}
					
					// calculate the winner
					if ($ERpoint > $EEpoint)
					{
						$vincitore	= $row['1vs1_challanger'];
					}
					else if ($ERpoint < $EEpoint)
					{
						$vincitore	= $row['1vs1_challangee'];
					}
					else if ($ERpoint == $EEpoint)
					{
						if ($tipo_ladder == FOOTBALL_LADDER) // only football ladder can have draw result
						{
							$vincitore	= '9999999';
						}
						else
						{
							$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
							meta_refresh(4, $redirect_url);
							trigger_error(sprintf($user->lang['LADDER_NOT_ALLOW_DRAW'], '<a href="' . $redirect_url . '">', '</a>'));
						}
					}
					
					// updathe the match
					$sql_array	= array(
						'1vs1_challanger_score'	=> $ERpoint,
						'1vs1_challangee_score'	=> $EEpoint,
						'1vs1_winner'			=> $vincitore,
						'mode1_score_er'		=> $ERmode1,
						'mode2_score_er'		=> $ERmode2,
						'mode3_score_er'		=> $ERmode3,
						'mode1_score_ee'		=> $EEmode1,
						'mode2_score_ee'		=> $EEmode2,
						'mode3_score_ee'		=> $EEmode3,
						'1vs1_challanger_team'	=> $ERteam,
						'1vs1_challangee_team'	=> $EEteam,
						'1vs1_reporter'			=> $user->data['user_id'],
						"{$rightfeedfield}"		=> (!empty($feedback)) ? $feedback : 5,
						'rep_time'				=> time(),
					);
					$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_id = " . $matchid;
					$db->sql_query($sql);
				}
				else
				{
					$vincitore	= $winnerclan;
					
					$sql_array	= array(
						'1vs1_challanger_score'	=> 0,
						'1vs1_challangee_score'	=> 0,
						'1vs1_winner'			=> $vincitore,
						'mode1_score_er'		=> 0,
						'mode2_score_er'		=> 0,
						'mode3_score_er'		=> 0,
						'mode1_score_ee'		=> 0,
						'mode2_score_ee'		=> 0,
						'mode3_score_ee'		=> 0,
						'1vs1_challanger_team'	=> '',
						'1vs1_challangee_team'	=> '',
						'1vs1_reporter'			=> $user->data['user_id'],
						'rep_time'				=> time(),
					);
					$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_id = " . $matchid;
					$db->sql_query($sql);
				}
				
				// Send a PM to the loser to tell them to confirm the win...
				$subject	= $user->lang['PMCONFIRMWIN'];
				$message	= sprintf($user->lang['PMCONFIRMWINTXT'], getusername($user->data['user_id']));
				insert_pm($other, $user->data, $subject, $message);
			}
		}
		// finish the report
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_REPORTED');
	}
	
/********************************************************************
* CONFIRM OR CONTEST ACTION
********************************/
	if($confirm)
	{	
		$confirmed	= request_var('confirmed', array(0 => 0));
		$contested	= request_var('contested', array(0 => 0));
		nodouble_check($confirmed, $contested, 'i=rivals&amp;mode=matches_oneone');
		
		if (empty($confirmed) && empty($contested))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		// confirm action
		if (!empty($confirmed))
		{
			foreach ($confirmed AS $match_id)
			{
				// Get the match information.
				$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $match_id;
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				if ($row['1vs1_confirmer'] == 0) /* go on only for unconfirmed matches */
				{
					// Confirm that this is their match.
					validate_opponents($row['1vs1_challanger'], $row['1vs1_challangee'], true);
					
					// Get ladder information.
					$getladder	= $ladder->get_roots($row['1vs1_ladder']);
					
					$reporter		= $row['1vs1_reporter'];
					$challanger 	= $row['1vs1_challanger'];
					$challangee 	= $row['1vs1_challangee'];
					$winner			= $row['1vs1_winner'];
					$loser			= ($winner == $challanger) ? $challangee : $challanger; /* with a draw result aka 999... the loser will be always the challanger but the winner is 9999 remember */
					$ladderid		= $row['1vs1_ladder'];
					$ladder_rank	= $getladder['SUBLADDER_RAKING'];
					$winner_goals	= ($winner == $challanger) ? $row['1vs1_challanger_score'] : $row['1vs1_challangee_score'];
					$loser_goals	= ($loser == $challanger) ? $row['1vs1_challanger_score'] : $row['1vs1_challangee_score'];
					
					
					// only if the match are ranked
					if ($row['1vs1_unranked'] == 0)
					{
						// FOOTBALL
						if ($getladder['SUBLADDER_STYLE'] == FOOTBALL_LADDER)
						{
							// IF we have a draw result i use er and ee
							if ($winner == '9999999')
							{
								// ER score.
								$sql_er		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$challanger} AND 1vs1_ladder = {$ladderid}";
								$result_er	= $db->sql_query_limit($sql_er, 1);
								$row_er		= $db->sql_fetchrow($result_er);
								$er_score	= $row_er['user_score'];
								$db->sql_freeresult($result_er);
						
								// EE score.
								$sql_ee		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$challangee} AND 1vs1_ladder = {$ladderid}";
								$result_ee	= $db->sql_query_limit($sql_ee, 1);
								$row_ee		= $db->sql_fetchrow($result_ee);
								$ee_score	= $row_ee['user_score'];
								$db->sql_freeresult($result_ee);
								
								// RTH MOD
								if ($ladder_rank == 2)
								{
									$er_punti	= $er_score + 10;
									$ee_punti	= $ee_score + 10;
								}
								else
								{
									// Calculate score by challangers position on ladder. 
									if ($er_score > $ee_score)
									{
										$er_punti	= $er_score + (10 + ceil(($er_score - $ee_score)/100));
										$ee_punti	= $ee_score + (10 + ceil(($er_score - $ee_score)/50));
									}
									else if ($er_score < $ee_score)
									{
										$er_punti	= $er_score + (10 + ceil(($ee_score - $er_score)/50));
										$ee_punti	= $ee_score + (10 + ceil(($ee_score - $er_score)/100));
									}
									else // if ER = EE
									{
										$er_punti	= $er_score + 10;
										$ee_punti	= $ee_score + 10;
									}
								}
							
								// Update ER
								$sql_array1	= array(
									'user_score'		=> $er_punti,
									'user_streak'		=> 0,
									'user_lastscore'	=> $er_score,
									'user_pari'			=> $row_er['user_pari'] + 1,
									'user_goals_fatti'	=> $row_er['user_goals_fatti'] + $row['1vs1_challanger_score'],
									'user_goals_subiti'	=> $row_er['user_goals_subiti'] + $row['1vs1_challangee_score']
								);
								$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE user_id = {$challanger} AND 1vs1_ladder = {$ladderid}";
								$db->sql_query($sql);
							
								// Update EE
								$sql_array2	= array(
									'user_score'		=> $ee_punti,
									'user_streak'		=> 0,
									'user_lastscore'	=> $ee_score,
									'user_pari'			=> $row_ee['user_pari'] + 1,
									'user_goals_fatti'	=> $row_ee['user_goals_fatti'] + $row['1vs1_challangee_score'],
									'user_goals_subiti'	=> $row_ee['user_goals_subiti'] + $row['1vs1_challanger_score']
								);
								$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE user_id = {$challangee} AND 1vs1_ladder = {$ladderid}";
								$db->sql_query($sql);
							
								// Now, update the ranks. Swap if needed.
								$xladder	= new ladder();
								$xladder->update_ranks_user($challanger, $challangee, $ladderid);
							}
							else /* we have a winner */
							{
								// Winner score.
								$sql_er		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
								$result_er	= $db->sql_query_limit($sql_er, 1);
								$row_er		= $db->sql_fetchrow($result_er);
								$w_score	= $row_er['user_score'];
								$db->sql_freeresult($result_er);
						
								// Loser score.
								$sql_ee		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$loser} AND 1vs1_ladder = {$ladderid}";
								$result_ee	= $db->sql_query_limit($sql_ee, 1);
								$row_ee		= $db->sql_fetchrow($result_ee);
								$l_score	= $row_ee['user_score'];
								$db->sql_freeresult($result_ee);
								
								// RTH MOD
								if ($ladder_rank == 2)
								{
									// Calculate score by challangers position on ladder. 
									if ($w_score > $l_score)
									{
										$w_punti	= $w_score + ceil($l_score / 5); // winner got 20% of loser points
										$l_punti_t	= $l_score - ceil($l_score / 5);
									}
									else if ($w_score < $l_score)
									{
										$w_punti	= $w_score + ceil($l_score / 2); // winner got 50% of loser points
										$l_punti_t	= $l_score - ceil($l_score / 2);
									}
									else // ER = EE
									{
										$w_punti	= $w_score + ceil($l_score * 35 / 100); // winner got 35% of loser pints
										$l_punti_t	= $l_score - ceil($l_score * 35 / 100);
									}
								
									// do allow score under 50.
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
											$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
											$db->sql_query($sql);
										
											// update powener clan list
											$sql = "UPDATE " . USERS_TABLE . " SET user_powns = user_powns + 1 WHERE user_id = {$winner}";
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
											$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
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
								else /* ELO ladder */
								{
									// Calculate score by challangers position on ladder. 
									if ($w_score > $l_score)
									{
										$w_punti	= $w_score + (30 + ceil(($w_score - $l_score)/100) + ($winner_goals - $loser_goals));
										$l_punti	= $l_score - (30 + ($winner_goals - $loser_goals) + ceil(($w_score - $l_score)/100));
									}
									else if ($w_score < $l_score)
									{
										$w_punti	= $w_score + (30 + ceil(($l_score - $w_score)/20) + ($winner_goals - $loser_goals));
										$l_punti	= $l_score - (30 + ($winner_goals - $loser_goals) + ceil(($l_score - $w_score)/20));
									}
									else // ER = EE
									{
										$w_punti	= $w_score + 30;
										$l_punti	= $l_score - 30;
									}
							
									// Check if the loser is the clan with best ratio
									$sqlx		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladderid} ORDER BY user_ratio DESC";
									$resultx	= $db->sql_query_limit($sqlx, 1);
									$rowx		= $db->sql_fetchrow($resultx);
									$db->sql_freeresult($resultx);
						
									if ($loser == $rowx['user_id'] && $row_er['user_wins'] >= 8)
									{
										$price = 5;
									}
									else
									{
										$price = 0;
									}
							
									$price2 = ($row_ee['user_streak'] >= 4) ? 5 : 0;
								}
							
								// update winner
								$streak_w	= ($row_er['user_streak'] >= 0) ? $row_er['user_streak'] + 1 : 0;
								$w_ratio	= ($row_er['user_losses'] == 0) ? '1.00' : round(($row_er['user_wins'] + 1) / $row_er['user_losses'], 2);
								$sql_array1	= array(
									'user_score'			=> $w_punti + $price + $price2,
									'user_streak'			=> $streak_w,
									'user_lastscore'		=> $w_score,
									'user_wins'				=> $row_er['user_wins'] + 1,
									'user_goals_fatti'		=> $row_er['user_goals_fatti'] + $winner_goals,
									'user_goals_subiti'		=> $row_er['user_goals_subiti'] + $loser_goals,
									'user_ratio'			=> $w_ratio
								);
								$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
								$db->sql_query($sql);
							
								// update loser
								$streak_l	= ($row_ee['user_streak'] <= 0) ? $row_ee['user_streak'] - 1 : 0;
								$l_ratio	= ($row_ee['user_wins'] == 0) ? 1 : round($row_ee['user_wins'] / ($row_ee['user_losses'] + 1), 2);
								$sql_array2	= array(
									'user_score'			=> $l_punti,
									'user_streak'			=> $streak_l,
									'user_lastscore'		=> $l_score,
									'user_losses'			=> $row_ee['user_losses'] + 1,
									'user_goals_fatti'		=> $row_ee['user_goals_fatti'] + $loser_goals,
									'user_goals_subiti'		=> $row_ee['user_goals_subiti'] + $winner_goals,
									'user_ratio'			=> $l_ratio
								);
								$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE user_id = {$loser} AND 1vs1_ladder = {$ladderid}";
								$db->sql_query($sql);
							
								// Now, update the ranks. Swap if needed.
								$xladder	= new ladder();
								$xladder->update_ranks_user($winner, $loser, $ladderid);
							}
						}
						// for all other ladder(aka not football)
						else
						{
							// Get both group's data.
							$sql_2		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
							$result_2	= $db->sql_query_limit($sql_2, 1);
							$row_2		= $db->sql_fetchrow($result_2);
							$db->sql_freeresult($result_2);

							$sql_3		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$loser} AND 1vs1_ladder = {$ladderid}";
							$result_3	= $db->sql_query_limit($sql_3, 1);
							$row_3		= $db->sql_fetchrow($result_3);
							$db->sql_freeresult($result_3);
							
							// RTH MOD
							if ($ladder_rank == 2)
							{
								// Calculate score by challangers position on ladder.
								if ($row_2['user_score'] > $row_3['user_score'])
								{
									$w_punti	= $row_2['user_score'] + ceil($row_3['user_score'] / 5);
									$l_punti_t	= $row_3['user_score'] - ceil($row_3['user_score'] / 5);
								}
								else if ($row_2['user_score'] < $row_3['user_score'])
								{
									$w_punti	= $row_2['user_score'] + ceil($row_3['user_score'] / 2);
									$l_punti_t	= $row_3['user_score'] - ceil($row_3['user_score'] / 2);
								}
								else // ER = EE
								{
									$w_punti	= $row_2['user_score'] + ceil($row_3['user_score'] * 35 / 100);
									$l_punti_t	= $row_3['user_score'] - ceil($row_3['user_score'] * 35 / 100);
								}
								
								// do not allow score under 50.
								if ($l_punti_t < 50)
								{
									$l_punti = 50;
								}
								else
								{
									$l_punti = $l_punti_t;
								}
							
								// CHECK FOR POWNS! AWARDS
								if ($winner_goals == 3 && $loser_goals == 0)
								{
									if ($row_2['powns_award'] == 9)
									{
										// reset the powner count
										$sql_array8	= array(
											'powns_award'	=> 0,
										);
										$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
										$db->sql_query($sql);
									
										// update powener clan list
										$sql = "UPDATE " . USERS_TABLE . " SET user_powns = user_powns + 1 WHERE user_id = {$winner}";
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
										$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array8) . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
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
							else /* if not RTH */
							{
								// Check if the loser was the clan with best w/l ratio
								$sqlx		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladderid} ORDER BY user_ratio DESC";
								$resultx	= $db->sql_query_limit($sqlx, 1);
								$rowx		= $db->sql_fetchrow($resultx);
								$db->sql_freeresult($resultx);
							
								if ($loser == $rowx['user_id'] && $row_2['user_wins'] >= 8)
								{
									$price = 10;
								}
								else
								{
									$price = 0;
								}
						
								$price2 = ($row_3['user_streak'] >= 4) ? 5 : 0;

								// Calculate the new group stats for the winning group.
								// ELO scoring system.
								$w_score	= calculate_elo($row_2['user_score'], $row_3['user_score'], true);
								$l_score	= calculate_elo($row_3['user_score'], $row_2['user_score'], false);
							}
							
							// Update winner
							$w_streak	= ($row_2['user_streak'] >= 0) ? $row_2['user_streak'] + 1 : 0;
							$w_ratio	= ($row_2['user_losses'] == 0) ? '1.00' : round(($row_2['user_wins'] + 1) / $row_2['user_losses'],2);
							$sql_array1	= array(
								'user_score'			=> $w_score + $price + $price2,
								'user_streak'			=> $w_streak,
								'user_lastscore'		=> $row_2['user_score'],
								'user_wins'				=> $row_2['user_wins'] + 1,
								'user_goals_fatti'		=> $row_2['user_goals_fatti'] + $winner_goals,
								'user_goals_subiti'		=> $row_2['user_goals_subiti'] + $loser_goals,
								'user_ratio'			=> $w_ratio
							);
							$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE user_id = {$winner} AND 1vs1_ladder = {$ladderid}";
							$db->sql_query($sql);
						
							// Update loser
							$l_streak	= ($row_3['user_streak'] <= 0) ? $row_3['user_streak'] - 1 : 0;
							$l_ratio	= ($row_3['user_losses'] == 0) ? 1 : round($row_3['user_wins'] / ($row_3['user_losses'] + 1),2);	
							$sql_array2	= array(
								'user_score'			=> $l_score,
								'user_streak'			=> $l_streak,
								'user_lastscore'		=> $row_3['user_score'],
								'user_losses'			=> $row_3['user_losses'] + 1,
								'user_goals_fatti'		=> $row_3['user_goals_fatti'] + $loser_goals,
								'user_goals_subiti'		=> $row_3['user_goals_subiti'] + $winner_goals,
								'user_ratio'			=> $l_ratio
							);
							$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE user_id = {$loser} AND 1vs1_ladder = {$ladderid}";
							$db->sql_query($sql);


							// Get the match information (repopulate).
							$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $match_id;
							$result	= $db->sql_query_limit($sql, 1);
							$row	= $db->sql_fetchrow($result);
							$db->sql_freeresult($result);
							
							$winner		= $row['1vs1_winner'];
							$loser		= ($winner == $row['1vs1_challanger']) ? $row['1vs1_challangee'] : $row['1vs1_challanger'];
							$ladderid	= $row['1vs1_ladder'];

							// Now, update the ranks. Swap if needed.
							$ladder	= new ladder();
							$ladder->update_ranks_user($winner, $loser, $ladderid);
						}
						
						// UPDATE GENERAL USERS SCORE FOR LEADERBOARD
						// challanger
						$sqlu1		= "SELECT user_id, user_round_wins, user_round_losses FROM " . USERS_TABLE . " WHERE user_id = " . $challanger;
						$resultu1	= $db->sql_query_limit($sqlu1, 1);
						$rowu1		= $db->sql_fetchrow($resultu1);
						$db->sql_freeresult($resultu1);
						
						$exp1		= round(($rowu1['user_round_wins'] + $row['1vs1_challanger_score']) / ($rowu1['user_round_losses'] + $row['1vs1_challangee_score']), 4);	
						$sql_array_3	= array(
							'user_exp'			=> $exp1,
							'user_round_wins'	=> $rowu1['user_round_wins'] + $row['1vs1_challanger_score'],
							'user_round_losses'	=> $rowu1['user_round_losses'] + $row['1vs1_challangee_score']
						);
						$sql = "UPDATE " . USERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array_3) . " WHERE user_id = " . $challanger;
						$db->sql_query($sql);
						
						// challange
						$sqlu2		= "SELECT user_id, user_round_wins, user_round_losses FROM " . USERS_TABLE . " WHERE user_id = " . $challangee;
						$resultu2	= $db->sql_query_limit($sqlu2, 1);
						$rowu2		= $db->sql_fetchrow($resultu2);
						$db->sql_freeresult($resultu2);
						
						$exp2		= round(($rowu2['user_round_wins'] + $row['1vs1_challangee_score']) / ($rowu2['user_round_losses'] + $row['1vs1_challanger_score']), 4);	
						$sql_array_4	= array(
							'user_exp'			=> $exp2,
							'user_round_wins'	=> $rowu2['user_round_wins'] + $row['1vs1_challangee_score'],
							'user_round_losses'	=> $rowu2['user_round_losses'] + $row['1vs1_challanger_score']
						);
						$sql = "UPDATE " . USERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array_4) . " WHERE user_id = " . $challangee;
						$db->sql_query($sql);
					} // FINE SE RANKED
					
					// get feedback
					$frep	= (int) request_var("vsrep_{$match_id}", 5);
					$fieldK	= ($challanger == $user->data['user_id']) ? 'ee_feedback' : 'er_feedback';
					
					// set the values for match table
					$sql_array	= array(
						'1vs1_confirmer'	=> $user->data['user_id'],
						'end_time'			=> time(),
						"{$fieldK}"			=> (!empty($frep)) ? $frep : 5,
						'1vs1_contestested'	=> 0
					);
					$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_id = " . $match_id;
					$db->sql_query($sql);
					
					// make the new general reputation value for reporter * repopulate
					$sql_R		= "SELECT 1vs1_id, er_feedback, ee_feedback FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = {$match_id}";
					$result_R	= $db->sql_query_limit($sql_R, 1);
					$row_R		= $db->sql_fetchrow($result_R);
					$db->sql_freeresult($result_R);
					
					// load right value for each users				
					$sql_array9	= array(
						'rep_value'	=> getuserdata('rep_value', $challanger) + $row_R['er_feedback'], /* sum the reputation gived */
						'rep_time'	=> getuserdata('rep_time', $challanger) + 1
					);
					$sql = "UPDATE " . USERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array9) . " WHERE user_id = " . $challanger;
					$db->sql_query($sql);
					
					$sql_array10	= array(
						'rep_value'	=> getuserdata('rep_value', $challangee) + $row_R['ee_feedback'], /* sum the reputation gived */
						'rep_time'	=> getuserdata('rep_time', $challangee) + 1
					);
					$sql = "UPDATE " . USERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array10) . " WHERE user_id = " . $challangee;
					$db->sql_query($sql);
					
					
					// Send a PM to the winner to tell them that it was confirmed
					$subject	= $user->lang['PMWINCONFIRMED'];
					$message	= sprintf($user->lang['PMWINCONFIRMEDTXT'], getusername($user->data['user_id']));
					insert_pm($reporter, $user->data, $subject, $message);
					
					// Recalculate eser stats
					recalculate_totalEXP($challanger);
					recalculate_totalEXP($challangee);
				} // end check for unconfirmed
			}
		}
		
		// contest action
		if (!empty($contested))
		{
			foreach ($contested AS $xvalue)
			{
				// Get the challenge detials.
				$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $xvalue;
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their match.
				validate_opponents($row['1vs1_challanger'], $row['1vs1_challangee'], true);
				
				$sql_array	= array(
					'1vs1_contestested'	=> 1,
					'1vs1_confirmer'	=> 0
				);
				$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_id = " . $xvalue;
				$db->sql_query($sql);
			}
		}
		
		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone");
		meta_refresh(2, $redirect_url);
		trigger_error('MATCH_REPORTED');
	}

	// Assign the other variables to the template.
	$template->assign_vars(array('U_ACTION' => $u_action));
}

?>