<?php
/**
*
* @package mcp
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
* Edit reported match RIVALS
*/

class mcp_rivals
{
	function main($id, $mode)
	{
		global	$db, $user, $template, $config;
		global	$phpbb_root_path, $phpEx;

		// Include Rivals' classes and phpBB functions.
		include($phpbb_root_path . 'rivals/classes/class_group.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_tournament.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_ladder.' . $phpEx);
		include($phpbb_root_path . 'rivals/functions.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

		// Setup the language.
		$user->add_lang('mods/lang_rivals');

		// Switch between the modes to manage Rivals.
		switch($mode)
		{
			case 'main' :
				$this->tpl_name		= 'rivals/mcp_rivals_main';
				$this->page_title	= 'MCP_RIVALS';

				$group		= new group();
				$ladder		= new ladder();
				$tournament	= new tournament();
				
				$limter	= ($config['rivals_modstraight'] == 1) ? "AND l.ladder_mod = {$user->data['user_id']}" : "";
			
			// CLANS MATCHES LIST
				$sql	= "SELECT * FROM " . MATCHES_TABLE . " AS m LEFT JOIN " .LADDERS_TABLE . " AS l ON m.match_ladder = l.ladder_id 
						WHERE m.match_status = 2 AND m.match_confirmed = 0 AND m.match_reported > 0 {$limter} ORDER BY m.match_id DESC";
				$result	= $db->sql_query($sql);
				$i	= 0;
				while($row = $db->sql_fetchrow($result))
				{
					$ladder_data	= $ladder->get_roots($row['match_ladder']);
					
					$template->assign_block_vars('block_match_contested', array(
						'MATCH_ID'	=> $row['match_id'],
						'MATCH_ER'	=> $group->data('group_name', $row['match_challenger']),
						'MATCH_EE'	=> $group->data('group_name', $row['match_challengee']),
						'REPORTER'	=> $group->data('group_name', $row['match_reported']),
						'PLATFORM'	=> $ladder_data['PLATFORM_NAME'],
						'LADDER' 	=> $ladder_data['LADDER_NAME'],
						'SUBLADDER' => $ladder_data['SUBLADDER_NAME'],
						'EDIT_URL'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=edit_match&amp;mid={$row['match_id']}")
					));
					$i++;
				}
				$db->sql_freeresult($result);
			
			// USERS MATCHES LIST
				$sqlu	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " AS m LEFT JOIN " .LADDERS_TABLE . " AS l ON m.1vs1_ladder = l.ladder_id 
						WHERE m.1vs1_contestested = 1 AND m.1vs1_confirmer = 0 AND m.1vs1_reporter > 0 {$limter} ORDER BY m.1vs1_id DESC";
				$resultu	= $db->sql_query($sqlu);
				$iu	= 0;
				while($rowu = $db->sql_fetchrow($resultu))
				{
					$ladder_datau	= $ladder->get_roots($rowu['1vs1_ladder']);
					
					$template->assign_block_vars('block_user_match_contested', array(
						'MATCH_ID'	=> $rowu['1vs1_id'],
						'MATCH_ER'	=> getusername($rowu['1vs1_challanger']),
						'MATCH_EE'	=> getusername($rowu['1vs1_challangee']),
						'REPORTER'	=> getusername($rowu['1vs1_reporter']),
						'PLATFORM'	=> $ladder_datau['PLATFORM_NAME'],
						'LADDER' 	=> $ladder_datau['LADDER_NAME'],
						'SUBLADDER' => $ladder_datau['SUBLADDER_NAME'],
						'EDIT_URL'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=edit_match&amp;mid={$rowu['1vs1_id']}&amp;user=69")
					));
					$iu++;
				}
				$db->sql_freeresult($resultu);
				
			// TOURNAMENTS MATCHES
				$sql2	= "SELECT * FROM " . TGROUPS_TABLE . " AS tt LEFT JOIN " . TMATCHES . " AS tm ON tm.group_uid = tt.group_uid 
						WHERE tm.match_problem >= 1 AND tt.loser_confirm >= 0 AND tt.group_reported >= 0 AND tt.group_position MOD 2 ORDER BY tt.group_tournament DESC";
				$result2	= $db->sql_query($sql2);
				$y		= 0;
				while ($chapa = $db->sql_fetchrow($result2))
				{				
					$clan1name	= ($tournament->data('tournament_userbased', $chapa['group_tournament']) == 0) ? $group->data('group_name', $chapa['group_id']) : getusername($chapa['group_id']);
					$clan2	 	= $tournament->get_vsclan($chapa['group_tournament'], $chapa['group_id'], $chapa['group_bracket'], false);
					$clan2name	= ($tournament->data('tournament_userbased', $chapa['group_tournament']) == 0) ? $group->data('group_name', $clan2) : getusername($clan2);
					$reporter	= ($tournament->data('tournament_userbased', $chapa['group_tournament']) == 0) ? $group->data('group_name', $chapa['group_reported']) : getusername($chapa['group_reported']);
					$ss_flag	= ($tournament->data('tournament_userbased', $chapa['group_tournament']) == 0) ? '&amp;tmnt=69' : '&amp;tmnt=69&amp;user=69';
					
					if (!empty($clan2))
					{
						$template->assign_block_vars('block_tournament_match_contested', array(
							'EDIT_URL'		=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=edit_match&amp;mid={$chapa['group_uid']}" . $ss_flag),
							'CHALLANGER'	=> $clan1name,
							'CHALLANGEE'	=> $clan2name,
							'REPORTER'		=> $reporter,
							'TOURNAMENT'	=> $tournament->data('tournament_name', $chapa['group_tournament'])
						));
					}
					$y++;
				}
				$db->sql_freeresult($result2);
				
				// block logs
				$sql_where = "0 AND (log_operation = 'LOG_MATCH_EDITED' OR log_operation = 'LOG_SMSG_REMOVED')";
				$sql_sort = "log_time DESC";
				$keywords = utf8_normalize_nfc(request_var('keywords', '', true));
				$keywords_param = !empty($keywords) ? '&amp;keywords=' . urlencode(htmlspecialchars_decode($keywords)) : '';
				$log_data = array();
				$log_count = 0;
				view_log('mod', $log_data, $log_count, 5, 0, 0, 0, 0, $sql_where, $sql_sort, $keywords);
				
				foreach ($log_data as $row2)
				{
					$data = array();

					$checks = array('viewtopic', 'viewforum');
					foreach ($checks as $check)
					{
						if (isset($row[$check]) && $row[$check])
						{
							$data[] = '<a href="' . $row[$check] . '">' . $user->lang['LOGVIEW_' . strtoupper($check)] . '</a>';
						}
					}

					$template->assign_block_vars('block_match_logs', array(
						'USERNAME'		=> $row2['username_full'],
						'IP'			=> $row2['ip'],
						'DATE'			=> $user->format_date($row2['time']),
						'ACTION'		=> $row2['action'],
						'DATA'			=> (sizeof($data)) ? implode(' | ', $data) : '',
						'ID'			=> $row2['id'],
					));
				}
				
				// mod del a clans chat entries
				$editingid	= (int) request_var('delsmsg', 0);
				
				if ($editingid >0)
				{
					// get clan chat id
					$sql1		= "SELECT smsg_id, group_id FROM " . CLANSMSG_TABLE . " WHERE smsg_id = " . $editingid;
					$result1	= $db->sql_query_limit($sql1, 1);
					$row1		= $db->sql_fetchrow($result1);
					$db->sql_freeresult($result1);
					
					// add log reference
					$clanref_id	= $row1['group_id'];
					$clanname	= $group->data('group_name', $clanref_id);
					add_log('mod', 0, 0, 'LOG_SMSG_REMOVED', $clanname);
					
					// remove entries
					$sql	= "DELETE FROM " . CLANSMSG_TABLE . " WHERE smsg_id = " . $editingid;
					$db->sql_query($sql);
					
					$redirect_url = append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $clanref_id);
					redirect($redirect_url);
				}
				
			
			break;
			case 'edit_match' :
				$this->tpl_name		= 'rivals/mcp_rivals_edit_match';
				$this->page_title	= 'MCP_RIVALS_EDIT_MATCH';
				
				$group		= new group();
				$ladder		= new ladder();
				$tournament	= new tournament();
				
				$editingid	= (int) request_var('mid', 0);
				$userwar	= (int) request_var('user', 0);
				$tournmtwar	= (int) request_var('tmnt', 0);
				$submit		= (!empty($_POST['submit'])) ? true : false;
				$chatting	= (!empty($_POST['chatting'])) ? true : false;
				
				if (empty($editingid))
				{
					trigger_error('MID_NON_IMPOSTATA');
				}
				
				if ($userwar == 69 && $tournmtwar == 0)
				{
					$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = {$editingid} AND 1vs1_contestested = 1"; /* user match */
				}
				else if ($userwar == 0 && $tournmtwar == 69)
				{
					$sql	= "SELECT * FROM " . TMATCHES . " WHERE group_uid = {$editingid} AND match_problem >= 1"; /* tournament match */
				}
				else if ($userwar == 69 && $tournmtwar == 69)
				{
					$sql	= "SELECT * FROM " . TMATCHES . " WHERE group_uid = {$editingid} AND match_problem >= 1"; /* tournament match 1vs1 */
				}
				else
				{
					$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = {$editingid} AND match_status = 2"; /* clan match */
				}
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				if ($userwar == 69 && $tournmtwar == 0)
				{
					$themidid	= $row['1vs1_id']; /* user match */
				}
				else if ($userwar == 0 && $tournmtwar == 69)
				{
					$themidid	= $row['group_uid']; /* tournament match */
				}
				else if ($userwar == 69 && $tournmtwar == 69)
				{
					$themidid	= $row['group_uid']; /* tournament match 1vs1 */
				}
				else
				{
					$themidid	=  $row['match_id']; /* clan match */
				}
				
				if (empty($themidid))
				{
					trigger_error('MID_NON_IMPOSTATA');
				}
				
				// non tournament match
				if ($tournmtwar != 69)
				{
					$matchladder	= ($userwar == 69) ? $row['1vs1_ladder'] : $row['match_ladder'];
					$ladder_data	= $ladder->get_roots($matchladder);
					
					//check if mod can edit that match
					if (($config['rivals_modstraight'] == 1) && ($ladder_data['SUBLADDER_MOD'] != $user->data['user_id']))
					{
						trigger_error('YOU_CANT_EDIT_THAT_MATCH');
						break;
					}
					
					$unranked		= ($userwar == 69) ? $row['1vs1_unranked'] : $row['match_unranked'];
					$matchwinner	= ($userwar == 69) ? $row['1vs1_winner'] : $row['match_winner'];
					$challanger		= ($userwar == 69) ? $row['1vs1_challanger'] : $row['match_challenger'];
					$challangee		= ($userwar == 69) ? $row['1vs1_challangee'] : $row['match_challengee'];
					
					$template->assign_block_vars('block_match', array(
						'MATCH_ID'	=> ($userwar == 69) ? $row['1vs1_id'] : $row['match_id'],
						'MATCH_ER'	=> ($userwar == 69) ? getusername($row['1vs1_challanger']) : $group->data('group_name', $row['match_challenger']),
						'MATCH_EE'	=> ($userwar == 69) ? getusername($row['1vs1_challangee']) : $group->data('group_name', $row['match_challengee']),
						'IP_ER'		=> ($userwar == 69) ? $row['1vs1_challanger_ip'] : $row['match_challenger_ip'],
						'U_IP_ER'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=ipwhois&amp;ip=" . (($userwar == 69) ? $row['1vs1_challanger_ip'] : $row['match_challenger_ip'])),
						'IP_EE'		=> ($userwar == 69) ? $row['1vs1_challangee_ip'] : $row['match_challengee_ip'],
						'U_IP_EE'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=ipwhois&amp;ip=" . (($userwar == 69) ? $row['1vs1_challangee_ip'] : $row['match_challengee_ip'])),
						'RANKED'	=> ($unranked == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
						'WINNERER' 	=> ($matchwinner == $challanger) ? 'selected="selected"' : '',
						'WINNEREE' 	=> ($matchwinner == $challangee) ? 'selected="selected"' : '',
						'WINNERPP' 	=> ($matchwinner == '9999999') ? 'selected="selected"' : '',
						'ER_POINT'	=> ($userwar == 69) ? $row['1vs1_challanger_score'] : $row['match_challanger_score'],
						'EE_POINT'	=> ($userwar == 69) ? $row['1vs1_challangee_score'] : $row['match_challangee_score'],
						'ER_SCORE1'	=> ($userwar == 69) ? $row['mode1_score_er'] : $row['match_challanger_score_mode1'],
						'EE_SCORE1'	=> ($userwar == 69) ? $row['mode1_score_ee'] : $row['match_challangee_score_mode1'],
						'ER_SCORE2'	=> ($userwar == 69) ? $row['mode2_score_er'] : $row['match_challanger_score_mode2'],
						'EE_SCORE2'	=> ($userwar == 69) ? $row['mode2_score_ee'] : $row['match_challangee_score_mode2'],
						'ER_SCORE3'	=> ($userwar == 69) ? $row['mode3_score_er'] : $row['match_challanger_score_mode3'],
						'EE_SCORE3'	=> ($userwar == 69) ? $row['mode3_score_ee'] : $row['match_challangee_score_mode3'],
						'ER_TEAM'	=> ($userwar == 69) ? $row['1vs1_challanger_team'] : $row['challenger_team'],
						'EE_TEAM'	=> ($userwar == 69) ? $row['1vs1_challangee_team'] : $row['challengee_team'],
						'MVP1'		=> ($userwar == 69) ? 0 : $row['mvp1'],
						'MVP2'		=> ($userwar == 69) ? 0 : $row['mvp2'],
						'MVP3'		=> ($userwar == 69) ? 0 : $row['mvp3'],
						'MODE1'		=> ($userwar == 69) ? $row['mode1'] : $row['mappa_mode1'],
						'MODE2'		=> ($userwar == 69) ? $row['mode2'] : $row['mappa_mode2'],
						'MODE3'		=> ($userwar == 69) ? $row['mode3'] : $row['mappa_mode3'],
						'ID_ER'		=> $challanger,
						'ID_EE'		=> $challangee,
						'LADDER_ID'	=> $matchladder,
						'ADVSTATS'	=> ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? true : false,
						'DECERTO'	=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
						'CALCIO'	=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? true : false,
						'POINTRES'	=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
						'TOURNNT'	=> false,
						'ACCEPT_TM'	=> ($userwar == 69) ? $user->format_date($row['start_time']) : $user->format_date($row['match_posttime']),
						'REPORT_TM'	=> ($userwar == 69) ? $user->format_date($row['rep_time']) : $user->format_date($row['match_reptime'])
					));
					
					if ($userwar != 69 && $ladder_data['SUBLADDER_ADVSTAT'] == 1) /* user stats for match */
					{
						// challanger members
						$ERmembers	= $group->members('get_members', $challanger);
						foreach ($ERmembers as $ERmember)
						{
							$sqlu		= "SELECT * FROM " . MATCH_TEMP_USTATS . " WHERE id_ladder = {$row['match_ladder']} AND user_id = {$ERmember} AND id_match = " . $themidid;
							$resultu	= $db->sql_query_limit($sqlu, 1);
							$rowu		= $db->sql_fetchrow($resultu);
							$db->sql_freeresult($resultu);
							
							$template->assign_block_vars('block_match.er_ustats', array(
								'USERNAME'	=> getusername($ERmember),
								'USER_ID'	=> $ERmember,
								'KILL'		=> (!empty($rowu['kills'])) ? $rowu['kills'] : 0,
								'DEAD'		=> (!empty($rowu['deads'])) ? $rowu['deads'] : 0,
								'ASSIST'	=> (!empty($rowu['assists'])) ? $rowu['assists'] : 0,
								'GOAL_F'	=> (!empty($rowu['goal_f'])) ? $rowu['goal_f'] : 0,
								'GOAL_A'	=> (!empty($rowu['goal_a'])) ? $rowu['goal_a'] : 0
							));
						}
						
						// challangee members
						$EEmembers	= $group->members('get_members', $challangee);
						foreach ($EEmembers as $EEmember)
						{
							$sqlu		= "SELECT * FROM " . MATCH_TEMP_USTATS . " WHERE id_ladder = {$row['match_ladder']} AND user_id = {$EEmember} AND id_match = " . $themidid;
							$resultu	= $db->sql_query_limit($sqlu, 1);
							$rowu		= $db->sql_fetchrow($resultu);
							$db->sql_freeresult($resultu);
							
							$template->assign_block_vars('block_match.ee_ustats', array(
								'USERNAME'	=> getusername($EEmember),
								'USER_ID'	=> $EEmember,
								'KILL'		=> (!empty($rowu['kills'])) ? $rowu['kills'] : 0,
								'DEAD'		=> (!empty($rowu['deads'])) ? $rowu['deads'] : 0,
								'ASSIST'	=> (!empty($rowu['assists'])) ? $rowu['assists'] : 0,
								'GOAL_F'	=> (!empty($rowu['goal_f'])) ? $rowu['goal_f'] : 0,
								'GOAL_A'	=> (!empty($rowu['goal_a'])) ? $rowu['goal_a'] : 0
							));
						}
					}
					
					// MATCH CHAT					
					if ($userwar == 69 && $tournmtwar == 0)
					{
						$xflag = 1;
					}
					else if ($userwar == 69 && $tournmtwar == 69)
					{
						$xflag = 3;
					}
					else if ($userwar == 0 && $tournmtwar == 69)
					{
						$xflag = 2;
					}
					else
					{
						$xflag = 0;
					}
					
					$sql7		= "SELECT * FROM " . RIVALS_MATCH_CHAT . " WHERE id_match = {$themidid} AND chat_flag = {$xflag} ORDER BY chat_time DESC";
					$result7	= $db->sql_query($sql7);
					$i7	= 0;
					while($row7 = $db->sql_fetchrow($result7))
					{
						$template->assign_block_vars('block_match_chat', array(
							'POSTER_URL'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row7['id_writer']),
							'POSTER_NAME'	=> getusername($row7['id_writer']),
							'CLAN_NAME'		=> ($userwar != 69) ? (($row7['id_clan'] > 0) ? $group->data('group_name', $row7['id_clan']) : $user->lang['LADDER_STAFF']) : '',
							'CLAN_URL'		=> ($row7['id_clan'] > 0) ? append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $row7['id_clan']) : '#',
							'CHAT_TIME'		=> $user->format_date($row7['chat_time']),
							'CHAT_TEXT'		=> generate_text_for_display($row7['chat_text'], $row7['bbcode_uid'], $row7['bbcode_bitfield'], $row7['bbcode_options'])
						));
						$i7++;
					}
					$db->sql_freeresult($result7);
				}
				else
				{
					// Get the match tournament information.
					$sqlx		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_uid = {$row['group_uid']} AND group_position MOD 2"; /* get only not pair */
					$resultx	= $db->sql_query_limit($sqlx, 1);
					$rowx		= $db->sql_fetchrow($resultx);
					$db->sql_freeresult($resultx);
					
					// check if is a user tournament or a clan tournament
					$wherelimiter	= ($tournament->data('tournament_userbased', $row['id_torneo']) == 0) ? "id_clan = {$row['group1']} OR id_clan = {$row['group2']} OR id_writer = {$user->data['user_id']}" : "id_writer = {$row['group1']} OR id_writer = {$row['group2']} OR id_writer = {$user->data['user_id']}";
					
					$matchwinner	= $row['vincitore'];
					
					if ($tournament->data('tournament_tipo', $row['id_torneo'] == 1))
					{
						$firsthome	= ($tournament->data('tournament_userbased', $row['id_torneo']) == 0) ? $group->data('group_name', $row['first_home']) : getusername($row['first_home']);
					}
					else
					{
						$firsthome	= '';
					}
					
					$template->assign_block_vars('block_match', array(
						'MATCH_ID'		=> $row['group_uid'],
						'MATCH_ER'		=> ($tournament->data('tournament_userbased', $row['id_torneo']) == 0) ? $group->data('group_name', $row['group1']) : getusername($row['group1']),
						'MATCH_EE'		=> ($tournament->data('tournament_userbased', $row['id_torneo']) == 0) ? $group->data('group_name', $row['group2']) : getusername($row['group2']),
						'WINNERER' 		=> ($matchwinner == $row['group1']) ? 'selected="selected"' : '',
						'WINNEREE' 		=> ($matchwinner == $row['group2']) ? 'selected="selected"' : '',
						'RANKED'		=> $user->lang['TOURNAMENT'],
						'ER_POINT'		=> $row['punti1'],
						'ER_POINT_HOME'	=> $row['home_punti1'],
						'EE_POINT'		=> $row['punti2'],
						'EE_POINT_HOME'	=> $row['home_punti2'],
						'FIRST_HOME'	=> $firsthome,
						'MVP1'			=> $row['mvp1'],
						'MVP2'			=> $row['mvp2'],
						'MVP3'			=> $row['mvp3'],
						'ID_ER'			=> $row['group1'],
						'ID_EE'			=> $row['group2'],
						'TOURNM_ID'		=> $row['id_torneo'],
						'ADVSTATS'		=> ($tournament->data('tournament_advstats', $row['id_torneo']) == 1) ? true : false,
						'DECERTO'		=> ($tournament->data('tournament_decerto', $row['id_torneo']) == 1) ? true : false,
						'HOMEAWAY'		=> ($tournament->data('tournament_tipo', $row['id_torneo']) == 2) ? true : false,
						'CALCIO'		=> false,
						'POINTRES'		=> true,
						'TOURNNT'		=> true,
						'ACCEPT_TM'	=>  $user->format_date($rowx['group_time']),
						'REPORT_TM'	=>  $user->format_date($rowx['group_time'])
					));
					
					if ($tournament->data('tournament_advstats', $row['id_torneo']) == 1 && $tournament->data('tournament_userbased', $row['id_torneo']) == 0) /* user stats for match */
					{
						// challanger members
						$ERmembers	= $group->members('get_members', $row['group1']);
						foreach ($ERmembers as $ERmember)
						{
							$sql7		= "SELECT * FROM " . TUSER_DATA . " WHERE group_id = {$row['group1']} AND user_id = {$ERmember} AND group_uid = " . $row['group_uid'];
							$result7	= $db->sql_query_limit($sql7, 1);
							$row7		= $db->sql_fetchrow($result7);
							$db->sql_freeresult($result7);
							
							$template->assign_block_vars('block_match.er_ustats', array(
								'USERNAME'	=> getusername($ERmember),
								'USER_ID'	=> $ERmember,
								'KILL'		=> (!empty($row7['kills'])) ? $row7['kills'] : 0,
								'DEAD'		=> (!empty($row7['morti'])) ? $row7['morti'] : 0,
								'ASSIST'	=> (!empty($row7['assist'])) ? $row7['assist'] : 0
							));
						}
						
						// challangee members
						$EEmembers	= $group->members('get_members', $row['group2']);
						foreach ($EEmembers as $EEmember)
						{
							$sql8		= "SELECT * FROM " . TUSER_DATA . " WHERE group_id = {$row['group2']} AND user_id = {$EEmember} AND group_uid = " . $row['group_uid'];
							$result8	= $db->sql_query_limit($sql8, 1);
							$row8		= $db->sql_fetchrow($result8);
							$db->sql_freeresult($result8);
							
							$template->assign_block_vars('block_match.ee_ustats', array(
								'USERNAME'	=> getusername($EEmember),
								'USER_ID'	=> $EEmember,
								'KILL'		=> (!empty($row8['kills'])) ? $row8['kills'] : 0,
								'DEAD'		=> (!empty($row8['morti'])) ? $row8['morti'] : 0,
								'ASSIST'	=> (!empty($row8['assist'])) ? $row8['assist'] : 0
							));
						}
					}
					
					// MATCH CHAT (themidid is the tournament id...)
					if ($userwar == 69 && $tournmtwar == 0)
					{
						$xflag = 1;
					}
					else if ($userwar == 69 && $tournmtwar == 69)
					{
						$xflag = 3;
					}
					else if ($userwar == 0 && $tournmtwar == 69)
					{
						$xflag = 2;
					}
					else
					{
						$xflag = 0;
					}
					
					$xoppon		= ($rowx['group_position'] & 1) ? $rowx['group_position'] + 1 : $rowx['group_position'] - 1;
					$modtpos	= $row['group1'] . $row['group2'] . '_mod';
					$modtpos2	= $row['group2'] . $row['group1'] . '_mod';
										
					$sql7		= "SELECT * FROM " . RIVALS_MATCH_CHAT . " WHERE id_match = {$row['id_torneo']} AND 
								(tposition = {$rowx['group_position']} OR tposition = {$xoppon} OR tposition = '{$modtpos}' OR tposition = '{$modtpos2}') 
								AND chat_flag = {$xflag} AND tround = {$rowx['group_bracket']} ORDER BY chat_time DESC";				
					$result7	= $db->sql_query($sql7);
					$i7	= 0;
					while($row7 = $db->sql_fetchrow($result7))
					{
						$template->assign_block_vars('block_match_chat', array(
							'POSTER_URL'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row7['id_writer']),
							'POSTER_NAME'	=> getusername($row7['id_writer']),
							'CLAN_NAME'		=> ($tournament->data('tournament_userbased', $row['id_torneo']) == 0) ? (($row7['id_clan'] > 0) ? $group->data('group_name', $row7['id_clan']) : $user->lang['LADDER_STAFF']) : ((strpos($row7['tposition'], '_mod') !== false) ? $user->lang['LADDER_STAFF'] : ''),
							'CLAN_URL'		=> ($row7['id_clan'] > 0) ? append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $row7['id_clan']) : '#',
							'CHAT_TIME'		=> $user->format_date($row7['chat_time']),
							'CHAT_TEXT'		=> generate_text_for_display($row7['chat_text'], $row7['bbcode_uid'], $row7['bbcode_bitfield'], $row7['bbcode_options'])
						));
						$i7++;
					}
					$db->sql_freeresult($result7);
				}
/***************************
*	ACTION
*************/				
				if ($submit)
				{
					if ($tournmtwar == 69)
					{
						$uid		= (int) request_var('editedmatch_id', 0);
						$tourid		= (int) request_var('tourid', 0);
						$mvp1		= (int) request_var('mvp1', 0);
						$mvp2		= (int) request_var('mvp2', 0);
						$mvp3		= (int) request_var('mvp3', 0);
						$winner		= (int) request_var('winner', 0);
						$er_score	= (int) request_var('er_score', 0);
						$ee_score	= (int) request_var('ee_score', 0);
						$er_scoreHM	= (int) request_var('er_score_home', 0);
						$ee_scoreHM	= (int) request_var('ee_score_home', 0);
						$er_id		= (int) request_var('er_id', 0);
						$ee_id		= (int) request_var('ee_id', 0);
						
						
						// advanced stats
						if ($tournament->data('tournament_advstats', $tourid) == 1)
						{
							$advstats	= isset($_POST['stats']) ? $_POST['stats'] : array();
							foreach ($advstats as $ID_utente => $values)
							{
								$xkill	= (!empty($values['kills'])) ? $values['kills'] : 0;
								$xdeads	= (!empty($values['morti'])) ? $values['morti'] : 0;
								$xasist = (!empty($values['assist'])) ? $values['assist'] : 0;
						
								//CHECK FOR NUMBERS ENTRIES
								if (!is_numeric($xkill)
								|| !is_numeric($xdeads)
								|| !is_numeric($xasist))
								{
									$redirect_url = append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=edit_match&amp;tmnt=69&amp;mid={$uid}");
									meta_refresh(4, $redirect_url);
									trigger_error(sprintf($user->lang['STATS_SOLO_NUMERI'], '<a href="' . $redirect_url . '">', '</a>'));
								}
						
								//check if the user exist
								$sqlu		= "SELECT * FROM " . TUSER_DATA . " WHERE group_uid = {$uid} AND user_id = " . $ID_utente;
								$resultu	= $db->sql_query_limit($sqlu, 1);
								$rowu		= $db->sql_fetchrow($resultu);
								$db->sql_freeresult($resultu);
								
								if (!empty($rowu['user_id'])) /* if are already reported update it */
								{
									$sql_array4	= array(
										'kills'		=> $xkill,
										'morti'		=> $xdeads,
										'assist'	=> $xasist,
									);
									$sql = "UPDATE " . TUSER_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE group_uid = {$uid} AND user_id = " . $ID_utente;
									$db->sql_query($sql);
								}
								else if (empty($rowu['user_id']) && ($xkill + $xdeads + $xasist) > 1) /* if do not are reported before but now have at least a stats > 0 insert it */
								{
									// find the clan of this user
									$sqlQ		= "SELECT grt.group_id, grt.group_uid FROM " . TGROUPS_TABLE . " AS grt 
												LEFT JOIN " . USER_CLAN_TABLE . " AS uct ON uct.group_id = grt.group_id 
												WHERE grt.group_uid = {$uid} AND uct.user_id = " . $ID_utente;
									$resultQ	= $db->sql_query_limit($sqlQ, 1);
									$rowQ		= $db->sql_fetchrow($resultQ);
									$clanid		= $rowQ['group_id'];
									$db->sql_freeresult($resultQ);
									
									$sql_array4	= array(
										'group_uid'		=> $uid,
										'tournament_id'	=> $tourid,
										'user_id'		=> $ID_utente,
										'group_id'		=> $clanid,
										'kills'			=> $xkill,
										'morti'			=> $xdeads,
										'assist'		=> $xasist,
										'conferma1'		=> 1,
										'conferma2'		=> 0
									);
									$sql = "INSERT INTO " . TUSER_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array4);
									$db->sql_query($sql);
								}
							}
						}
						
						// update match						
						$sql_array3	= array(
							'vincitore'		=> $winner,
							'punti1'		=> $er_score,
							'punti2'		=> $ee_score,
							'mvp1'			=> $mvp1,
							'mvp2'			=> $mvp2,
							'mvp3'			=> $mvp3,
							'home_punti1'	=> $er_scoreHM,
							'home_punti2'	=> $ee_scoreHM,
							'match_problem'	=> 0, // remove the contested flag
						);
						$sql = "UPDATE " . TMATCHES . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE group_uid = " . $uid;
						$db->sql_query($sql);
						
						// add log reference
						$theer_name	= ($userwar == 69) ? getusername($er_id) : $group->data('group_name', $er_id);
						$theee_name	= ($userwar == 69) ? getusername($ee_id) : $group->data('group_name', $ee_id);
						add_log('mod', 0, 0, 'LOG_MATCH_EDITED', $uid, $theer_name, $theee_name);
					}
					else /* NOT TOURNAMENT MATCH */
					{
						$idmatch	= (int) request_var('editedmatch_id', 0);
						$winner		= (int) request_var('winner', 0);
						$xladder	= (int) request_var('xladder', 0);
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
						$er_team	= (string) utf8_normalize_nfc(request_var('er_team', '', true));
						$ee_team	= (string) utf8_normalize_nfc(request_var('ee_team', '', true));
						
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
				
						if ($userwar == 69)
						{
							$sql_array3	= array(
								'1vs1_winner'			=> $winner,
								'1vs1_challanger_score'	=> (int) $er_score,
								'1vs1_challangee_score'	=> (int) $ee_score,
								'mode1_score_er'		=> (int) $er_scorem1,
								'mode1_score_ee'		=> (int) $ee_scorem1,
								'mode2_score_er'		=> (int) $er_scorem2,
								'mode2_score_ee'		=> (int) $ee_scorem2,
								'mode3_score_er'		=> (int) $er_scorem3,
								'mode3_score_ee'		=> (int) $ee_scorem3,
								'1vs1_challanger_team'	=> (string) $er_team,
								'1vs1_challangee_team'	=> (string) $ee_team,
								'1vs1_contestested'		=> 0, // remove the contested flag
							);
							$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE 1vs1_id = {$idmatch}";
							$db->sql_query($sql);
						}
						else
						{
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
										$redirect_url = append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=edit_match&amp;mid={$idmatch}");
										meta_refresh(4, $redirect_url);
										trigger_error(sprintf($user->lang['STATS_SOLO_NUMERI'], '<a href="' . $redirect_url . '">', '</a>'));
									}
							
									//check if the user exist
									$sqlu		= "SELECT * FROM " . MATCH_TEMP_USTATS . " WHERE id_match = {$idmatch} AND user_id = " . $ID_utente;
									$resultu	= $db->sql_query_limit($sqlu, 1);
									$rowu		= $db->sql_fetchrow($resultu);
									$db->sql_freeresult($resultu);
									
									if (!empty($rowu['user_id'])) /* if are already reported update it */
									{
										$sql_array4	= array(
											'kills'		=> $xkill,
											'deads'		=> $xdeads,
											'assists'	=> $xasist,
											'goal_f'	=> $xgoalf,
											'goal_a'	=> $xgoala
										);
										$sql = "UPDATE " . MATCH_TEMP_USTATS . " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE id_match = {$idmatch} AND user_id = {$ID_utente}";
										$db->sql_query($sql);
									}
									else if (empty($rowu['user_id']) && ($xkill + $xdeads + $xasist + $xgoalf + $xgoala) > 1) /* if do not are reported before but now have at least a stats > 0 insert it */
									{
										$sql_array4	= array(
											'id_match'	=> $idmatch,
											'id_ladder'	=> $xladder,
											'user_id'	=> $ID_utente,
											'kills'		=> $xkill,
											'deads'		=> $xdeads,
											'assists'	=> $xasist,
											'goal_f'	=> $xgoalf,
											'goal_a'	=> $xgoala
										);
										$sql = "INSERT INTO " . MATCH_TEMP_USTATS . " SET " . $db->sql_build_array('UPDATE', $sql_array4);
										$db->sql_query($sql);
									}
								}
							}
							
							$sql_array3	= array(
								'match_winner'					=> $winner,
								'match_loser'					=> $loser,
								'match_challanger_score'		=> (int) $er_score,
								'match_challangee_score'		=> (int) $ee_score,
								'match_challanger_score_mode1'	=> (int) $er_scorem1,
								'match_challangee_score_mode1'	=> (int) $ee_scorem1,
								'match_challanger_score_mode2'	=> (int) $er_scorem2,
								'match_challangee_score_mode2'	=> (int) $ee_scorem2,
								'match_challanger_score_mode3'	=> (int) $er_scorem3,
								'match_challangee_score_mode3'	=> (int) $ee_scorem3,
								'mvp1'							=> (int) $mvp1,
								'mvp2'							=> (int) $mvp2,
								'mvp3'							=> (int) $mvp3,
								'challenger_team'				=> (string) $er_team,
								'challengee_team'				=> (string) $ee_team,
								'match_status'					=> 0, // remove the contested flag
							);
							$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array3) . " WHERE match_id = {$idmatch}";
							$db->sql_query($sql);
						}
						
						// add log reference				
						$theer_name	= ($userwar == 69) ? getusername($er_id) : $group->data('group_name', $er_id);
						$theee_name	= ($userwar == 69) ? getusername($ee_id) : $group->data('group_name', $ee_id);
						add_log('mod', 0, 0, 'LOG_MATCH_EDITED', $idmatch, $theer_name, $theee_name);
					}
					
					$redirect_url = append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=main");
					meta_refresh(2, $redirect_url);
					trigger_error('MATCH_UPDATED');
				}
				
				// CHAT insert message
				if ($chatting)
				{
					$textchat	= (string) utf8_normalize_nfc(request_var('chat_text', '', true));
					
					$uid = $bitfield = $options = '';
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($textchat, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
					
					// if tournament get tournament id here
					if ($tournmtwar == 69)
					{
						$sql	= "SELECT * FROM " . TMATCHES . " WHERE group_uid = " . $themidid;
						$result	= $db->sql_query_limit($sql, 1);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						
						$xtourid	= $row['id_torneo'];
						$g1id		= $row['group1'];
						$g2id		= $row['group2'];
						
						// Get the match tournament information.
						$sqlx		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_uid = {$themidid} AND group_position MOD 2"; /* get only not pair */
						$resultx	= $db->sql_query_limit($sqlx, 1);
						$rowx		= $db->sql_fetchrow($resultx);
						$db->sql_freeresult($resultx);
						
						$ttround	= $rowx['group_bracket'];
					}
					
					$midfixed	= ($tournmtwar == 69) ? $xtourid : $themidid;
					$chatflag	= ($tournmtwar == 69) ? (($userwar == 69) ? 3 : 2) : (($userwar == 69) ? 1 : 0);
					$xposition	= ($tournmtwar == 69) ? "{$g1id}{$g2id}_mod" : 999999;
					$ttround	= ($tournmtwar == 69) ? $ttround : 1;
					
					$sql_array	= array(
						'id_match'			=> $midfixed,
						'id_writer'			=> $user->data['user_id'],
						'id_clan'			=> 0,
						'chat_text'			=> $textchat,
						'chat_flag'			=> $chatflag,
						'tposition'			=> $xposition,
						'tround'			=> $ttround,
						'chat_time'			=> time(),
						'bbcode_uid'		=> $uid,
						'bbcode_bitfield'	=> $bitfield,
						'bbcode_options'	=> $options			
					);
					$sql	= "INSERT INTO " . RIVALS_MATCH_CHAT . " " . $db->sql_build_array('INSERT', $sql_array);
					$db->sql_query($sql);
					
					if ($userwar == 69 && $tournmtwar == 0)
					{
						// Get the match information.
						$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $themidid;
						$result	= $db->sql_query_limit($sql, 1);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						
						$theER	= $row['1vs1_challanger'];
						$theEE	= $row['1vs1_challangee'];
						
						$yfix	= '&amp;user=69';
					}
					else if ($userwar == 0 && $tournmtwar == 69)
					{
						// Get the tournament match information. Repopulate.
						$sql	= "SELECT * FROM " . TMATCHES . " WHERE group_uid = " . $themidid;
						$result	= $db->sql_query_limit($sql, 1);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						
						$theER	= $group->data('user_id', $row['group1']);
						$theEE	= $group->data('user_id', $row['group2']);
						
						$yfix	= '&amp;tmnt=69';
					}
					else if ($userwar == 69 && $tournmtwar == 69)
					{
						// Get the tournament match information. Repopulate.
						$sql	= "SELECT * FROM " . TMATCHES . " WHERE group_uid = " . $themidid;
						$result	= $db->sql_query_limit($sql, 1);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						
						$theER	= $row['group1'];
						$theEE	= $row['group2'];
						
						$yfix	= '&amp;tmnt=69&amp;user=69';
					}
					else
					{
						// Get the match information.
						$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $themidid;
						$result	= $db->sql_query_limit($sql, 1);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						
						$theER	= $group->data('user_id', $row['match_challenger']);
						$theEE	= $group->data('user_id', $row['match_challengee']);
						
						$yfix	= '';
					}
					
					// send pm to clans leader
					$subject	= $user->lang['MOD_CHATWRITE'];
					$message	= $user->lang['MOD_CHATWRITETEXT'];
					insert_pm($theER, $user->data, $subject, $message);
					insert_pm($theEE, $user->data, $subject, $message);
					
					$redirect_url = append_sid("{$phpbb_root_path}mcp.$phpEx", "i=rivals&amp;mode=edit_match{$yfix}&amp;mid=" . $themidid);
					redirect($redirect_url);
				}
			break;			
			case 'ipwhois':
				$this->tpl_name		= 'rivals/mcp_rivals_whois';
				$this->page_title	= 'MCP_RIVALS';
				
				$ip = request_var('ip', '');
				include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

				$template->assign_vars(array(
					'WHOIS'			=> user_ipwhois($ip),
				));
			break;
		}
	}
}

?>