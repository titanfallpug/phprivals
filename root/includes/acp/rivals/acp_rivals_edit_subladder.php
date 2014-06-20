<?php
/**
*
* @package acp
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
 * Edit a Sub-Ladder
 * Called from acp_rivals with mode == 'edit_subladder'
 */
function acp_rivals_edit_subladder($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$ladder	= new ladder();
	$errore	= array();

	$submit		= (!empty($_POST['submit'])) ? true : false;
	$ladder_id	= (int) request_var('ladder_id', 0);
	$confirm	= (int) request_var('confirm', 0);
	$delete		= (int) request_var('delete', 0);

	// Are we submitting a form?
	if ($submit || $confirm > 0)
	{
		// CHECK LADDER 1vs1
		$sql_3		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
		$result_3	= $db->sql_query($sql_3);
		$row_3		= $db->sql_fetchrow($result_3);
		$db->sql_freeresult($result_3);
		
		if ($row_3['ladder_oneone'] == 1)
		{
			// Yes, handle the form.
			if (!empty($delete))
			{
				if ($confirm > 0)
				{
					// Delete the ladder.
					$sql	= "DELETE FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
					$db->sql_query($sql);

					$sql	= "DELETE FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder  = " . $ladder_id; /* 1vs1 */
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder  = " . $ladder_id;
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . MATCHES_TABLE . " WHERE match_ladder  = " . $ladder_id; /* clan */
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_ladder  = " . $ladder_id;
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_ladder  = " . $ladder_id;
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . RIVAL_LADDER_RULES . " WHERE rules_ladder  = " . $ladder_id; /* rules */
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_ladder  = " . $ladder_id; /* match finder */
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . RIVAL_MVP . " WHERE ladder_mvp  = " . $ladder_id; /* mvp chart */
					$db->sql_query($sql);

					// Completed. Let the user know.
					$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
					meta_refresh(2, $redirect_url);
					trigger_error('LADDER_UPDATED');
					break;
				}
				else
				{
					// needs confirmation
					$no_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_subladder&amp;ladder_id= " . $ladder_id);
					$si_url	= append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_subladder&amp;ladder_id={$ladder_id}&amp;submit=1&amp;delete=1&amp;confirm=1");
					trigger_error(sprintf($user->lang['SUBLADDER_DELETE_TXT'], '<a href="' . $no_url . '">', '</a>', '<a href="' . $si_url . '">', '</a>'));
					break;
				}
			}

			// Check if we are resetting the ladder stats.
			$ladder_reset	= (int) request_var('ladder_reset', 0);
			if ($ladder_reset != 0)
			{
				// Reset everything for every group!
				$sql_array	= array(
					'user_wins'				=> 0,
					'user_losses'			=> 0,
					'user_pari'				=> 0,
					'user_score'			=> 1200,
					'user_lastscore'		=> 0,
					'user_streak'			=> 0,
					'user_current_rank'		=> 0,
					'user_last_rank'		=> 0,
					'user_worst_rank'		=> 0,
					'user_best_rank'		=> 0,
					'user_goals_fatti'		=> 0,
					'user_goals_subiti'		=> 0,
					'user_ratio'			=> 0
				);
				$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_ladder = " . $ladder_id;
				$db->sql_query($sql);

				// Now, set their ranks!
				$sql	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . $ladder_id;
				$result	= $db->sql_query($sql);
				$i	= 1;
				while ($row = $db->sql_fetchrow($result))
				{
					// Update their ranks.
					$sql_array2	= array(
						'user_current_rank'		=> $i,
						'user_last_rank'		=> 0,
						'user_worst_rank'		=> $i,
						'user_best_rank'		=> $i
					);
					$sql2 = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE user_id = {$row['user_id']} AND 1vs1_ladder = " . $ladder_id;
					$db->sql_query($sql2);

					$i++;
				}
			}
			
			$ladder_name	= (string) utf8_normalize_nfc(request_var('ladder_name', '', true));
			$ladder_desc	= (string) utf8_normalize_nfc(request_var('ladder_desc', '', true));
			$ladder_shorty	= (string) utf8_normalize_nfc(request_var('shorty', '', true));
			$ladder_locked	= (int) request_var('ladder_locked', 0);
			$ladder_cl		= (int) request_var('ladder_cl', 0);
			$ladder_ranking	= (int) request_var('ladder_ranking', 0);
			$ladder_style	= (int) request_var('ladder_style', 0);
			$win_system		= (int) request_var('win_system', 0);
			$ladder_mod		= (string) utf8_normalize_nfc(request_var('ladder_mod', 0, true));
			$ladder_limit	= (int) request_var('ladder_limit', 0);
			
			// Check to see if we switched systems.
			if ($ladder_ranking == 1 && $ladder->data('ladder_ranking', $ladder_id) != 1)
			{
				/* Switching from ELO to SWAP. You must keep the same placings for groups, to be fair, so this syncs the rankings and keeps them the same when switching from ELO to SWAP.*/

				// Order the groups by their ELO scoring.
				$sql	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} ORDER BY user_score DESC";
				$result	= $db->sql_query($sql);
				$i	= 1;
				while ($row = $db->sql_fetchrow($result))
				{
					// Set the new ranks for the groups.
					$sql_array2	= array(
						'user__current_rank'	=> $i,
						'user_last_rank'		=> 0,
						'user_worst_rank'		=> 0,
						'user_best_rank'		=> $i
					);
					$sql2 = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE user_id = {$row['user_id']} AND 1vs1_ladder = " . $ladder_id;
					$db->sql_query($sql2);

					$i++;
				}
				$db->sql_freeresult($sql);
			}

			// Setup the BBcode for the ladder description.
			$uid			= $bitfield = $options = '';
			$allow_bbcode	= $allow_urls = $allow_smilies = true;
			generate_text_for_storage($ladder_desc, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			if (empty($ladder_name))
			{
				$errore[] = $user->lang['NAME_LADDER_EMPTY'];
			}
			if (($ladder_style == 3) && ($win_system == 1))
			{
				$errore[] = $user->lang['CALCIO_NON_RISULTATO_PER_SCELTA'];
			}
			if (($ladder_style == 1) && ($win_system == 1))
			{
				$errore[] = $user->lang['DECERTO_NON_RISULTATO_PER_SCELTA'];
			}
			if (($ladder_style == 1) && (empty($ladder_shorty)) || ($ladder_style == 2) && (empty($ladder_shorty)))
			{
				$errore[] = $user->lang['DEVI_COLLEGARE_DECERTO_CPC'];
			}
			if (($ladder_mod != 0) && !is_numeric($ladder_mod))
			{
				$errore[] = $user->lang['MOD_ID_NON_NUMERIC'];
			}
				
			$template->assign_vars(array(
				'ERRORE' => (sizeof($errore)) ? implode('<br />', $errore) : ''
			));
				
			if (!sizeof($errore))
			{
				$sql_array	= array(
					'ladder_name'		=> $ladder_name,
					'ladder_desc'		=> $ladder_desc,
					'ladder_locked' 	=> $ladder_locked,
					'ladder_cl'			=> $ladder_cl,
					'ladder_ranking'	=> $ladder_ranking,
					'ladder_style'		=> $ladder_style,
					'shortname'			=> $ladder_shorty,
					'ladder_win_system'	=> $win_system,
					'ladder_mod' 		=> $ladder_mod,
					'ladder_limit'		=> $ladder_limit,
					'bbcode_uid' 		=> $uid,
					'bbcode_bitfield' 	=> $bitfield,
					'bbcode_options'	=> $options
				);

				// Update the ladder.
				$sql = "UPDATE " . LADDERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = " . $ladder_id;
				$db->sql_query($sql);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_UPDATED');		
			}
		}
		else // STANDARD SUBLADDER FROM HERE
		{
			// Yes, handle the form.
			if (!empty($delete))
			{
				if ($confirm > 0)
				{
					// Delete the ladder.
					$sql	= "DELETE FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
					$db->sql_query($sql);

					$sql	= "DELETE FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder  = " . $ladder_id; /* 1vs1 */
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder  = " . $ladder_id;
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . MATCHES_TABLE . " WHERE match_ladder  = " . $ladder_id; /* clan */
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_ladder  = " . $ladder_id;
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_ladder  = " . $ladder_id;
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . RIVAL_LADDER_RULES . " WHERE rules_ladder  = " . $ladder_id; /* rules */
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_ladder  = " . $ladder_id; /* match finder */
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . RIVAL_MVP . " WHERE ladder_mvp  = " . $ladder_id; /* mvp chart */
					$db->sql_query($sql);

					// Completed. Let the user know.
					$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
					meta_refresh(2, $redirect_url);
					trigger_error('LADDER_UPDATED');
					break;
				}
				else
				{
					// needs confirmation
					$no_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_subladder&amp;ladder_id= " . $ladder_id);
					$si_url	= append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_subladder&amp;ladder_id={$ladder_id}&amp;submit=1&amp;delete=1&amp;confirm=1");
					trigger_error(sprintf($user->lang['SUBLADDER_DELETE_TXT'], '<a href="' . $no_url . '">', '</a>', '<a href="' . $si_url . '">', '</a>'));
					break;
				}
			}

			// Check if we are resetting the ladder stats.
			$ladder_reset	= (int) request_var('ladder_reset', 0);
			if ($ladder_reset != 0)
			{
				// Reset everything for every group!
				$sql_array	= array(
					'group_wins'			=> 0,
					'group_losses'			=> 0,
					'group_pari'			=> 0,
					'group_score'			=> 1200,
					'group_lastscore'		=> 0,
					'group_streak'			=> 0,
					'group_current_rank'	=> 0,
					'group_last_rank'		=> 0,
					'group_worst_rank'		=> 0,
					'group_best_rank'		=> 0,
					'group_goals_fatti'		=> 0,
					'group_goals_subiti'	=> 0,
					'group_ratio'			=> 0
				);
				$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_ladder = " .  $ladder_id;
				$db->sql_query($sql);

				// Now, set their ranks!
				$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . $ladder_id;
				$result	= $db->sql_query($sql);

				$i	= 1;
				while ($row = $db->sql_fetchrow($result))
				{
					// Update their ranks.
					$sql_array2	= array(
					'group_current_rank'	=> $i,
					'group_last_rank'		=> 0,
					'group_worst_rank'		=> $i,
					'group_best_rank'		=> $i
				);
				$sql2 = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = {$row['group_id']} AND group_ladder = " . $ladder_id;
				$db->sql_query($sql2);

					$i++;
				}
			}

			$ladder_name	= (string) utf8_normalize_nfc(request_var('ladder_name', '', true));
			$ladder_desc	= (string) utf8_normalize_nfc(request_var('ladder_desc', '', true));
			$ladder_shorty	= (string) utf8_normalize_nfc(request_var('shorty', '', true));
			$ladder_locked	= (int) request_var('ladder_locked', 0);
			$ladder_cl		= (int) request_var('ladder_cl', 0);
			$ladder_ranking	= (int) request_var('ladder_ranking', 0);
			$ladder_rm		= (int) request_var('ladder_rm', 0);
			$ladder_style	= (int) request_var('ladder_style', 0);
			$ladder_mvp  	= (int) request_var('ladder_mvp', 0);
			$ladder_advstat	= (int) request_var('ladder_advstats', 0);
			$win_system		= (int) request_var('win_system', 0);
			$ladder_mod		= (string) utf8_normalize_nfc(request_var('ladder_mod', 0, true));
			$ladder_limit	= (int) request_var('ladder_limit', 0);

			// Check to see if we switched systems.
			if ($ladder_ranking == 1 && $ladder->data ('ladder_ranking', $ladder_id) != 1)
			{
				/* Switching from ELO to SWAP. You must keep the same placings for groups, to be fair, so this syncs the rankings and keeps them the same when switching from ELO to SWAP.*/

				// Order the groups by their ELO scoring.
				$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} ORDER BY group_score DESC";
				$result	= $db->sql_query($sql);

				$i	= 1;
				while ($row = $db->sql_fetchrow($result))
				{
					// Set the new ranks for the groups.
					$sql_array2	= array(
						'group_current_rank'	=> $i,
						'group_last_rank'		=> 0,
						'group_worst_rank'		=> 0,
						'group_best_rank'		=> $i
					);
					$sql2 = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_id = {$row['group_id']} AND group_ladder = " . $ladder_id;
					$db->sql_query($sql2);

					$i++;
				}
				$db->sql_freeresult($sql);
			}

			// Setup the BBcode for the ladder description.
			$uid			= $bitfield = $options = '';
			$allow_bbcode	= $allow_urls = $allow_smilies = true;
			generate_text_for_storage($ladder_desc, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
				if (empty($ladder_name))
				{
					$errore[] = $user->lang['NAME_LADDER_EMPTY'];
				}
				if (($ladder_style == 3) && ($win_system == 1))
				{
					$errore[] = $user->lang['CALCIO_NON_RISULTATO_PER_SCELTA'];
				}
				if (($ladder_style == 1) && ($win_system == 1))
				{
					$errore[] = $user->lang['DECERTO_NON_RISULTATO_PER_SCELTA'];
				}
				if (($ladder_style == 1) && (empty($ladder_shorty)) || ($ladder_style == 2) && (empty($ladder_shorty)))
				{
					$errore[] = $user->lang['DEVI_COLLEGARE_DECERTO_CPC'];
				}
				if (($ladder_mod != 0) && !is_numeric($ladder_mod))
				{
					$errore[] = $user->lang['MOD_ID_NON_NUMERIC'];
				}
				
				$template->assign_vars(array(
					'ERRORE' => (sizeof($errore)) ? implode('<br />', $errore) : ''
				));
				
			
			if (!sizeof($errore))
			{
				$sql_array	= array(
					'ladder_name'		=> $ladder_name,
					'ladder_desc'		=> $ladder_desc,
					'ladder_locked' 	=> $ladder_locked,
					'ladder_cl'			=> $ladder_cl,
					'ladder_style'		=> $ladder_style,
					'shortname'			=> $ladder_shorty,
					'ladder_mvp'		=> $ladder_mvp,
					'ladder_advstat'	=> $ladder_advstat,
					'ladder_ranking'	=> $ladder_ranking,
					'ladder_win_system'	=> $win_system,
					'ladder_mod' 		=> $ladder_mod,
					'ladder_limit'		=> $ladder_limit,
					'ladder_rm'	 		=> $ladder_rm,
					'bbcode_uid' 		=> $uid,
					'bbcode_bitfield' 	=> $bitfield,
					'bbcode_options'	=> $options
				);

				// Update the ladder.
				$sql = "UPDATE " . LADDERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = " . $ladder_id;
				$db->sql_query($sql);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_UPDATED');		
			}
		}
	}
	else
	{
		// Get the ladder's information.
		$ladder_data	= $ladder->data ('*', $ladder_id);
		decode_message($ladder_data['ladder_desc'], $ladder_data['bbcode_uid']);
		
		$fufa = $ladder_data['shortname'];
		
		// Assign the information to the template.
		$template->assign_vars(array(
			'U_ACTION' 			=> $u_action,
			'LADDER_ID' 		=> $ladder_id,
			'LADDER_NAME'		=> $ladder_data['ladder_name'],
			'LADDER_DESC' 		=> $ladder_data['ladder_desc'],
			'LADDER_PARENT' 	=> $ladder_data['ladder_id'],
			'LADDER_RM'			=> $ladder_data['ladder_rm'],
			'LADDER_STYLE'		=> $ladder_data['ladder_style'],
			'LADDER_MVP'		=> $ladder_data['ladder_mvp'],
			'LADDER_MOD'		=> $ladder_data['ladder_mod'],
			'LADDER_STYLE_0'	=> ($ladder_data['ladder_style'] == 0) ? 'selected="selected"' : '',
			'LADDER_STYLE_1'	=> ($ladder_data['ladder_style'] == 1) ? 'selected="selected"' : '',
			'LADDER_STYLE_2'	=> ($ladder_data['ladder_style'] == 2) ? 'selected="selected"' : '',
			'LADDER_STYLE_3'	=> ($ladder_data['ladder_style'] == 3) ? 'selected="selected"' : '',
			'LADDER_STYLE_4'	=> ($ladder_data['ladder_style'] == 4) ? 'selected="selected"' : '',
			'LADDER_LOCKED' 	=> ($ladder_data['ladder_locked'] == 0) ? 'checked="checked"' : '',
			'LADDER_LOCKED2' 	=> ($ladder_data['ladder_locked'] == 1) ? 'checked="checked"' : '',
			'LADDER_CL' 		=> ($ladder_data['ladder_cl'] == 1) ? 'checked="checked"' : '',
			'LADDER_MVP' 		=> ($ladder_data['ladder_mvp'] == 1) ? 'checked="checked"' : '',
			'LADDER_ADVSTAT'	=> ($ladder_data['ladder_advstat'] == 1) ? 'checked="checked"' : '',
			'LADDER_WINSYS1' 	=> ($ladder_data['ladder_win_system'] == 0) ? 'checked="checked"' : '',
			'LADDER_WINSYS2' 	=> ($ladder_data['ladder_win_system'] == 1) ? 'checked="checked"' : '',
			'LADDER_RANKING' 	=> ($ladder_data['ladder_ranking'] == 0) ? 'checked="checked"' : '',
			'LADDER_RANKING2'	=> ($ladder_data['ladder_ranking'] == 1) ? 'checked="checked"' : '',
			'LADDER_RANKING3'	=> ($ladder_data['ladder_ranking'] == 2) ? 'checked="checked"' : '',
			'LADDER_LIMIT_0'	=> ($ladder_data['ladder_limit'] == 0) ? 'selected="selected"' : '',
			'LADDER_LIMIT_1'	=> ($ladder_data['ladder_limit'] == 1) ? 'selected="selected"' : '',
			'LADDER_LIMIT_2'	=> ($ladder_data['ladder_limit'] == 2) ? 'selected="selected"' : '',
			'NON1VS1'			=> ($ladder_data['ladder_oneone'] == 0) ? true : false,
			'INFOLOGO'			=> "{$phpbb_root_path}rivals/images/infologo.gif",
		));
		
		// decerto
		$sql_43		= "SELECT * FROM " . DECERTO_CAT . " GROUP BY nome_corto";
		$result_43	= $db->sql_query($sql_43);

		while ($row_43 = $db->sql_fetchrow($result_43))
		{
			if (validate_decerto($row_43['nome_corto']) === true)
			{
				$template->assign_block_vars('block_decerto', array(
					'NOMECORTO' => $row_43['nome_corto'],
					'NOMELUNGO' => $row_43['nome_gioco'],
					'ERRORE' 	=> (sizeof($errore)) ? implode('<br />', $errore) : '',
					'TIPO'		=> ($row_43['cpc'] == 1) ? $user->lang['DECERTO'] : $user->lang['CPC'],
					'SELECTED'	=> ($fufa == $row_43['nome_corto']) ? 'selected="selected"' : ''
				));
			}
		}
		$db->sql_freeresult($result_43);
	}
}

?>