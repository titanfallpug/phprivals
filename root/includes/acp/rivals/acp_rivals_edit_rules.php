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
 * Edit Rules
 * Called from acp_rivals with mode == 'edit_rules'
 */
function acp_rivals_edit_rules($id, $mode, $u_action)
{
	global	$db, $user, $config, $auth, $template, $table_prefix;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$submit		= (!empty($_POST['submit'])) ? true : false;
	$editalo	= (!empty($_POST['editalo'])) ? true : false;
	$delete		= (!empty($_POST['delete'])) ? true : false;
	$modda		= (!empty($_POST['modifica'])) ? true : false;
	$rules_id	= (int) request_var('id_lavorata', 0);
	$dettaglio	= (int) request_var('rules', 0);
	$ladderids	= array();
	$ladder		= new ladder();

	if (empty($dettaglio))
	{
		// Loop through the ladders.
		$template->assign_vars(array(
			'S_LADDER'	=> $ladder->make_ladder_select(false, false, true, false)
		));
		
		// Are we submitting a form?
		if (!empty($submit))
		{
			// Yes, handle the form.
			$addnew     = (int) request_var('addnew', 0);
			
			// Add new one
			if (!empty($addnew))
			{
				$nid_ladder = (int) request_var('nid_ladder', 0);	
					
				// Get Ladder e Platform ID for this.
				$sql		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $nid_ladder;
				$result		= $db->sql_query($sql);
				$row		= $db->sql_fetchrow($result);
				$mainladder = $row['ladder_parent'];
				$db->sql_freeresult($result);
				
				$sql_mld		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $mainladder;
				$result_mld		= $db->sql_query($sql_mld);
				$row_mld		= $db->sql_fetchrow($result_mld);
				$nid_platform 	= $row_mld['ladder_platform'];
				$db->sql_freeresult($result_mld);
			
				// check for double insert
				$sql_check		= "SELECT * FROM " . RIVAL_LADDER_RULES . " WHERE rules_ladder = {$nid_ladder} AND rules_platform = {$nid_platform} ";
				$result_check	= $db->sql_query($sql_check);
				$row_check		= $db->sql_fetchrow($result_check);
				$nid_check 		= $row_check['rules_id'];
				$db->sql_freeresult($result_check);
			
				if (!empty($nid_check))
				{
					trigger_error('RULES_DOPPIA');
				}
				else
				{
					$sql_array	= array(
						'rules_ladder'			=> $nid_ladder,
						'rules_platform'		=> $nid_platform,
						'requisiti_iscrizione'	=> '<em>na</em>',
						'regole_generali'		=> '<em>na</em>',
						'configurazione'		=> '<em>na</em>',
						'divieti'				=> '<em>na</em>',
					);
					// Add it.
					$sql = "INSERT INTO " . RIVAL_LADDER_RULES . " " . $db->sql_build_array('INSERT', $sql_array);
					$db->sql_query($sql);

					// Completed. Let the user know.
					$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_rules");
					meta_refresh(2, $redirect_url);
					trigger_error('RULES_CHART_ADDED');
				}
				
			}
		} // fine new	
	 
		// Get the platform data.
		$sql	= "SELECT * FROM " . RIVAL_LADDER_RULES;
		$result	= $db->sql_query($sql);
		$i	= 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$ladder_data	= $ladder->get_roots($row['rules_ladder']);
			
			// Assign the information to the template.
			$template->assign_block_vars('block_rules', array(
				'RULES_ID'		=> $row['rules_id'],
				'LADDER_NAME'	=> $ladder_data['SUBLADDER_NAME'],
				'MAIN_LADDER'	=> $ladder_data['LADDER_NAME'],
				'PLATFORM'		=> $ladder_data['PLATFORM_NAME']
			));
			$i++;
		}
		$db->sql_freeresult($result);
		

		// ACTION DATA
		if ($editalo)
		{
			redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_rules&amp;rules={$rules_id}"));
		}
		if ($delete)
		{
			// Yes. Delete the ladder.
			$sql = "DELETE FROM " . RIVAL_LADDER_RULES . " WHERE rules_id = " . $rules_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$urls2 = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_rules");
			meta_refresh(2, $urls2);
			trigger_error('RULES_CHART_DELETED');
		}
	}
/******************************************
* EDIT DETAIL STEP
*******************/
	else
	{
		// Get rules data
		$sql	= "SELECT * FROM " . RIVAL_LADDER_RULES . " WHERE rules_id = " . $dettaglio;
		$result	= $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$ladder_data	= $ladder->get_roots($row['rules_ladder']);
		
		// Assign the information to the template.
		$template->assign_block_vars('block_rules_detail', array(
			'U_ACTION'		=> $u_action,
			'RULES_ID'		=> $row['rules_id'],
			'LADDER_ID'		=> $row['rules_ladder'],
			'PLATFORM_ID'	=> $row['rules_platform'],
			'LADDER_NAME'	=> $ladder_data['SUBLADDER_NAME'],
			'MAIN_LADDER'	=> $ladder_data['LADDER_NAME'],
			'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
			'REQUISITI'		=> $row['requisiti_iscrizione'],
			'REG_GENERALI'	=> $row['regole_generali'],
			'CONFIG'		=> $row['configurazione'],
			'VIETATO' 		=> $row['divieti']
		));

		if ($modda)
		{
			$rules_id	= (int) request_var('rules', 0);

			$rules_ladder	        = (int) request_var('rules_ladder', 0);
			$rules_platform	        = (int) request_var('rules_platform', 0);
			$requisiti_iscrizione	= (string) utf8_normalize_nfc(request_var('requisiti_iscrizione', '', true));
			$regole_generali 	    = (string) utf8_normalize_nfc(request_var('regole_generali', '', true));
			$configurazione	        = (string) utf8_normalize_nfc(request_var('configurazione', '', true));
			$divieti 	            = (string) utf8_normalize_nfc(request_var('divieti', '', true));

			// Update the ladder's data.
			$sql_array	= array(
				'rules_ladder'			=> $rules_ladder,
				'rules_platform'		=> $rules_platform,
				'requisiti_iscrizione'	=> $requisiti_iscrizione,
				'regole_generali' 		=> $regole_generali,
				'configurazione'		=> $configurazione,
				'divieti'				=> $divieti,
			);
			$sql = "UPDATE " . RIVAL_LADDER_RULES . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE rules_id = " . $rules_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$urls2 = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_rules");
			meta_refresh(2, $urls2);
			trigger_error('RULES_CHART_UPDATED');
		}

	}
}

?>