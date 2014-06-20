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
 * Edit Platforms
 * Called from acp_rivals with mode == 'edit_platforms'
 */
function acp_rivals_edit_platforms($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	$error	= array();
	
	if ($submit)
	{
		// Yes, handle the form.
		$delete			= (int) request_var('delete', 0);
		$platform_id	= (int) request_var('platform_id', 0);

		// Are we deleting?
		if (!empty($delete))
		{
			// Yes. Delete the platform.
			$sql	= "DELETE FROM " . PLATFORMS_TABLE . " WHERE platform_id = " . $platform_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_platforms");
			meta_refresh(2, $redirect_url);
			trigger_error('PLATFORM_UPDATED');
		}

		$platform_name	= (string) utf8_normalize_nfc(request_var('platform_name', '', true));
		
		// get current platform infos.
		$sql_2		= "SELECT * FROM " . PLATFORMS_TABLE . " WHERE platform_id = " . $platform_id;
		$result_2	= $db->sql_query_limit($sql_2, 1);
		$row_2		= $db->sql_fetchrow($result_2);
		$db->sql_freeresult($result_2);
		
		if (empty($platform_name))
		{
			$error[] = $user->lang['PLATFORM_NAME_EMPTY'];
		}
		
		// cover
		if (!empty($_FILES['platform_logo']['type']))
		{
			include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
			$upload = new fileupload('LOGOMEN_', array('jpg', 'jpeg', 'gif', 'png'), 163840, 400, 100, 400, 100, explode('|', $config['mime_triggers']));
		
			$foto = $upload->form_upload('platform_logo');
			$foto->clean_filename('real', '', $user->data['user_id']);
		
			$destination = "{$phpbb_root_path}images/rivals/platformlogo/";
			if (substr($destination, -1, 1) == '/' || substr($destination, -1, 1) == '\\')
			{
				$destination = substr($destination, 0, -1);
			}
			$destination	= str_replace(array('../', '..\\', './', '.\\'), '', $destination);
			if ($destination && ($destination[0] == '/' || $destination[0] == "\\"))
			{
				$destination = '';
			}
		
			$foto->move_file($destination, true, false, 0644);
			$pltf_logo_name		= $foto->get('realname');
			$pltf_logo_width	= $foto->get('width');
			$pltf_logo_height 	= $foto->get('height');
			
			if (sizeof($foto->error))
			{
				$foto->remove();
				$error = array_merge($error, $foto->error);
			}
			else
			{
				$shalla	= "{$phpbb_root_path}images/rivals/platformlogo/";
				chmod($shalla . $pltf_logo_name, 0644);
			}
			
			$sql_array	= array(
				'platform_name'		=> $platform_name,
				'platform_logo'		=> $pltf_logo_name,
				'platform_logo_w'	=> $pltf_logo_width,
				'platform_logo_h'	=> $pltf_logo_height
			);
			
			if (!sizeof($error))
			{
				$sql = "UPDATE " . PLATFORMS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE platform_id = " . $platform_id;
				$db->sql_query($sql);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_platforms");
				meta_refresh(2, $redirect_url);
				trigger_error('PLATFORM_UPDATED');
			}
			
			$template->assign_vars(array(
				'ERROR' => (sizeof($error)) ? implode('<br />', $error) : ''
			));
		}
		else
		{
			if (!sizeof($error))
			{
				$sql_array	= array(
					'platform_name'		=> $platform_name
				);
				
				$sql = "UPDATE " . PLATFORMS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE platform_id = " . $platform_id;
				$db->sql_query($sql);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_platforms");
				meta_refresh(2, $redirect_url);
				trigger_error('PLATFORM_UPDATED');
			}
			
			$template->assign_vars(array(
				'ERROR' => (sizeof($error)) ? implode('<br />', $error) : ''
			));
		}
	}

	// Get the platform data.
	$sql	= "SELECT * FROM " . PLATFORMS_TABLE;
	$result	= $db->sql_query($sql);
	$i	= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign each platform to the template.
		$template->assign_block_vars('block_platforms', array(
			'U_ACTION'		=> $u_action,
			'ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : '',
			'PLATFORM_ID'	=> $row['platform_id'],
			'CURRENT_LOGO'	=> "{$phpbb_root_path}images/rivals/platformlogo/{$row['platform_logo']}",
			'PLATFORM_NAME'	=> $row['platform_name'])
		);
		$i++;
	}
	$db->sql_freeresult($result);
	
	// If empty
	if ($i == 0)
	{
		// They have not added a platform.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_platform");
		meta_refresh(8, $redirect_url);
		trigger_error($user->lang['MUST_ADD_PLATFORM'] . adm_back_link($redirect_url));
	}
}

?>