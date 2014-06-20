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
 * Edit Tournaments
 * Called from acp_rivals with mode == 'edit_tournaments'
 */
function acp_rivals_edit_tournaments($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$group	= new group();
	$tournament	= new tournament();

	// TEMPLATE
	$sql	= "SELECT * FROM " . TOURNAMENTS_TABLE;
	$result	= $db->sql_query($sql);

	$i	= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// status
		if ($row['tournament_status'] == 2)
		{
			$status = $user->lang['INIZIATO'];
		}
		else if ($row['tournament_status'] == 3)
		{
			$status = $user->lang['CHIUSO'];
		}
		else
		{
			$status = $user->lang['PRONTO_A_PARTIRE'];
		}
		
		switch ($row['tournament_tipo'])
		{
			case 1:
				$Ttipo	= $user->lang['TOURNAMENT_DIRECTELIM'];
			break;
			case 2:
				$Ttipo	= $user->lang['TOURNAMENT_HOMEAWAY_SHORT'];
			break;
		}
		
		$template->assign_block_vars('block_tournaments', array(
			'TOURNAMENT_NAME'	=> $row['tournament_name'],
			'SPOTS_TAKEN'		=> $tournament->get_take_tslots($row['tournament_id']),
			'SPOTS_OPEN'		=> $row['tournament_brackets'],
			'ACESSIBILITA'		=> ($row['tournament_type'] == 1) ? $user->lang['TOURNAMENT_FORALL'] : $user->lang['TOURNAMENT_INVITE'],
			'TIPO'				=> $Ttipo,
			'STATUS'			=> $status,
			'USERBASED'			=> ($row['tournament_userbased'] == 1) ? '(1vs1)' : '',
			'EDITA_URL'			=> ($row['tournament_status'] != 3) ? '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournament&amp;tournament_id=" . $row['tournament_id']) . '">' . $user->lang['EDIT'] . '</a>' : '',
			'ORGANIZZA_URL'		=> ($row['tournament_status'] != 3) ? '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_brackets&amp;tournament_id=" . $row['tournament_id'] . "&amp;kind=" . $row['tournament_tipo']) . '">' . $user->lang['ORGANIZZA_TORNEO'] . '</a>'  : '',
			'CANCELLA_URL'		=> '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournaments&amp;cancella=" . $row['tournament_id']) . '">' . $user->lang['DELETE'] . '</a>',
			'CHIUDI_URL'		=> ($row['tournament_status'] != 3) ? '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournaments&amp;chiudi=" . $row['tournament_id']) . '">' . $user->lang['CLOSE'] . '</a>' : ''
		));
		$i++;
	}
	$db->sql_freeresult($result);
	
/*  DELETE TOURNAMENT */	
	$cancella = (int) request_var('cancella', 0);
	if ($cancella > 0)
	{
		$sql	= "DELETE FROM " . TOURNAMENTS_TABLE . " WHERE tournament_id = " . $cancella;
		$db->sql_query($sql);
		
		$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_tournament = " . $cancella;
		$db->sql_query($sql);
		
		$sql	= "DELETE FROM " . TUSER_DATA . " WHERE tournament_id = " . $cancella;
		$db->sql_query($sql);
		
		$sql	= "DELETE FROM " . TMATCHES . " WHERE id_torneo = " . $cancella;
		$db->sql_query($sql);
		   
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournaments");
		meta_refresh(2, $redirect_url);
		trigger_error('TOURNAMENT_DELETED');
	}
	
/*  CLOSE TOURNAMENT */	
	$chiudi = (int) request_var('chiudi', 0);
	if ($chiudi > 0)
	{
		$sql_array	= array(
			'tournament_status' => 3,
		);
		$sql = "UPDATE " . TOURNAMENTS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE tournament_id = " . $chiudi;
		$db->sql_query($sql);
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournaments");
		meta_refresh(2, $redirect_url);
		trigger_error('TOURNAMENT_UPDATED');
	}

}
?>