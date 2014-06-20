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
 * Add a Ladder
 * Called from acp_rivals with mode == 'add_ladder'
 */
function acp_rivals_add_ladder($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$ladder	= new ladder();
	$errore	= array();

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		// Yes, handle the form.
		$ladder_type	= (int) request_var('ladder_type', 0);
		if ($ladder_type == 0)
		{
			// Handling a ladder...
			$ladder_name		= (string) utf8_normalize_nfc(request_var('ladder_name', '', true));
			$ladder_rules		= (string) utf8_normalize_nfc(request_var('ladder_rules', '', true));
			$ladder_platform	= (int) request_var('ladder_platform', 0);

			// Setup the BBcode for the ladder rules.
			$uid			= $bitfield = $options = '';
			$allow_bbcode	= $allow_urls = $allow_smilies = true;
			generate_text_for_storage($ladder_rules, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			// cover
			if (!empty($_FILES['ladder_logo']['type']))
			{
				include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
				$upload	= new fileupload('LOGOMEN_', array('jpg', 'jpeg', 'gif', 'png'), 163840, 900, 150, 900, 150, explode('|', $config['mime_triggers']));
				
				$foto	= $upload->form_upload('ladder_logo');
				$foto->clean_filename('real', '', $user->data['user_id']);
				
				$destination	= "{$phpbb_root_path}images/rivals/ladderlogo/";
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
				$lad_logo_name		= $foto->get('realname');
				$lad_logo_width		= $foto->get('width');
				$lad_logo_height	= $foto->get('height');
				
				if (sizeof($foto->error))
				{
					$foto->remove();
					$errore = array_merge($errore, $foto->error);
				}
				else
				{
					$shalla	= "{$phpbb_root_path}images/rivals/ladderlogo/";
					chmod($shalla . $lad_logo_name, 0644);
				}
			}
			else
			{
				$lad_logo_name		= 'nologo.jpg';
				$lad_logo_width		= 900;
				$lad_logo_height	= 150;
			}
			
			if (empty($ladder_name))
			{
				$errore[] = $user->lang['NAME_LADDER_EMPTY'];
			}
			
			if (!sizeof($errore))
			{
				//Get the largest ladder order.
				$sql	= "SELECT MAX(ladder_order) AS lo FROM " . LADDERS_TABLE . " WHERE ladder_parent = 0";
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				// Set up the SQL to add the ladder.
				$sql_array	= array(
					'ladder_name' 		=> $ladder_name,
					'ladder_platform' 	=> $ladder_platform,
					'ladder_rules' 		=> $ladder_rules,
					'ladder_order' 		=> $row['lo'] + 1,
					'ladder_desc' 		=> '',
					'subladder_order' 	=> 0,
					'bbcode_uid' 		=> $uid,
					'bbcode_bitfield'	=> $bitfield,
					'bbcode_options' 	=> $options,
					'ladder_logo' 		=> $lad_logo_name,
					'ladder_logo_w' 	=> $lad_logo_width,
					'ladder_logo_h' 	=> $lad_logo_height
				); 
				$sql	= "INSERT INTO " . LADDERS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_ladder");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_ADDED');
			}
			
			$template->assign_vars(array(
				'ERRORE' => (sizeof($errore)) ? implode('<br />', $errore) : ''
			));
		}
		else if ($ladder_type == 1)
		{
			// Handling a sub-ladder.
			$ladder_name	= (string) utf8_normalize_nfc(request_var('ladder_name', '', true));
			$ladder_desc	= (string) utf8_normalize_nfc(request_var('ladder_desc', '', true));
			$shortname  	= (string) utf8_normalize_nfc(request_var('shorty', '', true));
			$ladder_cl		= (int) request_var('ladder_cl', 0);
			$ladder_parent	= (int) request_var('ladder_parent', 0);
			$ladder_ranking	= (int) request_var('ladder_ranking', 0);
			$ladder_rm		= (int) request_var('ladder_rm', 0);
			$ladder_style	= (int) request_var('ladder_style', 0);
			$ladder_mvp  	= (int) request_var('ladder_mvp', 0);
			$ladder_advstat	= (int) request_var('ladder_advstats', 0);
			$win_system		= (int) request_var('win_system', 0);
			$ladder_mod		= (string) utf8_normalize_nfc(request_var('ladder_mod', 0, true));
			$ladder_limit	= (int) request_var('ladder_limit', 0);
				
			// Setup the BBcode for the ladder description.
			$uid			= $bitfield = $options = '';
			$allow_bbcode	= $allow_urls = $allow_smilies = true;
			generate_text_for_storage($ladder_desc, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			if (empty($ladder_name))
			{
				$errore[] = $user->lang['NAME_LADDER_EMPTY'];
			}
			if (($ladder_style == 3) && ($win_system == 1))
			{
				$errore[] = $user->lang['CALCIO_NON_RISULTATO_PER_SCELTA'];
			}
			if (($ladder_style == 1) && ($win_system == 1))
			{
				$errore[] = $user->lang['DECERTO_NON_RISULTATO_PER_SCELTA'];
			}
			if (($ladder_style == 1) && (empty($shortname)) || ($ladder_style == 2) && (empty($shortname)))
			{
				$errore[] = $user->lang['DEVI_COLLEGARE_DECERTO_CPC'];
			}
			if (($ladder_mod != 0) && !is_numeric($ladder_mod))
			{
				$errore[] = $user->lang['MOD_ID_NON_NUMERIC'];
			}
			if (!sizeof($errore))
			{
				//Get the largest ladder order.
				$sql	= "SELECT MAX(subladder_order) AS lo FROM " . LADDERS_TABLE . " WHERE ladder_parent = " . $ladder_parent;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				// Set up the SQL to add the sub-ladder.
				$sql_array	= array(
					'ladder_name'		=> $ladder_name,
					'ladder_desc'		=> $ladder_desc,
					'ladder_parent'		=> $ladder_parent,
					'ladder_rules'		=> '',
					'ladder_order'		=> 0,
					'subladder_order'	=> $row['lo'] + 1,
					'ladder_cl'			=> $ladder_cl,
					'ladder_style'		=> $ladder_style,
					'shortname'			=> $shortname,
					'ladder_mvp'		=> $ladder_mvp,
					'ladder_advstat'	=> $ladder_advstat,
					'ladder_ranking'	=> $ladder_ranking,
					'ladder_win_system'	=> $win_system,
					'ladder_mod' 		=> $ladder_mod,
					'ladder_rm' 		=> $ladder_rm,
					'bbcode_uid' 		=> $uid,
					'bbcode_bitfield'	=> $bitfield,
					'bbcode_options' 	=> $options,
					'ladder_limit'		=> $ladder_limit,
					'ladder_oneone' 	=> 0
				);
				$sql	= "INSERT INTO " . LADDERS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_ladder");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_ADDED');
			}
			$template->assign_vars(array(
				'ERRORE' => (sizeof($errore)) ? implode('<br />', $errore) : ''
			));
		}
		else if ($ladder_type == 2)
		{
			// Handling a sub-ladder.
			$ladder_name	= (string) utf8_normalize_nfc(request_var('ladder_name', '', true));
			$ladder_desc	= (string) utf8_normalize_nfc(request_var('ladder_desc1', '', true));
			$shortname  	= (string) utf8_normalize_nfc(request_var('shorty1', '', true));
			$ladder_cl		= (int) request_var('ladder_cl1', 0);
			$ladder_parent	= (int) request_var('ladder_parent1', 0);
			$ladder_ranking	= (int) request_var('ladder_ranking1', 0);
			$ladder_style	= (int) request_var('ladder_style1', 0);
			$win_system		= (int) request_var('win_system1', 0);
			$ladder_mod		= (string) utf8_normalize_nfc(request_var('ladder_mod1', 0, true));
			$ladder_limit	= (int) request_var('ladder_limit1', 0);
			
			// Setup the BBcode for the ladder description.
			$uid			= $bitfield = $options = '';
			$allow_bbcode	= $allow_urls = $allow_smilies = true;
			generate_text_for_storage($ladder_desc, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			if (empty($ladder_name))
			{
				$errore[] = $user->lang['NAME_LADDER_EMPTY'];
			}
			if (($ladder_style == 3) && ($win_system == 1))
			{
				$errore[] = $user->lang['CALCIO_NON_RISULTATO_PER_SCELTA'];
			}
			if (($ladder_style == 1) && ($win_system == 1))
			{
				$errore[] = $user->lang['DECERTO_NON_RISULTATO_PER_SCELTA'];
			}
			if (($ladder_style == 1) && (empty($shortname)) || ($ladder_style == 2) && (empty($shortname)))
			{
				$errore[] = $user->lang['DEVI_COLLEGARE_DECERTO_CPC'];
			}
			if (($ladder_mod != 0) && !is_numeric($ladder_mod))
			{
				$errore[] = $user->lang['MOD_ID_NON_NUMERIC'];
			}
			
			if (!sizeof($errore))
			{
				//Get the largest ladder order.
				$sql	= "SELECT MAX(subladder_order) AS lo FROM " . LADDERS_TABLE . " WHERE ladder_parent = " . $ladder_parent;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				// Set up the SQL to add the sub-ladder.
				$sql_array	= array(
					'ladder_name' 		=> $ladder_name,
					'ladder_desc'		=> $ladder_desc,
					'ladder_parent' 	=> $ladder_parent,
					'ladder_rules' 		=> '',
					'ladder_order' 		=> 0,
					'subladder_order' 	=> $row['lo'] + 1,
					'ladder_cl' 		=> $ladder_cl,
					'ladder_style' 		=> $ladder_style,
					'shortname' 		=> $shortname,
					'ladder_mvp'		=> 0,
					'ladder_advstat' 	=> 0,
					'ladder_ranking' 	=> $ladder_ranking,
					'ladder_win_system' => $win_system,
					'ladder_mod' 		=> $ladder_mod,
					'ladder_rm' 		=> 0,
					'bbcode_uid' 		=> $uid,
					'bbcode_bitfield' 	=> $bitfield,
					'bbcode_options' 	=> $options,
					'ladder_limit'		=> $ladder_limit,
					'ladder_oneone' 	=> 1
				);
				$sql	= "INSERT INTO " . LADDERS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_ladder");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_ADDED');
			}
			$template->assign_vars(array(
				'ERRORE' => (sizeof($errore)) ? implode('<br />', $errore) : ''
			));
		}
	}
	
	// Get the number of platforms.
	$sql	= "SELECT COUNT(platform_id) AS num_platforms FROM " . PLATFORMS_TABLE;
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	// Check if they can make a ladder (platform must be made first!).
	if($row['num_platforms'] == 0)
	{
		// They have not added a platform.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_platform");
		meta_refresh(8, $redirect_url);
		trigger_error($user->lang['MUST_ADD_PLATFORM'] . adm_back_link($redirect_url));
	}

	$sql	= "SELECT * FROM " . PLATFORMS_TABLE;
	$result	= $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign each platform to the template.
		$template->assign_block_vars('block_platforms', array(
			'PLATFORM_ID'	=> $row['platform_id'],
			'PLATFORM_NAME' => $row['platform_name']
		));
	}
	$db->sql_freeresult($result);

	// decerto
	$sql_43	= "SELECT * FROM " . DECERTO_CAT . " GROUP BY nome_corto";
	$result_43	= $db->sql_query($sql_43);
	while ($row_43 = $db->sql_fetchrow($result_43))
	{
		if (validate_decerto($row_43['nome_corto']) === true)
		{
			$template->assign_block_vars('block_decerto', array(
				'NOMECORTO' => $row_43['nome_corto'],
				'NOMELUNGO' => $row_43['nome_gioco'],
				'TIPO'		=>($row_43['cpc'] == 1) ? $user->lang['DECERTO'] : $user->lang['CPC']
			));
		}
	}
	$db->sql_freeresult($result_43);
			
	// Loop through the ladders.
	$sql	= "SELECT l.*, p.* FROM " . LADDERS_TABLE . " l, " . PLATFORMS_TABLE . " p WHERE l.ladder_platform = p.platform_id";
	$result	= $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign each ladder to the template.
		$template->assign_block_vars('block_ladders', array(
			'LADDER_ID'			=> $row['ladder_id'],
			'LADDER_PLATFORM'	=> $row['platform_name'],
			'LADDER_NAME'		=> $row['ladder_name']
		));
	}
	$db->sql_freeresult($result);

	// Assign the other variables to the template.
	$template->assign_vars(array(
		'INFOLOGO'	=> "{$phpbb_root_path}rivals/images/infologo.gif",
		'U_ACTION'	=> $u_action
	));
}

?>