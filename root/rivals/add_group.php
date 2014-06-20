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
if(!defined('IN_PHPBB'))
{
	exit;
}

$ladder	= new ladder();
$group	= new group();
$error	= array();
$submit	= (!empty($_POST['submit'])) ? true : false;

// Permissions check
if (!$user->data['is_registered'])
{
	$url = append_sid("{$phpbb_root_path}index.$phpEx");
	meta_refresh(4, $url);
	trigger_error('LOGIN_INFO');
	break;
}

// Validate user
include($phpbb_root_path . 'rivals/classes/function_security.' . $phpEx);
if (validate_user($user->data['user_id'], $config['rivals_bannedgroup'], $config['rivals_minpost']) == false)
{
	$url = append_sid("{$phpbb_root_path}index.$phpEx");
	meta_refresh(4, $url);
	trigger_error(sprintf($user->lang['USER_CANT_PLAY'], $config['rivals_minpost']));
	break;
}

if ($submit)
{
	$group_name	= utf8_normalize_nfc(request_var('group_name', '', true));
	$group_desc	= utf8_normalize_nfc(request_var('group_desc', '', true));
	$group_sito = utf8_normalize_nfc(request_var('group_sito', '', true));
	$group_logo	= utf8_normalize_nfc(request_var('group_logo', '', true));
	$clan_favouritemap  = utf8_normalize_nfc(request_var('clan_favouritemap', '', true));
	$clan_favouriteteam = utf8_normalize_nfc(request_var('clan_favouriteteam', '', true));
	
	// Check if a user has entered a clan name if not return the error
	if (empty($group_name))
	{
		$error[] = $user->lang['ENTER_GROUP_NAME'];
	}
	
	//check if there are a clan with same name
	$sql_2_array = array(
		'group_name'	=> $group_name
	);
	$sql9		= 'SELECT * FROM ' . CLANS_TABLE . ' WHERE ' . $db->sql_build_array('SELECT', $sql_2_array);
	$result9	= $db->sql_query($sql9);
    $row9		= $db->sql_fetchrow($result9);
	$db->sql_freeresult($result9);
	
	if (!empty($row9['group_id']))
	{
		$error[] = $user->lang['CLAN_NAME_USED'];
	}
	
	
	// Do we have a clan logo?
	if (!empty($_FILES['group_logo']['type']))
	{
		include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
		global $config;
		$upload = new fileupload('IMG_', array('jpg', 'jpeg', 'gif', 'png'), 163840, 100, 100, 500, 500, explode('|', $config['mime_triggers']));
		
		$foto = $upload->form_upload('group_logo');
		$foto->clean_filename('real', '', $user->data['user_id']);
		
		$destination = "{$phpbb_root_path}images/rivals/clanlogo/";
		
		$foto->move_file($destination, true, false, 0644);
		$clan_logo_ext  	= $foto->get('extension');
		$clan_logo_name		= $foto->get('realname');
		$clan_logo_width	= $foto->get('width');
		$clan_logo_height	= $foto->get('height');
		
		if (sizeof($foto->error))
		{
			$foto->remove();
			$error = array_merge($error, $foto->error);
		}
		else
		{
			chmod($destination . $clan_logo_name, 0644);
		}
	}
	else
	{
		$clan_logo_name		= 'nologo.jpg';
		$clan_logo_ext		= 'jpg';
		$clan_logo_width	= $clan_logo_height = 100;
	}

	
	// Everything went fine :D
	if (!sizeof($error))
	{
		// Add the group
		$sql_array	= array(
			'group_name'			=> $group_name,
			'group_desc'			=> $group_desc,
			'group_sito'			=> $group_sito,
			'clan_logo_name'		=> $clan_logo_name,
			'clan_logo_ext'			=> $clan_logo_ext,
			'clan_logo_width'		=> $clan_logo_width,
			'clan_logo_height'		=> $clan_logo_height,
			'group_tournaments'		=> '',
			'clan_alltime_wins'		=> 0,
			'clan_alltime_losses'	=> 0,
			'clan_alltime_pareggi'	=> 0,
			'clan_level'			=> 0,
			'clan_creation_date'	=> time(),
			'clan_target_10streak'	=> 0,
			'clan_target_ladderwin' => 0,
			'clan_closed'			=> 0,
			'clan_favouritemap'		=> $clan_favouritemap,
			'clan_favouriteteam'	=> $clan_favouriteteam			
		);
		$sql	= "INSERT INTO " . CLANS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);
		
		// Get the last group inserted.
		$sql = 'SELECT MAX(group_id) AS id
			FROM ' . CLANS_TABLE;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Check to see if this is their first group.
		if ($user->data['group_session'] == 0)
		{
			// Set their group session.
			$sql	= "UPDATE " . USERS_TABLE . " SET group_session = {$row['id']} WHERE user_id = " . $user->data['user_id'];
			$db->sql_query ($sql);
		}

		// Add the leader to the group.
		
		$sql_array2	= array(
			'group_id'		=> $row['id'],
			'user_id'		=> $user->data['user_id'],
			'group_leader'	=> 1,
			'user_pending'	=> 0,
            'mvp_utente'	=> 0			
		);
		$sql	= "INSERT INTO " . USER_CLAN_TABLE . " " . $db->sql_build_array('INSERT', $sql_array2);
		$db->sql_query($sql);

		// Completed. Let the user know. send them to rivals main panel
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&mode=main");
		meta_refresh(3, $redirect_url);
		$message = $user->lang['GROUP_ADDED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $redirect_url . '">', '</a>');
		trigger_error($message);
	}
}

// Assign the other variable to the template.	
$template->assign_vars(array(
	'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',
	'GROUP_DESC' 		=> isset($group_desc) ? $group_desc : '',
	'GROUP_LOGO' 		=> isset($group_logo) ? $group_logo : '',
	'GROUP_NAME' 		=> isset($group_name) ? $group_name : '',
	'GROUP_SITO'        => isset($group_sito) ? $group_sito : '',
	'GROUP_MAP'         => isset($clan_favouritemap) ? $clan_favouritemap : '',
	'GROUP_TEAM'        => isset($clan_favouriteteam) ? $clan_favouriteteam : '',
	'L_AVATAR_EXPLAIN'	=> $user->lang['AVATAR_EXPLAIN'],
	'U_ACTION' 			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=add_group'),
));

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['RIVALS_ADDGROUP'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=add_group'))
);

$template->set_filenames(array(
	'body' => 'rivals/add_group.html',
));
?>