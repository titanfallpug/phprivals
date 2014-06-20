<?php
/**
*
* @package ucp
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
 * Manage Challenges
 * Called from ucp_rivals with mode == 'challenges'
 */
function ucp_rivals_challenges($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group	= new group();
	$ladder	= new ladder();
	
	// Check if the group is apart of a ladder yet.
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
	
	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if($submit)
	{
		$accept		= request_var('accept', array(0 => 0));
		$decline	= request_var('decline', array(0 => 0));
		nodouble_check($accept, $decline, 'i=rivals&amp;mode=challenges');
		
		if (empty($accept) && empty($decline))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=challenges");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		if (!empty($accept))
		{
			foreach ($accept AS $value)
			{							
				// Get the challenge detials.
				$sql	= "SELECT * FROM " . CHALLENGES_TABLE . " WHERE challenge_id = " . $value;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their challange.
				validate_opponents($row['challenger'], $row['challengee']);

				$getladder	= $ladder->get_roots($row['challenge_ladder']);			
				$nome_corto = $getladder['SUBLADDER_SHORTNM'];
				$tipoladder = $getladder['SUBLADDER_STYLE'];
				$tiporank	= $getladder['SUBLADDER_RAKING'];
		
				if ($tipoladder == 1)
				{
					// modi
					$sql2		= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = {$tipoladder} AND nome_corto = '{$nome_corto}' ORDER BY RAND()";
					$result2	= $db->sql_query_limit($sql2, 1);
					$row2		= $db->sql_fetchrow($result2);
					$inter1 	= $row2['decerto_interid'];
					$mode1		= $row2['decerto_mode'];
					$db->sql_freeresult($result2);
				
					$sql3		= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = {$tipoladder} AND nome_corto = '{$nome_corto}' AND decerto_interid != {$inter1} ORDER BY RAND()";
					$result3	= $db->sql_query_limit($sql3, 1);
					$row3		= $db->sql_fetchrow($result3);
					$inter2 	= $row3['decerto_interid'];
					$mode2		= $row3['decerto_mode'];
					$db->sql_freeresult($result3);
				
					$sql4		= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = {$tipoladder} AND nome_corto = '{$nome_corto}' AND decerto_interid != {$inter1} AND decerto_interid != {$inter2} ORDER BY RAND()";
					$result4	= $db->sql_query_limit($sql4, 1);
					$row4		= $db->sql_fetchrow($result4);
					$mode3		= $row4['decerto_mode'];
					$db->sql_freeresult ($result4);
				
					// mappa 1
					$sql_a		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 ORDER BY RAND()";
					$result_a	= $db->sql_query_limit($sql_a, 1);
					$row_a		= $db->sql_fetchrow($result_a);
					$mappa1 	= $row_a['decerto_mappa']; //////////////
					$db->sql_freeresult($result_a);
					// mappa 2
					$sql_b		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 2 ORDER BY RAND()";
					$result_b	= $db->sql_query_limit($sql_b, 1);
					$row_b		= $db->sql_fetchrow($result_b);
					$mappa2 	= $row_b['decerto_mappa']; /////////////////
					$db->sql_freeresult($result_b);
					// mappa 3
					$sql_c		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 3 ORDER BY RAND()";
					$result_c	= $db->sql_query_limit($sql_c, 1);
					$row_c		= $db->sql_fetchrow($result_c);
					$mappa3 	= $row_c['decerto_mappa']; ///////////////
					$db->sql_freeresult($result_c);
			
				}
				else if ($tipoladder == 2)
				{
					// mappa 1
					$sql_a		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 2 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 ORDER BY RAND()";
					$result_a	= $db->sql_query_limit($sql_a, 1);
					$row_a		= $db->sql_fetchrow($result_a);
					$mappa1 	= $row_a['decerto_mappa']; //////////////
					$mappa1ID	= $row_a['id_mappa_decerto'];
					$db->sql_freeresult($result_a);
					// mappa 2
					$sql_b		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 2 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 
								AND id_mappa_decerto != {$mappa1ID} ORDER BY RAND()";
					$result_b	= $db->sql_query_limit($sql_b, 1);
					$row_b		= $db->sql_fetchrow($result_b);
					$mappa2 	= $row_b['decerto_mappa']; /////////////////
					$mappa2ID	= $row_b['id_mappa_decerto'];
					$db->sql_freeresult($result_b);
					// mappa 3
					$sql_c		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 2 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 
								AND id_mappa_decerto != {$mappa1ID} AND id_mappa_decerto != {$mappa2ID} ORDER BY RAND()";
					$result_c	= $db->sql_query_limit($sql_c, 1);
					$row_c		= $db->sql_fetchrow($result_c);
					$mappa3 	= $row_c['decerto_mappa']; ///////////////
					$mappa3ID	= $row_c['id_mappa_decerto'];
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

				// CHECK WAR if are they too mutch
				$superdata = time();
				$mindata = ($superdata - 259200);
		 
				$sql_rip	= "SELECT COUNT(match_id) AS checkers FROM " . MATCHES_TABLE . " WHERE (match_posttime BETWEEN {$mindata} AND {$superdata}) AND (match_challenger = " . $row['challenger'] ." AND match_challengee = " . $row['challengee'] .")
							OR (match_challenger = " . $row['challengee'] ." AND match_challengee = " . $row['challenger'] .") ";
				$result_rip	= $db->sql_query($sql_rip);
				$row_rip	= $db->sql_fetchrow($result_rip);
				$sborro		= $row_rip['checkers'];
				$db->sql_freeresult($result_rip);
		 
				if ($sborro >= 3)
				{ 
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=challenges");
					meta_refresh(6, $redirect_url);
					trigger_error('SFIDATO_TROPPO');
				}
				
				// Do not allow in any case up to 6 matches
				$sql_rip	= "SELECT COUNT(match_id) AS checkers1 FROM " . MATCHES_TABLE . " WHERE (match_challenger = " . $row['challenger'] ." AND match_challengee = " . $row['challengee'] .")
							OR (match_challenger = " . $row['challengee'] ." AND match_challengee = " . $row['challenger'] .") ";
				$result_rip	= $db->sql_query($sql_rip);
				$row_rip	= $db->sql_fetchrow($result_rip);
				$sborro1	= $row_rip['checkers1'];
				$db->sql_freeresult($result_rip);
				
				if ($sborro > 6)
				{ 
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=challenges");
					meta_refresh(6, $redirect_url);
					trigger_error('SFIDATO_TROPPO');
				}

				// Accept the challenge. "Move" the challenge to the matches table.
				$sql_array	= array(
					'match_challenger' 		=> $row['challenger'],
					'match_challengee'		=> $row['challengee'],
					'match_challenger_ip'	=> $row['challenger_ip'],
					'match_challengee_ip'	=> (!empty($user->data['user_ip'])) ? $user->data['user_ip'] : $_SERVER['REMOTE_ADDR'],
					'match_posttime' 		=> $row['challenge_posttime'],
					'match_unranked' 		=> $row['challenge_unranked'],
					'match_details' 		=> $row['challenge_details'],
					'match_ladder' 			=> $row['challenge_ladder'],
					'mappa_mode1' 			=> $mappa1,
					'mappa_mode2' 			=> $mappa2,
					'mappa_mode3' 			=> $mappa3,
					'mode1' 				=> $mode1,
					'mode2' 				=> $mode2,
					'mode3' 				=> $mode3
				);
				$sql		= "INSERT INTO " . MATCHES_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
				

				// Delete the challenge, its now a match.
				$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_id = " . $value;
				$db->sql_query($sql);
				
				// Delete clan entries if are on rth chicken risk list
				if ($tiporank == 2)
				{
					$sql	= "DELETE FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$row['challengee']} AND oneone = 0 AND ladder_id = " . $row['challenge_ladder'];
					$db->sql_query($sql);
				}
				
				// Reset hibernated status
				$sql_array5	= array(
					'group_frosted'			=> 0,
					'group_frosted_time'	=> 0,
				);
				$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array5) . " WHERE group_id = {$row['challengee']} AND group_ladder = {$row['challenge_ladder']}";
				$db->sql_query($sql);

				// Send a PM to the challenger's group leader telling them it was accepted.
				$subject	= $user->lang['PM_CHALLENGEACCEPTED'];
				$message	= sprintf($user->lang['PM_CHALLENGEACCEPTEDTXT'], $group->data['group_name']);
				insert_pm($group->data('user_id', $row['challenger']), $user->data, $subject, $message);
			}
		}
		if (!empty($decline))
		{
			foreach ($decline AS $value)
			{
				// Get the challenge details.
				$sql	= "SELECT * FROM " . CHALLENGES_TABLE . " WHERE challenge_id = " . $value;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Confirm that this is their challange.
				validate_opponents($row['challenger'], $row['challengee']);
				
				$getladder	= $ladder->get_roots($row['challenge_ladder']);			
				
				// ADDON FOR RTH
				// we must count the number of declination
				if ($getladder['SUBLADDER_RAKING'] == 2 && !in_array($row['challenge_ladder'], $group->data['group_frosteds']))
				{
					$sql_c		= "SELECT * FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$group->data['group_id']} AND oneone = 0 AND ladder_id = " . $row['challenge_ladder'];
					$result_c	= $db->sql_query($sql_c);
					$row_c		= $db->sql_fetchrow($result_c);
					$db->sql_freeresult($result_c);
					
					if (!empty($row_c['group_id']))
					// Your clan just have done a decline so i set +1 chicken to it
					{
						$sql = "UPDATE " . CLANS_TABLE . " SET rth_chicken = rth_chicken + 1 WHERE group_id = {$row_c['group_id']}";
						$db->sql_query($sql);
						
					// remove 25% of clan points
						$sql_v		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$group->data['group_id']} AND group_ladder = " . $row['challenge_ladder'];
						$result_v	= $db->sql_query($sql_v);
						$row_v		= $db->sql_fetchrow($result_v);
						$db->sql_freeresult($result_v);
						
						if ($row_v['group_score'] >= 200)
						{
							$nuovopunteggio = ceil($row_v['group_score'] / 4);
						}
						else
						{
							$nuovopunteggio = 50;
						}
						
						$sql = "UPDATE " . GROUPDATA_TABLE . " SET group_score = {$nuovopunteggio} WHERE group_id = {$group->data['group_id']} AND group_ladder = " . $row['challenge_ladder'];
						$db->sql_query($sql);
						
					// now clean datas stored
						$sql	= "DELETE FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$row_c['group_id']} AND oneone = 0 AND ladder_id = " . $row['challenge_ladder'];
						$db->sql_query($sql);
					}
					else
					{
					// this clan dont have declined chain so add it for future combo
						$sql_array	= array(
							'group_id' 	=> $group->data['group_id'],
							'ladder_id'	=> $row['challenge_ladder'],
							'oneone'	=> 0
						);
						$sql		= "INSERT INTO " . RTH_CHECK_TABLE . " " . $db->sql_build_array ('INSERT', $sql_array);
						$db->sql_query($sql);
					}
				}

				// Decline the challenge. Delete it.
				$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_id = " . $value;
				$db->sql_query($sql);

				// Send a PM to the challenger's group leader and to the logged in group.
				$subject	= $user->lang['PM_CHALLENGEDECLINED'];
				$message	= sprintf($user->lang['PM_CHALLENGEDECLINEDTXT'], $group->data['group_name']);
				insert_pm($group->data('user_id', $row['challenger']), $user->data, $subject, $message);
			}
		}

		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=challenges");
		meta_refresh(2, $redirect_url);
		trigger_error('CHALLENGES_UPDATED');
	}
	else
	{
		foreach ($group->data['group_ladders'] AS $value)
		{
			// Get the ladder's root detials to show.
			$ladder_data	= $ladder->get_roots($value);

			// Check to see if the ladder is locked.
			if ($ladder_data['SUBLADDER_LOCKED'] == 0)
			{
				// Assign each ladder to the template.
				$template->assign_block_vars('block_ladders', array(
					'LADDER_ICON'	=> ($ladder_data['SUBLADDER_STYLE'] > 0) ? '<img src="' . $phpbb_root_path .'rivals/images/iconlad' . $ladder_data['SUBLADDER_STYLE'] . '.gif" alt="' . $user->lang['ICON_LADDER'] . '" title="' . $user->lang["{$ladder_data['SUBLADDER_STYLE']}ICON_LADDER"] . '" />' : '',
					'PLATFORM' 		=> $ladder_data['PLATFORM_NAME'],
					'LADDER' 		=> $ladder_data['LADDER_NAME'],
					'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME']
				));

				// Get the challenges for this ladder and group. /MODDATO
				$sql	= "SELECT * FROM " . CHALLENGES_TABLE . " WHERE (challengee = {$group->data['group_id']} AND challenge_ladder = {$value}) ORDER BY challenge_posttime DESC";
				$result	= $db->sql_query ($sql);
				$i	= 0;
				while ($row = $db->sql_fetchrow($result))
				{
			   
			    if ($row['challenge_unranked'] == 1)
				{
					$classficata = "(" . sprintf($user->lang['NONCLASSIFICATA']) . ")";
				}
				else
				{
					$classficata = '';
				}
				
				// Chicken Risk
				$sql_u		= "SELECT * FROM " . RTH_CHECK_TABLE . " WHERE group_id = {$group->data['group_id']} AND ladder_id = " . $value;
				$result_u	= $db->sql_query($sql_u);
				$row_u		= $db->sql_fetchrow($result_u);
				$db->sql_freeresult($result_u);
				
					// Assign each challenge to the template.
					$template->assign_block_vars('block_ladders.block_challenges', array(
						'U_CHALLENGER'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['challenger']),
						'CHALLENGER'	=> $group->data('group_name', $row['challenger']),
						'TIME' 			=> $user->format_date($row['challenge_posttime']),
						'DETAILS' 		=> (!empty($row['challenge_details'])) ? nl2br($row['challenge_details']) : $user->lang['NO_DETTAGLIO'],
						'CHALLENGE_ID' 	=> $row['challenge_id'],
						'CHICKEN_RISK'	=> (!empty($row_u['group_id'])) ? '<img src="' . $phpbb_root_path . 'rivals/images/chickenrisk.gif" alt="" class="rivalsicon" />' : '',
						'CLASSIFICATA' 	=> $classficata,
						'BG_COLOR' 		=> ($i % 2) ? 'bg1' : 'bg2',
						'ROW_COLOR' 	=> ($i % 2) ? 'row1' : 'row2')
					);

					$i++;
				}

				$db->sql_freeresult($result);
			}
		}

		// Assign the other variables to the template.
		$template->assign_vars(array('U_ACTION' => $u_action));
	}
}

?>