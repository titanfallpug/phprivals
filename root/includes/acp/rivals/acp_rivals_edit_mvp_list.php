<?php
/**
*
* @package RivalsMod
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
 * Edit MVP list
 * Called from acp_rivals with mode == 'edit_mvp_list'
 */
function acp_rivals_edit_mvp_list($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;
	
	$ladder = new ladder();
	$submit	= (!empty($_POST['submit'])) ? true : false;

	// Are we submitting a form?
	if ($submit)
	{
		// Yes, handle the form.
		$delete	= request_var('delete', 0);
		$mvp_id	= request_var('mvp_id', 0);
		
		$nome_mvp	        = utf8_normalize_nfc(request_var('nome_mvp', '', true));
		$descrizione_mvp	= utf8_normalize_nfc(request_var('descrizione_mvp', '', true));

		// Are we deleting?
		if (!empty($delete))
		{
			// Yes. Delete the ladder.
			$sql = "DELETE FROM " . RIVAL_MVP. " WHERE mvp_id = " . $mvp_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_mvp_list");
			meta_refresh(2, $redirect_url);
			trigger_error('MVP_CHART_UPDATED');
		}
		else
		{
			// Update the ladder's data.
			$sql_array	= array(
				'nome_mvp'			=> $nome_mvp,
				'descrizione_mvp'	=> $descrizione_mvp
			);
			$sql = "UPDATE " . PLATFORMS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE mvp_id = " . $mvp_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_mvp_list");
			meta_refresh(2, $redirect_url);
			trigger_error('MVP_CHART_UPDATED');
		}
	}

	// Get the mvp list data.
	$sql	= "SELECT * FROM " . RIVAL_MVP;
	$result	= $db->sql_query($sql);
	$i		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$ladder_data	= $ladder->get_roots($row['ladder_mvp']);
		
		// Assign the information to the template.
		$template->assign_block_vars('block_mvp', array(
			'U_ACTION'		=> $u_action,
			'MVP_ID'		=> $row['mvp_id'],
			'MVP_NAME'		=> $row['nome_mvp'],
			'MVP_DESC'		=> $row['descrizione_mvp'],
			'PLATFORM_MVP'	=> $ladder_data['PLATFORM_NAME'],
			'MAIN_LADDER'	=> $ladder_data['LADDER_NAME'],
			'LADDER_MVP'	=> $ladder_data['SUBLADDER_NAME']
		));

		$i++;		
	}
	$db->sql_freeresult($result);
	
	// If empty
	if ($i == 0)
	{
		$addmvpurl = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_mvp_list");
		trigger_error($user->lang['ANY_MVP_LIST_ADDED'] . adm_back_link($addmvpurl));
	}
}
?>