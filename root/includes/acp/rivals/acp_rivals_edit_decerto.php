<?php
/**
*
* @package acp
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
 * Configure Decerto and CPC mapset
 * Called from acp_rivals with mode == 'edit_decerto'
 */
function acp_rivals_edit_decerto($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;
	
	$error		= array();
	$game		= array();
	$gameD		= array();
	$delGame	= (int) request_var('delg', 0);
	$delMap		= (int) request_var('delm', 0);
	$submitg	= (!empty($_POST['submit_game'])) ? true : false;
	$submitcpc	= (!empty($_POST['submit_cpc'])) ? true : false;
	$submit_game_dettaglio	= (!empty($_POST['submit_game_dettaglio'])) ? true : false;
	

	// TEMPLATE CATEGORIE
	$sql_g1		= "SELECT * FROM " . DECERTO_CAT . " ORDER BY nome_gioco ASC";
	$result_g1	= $db->sql_query($sql_g1);
	while ($row_g1 = $db->sql_fetchrow($result_g1))
	{
		$game['id'] 		= $row_g1['id_decerto'];
	    $game['gamename']	= $row_g1['nome_gioco'];
		$game['shortname']	= $row_g1['nome_corto'];
		$game['decmode']	= $row_g1['decerto_mode'];
		$game['selector']	= $row_g1['nome_corto'] . $row_g1['decerto_interid'] . $row_g1['cpc'];
		$game['cpc'] 		= (int) $row_g1['cpc'];
		$game['interid'] 	= (int) $row_g1['decerto_interid'];
		$game['active'] 	= $row_g1['active'];
		$gameD[] = $game;   
	}
	$db->sql_freeresult($result_g1);

	// Set template value
	foreach ($gameD AS $gID => $gamedata)
	{
		// For games or mode
		$template->assign_block_vars('categorie_decerto', array(
			'ID_GIOCO'			=> $gamedata['id'],
			'NOME_GIOCO'		=> $gamedata['gamename'],
			'NOME_GIOCO_CORTO'	=> $gamedata['shortname'],
			'U_DELETE'			=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto&amp;delg=" . $gamedata['id']),
			'MODALITA'			=> $gamedata['decmode'],
			'SELETTORE'			=> $gamedata['selector'],
			'ACTIVE'			=> ($gamedata['active'] == 1) ? 'active' : 'inactive',
			'CPC'				=> ($gamedata['cpc'] == 1) ? $user->lang['DECERTO'] : $user->lang['CPC'],
		));

		// For relative maps
		$sql_g3		= "SELECT * FROM " . DECERTO_MAP . " WHERE nome_corto = '{$gamedata['shortname']}' AND decerto_interid = {$gamedata['interid']} 
					AND decerto_cpc = {$gamedata['cpc']} ORDER BY decerto_mappa ASC";
		$result_g3	= $db->sql_query($sql_g3);
		while ($row_g3 = $db->sql_fetchrow($result_g3))
		{
			$template->assign_block_vars('categorie_decerto.mappe', array(
				'MAPPA'		=> $row_g3['decerto_mappa'],
				'ID_MAPPA'	=> $row_g3['id_mappa_decerto'],
				'U_DELETE'	=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto&amp;delm=" . $row_g3['id_mappa_decerto'])
			));
		}
		$db->sql_freeresult($result_g3);
	}
			   
	// TEMPLATE GIOCHI PER DETTAGLIO
	$sql_g2		= "SELECT * FROM " . DECERTO_CAT . " ORDER BY nome_gioco ASC";
	$result_g2	= $db->sql_query($sql_g2);
	while ($row_g2 = $db->sql_fetchrow($result_g2))
	{
	    $template->assign_block_vars('giochi', array(
			'NOME_GIOCO'	=> $row_g2['nome_gioco'],
			'LINKER'		=> $row_g2['nome_corto'] . "//" . $row_g2['decerto_interid'] . ".:." . $row_g2['cpc'],
			'MODALITA'		=> $row_g2['decerto_mode'],
			'CPC'			=> ($row_g2['cpc'] == 1) ? $user->lang['DECERTO'] : $user->lang['CPC']
			)
		);
	}
	$db->sql_freeresult($result_g2);
	
/**
*	ACTIONS
********************/

	// Add a game with decerto mode
	if ($submitg)
	{
		$nome_gioco			= utf8_normalize_nfc(request_var('decerto_gioco', '', true));
		$nome_gioco_corto_u	= utf8_normalize_nfc(request_var('decerto_gioco_corto', '', true));
		$modalita1			= utf8_normalize_nfc(request_var('decerto_mode1', '', true));
		$modalita2			= utf8_normalize_nfc(request_var('decerto_mode2', '', true));
		$modalita3			= utf8_normalize_nfc(request_var('decerto_mode3', '', true));
		$badchara			= array("'", " ", '"');
		$remplacer			= array("", "", '');
		$nome_gioco_corto	= str_replace($badchara, $remplacer, $nome_gioco_corto_u);
		
		//check shorty
		$sqlcc		= "SELECT * FROM " . DECERTO_CAT . " WHERE nome_corto = '{$nome_gioco_corto}'";
		$resultcc	= $db->sql_query_limit($sqlcc,1);
		$rowcc		= $db->sql_fetchrow($resultcc);
		$db->sql_freeresult($resultcc);
					
		if (!empty($rowcc['nome_corto']))
		{
			$error[] = $user->lang['SHORTY_PRESENTE'];
		}

		if (!sizeof($error))
		{
			//1)
			$sql_array1	= array(
				'nome_gioco'		=> $nome_gioco,
				'nome_corto'		=> $nome_gioco_corto,
				'decerto_interid'	=> 1,
				'decerto_mode'		=> $modalita1,
				'cpc'				=> 1
			);
			$sql	= "INSERT INTO " . DECERTO_CAT . " " . $db->sql_build_array('INSERT', $sql_array1);
			$db->sql_query($sql);
			
			//2)
			$sql_array2	= array(
				'nome_gioco'		=> $nome_gioco,
				'nome_corto'		=> $nome_gioco_corto,
				'decerto_interid'	=> 2,
				'decerto_mode'		=> $modalita2,
				'cpc'				=> 1
			);
			$sql	= "INSERT INTO " . DECERTO_CAT . " " . $db->sql_build_array('INSERT', $sql_array2);
			$db->sql_query($sql);
			
			//3)
			$sql_array3	= array(
				'nome_gioco'		=> $nome_gioco,
				'nome_corto'		=> $nome_gioco_corto,
				'decerto_interid'	=> 3,
				'decerto_mode'		=> $modalita3,
				'cpc'				=> 1
			);
			$sql	= "INSERT INTO " . DECERTO_CAT . " " . $db->sql_build_array('INSERT', $sql_array3);
			$db->sql_query($sql);
			
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto");
			redirect($redirect_url);
		}
	}
	
	// Add a game with cpc mode
	if ($submitcpc)
	{
		$nome_giocos		= utf8_normalize_nfc(request_var('decerto_gioco', '', true));
		$nome_gioco_corto_s	= utf8_normalize_nfc(request_var('decerto_gioco_corto', '', true));
		$modalita1s			= utf8_normalize_nfc(request_var('decerto_mode1', '', true));
		
		$badchara			= array("'", " ", '"');
		$remplacer			= array("", "", '');
		$nome_gioco_cortos	= str_replace($badchara, $remplacer, $nome_gioco_corto_s);
		
		//check shorty
		$sqlcc		= "SELECT * FROM " . DECERTO_CAT . " WHERE nome_corto = '{$nome_gioco_cortos}'";
		$resultcc	= $db->sql_query_limit($sqlcc,1);
		$rowcc		= $db->sql_fetchrow($resultcc);
		$db->sql_freeresult($resultcc);
					
		if (!empty($rowcc['nome_corto']))
		{
			$error[] = $user->lang['SHORTY_PRESENTE'];
		}

		if (!sizeof($error))
		{
			$sql_array3	= array(
				'nome_gioco'		=> $nome_giocos,
				'nome_corto'		=> $nome_gioco_cortos,
				'decerto_interid'	=> 1,
				'decerto_mode'		=> $modalita1s,
				'cpc'				=> 2
			);
			$sql	= "INSERT INTO " . DECERTO_CAT . " " . $db->sql_build_array('INSERT', $sql_array3);
			$db->sql_query($sql);
			
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto");
			redirect($redirect_url);
		}
	}

	// ADD map
	if ($submit_game_dettaglio)
	{
		$riferimenti_det	= utf8_normalize_nfc(request_var('gioco_dec', '', true));
		$mappa				= utf8_normalize_nfc(request_var('decerto_mappa', '', true));
		$got_interid		= (int) substr(strrchr($riferimenti_det, '//'), 1, -4);
		$got_shorty			= substr($riferimenti_det, 0, -7);
		$got_cpc			= substr(strrchr($riferimenti_det, '.:.'), 1);
			
		$sql_array	= array(
			'nome_corto'		=> $got_shorty,
			'decerto_interid'	=> $got_interid,
			'decerto_mappa'		=> $mappa,
			'decerto_cpc'		=> $got_cpc
		);
		$sql	= "INSERT INTO " . DECERTO_MAP . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);
		
		// Now check if is the game is active or not. Are needed at least 3 map for each mode to be active.
		$sql		= "SELECT COUNT(id_mappa_decerto) AS status FROM " . DECERTO_MAP . " WHERE nome_corto = '{$got_shorty}' 
					AND decerto_interid = {$got_interid} GROUP BY nome_corto";
		$result		= $db->sql_query_limit($sql,1);
		$row		= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if ($row['status'] == 3)
		{
			// Ok active the mode
			$sql_array1	= array(
				'active'	=> 1,
			);
			$sql = "UPDATE " . DECERTO_CAT . " SET " . $db->sql_build_array('UPDATE', $sql_array1) . " WHERE nome_corto = '{$got_shorty}' AND decerto_interid = {$got_interid}";
			$db->sql_query($sql);
		}
				
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto");
		redirect($redirect_url);		
	}

	
/*		 delete a game or mode
//////////////////////////////*/
	if ($delGame > 0)
	{		
		// Get all data wee need
		$sql		= "SELECT * FROM " . DECERTO_CAT. " WHERE id_decerto = " . $delGame;
		$result		= $db->sql_query_limit($sql,1);
		$row		= $db->sql_fetchrow($result);
		$shortlav	= $row['nome_corto'];
		$interlav	= $row['decerto_interid'];
		$intercpc	= $row['cpc'];
		$db->sql_freeresult($result);
		
		$sql	= "DELETE FROM " . DECERTO_MAP . " WHERE nome_corto = '{$shortlav}' AND decerto_interid = {$interlav} AND decerto_cpc = {$intercpc} ";
		$db->sql_query($sql);
		
		$sql	= "DELETE FROM " . DECERTO_CAT . " WHERE id_decerto = " . $delGame;
		$db->sql_query($sql);
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto");
		redirect($redirect_url);
	}
	
/*		 delete a map
//////////////////////////////*/
	if ($delMap > 0)
	{
		$sql	= "DELETE FROM " . DECERTO_MAP . " WHERE id_mappa_decerto = " . $delMap;
		$db->sql_query($sql);
		
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_decerto");
		redirect($redirect_url);
	}

	
	// General definition
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action,
		'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : ''
	));
}

?>