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
 * Edit Brackets
 * Called from acp_rivals with mode == 'edit_brackets'
 */
function acp_rivals_edit_brackets($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;


	$group		= new group();
	$tournament	= new tournament();

	$id_torneo		= (int) request_var('tournament_id', 0);
    $tipo_torneo	= (int) request_var('kind', 0);
	
/*********************************
*	TONEO SINGLE BRECKETS
*/
	switch ($tipo_torneo)
	{
		case 1:
		case 2:		
			// impagino
			$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_position != 0 AND group_bracket = 1 ORDER BY group_position ASC";
			$result	= $db->sql_query($sql);

			$i	= 0;
			while ($row = $db->sql_fetchrow($result))
			{
			 
				$cancellaurl	= append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_brackets&amp;tournament_id={$id_torneo}&amp;kind={$tipo_torneo}&amp;cancella=" . $row['group_position']);				
				$groupname		= ($tournament->data('tournament_userbased', $id_torneo) == 0) ? $group->data('group_name', $row['group_id']) : getusername($row['group_id']);
				$roster			= ($row['roster_id'] != 0) ? ' (' . $user->lang['ROSTERS'] . ': ' . get_roster_name($row['roster_id']) . ') ' : '';
				 
				$template->assign_block_vars('block_rounds', array(
					'POSIZIONE' => $row['group_position'],
					'TD'		=> ($row['group_position'] & 1) ?  '<tr><th width="96%" bgcolor="#00CCFF" scope="row">' . $row['group_position'] . ') ' . $groupname . $roster . '<a href="' . $cancellaurl . '"> ' . $user->lang['DELETE'] . '</a></th><th width="4%" rowspan="3" bgcolor="#00CCFF" align="center">VS</th></tr>' : '<tr><th bgcolor="#00CCFF" scope="row">' . $row['group_position'] . ') ' . $groupname . $roster . '<a href="' . $cancellaurl . '"> ' . $user->lang['DELETE'] . '</a></th></tr>',
					'TD_OPTION'	=> ($row['group_position'] & 1) ? '' : 'height="50" colspan="2"'
				));
				$i++;
			}
			$db->sql_freeresult($result);
		
			// assign random place
			$sql_1		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_position = 0 ORDER BY RAND()";
			$result_1	= $db->sql_query($sql_1);
			$salome		= 0;
			
			$lista = array();
			while ($row_1 = $db->sql_fetchrow($result_1))
			{
				$salome		= count($row_1['group_id']);
				$lista[]	= $row_1;
			}
			$db->sql_freeresult($result_1);

			$y = 0;
			foreach ($lista as $data)
			{
				$numero = $y + 1;
				
				$sql_array2	= array(
					'group_position_temp' => $numero,
				);
				$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_tournament = {$id_torneo} AND group_id = {$data['group_id']} AND roster_id = " . $data['roster_id'];
				$db->sql_query($sql);
				 
				$roster	= ($data['roster_id'] != 0) ? ' (' . $user->lang['ROSTERS'] . ': ' . get_roster_name($data['roster_id']) . ')' : '';
				
				$template->assign_block_vars('non_assegnati', array(
					'NUMMERO'		=> $numero,
					'TOURNAMENT'	=> $id_torneo,
					'CLAN_ID'		=> $data['group_id'],
					'CLAN'			=> ($tournament->data('tournament_userbased', $id_torneo) == 0) ? $group->data('group_name', $data['group_id']) . $roster : getusername($data['group_id'])
				));	
				$y++;
			}
	   
			$sql_cd		= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_id = " . $id_torneo;
			$result_cd	= $db->sql_query($sql_cd);
			$row_cd		= $db->sql_fetchrow($result_cd);
			$db->sql_freeresult($result_cd);
	   
			if ($row_cd['tournament_status'] == 2)
			{
				$template->assign_vars(array(
					'BOTTONE'	=> $user->lang['TORNEO_INIZIATO']
				));
			}
			else if ($row_cd['tournament_status'] == 3)
			{
				$template->assign_vars(array(
					'BOTTONE' => $user->lang['TORNEO_CHIUSO']
				));
			}
		   else
			{
				if ($tournament->get_take_tslots($id_torneo) > 0)
				{
					$template->assign_vars(array(
						'BOTTONE' => (!empty($salome)) ? $user->lang['STEP'] . '1: <input type="submit" name="posiziona" value="' . $user->lang['DISPONI_CLAN'] . '" class="button1" />' : $user->lang['STEP'] . '2: <input type="submit" name="pubblica" value="' . $user->lang['PUBBLICA'] . '" class="button1" />'
					));
				}
			}
	   
	//// POSITIONING CLAN ACTION
		$posiziona = (!empty($_POST['posiziona'])) ? true : false;
		if ($posiziona)
		{
			$ids 	= request_var('id_clan', array(0 => 0));
			$xpos	= request_var('posizione', array(0 => 0));

			if ($tournament->data('tournament_stricted', $id_torneo) == 0)
			{
				foreach ($ids AS $value => $yu)
				{
					$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_id = " . $yu;
					$result	= $db->sql_query($sql);
					$row	= $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					
					$sql_array2	= array(
						'group_position' => $row['group_position_temp'],
					);
					$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_tournament = {$id_torneo} AND group_id = " . $yu;
					$db->sql_query($sql);
				}
			}
			else
			{
				foreach ($xpos AS $value => $yu)
				{
					$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_position_temp = " . $yu;
					$result	= $db->sql_query($sql);
					$row	= $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					
					$sql_array2	= array(
						'group_position' => $row['group_position_temp'],
					);
					$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_tournament = {$id_torneo} AND group_id = {$row['group_id']} AND roster_id = " . $row['roster_id'];
					$db->sql_query($sql);
				}
			}

			// Add beye clan for full the slots
			$slot_presi	= $tournament->get_take_tslots($id_torneo);
			$slot_tot	= $tournament->data('tournament_brackets', $id_torneo);
				
			if ($slot_tot > $slot_presi)
			{
				if ($slot_presi >= 0 && $slot_presi <= 2)
				{
					$newslot = 2;
				}
				if ($slot_presi > 2 && $slot_presi <= 4)
				{
					$newslot = 4;
				}
				else if ($slot_presi > 4 && $slot_presi <= 8)
				{
					$newslot = 8;
				}
				else if ($slot_presi > 8 && $slot_presi <= 16)
				{
					$newslot = 16;
				}
				else if ($slot_presi > 16 && $slot_presi <= 32)
				{
					$newslot = 32;
				}
				else if ($slot_presi > 32 && $slot_presi <= 64)
				{
					$newslot = 64;
				}
				else if ($slot_presi > 64 && $slot_presi <= 128)
				{
					$newslot = 128;
				}
				
				// set the newslot value
				$sql_array	= array(
					'tournament_brackets' => $newslot,
				);
				$sql = "UPDATE " . TOURNAMENTS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE tournament_id = " . $id_torneo;
				$db->sql_query($sql);
				
				// Repopulate tournament data
				$sql_8		= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_id = " . $id_torneo;
				$result_8	= $db->sql_query_limit($sql_8, 1);
				$row_8		= $db->sql_fetchrow($result_8);
				$db->sql_freeresult($result_8);
				$slot_tot2	= $row_8['tournament_brackets']; 
				$decertos	= $row_8['tournament_decerto'];
				$nome_corto	= $row_8['shorty'];
				
				// now add bye clan if need
				if ($slot_presi < $slot_tot2)
				{
					$tournament->add_byes($id_torneo);
				}
				else
				{
					// re-randomize all clan
					$sql_rr		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} ORDER BY RAND()";
					$result_rr	= $db->sql_query($sql_rr);
					$lista_rr	= array();
					while ($row_rr = $db->sql_fetchrow($result_rr))
					{
						$lista_rr[] = $row_rr;
					}
					$db->sql_freeresult($result_rr);
					
					$ih = 1;
					foreach ($lista_rr as $data_rr)
					{			
						$sql_array33 = array(
							'group_position' => $ih,
						);
						$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array33) . " WHERE group_tournament = {$id_torneo} AND group_id = {$data_rr['group_id']} AND roster_id = " . $data_rr['roster_id'];
						$db->sql_query($sql);
							
						$ih++;
					}
				}
			}
			
			// fix empty oppoent
			$sql_yy		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_bracket = 1";
			$result_yy	= $db->sql_query($sql_yy);
			$tobefixed	= array();
			while ($row_yy = $db->sql_fetchrow($result_yy))
			{
				$opponent	= $tournament->get_vsclan($id_torneo, $row_yy['group_id'], 1, false);
				
				if ($opponent == 0)
				{
					$tobefixed[] = $row_yy['group_id'];
				}
			}
			$db->sql_freeresult($result_yy);
			
			foreach ($tobefixed as $idfix)
			{
				// check if it do not exist for real
				$sql_07		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_bracket = 1 AND group_id = " . $idfix;
				$result_07	= $db->sql_query_limit($sql_07, 1);
				$row_07		= $db->sql_fetchrow($result_07);
				$db->sql_freeresult($result_07);
				
				$opponentpost	= ($row_07['group_position']%2 == 0) ? $row_07['group_position'] - 1 : $row_07['group_position'] + 1;
				
				$sql_03		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_bracket = 1 AND group_position = " . $opponentpost;
				$result_03	= $db->sql_query_limit($sql_03, 1);
				$row_03		= $db->sql_fetchrow($result_03);
				$db->sql_freeresult($result_03);
				
				if (empty($row_03['group_id']))
				{
					// add a bye
					$sql_array90	= array(
						'group_tournament'	 	=> $id_torneo,
						'group_id'				=> ($row_8['tournament_userbased'] == 0) ? $config['rivals_byegroup'] : 1,
						'roster_id'				=> 0,
						'group_bracket'			=> 1,
						'group_position'		=> $opponentpost,
						'group_loser'			=> 0,
						'group_position_temp'	=> 0,
						'group_reported'		=> 0,
						'loser_confirm'			=> 0,
						'group_uid'				=> 0,
						'group_time'			=> 0,
						'reputation'			=> 5
					);
					$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array90);
					$db->sql_query($sql);
				}
			}
			
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_brackets&amp;tournament_id={$id_torneo}&amp;kind={$tipo_torneo}");
			redirect($redirect_url);
		}
		
	//// PUBLIC ACTION
		$pubblica = (!empty($_POST['pubblica'])) ? true : false;
		if ($pubblica)
		{
			$slot_tot2	= $tournament->data('tournament_brackets', $id_torneo);
			$decertos	= $tournament->data('tournament_decerto', $id_torneo);
			$nome_corto	= $tournament->data('shorty', $id_torneo);
			
			// RANDOM MODE DECERTO
			if ($decertos == 1)
			{
				$sql2		= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = 1 AND nome_corto = '{$nome_corto}' ORDER BY RAND()";
				$result2	= $db->sql_query_limit($sql2, 1);
				$row2		= $db->sql_fetchrow($result2);
				$inter1 	= $row2['decerto_interid'];
				$mode1 		= $row2['decerto_mode'];
				$db->sql_freeresult($result2);
				
				$sql3		= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid != {$inter1} ORDER BY RAND()";
				$result3	= $db->sql_query_limit($sql3, 1);
				$row3		= $db->sql_fetchrow($result3);
				$inter2 	= $row3['decerto_interid'];
				$mode2		= $row3['decerto_mode'];
				$db->sql_freeresult($result3);
				
				$sql4		= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid != {$inter1} AND decerto_interid != {$inter2} ORDER BY RAND()";
				$result4	= $db->sql_query_limit($sql4, 1);
				$row4		= $db->sql_fetchrow($result4);
				$mode3		= $row4['decerto_mode'];
				$db->sql_freeresult($result4);
				
				$ordine = "1) {$mode1} 2) {$mode2} 3) {$mode3}"; /* USE THIS */
				
				// mappa 1
				$sql_a		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 1 ORDER BY RAND()";
				$result_a	= $db->sql_query_limit($sql_a, 1);
				$row_a		= $db->sql_fetchrow($result_a);
				$mappa1		= $row_a['decerto_mappa'];
				$db->sql_freeresult($result_a);
				// mappa 2
				$sql_b		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 2 ORDER BY RAND()";
				$result_b	= $db->sql_query_limit($sql_b, 1);
				$row_b		= $db->sql_fetchrow($result_b);
				$mappa2		= $row_b['decerto_mappa'];
				$db->sql_freeresult($result_b);
				// mappa 3
				$sql_c		= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '{$nome_corto}' AND decerto_interid = 3 ORDER BY RAND()";
				$result_c	= $db->sql_query_limit($sql_c, 1);
				$row_c		= $db->sql_fetchrow($result_c);
				$mappa3		= $row_c['decerto_mappa'];
				$db->sql_freeresult($result_c);
				
				$sql_array1	= array(
					'id_torneo' => $id_torneo,
					'round'		=> 1,
					'modi'		=> $ordine,
					'map1'		=> $mappa1,
					'map2'		=> $mappa2,
					'map3'		=> $mappa3
				);
				$sql = "INSERT INTO " . TDECERTO . " " . $db->sql_build_array('INSERT', $sql_array1);
				$db->sql_query($sql);
			}
			
			// RANDOM MODE CPC
			if ($decertos == 2)
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
				
				$sql_array1	= array(
					'id_torneo' => $id_torneo,
					'round'		=> 1,
					'modi'		=> '',
					'map1'		=> $mappa1,
					'map2'		=> $mappa2,
					'map3'		=> $mappa3
				);
				$sql = "INSERT INTO " . TDECERTO . " " . $db->sql_build_array('INSERT', $sql_array1);
				$db->sql_query($sql);
			}
			
			$sql_array	= array(
				'tournament_status' => 2,
			);
			$sql = "UPDATE " . TOURNAMENTS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE tournament_id = " . $id_torneo;
			$db->sql_query($sql);
					
			// Send a PM to all user clan leader for make they know that the tournament is started
			$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_bracket = 1";
			$result	= $db->sql_query($sql);			
			while ($row = $db->sql_fetchrow($result))
			{
				if ($tournament->data('tournament_userbased', $id_torneo) == 1)
				{
					$destinatario	= $row['group_id'];
					if ($destinatario != ANONYMOUS)
					{
						$subject		= sprintf($user->lang['PM_TOURNAMENT']);
						$message		= sprintf($user->lang['PM_TOURNAMENT_TXT'], $tournament->data('tournament_name', $id_torneo));
						insert_pm($destinatario, $user->data, $subject, $message);
					}
				}
				else
				{
					$destinatario	= $group->data('user_id', $row['group_id']);
					$subject		= sprintf($user->lang['PM_TOURNAMENT']);
					$message		= sprintf($user->lang['PM_TOURNAMENT_TXT'], $tournament->data('tournament_name', $id_torneo));
					insert_pm($destinatario, $user->data, $subject, $message);
				}			
			}
			$db->sql_freeresult($result);
			
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_brackets&amp;tournament_id={$id_torneo}&amp;kind={$tipo_torneo}");
			redirect($redirect_url);
		}
	break;
}

/*********************************
*	LEAGUES TOURNAMENT
*/

// to do

/*********************************
*	OTHER ACTION AND DEFINITION
*/

	// CHANGE CLAN WITH BYE CLAN
	$cancella = (int) request_var('cancella', 0);
	if (!empty($cancella))
	{
		$sql_cc		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$id_torneo} AND group_position = {$cancella} ORDER BY group_bracket DESC";
		$result_cc	= $db->sql_query_limit($sql_cc, 1);
		$row_cc		= $db->sql_fetchrow($result_cc);
		$db->sql_freeresult($result_cc);
		
		$maxpos = $row_cc['group_bracket'];
		$sql_array	= array(
			'group_id'	=> ($tournament->data('tournament_userbased', $id_torneo) == 0) ? $config['rivals_byegroup'] : 1,
			'roster_id'	=> 0
		);
		$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_tournament = {$id_torneo} AND group_position = {$cancella} AND group_bracket = " . $maxpos;
		$db->sql_query($sql);
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_brackets&amp;tournament_id={$id_torneo}&amp;kind={$tipo_torneo}");
		redirect($redirect_url);
	}
}
?>