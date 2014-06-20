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

define('SINGLE_BRACKET_DIRECT_ELIM', 1);
define('SINGLE_BRACKET_HOME_AWAY', 2);

/**
 * Manage Tournaments
 * Called from ucp_rivals with mode == 'tournaments'
 */
function ucp_rivals_tournaments($id, $mode, $u_action)
{
	global	$db, $user,$template, $config;
	global	$phpbb_root_path, $phpEx;
	

	$group		= new group();
	$tournament	= new tournament();
	$mio_id		= (int) $user->data['user_id'];
	$mioclan	= (int) $user->data['group_session'];
	$time		= time();
	
/*****************************************************************************************************************************
* UNREPORTED MATCH LIST
***********************/
  	$sql	= "SELECT * FROM " . TGROUPS_TABLE . " AS gr LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON gr.group_tournament = tt.tournament_id
			WHERE tt.tournament_userbased = 0 AND gr.group_id = {$mioclan} AND gr.group_position != 0 AND gr.group_reported = 0 AND gr.loser_confirm = 0 ORDER BY gr.group_tournament DESC";
	$result	= $db->sql_query($sql);

	$i	= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$idtorneo		= $row['group_tournament'];
		$miapos			= $row['group_position'];
		$mioround		= $row['group_bracket'];
		$mioclanx		= $row['group_id']; /* repopulate */
		$idavversario	= $tournament->get_vsclan($idtorneo, $mioclanx, $mioround, false);
		$vspos			= ($miapos & 1) ? ($miapos + 1) : ($miapos - 1);
			 
		if ($idavversario != 0) /* if it is empty that will be a big problem :D */
		{
			// Check for bye clan and go on if yes giving to the real clan the win match up
			if ($idavversario == $config['rivals_byegroup'])
			{
				$sql_array	= array(
					'group_uid'			=> 69, /* standard uid for match up */
					'group_reported'	=> $mioclanx,
					'group_time'		=> $time,
					'loser_confirm'		=> 1
				);
				$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_tournament = {$idtorneo} AND group_id = " . $mioclanx;
				$db->sql_query($sql);
				
				$sql_array7	= array(
					'group_uid'			=> 69, /* standard uid for match up */
					'group_reported'	=> $mioclanx,
					'group_loser'		=> 1,
					'group_time'		=> $time,
					'loser_confirm'		=> 1
				);
				$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array7) . " WHERE group_tournament = {$idtorneo} AND group_position = {$vspos} AND group_id = " . $idavversario;
				$db->sql_query($sql);
				
				/* go on to next bracket */
				$sql_array3	= array(
					'group_tournament'		=> $idtorneo,
					'group_id'				=> $mioclanx,
					'group_bracket'			=> $mioround + 1,
					'group_position'		=> ($miapos & 1) ? ($miapos + 1) / 2 : $miapos / 2,
					'group_loser'			=> 0,
					'group_position_temp'	=> 0,
					'group_reported'		=> 0,
					'loser_confirm'			=> 0,
					'group_uid'				=> 0,
					'group_time'			=> 0
				);
				$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array ('INSERT', $sql_array3);
				$db->sql_query($sql);
				
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
				redirect($redirect_url);
			}

			$template->assign_block_vars('non_riportate', array(
				'MIOCLAN'		=> $group->data('group_name', $mioclanx),
				'MIOID'			=> $mioclanx,
				'MIOURL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $mioclanx),
				'MIAPOS'		=> $miapos,
				'AVVERSARIO'	=> $group->data('group_name', $idavversario),
				'AVVERSARIOID'	=> $idavversario,
				'AVV_URL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $idavversario),
				'ROUND'			=> $mioround,
				'TORNEO'		=> $row['tournament_name'],
				'ID_TORNEO'		=> $idtorneo,
				'SELETTORE'		=> $idtorneo . $mioround . $idavversario,
				'HOME_AWAY'		=> ($row['tournament_tipo'] == 2) ? true : false,
				'ADVSTATS'		=> ($row['tournament_advstats'] == 1) ? true : false,
				'FIRST_HOME'	=> ($miapos & 1) ? $group->data('group_name', $mioclanx) : $group->data('group_name', $idavversario),
				'MATCH_CHAT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;twar=1&amp;mid={$idtorneo}&amp;lb=1"),
				'ROW_COLOR'		=> ($i % 2) ? 'row1' : 'row2'
			));
		
			// clan1 members list
			$clan1members	= $group->members('get_members', $mioclanx);
			foreach ($clan1members as $value1)
			{
				$template->assign_block_vars('non_riportate.blocco_user_1', array(
					'USER_ID'	=> $value1,
					'USERNAME'	=> getusername($value1),
					'GAMERNAME' => getgamername($value1)
				));
			}

			// clan2 members list
			$clan2members	= $group->members('get_members', $idavversario);
			foreach ($clan2members as $value2)
			{
				$template->assign_block_vars('non_riportate.blocco_user_2', array(
					'USER_ID'	=> $value2,
					'USERNAME'	=> getusername($value2),
					'GAMERNAME' => getgamername($value2)
				));
			}

		}
		$i++;
	}
    $db->sql_freeresult($result);
		
/*****************************************************************************************************************************
* REPORTED MATCH LIST
***********************/		
	$sql2		= "SELECT * FROM " . TGROUPS_TABLE . " AS gr LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON gr.group_tournament = tt.tournament_id
			LEFT JOIN " . TMATCHES . " AS tm ON gr.group_uid = tm.group_uid
			WHERE tt.tournament_userbased = 0 AND gr.group_id = {$mioclan} AND gr.group_position != 0 AND gr.group_reported != {$mioclan} AND gr.group_reported != 0 AND gr.loser_confirm = 0 ORDER BY gr.group_tournament DESC";
	$result2	= $db->sql_query($sql2);
	$c			= 0;
	while ($row2 = $db->sql_fetchrow($result2))
	{
	    $idtorneo2		= $row2['group_tournament'];
		$miapos2		= $row2['group_position'];
		$superid2		= $row2['group_uid'];
		$mioround2		= $row2['group_bracket'];
		$mioclan2		= $row2['group_id']; /* repopulate */
		$idavversario2	= $tournament->get_vsclan($idtorneo2, $mioclan2, $mioround2, false);
			 
		$template->assign_block_vars('riportate', array(
			'MIOCLAN'		=> $group->data('group_name', $mioclan2),
			'MIOID'			=> $mioclan2,
			'MIOURL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $mioclan2),
			'AVVERSARIO'	=> $group->data('group_name', $idavversario2),
			'AVVERSARIOID'	=> $idavversario2,
			'AVV_URL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $idavversario2),
			'ROUND'			=> $mioround2,
			'TORNEO'		=> $row2['tournament_name'],
			'ID_TORNEO'		=> $idtorneo2,
			'TIPO_TORNEO'	=> $row2['tournament_tipo'],
			'MIEI_PUNTI'	=> ($mioclan2 == $row2['group1']) ? $row2['punti1'] : $row2['punti2'],
			'MIEI_PUNTI_HM'	=> ($mioclan2 == $row2['group1']) ? $row2['home_punti1'] : $row2['home_punti2'],
			'AVV_PUNTI'		=> ($idavversario2 == $row2['group2']) ? $row2['punti2'] : $row2['punti1'],
			'AVV_PUNTI_HM'	=> ($idavversario2 == $row2['group2']) ? $row2['home_punti2'] : $row2['home_punti1'],
			'MVP1'			=> ($row2['mvp1'] != 0) ? getusername($row2['mvp1']) : '',
			'MVP2'			=> ($row2['mvp2'] != 0) ? getusername($row2['mvp3']) : '',
			'MVP3'			=> ($row2['mvp3'] != 0) ? getusername($row2['mvp2']) : '',
			'RISULTATO'		=> $row2['vincitore'],
			'UID'			=> $superid2,
			'SELETTORE'		=> $superid2 . $mioround2 . "j",
			'HOME_AWAY'		=> ($row2['tournament_tipo'] == 2) ? true : false,
			'ADVSTATS'		=> ($row2['tournament_advstats'] == 1) ? true : false,
			'MATCH_CHAT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;twar=1&amp;mid={$idtorneo2}&amp;lb=1"),
			'FIRST_HOME'	=> ($miapos2 & 1) ? $group->data('group_name', $mioclan2) : $group->data('group_name', $idavversario2),
			'ROW_COLOR'		=> ($c % 2) ? 'row1' : 'row2'
		));
		
		// clan1 members list
		$clan1members2	= $group->members('get_members', $mioclan2);
		foreach ($clan1members2 as $value12)
		{
			$template->assign_block_vars('riportate.riportate_user1', array(
				'USER_ID'	=> $value12,
				'USERNAME'	=> getusername($value12),
				'GAMERNAME' => getgamername($value12),
				'KILLS'		=> get_tadvstats('KILLS', $superid2, $value12),
				'MORTI'		=> get_tadvstats('DEADS', $superid2, $value12),
				'ASSIST'	=> get_tadvstats('ASSISTS', $superid2, $value12)
			));
		}

		// clan2 members list
		$clan2members2	= $group->members('get_members', $idavversario2);
		foreach ($clan2members2 as $value22)
		{
			$template->assign_block_vars('riportate.riportate_user2', array(
				'USER_ID'	=> $value22,
				'USERNAME'	=> getusername($value22),
				'GAMERNAME' => getgamername($value22),
				'KILLS'		=> get_tadvstats('KILLS', $superid2, $value22),
				'MORTI'		=> get_tadvstats('DEADS', $superid2, $value22),
				'ASSIST'	=> get_tadvstats('ASSISTS', $superid2, $value22)
			));
		}
		$c++;
	}
	$db->sql_freeresult($result);
		
/*****************************************************************************************************************************
* REPORTED MATCH LIST WAITING THE CONFIRMATION STEP
***********************/		
	$sql_ww	= "SELECT * FROM " . TGROUPS_TABLE . " AS gr LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON gr.group_tournament = tt.tournament_id
			WHERE tt.tournament_userbased = 0 AND gr.group_reported = {$mioclan} AND group_id = {$mioclan} AND gr.group_position != 0 AND gr.loser_confirm = 0 ORDER BY gr.group_tournament DESC";
	$result_ww	= $db->sql_query($sql_ww);

	$tyr	= 0;
	while ($row_ww = $db->sql_fetchrow($result_ww))
	{
		$at_uid		= $row_ww['group_uid'];
		$ERreporter	= $row_ww['group_reported'];
		$ERnemico	= $tournament->get_vsclan($row_ww['group_tournament'], $ERreporter, $row_ww['group_bracket'], false);
		
		$template->assign_block_vars('in_attesa', array(
			'MIOCLAN'		=> $group->data('group_name', $ERreporter),
			'MIOID'			=> $ERreporter,
			'MIOURL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $ERreporter),
			'AVVERSARIO'	=> $group->data('group_name', $ERnemico),
			'AVVERSARIOID'	=> $ERnemico,
			'AVV_URL'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $ERnemico),
			'ID_TORNEO'		=> $row_ww['group_tournament'],
			'ROUND'			=> $row_ww['group_bracket'],
			'TORNEO'		=> $row_ww['tournament_name'],
			'UID'			=> $row_ww['group_uid'],
			'STIME'			=> $user->format_date($row_ww['group_time']),
			'ROW_COLOR'		=> ($i % 2) ? 'row1' : 'row2'
		));
		$tyr++;
	}	
	$db->sql_freeresult($result_ww);


/********************************************************************
* SEND MATCH RESULT
*/
	$sendresult = (isset($_POST['submit'])) ? true : false;

	if ($sendresult)
	{
		$tournRef	= isset($_POST['tournament']) ? $_POST['tournament'] : array();
		$stats		= isset($_POST['stats']) ? $_POST['stats'] : array();
		$tournID	= 0;
		
		if (empty($tournRef))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		// Menage tournamet data
		foreach ($tournRef as $tournID => $tvalues)
		{			
			if (isset($tvalues['tournamentrep']) && $tvalues['tournamentrep'] == 1) /* go on only for checked tournaments */
			{
				$TRound			= (int) (!empty($tvalues['tournamentroud'])) ? $tvalues['tournamentroud'] : 0;
				$ReportPoint	= (int) (!empty($tvalues['miei_point'])) ? $tvalues['miei_point'] : 0;
				$OppontPoint	= (int) (!empty($tvalues['vs_point'])) ? $tvalues['vs_point'] : 0;
				$OppontRep		= (int) (!empty($tvalues['vsrep'])) ? $tvalues['vsrep'] : 5;
				$mvp1			= (int) (!empty($tvalues['mvp1'])) ? $tvalues['mvp1'] : 0;
				$mvp2			= (int) (!empty($tvalues['mvp2'])) ? $tvalues['mvp2'] : 0;
				$mvp3			= (int) (!empty($tvalues['mvp3'])) ? $tvalues['mvp3'] : 0;
				
				$thereporter	= $group->data['group_id'];
				$theopponent	= $tournament->get_vsclan($tournID, $thereporter, $TRound, false);
				
				// get the winner and final score
				$tourtype	= $tournament->data('tournament_tipo', $tournID);
				
				switch ($tourtype)
				{
					case SINGLE_BRACKET_DIRECT_ELIM:
						if ($ReportPoint > $OppontPoint)
						{
							$thewinner	= $thereporter;
						}
						else if ($ReportPoint < $OppontPoint)
						{
							$thewinner	= $theopponent;
						}
						else if ($ReportPoint == $OppontPoint)
						{
							$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
							trigger_error(sprintf($user->lang['NO_PAREGGI_TORNEO'], '<a href="' . $redirect_url . '">', '</a>'));
							/* single bracket direct elimination do not allow draw result */ 
						}
						
						$firsthome			= 0;
						$ReportPoint_home	= 0;
						$OppontPoint_home	= 0;	
					break;
					
					case SINGLE_BRACKET_HOME_AWAY:
						$ReportPoint_home	= (int) (!empty($tvalues['miei_point_home'])) ? $tvalues['miei_point_home'] : 0;
						$OppontPoint_home	= (int) (!empty($tvalues['vs_point_home'])) ? $tvalues['vs_point_home'] : 0;
						$ReportPos			= (int) (!empty($tvalues['rep_pos'])) ? $tvalues['rep_pos'] : 0;
						
						$firsthome			= ($ReportPos & 1) ? $thereporter : $theopponent;
						
						if (($ReportPoint + $ReportPoint_home) > ($OppontPoint + $OppontPoint_home))
						{
							$thewinner	= $thereporter;
						}
						else if (($ReportPoint + $ReportPoint_home) < ($OppontPoint + $OppontPoint_home))
						{
							$thewinner	= $theopponent;
						}
						else if (($ReportPoint + $ReportPoint_home) == ($OppontPoint + $OppontPoint_home))
						{
							$FINAL_ReportPoint	= ($firsthome == $theopponent) ? (($ReportPoint_home * 2) + $ReportPoint) : (($ReportPoint * 2) + $ReportPoint_home);
							$FILAL_OppontPoint	= ($firsthome == $theopponent) ? (($OppontPoint * 2) + $OppontPoint_home) : (($OppontPoint_home * 2) + $OppontPoint);
							
							if ($FINAL_ReportPoint == $FILAL_OppontPoint)
							{
								$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
								trigger_error(sprintf($user->lang['NO_PAREGGI_TORNEO'], '<a href="' . $redirect_url . '">', '</a>'));
								/* single bracket home-away do not allow draw result at the and of counting */ 
							}
							else if ($FINAL_ReportPoint > $FILAL_OppontPoint)
							{
								$thewinner	= $thereporter;
							}
							else if ($FINAL_ReportPoint < $FILAL_OppontPoint)
							{
								$thewinner	= $theopponent;
							}
						}
					break;
				}
				
				$group_uid	= "1{$tournID}{$TRound}{$thereporter}{$theopponent}";				
			
				// Add match value for the confirmation step
				$sql_array2	= array(
					'group_uid'		=> $group_uid,
					'id_torneo'		=> $tournID,
					'group1'		=> $thereporter,
					'group2'		=> $theopponent,
					'punti1'		=> $ReportPoint,
					'punti2'		=> $OppontPoint,
					'vincitore'		=> $thewinner,
					'mvp1'			=> $mvp1,
					'mvp2'			=> $mvp2,
					'mvp3'			=> $mvp3,
					'conferma1' 	=> 1,
					'conferma2' 	=> 0,
					'first_home'	=> $firsthome,
					'home_punti1'	=> $ReportPoint_home,
					'home_punti2'	=> $OppontPoint_home
				);
				$sql = "INSERT INTO " . TMATCHES . " " . $db->sql_build_array('INSERT', $sql_array2);
				$db->sql_query($sql);
				
				// Update tournament status
				$sql_array	= array(
					'group_uid'			=> $group_uid,
					'group_reported'	=> $thereporter,
					'group_time'		=> $time,
					'reputation'		=> $OppontRep
				);
				$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_tournament = {$tournID} AND group_bracket = {$TRound} AND group_id = " . $thereporter;
				$db->sql_query ($sql);
				
				$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_tournament = {$tournID} AND group_bracket = {$TRound} AND group_id = " . $theopponent;
				$db->sql_query ($sql);		
			
				// Now update advanced user stats if there are for each tournament
				if ($tournament->data('tournament_advstats', $tournID) == 1)
				{
					foreach ($stats as $ID_utente => $values)
					{		
						$thepos		= strrpos($ID_utente, '_');
						$tournchek	= substr($ID_utente,0,$thepos);
						$uderidOK	= substr(strrchr($ID_utente, '_'), 1);
						
						$xkill		= (!empty($values['kills'])) ? (int) $values['kills'] : 0;
						$xdead		= (!empty($values['morti'])) ? (int) $values['morti'] : 0;
						$xassist	= (!empty($values['assist'])) ? (int) $values['assist'] : 0;
						
						if ($tournchek == $tournID && ($xkill > 0 || $xdead > 0 || $xassist > 0 )) /* update only the advanced stats of reported match and only users with at least a stats defined */
						{
							$sql_array3	= array(
								'group_uid'		=> $group_uid,
								'user_id'		=> $uderidOK,
								'group_id'		=> $values['group_id'],
								'tournament_id'	=> $tournID,
								'kills'			=> $xkill,
								'morti'			=> $xdead,
								'assist'		=> $xassist,
								'conferma1'		=> 1,
								'conferma2'		=> 0
							);
							$sql = "INSERT INTO " . TUSER_DATA . " " . $db->sql_build_array('INSERT', $sql_array3);
							$db->sql_query($sql);
						}
					}
				}
				// send notice
				$didurlx 	= generate_board_url() . "rivals.{$phpEx}?action=group_profile&amp;group_id=" . $thereporter;
				$subject	= $user->lang['PM_TOURNAMENT'];
				$message	= sprintf($user->lang['PM_TOURNAMENTMSG2'], $didurlx, $group->data('group_name', $thereporter), $tournament->data('tournament_name', $tournID));
				insert_pm($theopponent, $user->data, $subject, $message);
			}			
		
		}
		// Send finish match report advise
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
		meta_refresh(2, $redirect_url);
		trigger_error('T_RISULTATO_RIPORTATO');
	}

/********************************************************************
* CONFIRM OR CONTEST ACTION
*/
	$confirm = (isset($_POST['confirm'])) ? true : false;
	if ($confirm)
	{
		$confirmed	= request_var('confirmed', array(0 => 0));
		$contested	= request_var('contested', array(0 => 0));
		nodouble_check($confirmed, $contested, 'i=rivals&amp;mode=tournaments');
		
		if (empty($confirmed) && empty($contested))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		// confirm action
		if (!empty($confirmed))
		{
			foreach ($confirmed AS $cvalue)
			{
				// get details
				$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$cvalue} AND group_id = {$group->data['group_id']} 
						AND group_reported > 0 AND group_reported != {$group->data['group_id']} AND loser_confirm = 0 AND group_loser = 0";
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				/* Validate that this user can confirm this match */
				if (!empty($row['group_id']))
				{				
					// set the confirmation for tgroups
					$sql_array	= array(
						'loser_confirm' => 1
					);
					$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_tournament = {$cvalue} AND group_uid = " . $row['group_uid'];
					$db->sql_query($sql);
					
					// set the confirmation for tmatch
					$sql_array4	= array(
						'conferma2' => 1
					);
					$sql = "UPDATE " . TMATCHES . " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE group_uid = " . $row['group_uid'];
					$db->sql_query($sql);
					
					// get the winner and step up it
					$sql_P		= "SELECT * FROM " . TMATCHES . " WHERE id_torneo = {$cvalue} AND group_uid = " . $row['group_uid'];
					$result_P	= $db->sql_query_limit($sql_P, 1);
					$row_P		= $db->sql_fetchrow($result_P);
					$db->sql_freeresult($result_P);
					
					// set the loser flag
					$sql_array99	= array(
						'group_loser' => 1
					);
					$sql = "UPDATE " . TGROUPS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array99) . " WHERE group_tournament = {$cvalue} AND group_id != {$row_P['vincitore']} AND group_uid = " . $row['group_uid'];
					$db->sql_query($sql);
					
					// set the confirmation for tuserdata for advanced stats function
					if ($tournament->data('tournament_advstats', $cvalue) == 1)
					{
						$sql_array5	= array(
							'conferma2' => 1
						);
						$sql = "UPDATE " . TUSER_DATA . " SET " . $db->sql_build_array('UPDATE', $sql_array5) . " WHERE group_uid = " . $row['group_uid'];
						$db->sql_query($sql);
						
						// update general mvps
						if ($row_P['mvp1'] > 0)
						{
							$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + 1 WHERE user_id = " . $row_P['mvp1'];
							$db->sql_query($sql);
						}
						if ($row_P['mvp2'] > 0)
						{
							$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + 1 WHERE user_id = " . $row_P['mvp2'];
							$db->sql_query($sql);
						}
						if ($row_P['mvp3'] > 0)
						{
							$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + 1 WHERE user_id = " . $row_P['mvp3'];
							$db->sql_query($sql);
						}
					}
					
					// repopulate tgroups data for the winner
					$sql_G		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$cvalue} AND group_id = {$row_P['vincitore']} AND group_uid = " . $row['group_uid'];
					$result_G	= $db->sql_query_limit($sql_G, 1);
					$row_G		= $db->sql_fetchrow($result_G);
					$db->sql_freeresult($result_G);
					
					$sql_array3	= array(
						'group_tournament'		=> $cvalue,
						'group_id'				=> $row_P['vincitore'],
						'group_bracket'			=> $row_G['group_bracket'] + 1,
						'group_position'		=> ($row_G['group_position'] & 1) ? ($row_G['group_position'] + 1)/2 : $row_G['group_position'] / 2,
						'group_loser'			=> 0,
						'group_position_temp'	=> 0,
						'group_reported' 		=> 0,
						'loser_confirm' 		=> 0,
						'group_uid' 			=> 0,
						'group_time' 			=> 0
					);
					$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array3);
					$db->sql_query($sql);

					// get feedback
					$frep	= (int) request_var("vsrep_{$cvalue}", 5);
					
					$sql_array7	= array(
						'reputation' => $frep
					);
					$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array7) . " WHERE group_tournament = {$cvalue} AND group_uid = {$row['group_uid']} AND group_id = " . $row['group_reported'];
					$db->sql_query($sql);
					
					// make the new general reputation value for reporter
					$sql_R		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$cvalue} AND group_uid = {$row['group_uid']} AND group_id = " . $row['group_reported'];
					$result_R	= $db->sql_query_limit($sql_R, 1);
					$row_R		= $db->sql_fetchrow($result_R);
					$db->sql_freeresult($result_R);
					
					$sql_array9	= array(
						'clan_rep_value'	=> $group->data('clan_rep_value', $row_R['group_id']) + $row_R['reputation'], /* sum the reputation gived */
						'clan_rep_time'		=> $group->data('clan_rep_time', $row_R['group_id']) + 1
					);
					$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array9) . " WHERE group_id = " . $row_R['group_id'];
					$db->sql_query($sql);
					
					// make the new general reputation value for confirmer aka the current user clan
					$sql_S		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$cvalue} AND group_uid = {$row['group_uid']} AND group_id = " . $group->data['group_id'];
					$result_S	= $db->sql_query_limit($sql_S, 1);
					$row_S		= $db->sql_fetchrow($result_S);
					$db->sql_freeresult($result_S);
					
					$sql_array10	= array(
						'clan_rep_value'	=> $group->data('clan_rep_value', $row_S['group_id']) + $row_S['reputation'], /* sum the reputation gived */
						'clan_rep_time'		=> $group->data('clan_rep_time', $row_S['group_id']) + 1
					);
					$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array10) . " WHERE group_id = " . $row_S['group_id'];
					$db->sql_query($sql);
					
					// Update stats for each clan members
					if ($tournament->data('tournament_advstats', $cvalue) == 1)
					{
						$clan1members	= $group->members('get_members', $row_R['group_id']);
						foreach ($clan1members as $value1)
						{
							recalculate_totalEXP($value1);
						}
						
						$clan2members	= $group->members('get_members', $row_S['group_id']);
						foreach ($clan2members as $value2)
						{
							recalculate_totalEXP($value2);
						}
					}
					// send notice
					$mioclanZ	= $group->data['group_id'];
					$didurl  	= generate_board_url() . "rivals.{$phpEx}?action=group_profile&amp;group_id=" . $mioclanZ;
					$subject	= $user->lang['PM_TOURNAMENT'];
					$message	= sprintf($user->lang['PM_TOURNAMENTMSG'], $didurl, $group->data('group_name', $mioclanZ), $tournament->data('tournament_name', $cvalue));
					insert_pm($row['group_reported'], $user->data, $subject, $message);
				}
			}	
		}
		
		// contest action
		if (!empty($contested))
		{
			foreach ($contested AS $xvalue)
			{
				$mioclanW	= $group->data['group_id'];
				$board   	= generate_board_url();
				$subject	= $user->lang['PMTICKET'];
				$message	= sprintf($user->lang['T_CONTESTA_TESTO'], $board, $mioclanW, $group->data('group_name', $mioclanW), $tournament->data('tournament_name', $xvalue));
				insert_pm($config['rivals_ticketreceiver'], $user->data, $subject, $message);
				
				//set the the correct status to tournament match
				$sql_array	= array(
					'match_problem' => 1
				);
				$sql = "UPDATE " . TMATCHES . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE id_torneo = {$xvalue} AND conferma2 = 0 AND (group1 = {$mioclanW} OR group2 = {$mioclanW})";
				$db->sql_query($sql);
			}
		}
		
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
		meta_refresh(2, $redirect_url);
		trigger_error('T_RISULTATO_CONFERMATO');
	}
	
/********************************************************************
* SEND INACTIVITY REPORT
*/
	$inattivo = (isset($_POST['inattivo'])) ? true : false;
	if ($inattivo)
	{
		$tournRef	= isset($_POST['tournament2']) ? $_POST['tournament2'] : array();	
		$tournID	= 0;
		
		if (empty($tournRef))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
			meta_refresh(2, $redirect_url);
			trigger_error('ANY_MATCHES_SELECTED');
		}
		
		// Menage tournamet data
		foreach ($tournRef as $tournID => $tvalues)
		{			
			if (isset($tvalues['tournamentrep']) && $tvalues['tournamentrep'] == 1) /* go on only for checked tournaments */
			{
				$Tuid	= (int) (!empty($tvalues['uid'])) ? $tvalues['uid'] : 0;
				$Tround	= (int) (!empty($tvalues['round'])) ? $tvalues['round'] : 0;
				
				// load match infos
				$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = " . (int) $tournID . " AND group_uid = {$Tuid} 
						AND group_id = {$group->data['group_id']} AND group_reported = {$group->data['group_id']}";
				$result	= $db->sql_query_limit($sql, 1);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				if (empty($row['group_id'])) /* if do not exist the user can't menage this match */
				{
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
					trigger_error(sprintf($user->lang['CANT_MENAGE_THIS_MATCH'], '<a href="' . $redirect_url . '">', '</a>'));
				}
				else
				{
					if ($time < ($row['group_time'] + (3*24*60*60)))
					{
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
						trigger_error(sprintf($user->lang['T_POCO_TEMPO'], '<a href="' . $redirect_url . '">', '</a>'));
					}
					else
					{
						$soio		= $group->data['group_id'];
						$opponetid	= $tournament->get_vsclan($tournID, $soio, $Tround, false);
						$opponent	= $group->data('group_name', $opponetid);
						$fixtime	= $user->format_date($row['group_time']);
						$ticket		= "{$tournament->data('tournament_name', $tournID)} round {$Tround}.<br />{$opponent}" . $user->lang['T_SEGNALA_INATTIVO'] . "<br />" . $user->lang['GIOCATA_IL'] . $fixtime;
						$board   	= generate_board_url();
						
						$subject	= $user->lang['PMTICKET'];
						$message	= sprintf($user->lang['PMTICKET_TUR_TIMEOUT'], $board, $soio, $group->data('group_name', $soio), $ticket);
						insert_pm($config['rivals_ticketreceiver'], $user->data, $subject, $message);
						
						//set the the correct status to tournament match
						$sql_array	= array(
							'match_problem' => 2
						);
						$sql = "UPDATE " . TMATCHES . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_uid = " . $Tuid;
						$db->sql_query($sql);
					}
				}
			}
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=tournaments");
			meta_refresh(2, $redirect_url);
			trigger_error('TICKET_SENT');
		}
	}
}

?>