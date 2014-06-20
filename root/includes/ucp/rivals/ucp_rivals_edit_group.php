<?php
/**
*
* @package RivalsMod
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
 * Edit Group
 * Called from ucp_rivals with mode == 'edit_group'
 */
function ucp_rivals_edit_group($id, $mode, $u_action)
{
	global	$db, $config, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group	= new group();
	$ladder	= new ladder();
	
	// Check if the group is apart of a ladder yet.
	if (empty($user->data['group_session']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
	}
	
	$idgruppo 		= $group->data['group_id'];
	$error 			= array();
	$submit			= (!empty($_POST['submit'])) ? true : false;
	$group_delete	= (int) request_var('group_delete', 0);
	$logo_delete	= (int) request_var('logo_delete', 0);
	
	
	// Are we submitting a form?
	if ($submit)
	{
		$group_name			= (string) utf8_normalize_nfc(request_var('group_name', '', true));
		$group_desc			= (string) utf8_normalize_nfc(request_var('group_desc', '', true));
		$group_sito         = (string) utf8_normalize_nfc(request_var('group_sito', '', true));
		$group_logo			= (string) utf8_normalize_nfc(request_var('group_logo', '', true));
		$group_favmap       = (string) utf8_normalize_nfc(request_var('clan_favouritemap', '', true));
		$group_favteam      = (string) utf8_normalize_nfc(request_var('clan_favouriteteam', '', true));
		$group_id			= (int) request_var('group_id', 0);
		$group_guid			= (string) utf8_normalize_nfc(request_var('guid', '', true));
		$group_uac			= (int) request_var('uac', 0);
		
		// We are deleting the group	
		if (!empty($group_delete))
		{
			// CHECK IF THE CLAN HAVE OTHER FOUNDER AND COFOUNDER
			$sql1		= "SELECT * FROM " . USER_CLAN_TABLE . " WHERE group_id = {$group_id} AND group_leader != 0";
			$result1	= $db->sql_query($sql1);
			$iy			= 0;
			while ($row1 = $db->sql_fetchrow($result1))
			{
				// HAVE THEY 1 OTHER CLAN UNCLOSED!
				$sqls		= "SELECT * FROM " . USER_CLAN_TABLE . " AS uc LEFT JOIN " . CLANS_TABLE . " AS cl ON uc.group_id = cl.group_id
							WHERE uc.user_id = {$row1['user_id']} AND uc.group_id != {$group_id} AND uc.group_leader != 0 AND cl.clan_closed = 0";
				$results	= $db->sql_query_limit($sqls, 1);
				$rows		= $db->sql_fetchrow($results);
				$db->sql_freeresult($results);
				
				// IF YES PUT THIS CLAN IN SESSION IF NOT PUT 0
				$newclanses = (!empty($rows['group_id'])) ? $rows['group_id'] : 0;
				
				// UPDATE SESSION
				$sql	= "UPDATE " . USERS_TABLE . " SET group_session = {$newclanses} WHERE user_id = {$row1['user_id']}";
				$db->sql_query ($sql);
				
				$iy++;	
			}
			$db->sql_freeresult($result1);
			
			// CHECK IF THIS CLAN PLAY MATCHS OR IF JOIN TOURNAMENT
			$sql_n		= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_challenger = {$group_id} OR match_challengee = {$group_id}";
			$result_n	= $db->sql_query_limit($sql_n, 1);
			$row_n		= $db->sql_fetchrow($result_n);
			$db->sql_freeresult($result_n);
				
			$sql_t		= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = {$group_id}";
			$result_t	= $db->sql_query_limit($sql_t, 1);
			$row_t		= $db->sql_fetchrow($result_t);
			$db->sql_freeresult($result_t);
				
			if (!empty($row_n['match_id']) || !empty($row_t['group_tournament'])) 
			{
				// I DONT WANT LOSE STATS SO I DO NOT REMOVE CLAN, I CLOSE THE CLAN		 
				$sql	= "UPDATE " . CLANS_TABLE . " SET clan_closed = 1 WHERE group_id = {$group_id}";
				$db->sql_query($sql);
			}
			else
			{
				// I REMOVE THE CLAN AND ALL HIS REFERENCE
				$sql	= "DELETE FROM " . CLANS_TABLE . " WHERE group_id = {$group_id}";
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . USER_CLAN_TABLE . " WHERE group_id = {$group_id}";
				$db->sql_query($sql);
				
				// LADDER JOIN, CHALLANGE PENDING, MATCH FINDR
				$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_id = {$group_id}";
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE (challenger = {$group_id} OR challengee = {$group_id})";
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . MATCHES_TABLE . " WHERE (match_challengee = {$group_id} OR match_challenger = {$group_id})";
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_groupid = {$group_id}";
				$db->sql_query($sql);
				
				$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_id = {$group_id}";
				$db->sql_query($sql);
			}
			 
			$urls1 = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(2, $urls1);
			trigger_error('GROUP_DELETED');
		}
		
		// Check if a user has entered a clan name if not return the error
		if (empty($group_name))
		{
			$error[] = $user->lang['ENTER_GROUP_NAME'];
		}
		//check if there are a clan with same name	
		if ($group_name != $group->data['group_name'])
		{
			$sql_2_array = array(
				'group_name'	=> $group_name
			);
				
			$sql9		= 'SELECT group_id, group_name FROM ' . CLANS_TABLE . ' WHERE ' . $db->sql_build_array('SELECT', $sql_2_array);
			$result9	= $db->sql_query($sql9);
			$row9		= $db->sql_fetchrow($result9);
			$db->sql_freeresult($result9);
		
			if (!empty($row9['group_id']))
			{
				$error[] = $user->lang['CLAN_NAME_USED'];
			}
		}
			
		// Are we updating the clan logo?
		if (!empty($_FILES['group_logo']['type']) && empty($logo_delete))
		{
			include ($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
			
			$upload = new fileupload('IMG_', array('jpg', 'jpeg', 'gif', 'png'), 163840, 100, 100, 500, 500, explode('|', $config['mime_triggers']));
			
			$foto = $upload->form_upload('group_logo');
			$foto->clean_filename('real', '', $user->data['user_id']);
			
			$destination = "{$phpbb_root_path}images/rivals/clanlogo/";
			
			$foto->move_file($destination, true, false, 0644);
			$logotype 	= $foto->get('extension');
			$logo     	= $foto->get('realname');
			$logowidth	= $foto->get('width');
			$logoheight	= $foto->get('height');
			
			if (sizeof($foto->error))
			{
				$foto->remove();
				$error = array_merge($error, $foto->error);
			}
			else
			{
				chmod($destination . $logo, 0644);
			}
		}
		else if (!empty($logo_delete))
		{
			$logo		= 'nologo.jpg';
			$logotype	= 'jpg';
			$logowidth	= $logoheight = 100;
		}
		else
		{
			$sql		= "SELECT * FROM " . CLANS_TABLE . " WHERE group_id = {$group_id}";
			$result		= $db->sql_query_limit($sql, 1);
			$group_row2 = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
				
			$logo		= $group_row2['clan_logo_name'];
			$logotype	= $group_row2['clan_logo_ext'];
			$logowidth	= $group_row2['clan_logo_width'];
			$logoheight = $group_row2['clan_logo_height'];
		}

		// Validate GUID and UAC
		$sql5		= "SELECT group_id, uac FROM " . CLANS_TABLE . " WHERE uac = '{$group_uac}'";
		$result5	= $db->sql_query($sql5);
		$row5		= $db->sql_fetchrow($result5);
		$db->sql_freeresult($result5);
		
		if (!empty($row5['group_id']) && $group_uac != 0 && $group_uac != $group->data['uac'])
		{
			$error[] = $user->lang['UAC_USED'];
		}
		
		$sql5		= "SELECT group_id, guid FROM " . CLANS_TABLE . " WHERE guid = '{$group_guid}'";
		$result5	= $db->sql_query($sql5);
		$row5		= $db->sql_fetchrow($result5);
		$db->sql_freeresult($result5);
		
		if (!empty($row5['group_id']) && !empty($group_guid) && $group_guid != $group->data['guid'])
		{
			$error[] = $user->lang['GUID_USED'];
		}
		
		if (!empty($group_uac) && (strpos($group_uac , '&quot;') !== false || strpos($group_uac , '"') !== false || !preg_match('#^[0-9]+$#u', $group_uac) || strlen($group_uac) != 6))
		{
			$error[]	= $user->lang['UAC_NON_SIX'];
		}
		if (!empty($group_guid) && (strpos($group_guid , '&quot;') !== false || strpos($group_guid , '"') !== false || !preg_match('#^[a-zA-Z0-9\ ]+$#u', $group_guid) || strlen($group_guid) != 8))
		{
			$error[]	= $user->lang['GUID_NON_ALPHANUM'];
		}
		
		// Everything went fine :D
		if (!sizeof($error))
		{
			// Edit the group.
			$sql_array	= array(
				'group_name'			=> $group_name,
				'group_sito'			=> $group_sito,
				'group_desc'			=> $group_desc,
				'clan_logo_name'		=> $logo,
				'clan_logo_ext'			=> $logotype,
				'clan_logo_width'		=> $logowidth,
				'clan_logo_height'		=> $logoheight,
				'clan_favouritemap'		=> $group_favmap,
				'clan_favouriteteam'	=> $group_favteam,
				'guid'					=> $group_guid,
				'uac'					=> $group_uac,
			);
			$sql = "UPDATE " . CLANS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE group_id = {$group_id}";
			$db->sql_query($sql);

			// Completed. Let the user know.
			$urls1 = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=edit_group");
			meta_refresh(2, $urls1);
			trigger_error('GROUP_UPDATED');
		}
	}  
			 
	// Assign the other variables to the template
	$template->assign_vars(array(
		'AVATAR'			=> $group->data['clan_logo_name'],
		'AVATAR_X'	    	=> $group->data['clan_logo_width'],
		'AVATAR_Y'	    	=> $group->data['clan_logo_height'],
		'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',
		'GROUP_DESC' 		=> isset($group_desc) ? $group_desc : $group->data['group_desc'],
		'GROUP_NAME' 		=> isset($group_name) ? $group_name : $group->data['group_name'],
		'GROUP_SITO' 		=> isset($group_sito) ? $group_sito : $group->data['group_sito'],
		'L_AVATAR_EXPLAIN'	=> sprintf($user->lang['AVATAR_EXPLAIN'], $config['avatar_max_width'], $config['avatar_max_height'], $config['avatar_filesize'] / 1024),
		'U_ACTION' 			=> $u_action,
		'GROUP_ID'          => $group->data['group_id'],
		'FAVMAP'            => isset($clan_favouritemap) ? $clan_favouritemap : $group->data['clan_favouritemap'],
		'FAVTEAM'           => isset($clan_favouriteteam) ? $clan_favouriteteam : $group->data['clan_favouriteteam'],
		'GUID'				=> isset($group_guid) ? $group_guid : $group->data['guid'],
		'UAC'				=> isset($group_uac) ? $group_uac : $group->data['uac']
	));
}
?>