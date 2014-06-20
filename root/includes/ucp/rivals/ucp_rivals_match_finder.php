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
 * Match Finder
 * Called from ucp_rivals with mode == 'match_finder'
 */

function ucp_rivals_match_finder($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;
	
	$group			= new group();
	$ladder			= new ladder();
	$error 			= array();
	$clanladderin	= array();
	$userladderin	= array();
	$accept			= array();
	$group_id		= (int) request_var('group_id', 0);
	$user_id		= (int) $user->data['user_id'];
	$refreshor		= false;
	$submit			= (!empty($_POST['submit'])) ? true : false;
	$sfida			= (!empty($_POST['sfida'])) ? true : false;
		
	// Build match waiting request list
	$sql_0		= "SELECT * FROM " . MATCHFINDER_TABLE . " ORDER BY match_time DESC";
	$result_0	= $db->sql_query($sql_0);
    $i			= 0;
    while ($chapa = $db->sql_fetchrow($result_0))
    {
 		$match_expiretime	= ($chapa['match_initaltime'] + (60 * $chapa['match_time']));
		// delete all match that is expired
		if (time() >= $match_expiretime)
		{
			// Group is past it's set match time. Remove the expired match finder entry.
			$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_id = " . $chapa['match_id'];
			$db->sql_query($sql);
			
			$refreshor = true;
		}
		
		$id_dalavorare		= $chapa['match_id'];
		$id_ladder_relativa	= $chapa['match_ladder'];
		$gruppoproposto		= $chapa['match_groupid'];
		$ladder_data		= $ladder->get_roots($id_ladder_relativa);
				
		// clan match
		if ($ladder_data['SUBLADDER_USERDEF'] == 0 && $group->data['group_id'] == $gruppoproposto)
		{
			$template->assign_block_vars('blocco_clanattesa_io', array(
				'GROUP_LADDER'		=> $ladder_data['SUBLADDER_NAME'],
				'NOME_PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'NOME_MAINLADDER'	=> $ladder_data['LADDER_NAME'],
				'CLASSIFICATA'		=> ($chapa['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
				'FINO_ALLE'			=> $user->format_date($match_expiretime),
				'USER_ICON'			=> false,
				'BG_COLOR'			=> ($i % 2) ? 'bg1' : 'bg2',
				'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2'
			));
		}
		
		if ($ladder_data['SUBLADDER_USERDEF'] == 0 && $group->data['group_id'] != $gruppoproposto)
		{
			$template->assign_block_vars('blocco_clanattesa', array(
				/*'U_GROUP'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $gruppoproposto),*/
				'MATCH_ID'			=> $id_dalavorare,
				'GROUP_LADDER'		=> $ladder_data['SUBLADDER_NAME'],
				'NOME_PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'NOME_MAINLADDER'	=> $ladder_data['LADDER_NAME'],
				/*'GROUP_NOME'		=> $group->data('group_name', $gruppoproposto),*/
				'GROUP_WINS'		=> $group->data('group_wins', $gruppoproposto, $id_ladder_relativa),
				'GROUP_LOSSES'		=> $group->data('group_losses', $gruppoproposto, $id_ladder_relativa),
				'CLASSIFICATA'		=> ($chapa['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
				'USER_ICON'			=> false,
				'FINO_ALLE'			=> $user->format_date($match_expiretime),
				'BG_COLOR'			=> ($i % 2) ? 'bg1' : 'bg2',
				'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2'
			));
		}
		
		// user match
		if ($ladder_data['SUBLADDER_USERDEF'] == 1 && $user->data['user_id'] == $gruppoproposto)
		{
			$template->assign_block_vars('blocco_clanattesa_io', array(
				'GROUP_LADDER'		=> $ladder_data['SUBLADDER_NAME'],
				'NOME_PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'NOME_MAINLADDER'	=> $ladder_data['LADDER_NAME'],
				'CLASSIFICATA'		=> ($chapa['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
				'FINO_ALLE'			=> $user->format_date($match_expiretime),
				'USER_ICON'			=> true,
				'BG_COLOR'			=> ($i % 2) ? 'bg1' : 'bg2',
				'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2'
			));
		}
		
		if ($ladder_data['SUBLADDER_USERDEF'] == 1 && $user->data['user_id'] != $gruppoproposto)
		{
			$template->assign_block_vars('blocco_clanattesa', array(
				/*'U_GROUP'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $gruppoproposto),*/
				'MATCH_ID'			=> $id_dalavorare,
				'GROUP_LADDER'		=> $ladder_data['SUBLADDER_NAME'],
				'NOME_PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'NOME_MAINLADDER'	=> $ladder_data['LADDER_NAME'],
				/*'GROUP_NOME'		=> getuserdata('username', $gruppoproposto),*/
				'GROUP_WINS'		=> getuserdata('user_round_wins', $gruppoproposto),
				'GROUP_LOSSES'		=> getuserdata('user_round_losses', $gruppoproposto),
				'CLASSIFICATA'		=> ($chapa['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
				'USER_ICON'			=> true,
				'FINO_ALLE'			=> $user->format_date($match_expiretime),
				'BG_COLOR'			=> ($i % 2) ? 'bg1' : 'bg2',
				'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2'
			));
		}

		$i++;
	}
	$db->sql_freeresult($result_0);
	
	if ($refreshor == true)
	{
		redirect(append_sid( "{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_finder" ));
	}
	else
	{
	}

	// Build the minutes box.
	for ($i = 15; $i <= 240; $i += 15)
	{
		// Assign each minute time to the template.
		$template->assign_block_vars('block_minutes', array('MINUTE' => $i));
	}

	// GET LADDER USER AND CLAN WHERE IS JOINED
	$clanladderin	= (!empty($user->data['group_session'])) ? $group->data['group_ladders'] : array();
	
	$sql9		= "SELECT user_id, 1vs1_ladder FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = " . $user->data['user_id'];
	$result9	= $db->sql_query($sql9);
	while ($row9 = $db->sql_fetchrow($result9))
	{
		$userladderin[] = $row9['1vs1_ladder'];
	}
	$db->sql_freeresult($result9);
	
	// Build the ladder list.
	$sql	= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_locked = 0 AND ladder_parent > 0";
	$result	= $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		// clan ladder
		if (in_array($row['ladder_id'], $clanladderin))
		{	
			// Get the ladder's root detials to show.
			$ladder_data	= $ladder->get_roots($row['ladder_id']);
			
			// Assign each ladder to the template.
			$template->assign_block_vars('block_ladders', array(
				'SUBLADDER_ID'	=> $row['ladder_id'],
				'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'LADDER'		=> $ladder_data['LADDER_NAME'],
				'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME']
			));
		}
		
		// user ladder
		if (in_array($row['ladder_id'], $userladderin))
		{	
			// Get the ladder's root detials to show.
			$ladder_data	= $ladder->get_roots($row['ladder_id']);
			
			// Assign each ladder to the template.
			$template->assign_block_vars('user_ladders', array(
				'SUBLADDER_ID'	=> $row['ladder_id'],
				'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'LADDER'		=> $ladder_data['LADDER_NAME'],
				'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME']
			));
		}
	}
	$db->sql_freeresult($result);
			 
/***********************************************
*	Challange a team action
***********************/
	if ($sfida)
	{
		$accept	= request_var('accepted', array(0 => 0));

		if (!empty($accept))
		{
			foreach ($accept AS $value)
			{
		        // Get match finder data
				$sql_69		= "SELECT * FROM " . MATCHFINDER_TABLE . " WHERE match_id = " . $value;
				$result_69	= $db->sql_query_limit($sql_69, 1);
				$row_69 	= $db->sql_fetchrow($result_69);
				$db->sql_freeresult($result_69);
				
				$xmatchid	= $row_69['match_id'];
				$xladder	= $row_69['match_ladder'];
				$xteam		= $row_69['match_groupid'];
				
				//check if exist
				if (!empty($xmatchid))
				{
					$ladder_data	= $ladder->get_roots($xladder);
					$nome_corto 	= $ladder_data['SUBLADDER_SHORTNM'];
					$tipoladder		= $ladder_data['SUBLADDER_STYLE'];
					$tiporank		= $ladder_data['SUBLADDER_RAKING'];
					
					switch ($ladder_data['SUBLADDER_USERDEF'])
					{
						case 0: /* clan based */
							// check if user try a trick
							if ($xteam != $group->data['group_id'])
							{
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
									$db->sql_freeresult ($result4);
								
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
								
								// CHECK WARS if are they too mutch
								$superdata = time();
								$mindata = ($superdata - 259200);
						 
								$sql_rip	= "SELECT COUNT(match_id) AS checkers FROM " . MATCHES_TABLE . " WHERE (match_posttime BETWEEN {$mindata} AND {$superdata})
											AND ((match_challenger = {$xteam} AND match_challengee = {$group->data['group_id']})
											OR (match_challenger = {$group->data['group_id']} AND match_challengee = {$xteam})) AND match_ladder = " . $xladder;
								$result_rip	= $db->sql_query($sql_rip);
								$row_rip	= $db->sql_fetchrow($result_rip);
								$sborro		= $row_rip['checkers'];
								$db->sql_freeresult($result_rip);
						 
								if ($sborro >= 3)
								{ 
									$error[] = $user->lang['SFIDATO_TROPPO'];
								}
								
								// check if the your group are in the ladder
								$sql_check		= "SELECT group_id, group_ladder FROM " . GROUPDATA_TABLE . " WHERE group_id = {$group->data['group_id']} AND group_ladder = " . $xladder;
								$result_check	= $db->sql_query_limit($sql_check, 1);
								$row_check 		= $db->sql_fetchrow($result_check);
								$db->sql_freeresult($result_check);
								
								if (empty($row_check['group_id']))
								{
									$error[] = $user->lang['NON_SEI_NELLA_LADDER'];
								}
								
								// Accept the challenge. "Move" the challenge to the matches table.
								if (!sizeof($error))
								{
									$sql_array	= array(
										'match_challenger'	=> $group->data['group_id'],
										'match_challengee'	=> $xteam,
										'match_posttime'	=> time(),
										'match_unranked'	=> $row_69['match_unranked'],
										'match_details'		=> $user->lang['MATCH_FROM_MATCHFINDER'],
										'match_ladder'		=> $xladder,
										'mappa_mode1'		=> $mappa1,
										'mappa_mode2'		=> $mappa2,
										'mappa_mode3'		=> $mappa3,
										'mode1'				=> $mode1,
										'mode2'				=> $mode2,
										'mode3'				=> $mode3
									);
									
									$sql	= "INSERT INTO " . MATCHES_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
									$db->sql_query($sql);
									
									$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_id = " . $xmatchid;
									$db->sql_query($sql);
									
									$urls1 = append_sid( "{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_finder" );
									meta_refresh(2, $urls1);
									trigger_error('SFIDA_ACCETTATA');
								}
							}
						break;
						case 1: /* user based */
							// check if user try a trick
							if ($xteam != $user->data['user_id'])
							{
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
									$db->sql_freeresult ($result4);
								
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
								
								// CHECK WARS if are they too mutch
								$superdata = time();
								$mindata = ($superdata - 259200);
						 
								$sql_rip	= "SELECT COUNT(1vs1_id) AS checkers FROM " . ONEVSONE_MATCH_DATA . " WHERE (start_time BETWEEN {$mindata} AND {$superdata})
											AND ((1vs1_challanger = {$xteam} AND 1vs1_challangee = {$group->data['group_id']})
											OR (1vs1_challanger = {$group->data['group_id']} AND 1vs1_challangee = {$xteam})) AND 1vs1_ladder = " . $xladder;
								$result_rip	= $db->sql_query($sql_rip);
								$row_rip	= $db->sql_fetchrow($result_rip);
								$sborro		= $row_rip['checkers'];
								$db->sql_freeresult($result_rip);
						 
								if ($sborro >= 3)
								{ 
									$error[] = $user->lang['SFIDATO_TROPPO'];
								}
								
								// check if the your group are in the ladder
								$sql_check		= "SELECT user_id, 1vs1_ladder FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$user->data['user_id']} AND 1vs1_ladder = " . $xladder;
								$result_check	= $db->sql_query_limit($sql_check, 1);
								$row_check 		= $db->sql_fetchrow($result_check);
								$db->sql_freeresult($result_check);
								
								if (empty($row_check['user_id']))
								{
									$error[] = $user->lang['NON_SEI_NELLA_LADDER'];
								}
								
								// Accept the challenge. "Move" the challenge to the matches table.
								if (!sizeof($error))
								{
									$sql_array	= array(
										'1vs1_challanger'	=> $user->data['user_id'],
										'1vs1_challangee'	=> $xteam,
										'start_time'		=> time(),
										'1vs1_unranked'		=> $row_69['match_unranked'],
										'1vs1_details'		=> $user->lang['MATCH_FROM_MATCHFINDER'],
										'1vs1_ladder'		=> $xladder,
										'1vs1_accepted'		=> 1,
										'1vs1_mappa1'		=> $mappa1,
										'1vs1_mappa2'		=> $mappa2,
										'1vs1_mappa3'		=> $mappa3,
										'mode1'				=> $mode1,
										'mode2'				=> $mode2,
										'mode3'				=> $mode3
									);
									
									$sql	= "INSERT INTO " . ONEVSONE_MATCH_DATA . " " . $db->sql_build_array('INSERT', $sql_array);
									$db->sql_query($sql);
									
									$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_id = " . $xmatchid;
									$db->sql_query($sql);
									
									$urls1 = append_sid( "{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_finder" );
									meta_refresh(2, $urls1);
									trigger_error('SFIDA_ACCETTATA');
								}
							}
						break;
					}
				} // end check if exist
			} // end foreach
		}
	}
/***********************************************
*	Add a match request to list
***********************/
	if ($submit)
	{
		$match_time			= (int) request_var('match_time', 0);
		$group_ladder		= (int) request_var('group_ladder', 0);
		$challenge_unranked	= (int) request_var('challenge_unranked', 0);
		
		// CHECK IF THERE ARE ALREADY A YOUR MATCH REQUEST FOR THIS LADDER
		$sql_69		= "SELECT * FROM " . MATCHFINDER_TABLE . " WHERE match_ladder = {$group_ladder} AND (match_groupid = {$group->data['group_id']} OR match_groupid = {$user->data['user_id']})";
		$result_69	= $db->sql_query_limit($sql_69, 1);
		$row_69 	= $db->sql_fetchrow($result_69);
		$db->sql_freeresult($result_69);
		
		if (!empty($row_69['match_id']))
		{
			$error[] = $user->lang['HAI_UN_MATCHFINDER_INSERITO'];
		}
		
		if (empty($group_ladder))
		{
			$error[] = $user->lang['NAME_LADDER_EMPTY'];
		}
		
		// No error step up.
		if (!sizeof($error))
		{
			$ladder_data	= $ladder->get_roots($group_ladder);
			
			// Insert the group into the match finder.
			$sql_array	= array(
				'match_groupid'			=> ($ladder_data['SUBLADDER_USERDEF'] == 0) ? $group->data['group_id'] : $user->data['user_id'],
				'match_ladder'			=> $group_ladder,
				'match_time'			=> $match_time,
				'match_unranked'		=> $challenge_unranked,
				'match_initaltime'		=> time()
			);
			$sql		= "INSERT INTO " . MATCHFINDER_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);

			// Completed. Let the user know.
			$urls1 = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_finder");
			meta_refresh(2, $urls1);
			trigger_error('MATCH_FINDER_ADDED');
		}
	}
	
	$template->assign_vars(array(
		'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : '',
		'U_ACTION' 	=> $u_action
	));	
	
}

?>