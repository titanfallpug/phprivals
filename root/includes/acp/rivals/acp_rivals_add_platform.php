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
 * Add a Platform
 * Called from acp_rivals with mode == 'add_platform'
 */
function acp_rivals_add_platform($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	$error	= array();
	
	if ($submit)
	{
		// Yes, handle the form.
		$platform_name	= utf8_normalize_nfc(request_var('platform_name', '', true));
		
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
		}
		else
		{
			$pltf_logo_name		= 'nologo.jpg';
			$pltf_logo_width	= 400;
			$pltf_logo_height	= 100;
		}

		if (!sizeof($error))
		{
			// Run the query to add the platform.
			$sql_array	= array(
				'platform_name'		=> (string) $platform_name,
				'platform_logo'		=> $pltf_logo_name,
				'platform_logo_w'	=> $pltf_logo_width,
				'platform_logo_h'	=> $pltf_logo_height
			);
			$sql = "INSERT INTO " . PLATFORMS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_platform");
			meta_refresh(3, $redirect_url);
			trigger_error('PLATFORM_ADDED');
		}
		
		$template->assign_vars(array(
			'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : '',
		));
	}

	// Assign the other variables to the template.
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action,
		'ERROR'		=> (sizeof($error)) ? implode('<br />', $error) : '',
	));
}

?>