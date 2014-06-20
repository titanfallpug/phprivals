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

define('STANDARD_LADDER', 0);
define('DECERTO_LADDER', 1);
define('CPC_LADDER', 2);
define('FOOTBALL_LADDER', 3);

class ucp_rivals
{
	var	$u_action;
	var $u_accept;
	var $u_sfida;

	function main($id, $mode)
	{
		global	$db, $user, $template, $config;
		global	$phpbb_root_path, $phpEx;

		// Include Rivals' classes and phpBB functions.
		include($phpbb_root_path . 'rivals/classes/class_group.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_tournament.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_ladder.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/function_security.' . $phpEx);
		include($phpbb_root_path . 'rivals/functions.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

		// Setup the language.
		$user->add_lang('mods/lang_rivals');

		// Switch between the modes to manage Rivals.
		switch($mode)
		{
			case 'main' :
				$this->tpl_name		= 'rivals/ucp_rivals_main';
				$this->page_title	= 'UCP_RIVALS';

				$group	= new group();
				$ladder	= new ladder();

				// Check to see if the user has a group.
				if (empty($group->data['group_id']))
				{		
					// Get a list of the user's other groups.
					$sql	= "SELECT gd.*, ud.user_id FROM " . CLANS_TABLE . " gd, " . USER_CLAN_TABLE . " ud WHERE gd.clan_closed = 0 AND ud.group_leader != 0 AND ud.user_id = {$user->data['user_id']}
							AND ud.group_id = gd.group_id ORDER BY gd.group_name";
					$result	= $db->sql_query($sql);
					$i		= 0;
					while($row = $db->sql_fetchrow($result))
					{
						// Assign the groups to the template.
						$template->assign_block_vars('block_switch', array (
							'U_ACTION' 		=> $this->u_action . '&amp;switch=' . $row['group_id'],
							'GROUP_NAME'	=> $row['group_name'])
						);
						$i++;
					}
					
					// Clan switch action
					$switch	= (int) request_var('switch', 0);
					if (!empty($switch))
					{
						// Quick group switching feature. Check for hacking attempt.
						$sql	= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_leader != 0 AND user_id = {$user->data['user_id']} AND group_id = " . $switch;
						$result	= $db->sql_query($sql);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (sizeof($row) == 0)
						{
							// This group is not theirs. Redirect them back to the Clan CP.
							redirect($this->u_action);
						}

						// Update the user's session.
						$sql	= "UPDATE " . USERS_TABLE . " SET group_session = {$switch} WHERE user_id = " . $user->data['user_id'];
						$db->sql_query($sql);

						// Completed. Redirect the user back to the Clan CP.
						redirect($this->u_action);
					}
					
					//statistiche 1vs1
					$sql		= "SELECT COUNT(1vs1_id) AS pendings FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
								AND 1vs1_accepted = 0";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$pendings	= $row['pendings'];
					$db->sql_freeresult($result);
					
					$sql		= "SELECT COUNT(1vs1_id) AS ongoings FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
								AND 1vs1_accepted = 1 AND 1vs1_confirmer = 0";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$ongoings	= $row['ongoings'];
					$db->sql_freeresult($result);
					
					$sql		= "SELECT COUNT(1vs1_id) AS fnishes FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
								AND 1vs1_accepted = 1 AND 1vs1_confirmer > 0";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$fnishes	= $row['fnishes'];
					$db->sql_freeresult($result);
					
					
					
					$template->assign_vars(array(
						'UWELCOMETXT'	=> sprintf($user->lang['WELCOMETXT'], $user->data['username']),
						'1VS1PENDING'	=> (!empty($pendings)) ? $pendings : 0,
						'1VS1ONGOING'	=> (!empty($ongoings)) ? $ongoings : 0,
						'1VS1FINISHED'	=> (!empty($fnishes)) ? $fnishes : 0,
						'INGROUP'		=> false
					));
					
				}
				else
				{
					$switch	= (int) request_var('switch', 0);
					if (!empty($switch))
					{
						// Quick group switching feature. Check for hacking attempt.
						$sql	= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_leader != 0 AND user_id = {$user->data['user_id']} AND group_id = " . $switch;
						$result	= $db->sql_query($sql);
						$row	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (sizeof($row) == 0)
						{
							// This group is not theirs. Redirect them back to the Clan CP.
							redirect($this->u_action);
						}

						// Update the user's session.
						$sql	= "UPDATE " . USERS_TABLE . " SET group_session = {$switch} WHERE user_id = " . $user->data['user_id'];
						$db->sql_query($sql);

						// Completed. Redirect the user back to the Clan CP.
						redirect($this->u_action);
					}

					// Get a list of the user's other groups.
					$sql	= "SELECT gd.*, ud.user_id FROM " . CLANS_TABLE . " gd, " . USER_CLAN_TABLE . " ud WHERE gd.clan_closed = 0 AND ud.group_leader != 0 AND ud.user_id = {$user->data['user_id']}
							AND ud.group_id = gd.group_id AND gd.group_id <> " . (int) $group->data['group_id'];
					$result	= $db->sql_query($sql);

					$i	= 0;
					while($row = $db->sql_fetchrow($result))
					{
						// Assign the groups to the template.
						$template->assign_block_vars('block_switch',array(
							'U_ACTION'		=> $this->u_action . '&amp;switch=' . $row['group_id'],
							'GROUP_NAME'	=> $row['group_name']
						));

						$i++;
					}
					$db->sql_freeresult($result);

					// Get the challenges.
					$sql		= "SELECT COUNT(challenge_id) AS the_challenges FROM " . CHALLENGES_TABLE . " WHERE challengee = " . $group->data['group_id'];
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$challenges	= $row['the_challenges'];
					$db->sql_freeresult($result);

					// Get the on-going challenges.
					$sql		= "SELECT COUNT(match_id) AS the_ogmatches FROM " . MATCHES_TABLE . " WHERE match_finishtime = 0 AND (match_challenger = {$group->data['group_id']} OR match_challengee = {$group->data['group_id']})";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$ogmatches	= $row['the_ogmatches'];
					$db->sql_freeresult($result);

					$sql		= "SELECT COUNT(match_id) AS the_matches FROM " . MATCHES_TABLE . " WHERE match_finishtime > 0 AND (match_challenger = {$group->data['group_id']} OR match_challengee = {$group->data['group_id']})";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$matches	= $row['the_matches'];
					$db->sql_freeresult($result);
					
					//statistiche 1vs1
					$sql		= "SELECT COUNT(1vs1_id) AS pendings FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
								AND 1vs1_accepted = 0";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$pendings	= $row['pendings'];
					$db->sql_freeresult($result);
					
					$sql		= "SELECT COUNT(1vs1_id) AS ongoings FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
								AND 1vs1_accepted = 1 AND 1vs1_confirmer = 0";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$ongoings	= $row['ongoings'];
					$db->sql_freeresult($result);
					
					$sql		= "SELECT COUNT(1vs1_id) AS fnishes FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']})
								AND 1vs1_accepted = 1 AND 1vs1_confirmer > 0";
					$result		= $db->sql_query($sql);
					$row		= $db->sql_fetchrow($result);
					$fnishes	= $row['fnishes'];
					$db->sql_freeresult($result);

				// Check if we need to remove any challenges.
					$sqlx		= "SELECT * FROM " . CHALLENGES_TABLE . " WHERE challenger = " . $group->data['group_id'];
					$resultx	= $db->sql_query($sqlx);
					while($rowx = $db->sql_fetchrow($resultx))
					{
						// Get the time difference in days.
						$limittime	= $rowx['challenge_posttime'] + ($config['rivals_kickout_day']*60*60*24);

						if (time() > $limittime)
						{
							// check if are ranked and if the challangee clan are in frost status
							if ($rowx['challenge_unranked'] == 0 && $group->data('group_frosted', $rowx['challengee'], $rowx['challenge_ladder']) == 0)
							{
								// OK are ranked and the clan isnt hibernated so give him the penality
								$ladderd	= $ladder->get_roots($rowx['challenge_ladder']);
								
								if ($ladderd['SUBLADDER_RAKING'] == 2)
								{
									$sql_v		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = {$rowx['challengee']} AND group_ladder = " . $rowx['challenge_ladder'];
									$result_v	= $db->sql_query($sql_v);
									$row_v		= $db->sql_fetchrow($result_v);
									$db->sql_freeresult($result_v);
									
									$penality	= ($row_v['group_score'] >= 56) ? ceil($row_v['group_score'] / 10) : 0;
								}
								else
								{
									$penality	= $config['rivals_inactiv_penality'];
								}
								
								$sql = "UPDATE " . GROUPDATA_TABLE . " SET group_score = group_score - {$penality} WHERE group_id = {$rowx['challengee']} AND group_ladder = " . $rowx['challenge_ladder'];
								$db->sql_query($sql);
							}
							
							// Send a PM to the group owner to let them know.
							$challengee	= $group->data('group_name', $rowx['challengee']);
							$subject	= $user->lang['PM_CHALLENGEDELETED'];
							$message	= sprintf($user->lang['PM_CHALLENGEDELETEDTXT'], $challengee, $challengee);
							insert_pm($group->data['user_id'], $user->data, $subject, $message);
							
							// The challenge hasent been accepted within day limit. Delete.
							$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_id = " . $rowx['challenge_id'];
							$db->sql_query($sql);
						}
					}
					$db->sql_freeresult($resultx);
					
				// Check if we need to remove any challenges 1vs1.
					$sql3		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_challanger = {$user->data['user_id']} AND 1vs1_accepted = 0";
					$result3	= $db->sql_query($sql3);
					if ($row3 = $db->sql_fetchrow($result))
					{
						do
						{
							if (!empty($row3['1vs1_challangee']))
							{
								// Get the time difference in days.
								$limittimeuser	= $row3['start_time'] + ($config['rivals_kickout_day']*60*60*24);

								if (time() > $limittimeuser)
								{
									// check if are ranked and if the challangee clan are in frost status
									if ($row3['1vs1_unranked'] == 0 && user_frosted($row3['1vs1_challangee'], $row3['1vs1_ladder']) == 0)
									{
										// OK are ranked and the clan isnt hibernated so give him the penality
										$ladderdu	= $ladder->get_roots($row3['1vs1_ladder']);
										
										if ($ladderdu['SUBLADDER_RAKING'] == 2)
										{
											$sql_v		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$row3['1vs1_challangee']} AND 1vs1_ladder = " . $row3['1vs1_ladder'];
											$result_v	= $db->sql_query($sql_v);
											$row_v		= $db->sql_fetchrow($result_v);
											$db->sql_freeresult($result_v);
											
											$penality	= ($row_v['user_score'] >= 56) ? ceil($row_v['user_score'] / 10) : 0;
										}
										else
										{
											$penality	= $config['rivals_inactiv_penality'];
										}
										
										$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET user_score = user_score - {$penality} WHERE user_id = {$row3['1vs1_challangee']} AND 1vs1_ladder = " . $row3['1vs1_ladder'];
										$db->sql_query($sql);
									}
									
									// Send a PM to the group owner to let them know.
									$challenge3	= getusername($row3['1vs1_challangee']);
									$subject	= $user->lang['PM_CHALLENGEDELETED'];
									$message	= sprintf($user->lang['PM_CHALLENGEDELETEDTXT'], $challenge3, $challenge3);
									insert_pm($user->data['user_id'], $user->data, $subject, $message);
									
									// The challenge hasent been accepted within 24 hours. Delete.
									$sql	= "DELETE FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $row3['1vs1_id'];
									$db->sql_query($sql);
								}
							}
						}
						while($row3 = $db->sql_fetchrow($result3));
					}
					$db->sql_freeresult($result3);
					
				// Check if there are matches that wait too long to be confirmed - CLAN
					$sqly		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_confirmed = 0 AND match_status != 2 AND match_reported = " . $group->data['group_id'];
					$resulty	= $db->sql_query($sqly);
					while($rowy = $db->sql_fetchrow($resulty))
					{
						$onedaymore		= 60*60*24;
						$maxconfirmtime	= $rowy['match_reptime'] + ($config['rivals_maxreporthours']*60*60);
						
						if (time() > $maxconfirmtime && time() < ($maxconfirmtime + $onedaymore))
						{
							// Send a PM to the group owner to let them know.
							$exopponed	= ($rowy['match_challenger'] == $group->data['group_id']) ? $rowy['match_challengee'] : $rowy['match_challenger'];
							$yourclan	= $group->data('group_name', $group->data['group_id']);
							$oppleader	= $group->data('user_id', $exopponed);
							$subject	= $user->lang['PM_MATCH_UNCONFIRM_ADV'];
							$message	= sprintf($user->lang['PM_MATCH_UNCONFIRM_ADV_TXT'], $yourclan);
							insert_pm($oppleader, $user->data, $subject, $message);
						}
						else if (time() > ($maxconfirmtime + $onedaymore))
						{
							$exopponed	= ($rowy['match_challenger'] == $group->data['group_id']) ? $rowy['match_challengee'] : $rowy['match_challenger'];
							
							// Now put it like contested and change the reporter
							$sql_array	= array(
								'match_status'		=> 2,
								'match_reported'	=> $exopponed,
							);
							$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE match_id = " . $rowy['match_id'];
							$db->sql_query($sql);
						}
					}
					$db->sql_freeresult($resulty);
					
				// Check if there are matches that wait too long to be confirmed - USER
					$sqlz		= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_confirmer = 0 AND 1vs1_contestested = 0 AND 1vs1_reporter = " . $user->data['user_id'];
					$resultz	= $db->sql_query($sqlz);
					while($rowz = $db->sql_fetchrow($resultz))
					{
						$onedaymore		= 60*60*24;
						$maxconfirmtime	= $rowz['rep_time'] + ($config['rivals_maxreporthours']*60*60);
						
						if (time() > $maxconfirmtime && time() < ($maxconfirmtime + $onedaymore))
						{
							// Send a PM to the group owner to let them know.
							$exopponed	= ($rowz['1vs1_challanger'] == $user->data['user_id']) ? $rowz['1vs1_challangee'] : $rowz['1vs1_challanger'];
							$yourclan	= $user->data['username'];
							$subject	= $user->lang['PM_MATCH_UNCONFIRM_ADV'];
							$message	= sprintf($user->lang['PM_MATCH_UNCONFIRM_ADV_TXT'], $yourclan);
							insert_pm($exopponed, $user->data, $subject, $message);
						}
						else if (time() > ($maxconfirmtime + $onedaymore))
						{
							// Now put it like contested and change the reporter
							$exopponed	= ($rowz['1vs1_challanger'] == $user->data['user_id']) ? $rowz['1vs1_challangee'] : $rowz['1vs1_challanger'];
							
							$sql_array	= array(
								'1vs1_contestested'	=> 1,
								'1vs1_reporter'		=> $exopponed,
							);
							$sql = "UPDATE " . ONEVSONE_MATCH_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE 1vs1_id = " . $rowz['1vs1_id'];
							$db->sql_query($sql);
						}
					}
					$db->sql_freeresult($resultz);

					// Assign the other variables to the template.
					$template->assign_vars(array(
						'UWELCOMETXT'		=> sprintf($user->lang['WELCOME_CCPTXT'], '<span class="rivalwinner">', $group->data['group_name'], '</span>'),
						'GROUP_NAME'		=> $group->data['group_name'],
						'INGROUP'			=> true,
						'CHALLENGES'		=> (!empty($challenges)) ? $challenges : 0,
						'ONGOING_MATCHES'	=> (!empty($ogmatches)) ? $ogmatches : 0,
						'FINISHED_MATCHES'	=> (!empty($matches)) ? $matches : 0,
						'1VS1PENDING'		=> (!empty($pendings)) ? $pendings : 0,
						'1VS1ONGOING'		=> (!empty($ongoings)) ? $ongoings : 0,
						'1VS1FINISHED'		=> (!empty($fnishes)) ? $fnishes : 0,
						// UCP LINKS
						'U_EDIT_CLAN'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=edit_group"),
						'U_SMSG'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchcomm"),
						'U_MEN_MEMBERS'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=group_members"),
						'U_ROSTER_LINUP'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=set_roster"),
						'U_FIND_MATCH'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_finder"),
						'U_ADD_MATCH'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=add_challenge"),
						'U_PENDING_MATCH'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=challenges"),
						'U_REPORT_MATCHES'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches"),
						'U_CONFIRM_MATCH'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_confirm"),
						'U_CLAN_TOURNAMENT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments"),
						'U_ADD_USERM'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=add_challenge&amp;ulad=true"),
						'U_USER_MATCH'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matches_oneone"),
						'U_USER_TOURN'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments_oneone"),
						'U_MATCH_CHAT'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat"),
						'U_SEND_TICKET'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=ticket")
					));
				}
				
	/***************
	*	HIBERNATION
	***/
				$frost		= (int) request_var('frost', 0);
				$clan_id	= (int) request_var('clan_id', 0);
				$userfrost	= (int) request_var('user_id', 0);
				$ladder_id	= (int) request_var('ladder_id', 0);
				$confirm	= (int) request_var('confirm', 0);
			// clan based
				if ($frost === 1 && $clan_id != 0 && $userfrost == 0)
				{
					// check if you try to frost clan aren't yours
					$sql	= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_leader != 0 AND user_id = {$user->data['user_id']} AND group_id = " . $clan_id;
					$result	= $db->sql_query($sql);
					$row	= $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if (sizeof($row) == 0)
					{
						// This group is not theirs. Redirect them back to the Clan CP.
						redirect($this->u_action);
					}
					else if ($confirm == 0)
					{
						$ladderd	= $ladder->get_roots($ladder_id);
						$badpoints	= ($ladderd['SUBLADDER_RAKING'] == 2) ? 25 : $config['rivals_frost_cost'];
						
						$confirmurl	= append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main&amp;clan_id={$clan_id}&amp;ladder_id={$ladder_id}&amp;frost=1&amp;confirm=1");
						$no_url		= append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id=" . $ladder_id);

						trigger_error(sprintf($user->lang['HIBERNATION_CONFIRM_TXT'], $badpoints, '<a href="' . $confirmurl . '" class="rvbutton2">' . $user->lang['CONFIRM'] . '</a>', '<a href="' . $no_url . '" class="rvbutton2">' . $user->lang['ANNULLA'] . '</a>'));
					}
					else
					{
						$ladderd	= $ladder->get_roots($ladder_id);
						$badpoints	= ($ladderd['SUBLADDER_RAKING'] == 2) ? 25 : $config['rivals_frost_cost'];
						
						// Update the status to hibernated
						$sql_array	= array(
							'group_score'			=> ((current_clanscore($clan_id, $ladder_id, false) - $badpoints) < 50) ? 50 : current_clanscore($clan_id, $ladder_id, false) - $badpoints,
							'group_frosted'			=> 1,
							'group_frosted_time'	=> time(),
						);
						$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = {$clan_id} AND group_ladder = {$ladder_id}";
						$db->sql_query($sql);
						
						//Check if the clan are the ladder no.1
						$xorder		= ($ladderd['SUBLADDER_RAKING'] == 1) ? ' ORDER BY group_current_rank ASC' : ' ORDER BY group_score DESC';
						
						$sql		= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id}" . $xorder;
						$result		= $db->sql_query_limit($sql, 1);
						$row		= $db->sql_fetchrow($result);
						$thenoone	= $row['group_id'];
						$db->sql_freeresult($result);
							
						//if yes put him in second place
						if ($clan_id == $thenoone)
						{
							//select the second place of ladder
							$sql_cpc	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = {$ladder_id} AND group_id != {$clan_id} " . $xorder;
							$result_cpc	= $db->sql_query($sql_cpc);
							$row_cpc	= $db->sql_fetchrow($result_cpc);
							$db->sql_freeresult($result_cpc);
							
							if (!empty($row_cpc['group_id']))
							{
								//update the hibernated new score
								$sql_array4	= array(
									'group_score'	=> (($row_cpc['group_score'] - $badpoints) < 50) ? 50 : $row_cpc['group_score'] - $badpoints,
								);
								$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE group_id = {$clan_id} AND group_ladder = {$ladder_id}";
								$db->sql_query($sql);
								
								//update clans ranks
								$ladder->update_ranks($row_cpc['group_id'], $clan_id, $ladder_id);
							}
						}
						
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
						meta_refresh(4, $redirect_url);
						trigger_error('FROSTED_STATUS_ACTIVATE');
					}				
				}
		// user based
				if ($frost === 1 && $clan_id == 0 && $userfrost != 0)
				{
					// check if you try to frost a user that aren't you
					if ($userfrost != $user->data['user_id'])
					{
						redirect($this->u_action);
					}
					else if ($confirm == 0)
					{
						$ladderd	= $ladder->get_roots($ladder_id);
						$badpoints	= ($ladderd['SUBLADDER_RAKING'] == 2) ? 25 : $config['rivals_frost_cost'];
						
						$confirmurl	= append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main&amp;user_id={$userfrost}&amp;ladder_id={$ladder_id}&amp;frost=1&amp;confirm=1");
						$no_url		= append_sid("{$phpbb_root_path}rivals.$phpEx", "action=subladders&amp;ladder_id=" . $ladder_id);

						trigger_error(sprintf($user->lang['HIBERNATION_CONFIRM_TXT'], $badpoints, '<a href="' . $confirmurl . '" class="rvbutton2">' . $user->lang['CONFIRM'] . '</a>', '<a href="' . $no_url . '" class="rvbutton2">' . $user->lang['ANNULLA'] . '</a>'));
					}
					else
					{
						$ladderd	= $ladder->get_roots($ladder_id);
						$badpoints	= ($ladderd['SUBLADDER_RAKING'] == 2) ? 25 : $config['rivals_frost_cost'];
						
						// Update the status to hibernated
						$sql_array	= array(
							'user_score'	=> ((current_clanscore($userfrost, $ladder_id, true) - $badpoints) < 50) ? 50 : current_clanscore($userfrost, $ladder_id, true) - $badpoints,
							'user_frosted'	=> 1,
							'frosted_time'	=> time(),
						);
						$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE user_id = {$userfrost} AND 1vs1_ladder = {$ladder_id}";
						$db->sql_query($sql);
						
						//Check if the clan are the ladder no.1
						$xorder		= ($ladderd['SUBLADDER_RAKING'] == 1) ? ' ORDER BY user_current_rank ASC' : ' ORDER BY user_score DESC';
						
						$sql		= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id}" . $xorder;
						$result		= $db->sql_query_limit($sql, 1);
						$row		= $db->sql_fetchrow($result);
						$thenoone	= $row['user_id'];
						$db->sql_freeresult($result);
							
						//if yes put him in second place
						if ($userfrost == $thenoone)
						{
							//select the second place of ladder
							$sql_cpc	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = {$ladder_id} AND user_id != {$userfrost} " . $xorder;
							$result_cpc	= $db->sql_query($sql_cpc);
							$row_cpc	= $db->sql_fetchrow($result_cpc);
							$db->sql_freeresult($result_cpc);
							
							if (!empty($row_cpc['user_id']))
							{
								//update the hibernated new score
								$sql_array4	= array(
									'user_score'	=> (($row_cpc['user_score'] - $badpoints) < 50) ? 50 : $row_cpc['user_score'] - $badpoints,
								);
								$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE user_id = {$userfrost} AND 1vs1_ladder = {$ladder_id}";
								$db->sql_query($sql);
								
								//update clans ranks
								$ladder->update_ranks_user($row_cpc['user_id'], $userfrost, $ladder_id);
							}
						}
						
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
						meta_refresh(4, $redirect_url);
						trigger_error('FROSTED_STATUS_ACTIVATE');
					}				
				}
				
				// Clan frosted list
				$sql_j	= "SELECT * FROM " . GROUPDATA_TABLE . " AS gd LEFT JOIN " . USER_CLAN_TABLE . " AS uc ON gd.group_id = {$user->data['group_session']} AND uc.group_id = gd.group_id
						WHERE gd.group_frosted = 1 AND uc.group_leader != 0 AND uc.user_id = {$user->data['user_id']}
						ORDER BY gd.group_frosted_time ASC";
				$result_j	= $db->sql_query($sql_j);
				$j 		= 0;
				while ($row_j = $db->sql_fetchrow($result_j))
				{
					$ladder_data	= $ladder->get_roots($row_j['group_ladder']);
					$diff1	= ($row_j['group_frosted_time'] + (60*60*24*35)) - time();
					$days	= floor($diff1 / (60*60*24));
					$diff	= $diff1 - ($days * (60*60*24));
					$hours	= floor($diff / (60*60));
					$diff	= $diff - ($hours * (60*60));
					$minute	= floor($diff / 60);
					$diff	= $diff - ($minute * 60);
					$secs	= $diff; 
					
					// Assign it to the template.
					$template->assign_block_vars('clan_frost', array(
						'FROST_DA'	=> $user->format_date($row_j['group_frosted_time']),
						'TIME_RISK'	=> ($diff1 < 24*60*60) ? '<span class="rivalwinner">' . $hours . $user->lang['ORE'] . $minute . $user->lang['MINUTI'] . $secs . $user->lang['SECONDI'] . '</span>' : $days . $user->lang['GIORNI'] . $hours . $user->lang['ORE'] . $minute . $user->lang['MINUTI'] . $secs . $user->lang['SECONDI'],
						'PLATFORM' 	=> $ladder_data['PLATFORM_NAME'],
						'LADDER' 	=> $ladder_data['LADDER_NAME'],
						'SUBLADDER' => $ladder_data['SUBLADDER_NAME'],
						'LADDER_ID'	=> $row_j['group_ladder']
					));
					$j++;
				}
				$db->sql_freeresult($result_j);
				
				// User frosted list
				$sql_y	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE user_frosted = 1 AND user_id = {$user->data['user_id']}
						ORDER BY frosted_time ASC";
				$result_y	= $db->sql_query($sql_y);
				$y 		= 0;
				while ($row_y = $db->sql_fetchrow($result_y))
				{
					$ladder_datay	= $ladder->get_roots($row_y['1vs1_ladder']);
					$diff1y	= ($row_y['frosted_time'] + (60*60*24*35)) - time();
					$daysy	= floor($diff1y / (60*60*24));
					$diffy	= $diff1y - ($daysy * (60*60*24));
					$hoursy	= floor($diffy / (60*60));
					$diffy	= $diffy - ($hoursy * (60*60));
					$minutey	= floor($diffy / 60);
					$diffy	= $diffy - ($minutey * 60);
					$secsy	= $diffy; 
					
					// Assign it to the template.
					$template->assign_block_vars('user_frost', array(
						'FROST_DA'	=> $user->format_date($row_y['frosted_time']),
						'TIME_RISK'	=> ($diff1y < 24*60*60) ? '<span class="rivalwinner">' . $hoursy . $user->lang['ORE'] . $minutey . $user->lang['MINUTI'] . $secsy . $user->lang['SECONDI'] . '</span>' : $daysy . $user->lang['GIORNI'] . $hoursy . $user->lang['ORE'] . $minutey . $user->lang['MINUTI'] . $secsy . $user->lang['SECONDI'],
						'PLATFORM' 	=> $ladder_datay['PLATFORM_NAME'],
						'LADDER' 	=> $ladder_datay['LADDER_NAME'],
						'SUBLADDER' => $ladder_datay['SUBLADDER_NAME'],
						'LADDER_ID'	=> $row_y['1vs1_ladder']
					));
					$y++;
				}
				$db->sql_freeresult($result_y);
				
				if ($j >= 1 || $y >= 1)
				{
					$template->assign_vars(array(
						'SHOW_FROZEN_BOX' => true
					));
				}
				else
				{
					$template->assign_vars(array(
						'SHOW_FROZEN_BOX' => false
					));
				}
				
				// DEFROST action
				$sblocca	= (!empty($_POST['sblocca'])) ? true : false;
				if ($sblocca)
				{
					$whodefrost	= request_var('frostlad', array (0 => 0));
					foreach ($whodefrost as $lad_defrost)
					{
						if (isset($lad_defrost) > 0)
						{
							$getlad	= $ladder->get_roots($lad_defrost);
							if ($getlad['SUBLADDER_USERDEF'] == 1)
							{
								$sql_array5	= array(
									'user_frosted'	=> 0,
									'frosted_time'	=> 0,
								);
								$sql = "UPDATE " . ONEVSONEDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array5) . " WHERE user_id = {$user->data['user_id']} AND 1vs1_ladder = {$lad_defrost}";
								$db->sql_query($sql);
							}
							else
							{
								$sql_array5	= array(
									'group_frosted'			=> 0,
									'group_frosted_time'	=> 0,
								);
								$sql = "UPDATE " . GROUPDATA_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array5) . " WHERE group_id = {$group->data['group_id']} AND group_ladder = {$lad_defrost}";
								$db->sql_query($sql);
							}
						}
					}
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
					meta_refresh(2, $redirect_url);
					trigger_error('FROSTED_STATUS_REMOVED');
				}
			break;
			case 'add_challenge' :
				$this->tpl_name		= 'rivals/ucp_rivals_add_challenge';
				$this->page_title	= 'UCP_RIVALS_ADD_CHALLENGE';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_add_challenge.' . $phpEx);
				ucp_rivals_add_challenge($id, $mode, $this->u_action);
			break;
			case 'challenges' :
				$this->tpl_name		= 'rivals/ucp_rivals_challenges';
				$this->page_title	= 'UCP_RIVALS_CHALLENGES';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_challenges.' . $phpEx);
				ucp_rivals_challenges($id, $mode, $this->u_action);
			break;
			case 'edit_group' :
				$this->tpl_name		= 'rivals/ucp_rivals_edit_group';
				$this->page_title	= 'UCP_RIVALS_EDIT_GROUP';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_edit_group.' . $phpEx);
				ucp_rivals_edit_group($id, $mode, $this->u_action);
			break;
			case 'find_group' :
				$this->tpl_name		= 'rivals/ucp_rivals_find_group';
				$this->page_title	= 'UCP_RIVALS_FIND_GROUP';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_find_group.' . $phpEx);
				ucp_rivals_find_group($id, $mode, $this->u_action);
			break;
			case 'matches' :
				$this->tpl_name		= 'rivals/ucp_rivals_matches';
				$this->page_title	= 'UCP_RIVALS_MATCHES';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_matches.' . $phpEx);
				ucp_rivals_matches($id, $mode, $this->u_action);
			break;
			case 'match_finder' :
				$this->tpl_name		= 'rivals/ucp_rivals_match_finder';
				$this->page_title	= 'UCP_RIVALS_MATCH_FINDER';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_match_finder.' . $phpEx);
				ucp_rivals_match_finder($id, $mode, $this->u_action);
			break;
				
			case 'group_members' :
				$this->tpl_name		= 'rivals/ucp_rivals_group_members';
				$this->page_title	= 'UCP_RIVALS_GROUP_MEMBERS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_group_members.' . $phpEx);
				ucp_rivals_group_members($id, $mode, $this->u_action);
			break;
			case 'pending_members' :
				$this->tpl_name		= 'rivals/ucp_rivals_pending_members';
				$this->page_title	= 'UCP_RIVALS_PENDING_MEMBERS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_pending_members.' . $phpEx);
				ucp_rivals_pending_members($id, $mode, $this->u_action);
			break;
			case 'invite_members' :
				$this->tpl_name		= 'rivals/ucp_rivals_invite_members';
				$this->page_title	= 'UCP_RIVALS_INVITE_MEMBERS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_invite_members.' . $phpEx);
				ucp_rivals_invite_members($id, $mode, $this->u_action);
			break;
			case 'ticket' :
				$this->tpl_name		= 'rivals/ucp_rivals_ticket';
				$this->page_title	= 'UCP_RIVALS_TICKET';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_ticket.' . $phpEx);
				ucp_rivals_ticket($id, $mode, $this->u_action);
			break;
			case 'tournaments' :
				$this->tpl_name		= 'rivals/ucp_rivals_tournaments';
				$this->page_title	= 'UCP_RIVALS_TOURNAMENTS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_tournaments.' . $phpEx);
				ucp_rivals_tournaments($id, $mode, $this->u_action);
			break;
			case 'matchcomm' :
				$this->tpl_name		= 'rivals/ucp_rivals_matchcomm';
				$this->page_title	= 'UCP_RIVALS_MATCHCOMM';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_matchcomm.' . $phpEx);
				ucp_rivals_matchcomm($id, $mode, $this->u_action);
			break;
			case 'matchmvp' :
				$this->tpl_name		= 'rivals/ucp_rivals_matches_mvp';
				$this->page_title	= 'UCP_RIVALS_MATCHES_MVP';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_matches_mvp.' . $phpEx);
				ucp_rivals_matches_mvp($id, $mode, $this->u_action);
			break;
			case 'matches_confirm' :
				$this->tpl_name		= 'rivals/ucp_rivals_matches_confirm';
				$this->page_title	= 'UCP_RIVALS_MATCHES_CONFIRM';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_matches_confirm.' . $phpEx);
				ucp_rivals_matches_confirm($id, $mode, $this->u_action);
			break;
			case 'add_challenge_oneone' :
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=add_challenge&amp;ulad=true");
				redirect($redirect_url);
			break;
			case 'matches_oneone' :
				$this->tpl_name		= 'rivals/ucp_rivals_matches_oneone';
				$this->page_title	= 'UCP_RIVALS_MATCHES_ONEONE';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_matches_oneone.' . $phpEx);
				ucp_rivals_matches_oneone($id, $mode, $this->u_action);
			break;
			case 'tournaments_oneone' :
				$this->tpl_name		= 'rivals/ucp_rivals_tournaments_oneone';
				$this->page_title	= 'UCP_RIVALS_TOURNAMENTS_ONEONE';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_tournaments_oneone.' . $phpEx);
				ucp_rivals_tournaments_oneone($id, $mode, $this->u_action);
			break;
			case 'match_chat' :
				$this->tpl_name		= 'rivals/ucp_rivals_match_chat';
				$this->page_title	= 'UCP_RIVALS_MATCH_CHAT';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_match_chat.' . $phpEx);
				ucp_rivals_match_chat($id, $mode, $this->u_action);
			break;
			// Roster mod
			case 'set_roster' :
				$this->tpl_name		= 'rivals/ucp_rivals_set_roster';
				$this->page_title	= 'UCP_RIVALS_SET_ROSTER';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/ucp/rivals/ucp_rivals_set_roster.' . $phpEx);
				ucp_rivals_set_roster($id, $mode, $this->u_action);
			break;
		}
	}
}

?>