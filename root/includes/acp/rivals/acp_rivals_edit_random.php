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
 * Edit Random map
 * Called from acp_rivals with mode == 'edit_random'
 */
function acp_rivals_edit_random($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	// ADD GAMES
	$newrandom	= (!empty($_POST['newrandom'])) ? true : false;
	$eraser		= (!empty($_POST['cancella'])) ? true : false;
	$selettore	= (!empty($_POST['selettore'])) ? true : false;
	$add1map	= (!empty($_POST['add1map'])) ? true : false;
	$delAmap	= (int) request_var('singlemap_del', 0);
	$S_manual	= false;
	$S_hidden	= false;
	$error		= array();
	
	// Are we submitting a form?
	if ($newrandom)
	{
		$gioco = (string) utf8_normalize_nfc(request_var('gioco', '', true));
		$short = (string) utf8_normalize_nfc(request_var('short_name', '', true));
		
		//Fix shortname
		$short = (strlen($short) > 10) ? substr(strtolower($short),0,10) : strtolower($short);
		$short = str_replace(" ", "", $short);
		
		// Check the entries
		if (!$gioco || !$short)
		{
			$error[] = $user->lang['EMPTY_RANDOM_MAP_FIELD'];
		}
		
		// Check if the short is already used
		$sqlcc		= "SELECT * FROM " . RANDOM_TABLE . " WHERE short_name = '{$short}'";
		$resultcc	= $db->sql_query_limit($sqlcc,1);
		$rowcc		= $db->sql_fetchrow($resultcc);
		$db->sql_freeresult($resultcc);
					
		if (!empty($rowcc['short_name']))
		{
			$error[] = $user->lang['SHORTY_PRESENTE'];
		}
		
		if (!sizeof($error))
		{
			$newdir = "{$phpbb_root_path}images/rivals/random/{$short}"; // creo la nuova cartella
			mkdir("{$newdir}", 0755);
			
			// menage imgs
			$zipimgs = false;
			// Load with zip
			if (!empty($_FILES['images']['type']))
			{
				$uploaddir	= "{$phpbb_root_path}images/rivals/random/temp/";
				$filename	= $_FILES['images']['name'];
				move_uploaded_file($_FILES['images']['tmp_name'], $uploaddir . $_FILES['images']['name']);
				$op = "{$phpbb_root_path}images/rivals/random/temp/" . $_FILES['images']['name'];
				chmod($op, 0777);
				
				if (empty($filename))
				{
					$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_random");
					meta_refresh(4, $redirect_url);
					trigger_error(sprintf($user->lang['ZIP_MANCANTE'], '<a href="' . $redirect_url . '">', '</a>'));
				}
				else
				{
					include($phpbb_root_path . 'rivals/classes/pclzip.lib.' . $phpEx);
					$archive = new PclZip("{$op}");
					if (($v_result_list = $archive->extract(PCLZIP_OPT_PATH, "{$newdir}")) == 0)
					{
						die("Error : ".$archive->errorInfo(true));
					}
				}
				$zipimgs = true;
				
				$files = array(); // Make a array for map name
				if ($dh = opendir($newdir))
				{ 
					while(($file = readdir($dh)) !== false) // get single map each time
					$files[] = $file;
					closedir($dh); // close connection to dir
				}
				sort($files); // order alphabetically
				reset($files); // Reget the first map
				foreach ($files as $file)
				{
					if ($file != ".." && $file != ".")
					{
						$sql_array	= array(
							'randimg_short_name'	=> $short,
							'randimg_img' 			=> $file
						);
						// Make it
						$sql	= "INSERT INTO " . RANDOM_IMGS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
						$db->sql_query($sql);
					}
				}
				unlink($op); // delete zip file
			}
	  
			// Update datas
			$sql_array	= array(
				'gioco'			=> $gioco,
				'short_name'	=> $short,
				'tempo'			=> 0,
				'img_mappa'		=> 0
			);
			// Make it
			$sql	= "INSERT INTO " . RANDOM_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);
				
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_random");
			meta_refresh(2, $redirect_url);
			if (!$zipimgs)
			{
				trigger_error('RANDOM_ADDED_NOIMGS');
			}
			else
			{
				trigger_error('RANDOM_ADDED');
			}
		}
	}
	
	// The selector
	$sql_mld	= " SELECT * FROM " . RANDOM_TABLE . " ORDER BY gioco ASC ";
	$result_mld	= $db->sql_query($sql_mld);
	$i	= 0;
	while ($row_mld = $db->sql_fetchrow($result_mld))
	{
		$sel_gioco = $row_mld['gioco'];
		$sel_short = $row_mld['short_name'];
	   
		$template->assign_block_vars('selettore', array(
			'GIOCO' 		=> $sel_gioco,
			'SHORT' 		=> $sel_short
		));
		$i++;
	}
	$db->sql_freeresult($result_mld);
		
// Show mapset
	if ($selettore)
	{
		$shortnm	= (string) utf8_normalize_nfc(request_var('where', '', true));
		if (!empty($shortnm))
		{
			$sql_1		= "SELECT * FROM " . RANDOM_TABLE . " WHERE short_name = '{$shortnm}'";
			$result_1	= $db->sql_query($sql_1);
			$row_1		= $db->sql_fetchrow($result_1);
			$shorter	= $row_1['short_name'];
			$db->sql_freeresult($result_1);
		
			$template->assign_block_vars('mappa', array(
				'GIOCO' => $row_1['gioco'],
				'SHORT' => $shorter
			));

			// imgs selected
			$sql_img	= "SELECT * FROM " . RANDOM_IMGS_TABLE . " WHERE randimg_short_name = '{$shorter}' ";
			$result_img	= $db->sql_query($sql_img);
			$iy	= 0;
			while ($row_img = $db->sql_fetchrow($result_img))
			{
				// Assign the information to the template.
				$template->assign_block_vars('block_mappa', array(
					'URL'	=> "{$phpbb_root_path}images/rivals/random/" . $row_img['randimg_short_name'],
					'DELLK'	=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_random&singlemap_del=" . $row_img['randimg_id']),
					'IMG'	=> $row_img['randimg_img']
				));
				$iy++;
			}
			$db->sql_freeresult($result_img);
		}
		
		$S_manual	= true;
		$S_hidden	= $shorter;
	}
		
	// Delete a mapset
	if ($eraser)
	{
		$where	= (string) utf8_normalize_nfc(request_var('where', '', true));
		
		if (!empty($where))
		{
			$sql	= "DELETE FROM " . RANDOM_TABLE . " WHERE short_name = '{$where}' ";
			$db->sql_query($sql);
	   
			$sql	= "DELETE FROM " . RANDOM_IMGS_TABLE . " WHERE randimg_short_name = '{$where}' ";
			$db->sql_query($sql);
			
			// remove images and directory
			$cartella = "{$phpbb_root_path}images/rivals/random/{$where}/";
			$apertura = opendir($cartella);
			$imgs = array();
			while (false !== ($file = readdir($apertura)))
			{
				if (!is_dir($file))
				{
					$imgs[] = $file;
					unlink($cartella . $file);
				}    
			}
			closedir($apertura);
			rmdir("{$phpbb_root_path}images/rivals/random/{$where}/");

			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_random");
			meta_refresh(2, $redirect_url);
			trigger_error('RANDOM_DELETED');
		}
	}
		
	// Delete a single map
	if (!empty($delAmap))
	{
		// Get img name
		$sql_8		= "SELECT * FROM " . RANDOM_IMGS_TABLE . " WHERE randimg_id = " . $delAmap;
		$result_8	= $db->sql_query($sql_8);
		$row_8		= $db->sql_fetchrow($result_8);
		$db->sql_freeresult($result_1);
		
		@unlink("{$phpbb_root_path}images/rivals/random/{$row_8['randimg_short_name']}/" . $row_8['randimg_img']);
		
		$sql	= "DELETE FROM " . RANDOM_IMGS_TABLE . " WHERE randimg_id = " . $delAmap;
		$db->sql_query($sql);
			
		redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_random"));
	}
	
	// Add single map
	if ($add1map)
	{
		$xmap	= (string) utf8_normalize_nfc(request_var('xmapset', '', true));
		
		if (!empty($_FILES['mapimg']['type']) && !empty($xmap))
		{
			include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
			$upload	= new fileupload('MAP_', array('jpg', 'jpeg', 'gif', 'png'), 81920, 300, 160, 300, 170, explode('|', $config['mime_triggers']));
				
			$mapimg	= $upload->form_upload('mapimg');
			$mapimg->clean_filename('real', '', $user->data['user_id']);
			$destination	= "{$phpbb_root_path}images/rivals/random/{$xmap}/";
			
			if ($destination[strlen($destination) - 1] == '/' || $destination[strlen($destination) - 1] == '\\')
			{
				$destination = substr($destination, 0, -1);
			}

			$destination = str_replace(array('../', '..\\', './', '.\\'), '', $destination);
			if ($destination && ($destination[0] == '/' || $destination[0] == "\\"))
			{
				$destination = '';
			}
			
			$mapimg->move_file($destination, false, false, 0644);
			$mapname	= $mapimg->get('realname');
			
			if (sizeof($mapimg->error))
			{
				$mapimg->remove();
				$error = array_merge($error, $mapimg->error);
			}
			else
			{
				$shalla	= "{$phpbb_root_path}images/rivals/random/{$xmap}/";
				chmod($shalla . $mapname, 0644);
			}
			
			if (!sizeof($error))
			{
				$sql_array	= array(
					'randimg_short_name'	=> $xmap,
					'randimg_img' 			=> $mapname
				);
				$sql	= "INSERT INTO " . RANDOM_IMGS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
				
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_random");
				meta_refresh(2, $redirect_url);
				trigger_error('MAP_MANUALLY_ADDED');
			}
			
			$template->assign_vars(array(
				'ERROR' => (sizeof($error)) ? implode('<br />', $error) : ''
			));
		}
	}
	
	$template->assign_vars(array(
		'U_ACTION'				=> $u_action,
		'S_SHOW_MANUAL_UPLOAD'	=> $S_manual,
		'S_HIDDEN_MAPID'		=> (!$S_hidden) ? '' : '<input type="hidden" name="xmapset" value="' . $S_hidden . '" />',
		'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : ''
	));
}
?>