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
 * Add a mvp list
 * Called from acp_rivals with mode == 'add_mvp_list'
 */
function acp_rivals_add_mvp_list($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;
	
	$submit	= (!empty($_POST['submit'])) ? true : false;
	$ladder	= new ladder();
	
	if ($submit)
	{
		// Yes, handle the form.
		$nome_mvp	        = utf8_normalize_nfc(request_var('nome_mvp', '', true));
		$descrizione_mvp	= utf8_normalize_nfc(request_var('descrizione_mvp', '', true));
		$ladder_mvp      	= request_var('mvp_ladder', 0);
		$ladder_mvp      	= (int) $ladder_mvp;
		
		if ($ladder_mvp == 0 || empty($nome_mvp))
		{
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_mvp_list");
			meta_refresh(4, $redirect_url);
			trigger_error($user->lang['MPV_LIST_ZERO'] . adm_back_link($redirect_url));
		}		
		
		$sql_array	= array(
			'nome_mvp'			=> $nome_mvp,
			'descrizione_mvp'	=> $descrizione_mvp,
			'ladder_mvp'		=> $ladder_mvp
		);
		$sql	= "INSERT INTO " . RIVAL_MVP . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);

		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_mvp_list");
		meta_refresh(2, $redirect_url);
		trigger_error('MPV_LIST_ADDED');
	}
		
	// Assign the other variables to the template.
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action,
		'S_LADDER'	=> $ladder->make_ladder_select(false, true, false, true)
	));
}
?>