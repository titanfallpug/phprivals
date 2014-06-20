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
 * Edit a Season
 * Called from acp_rivals with mode == 'edit_season'
 */
function acp_rivals_edit_season($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$season_id	= (int) request_var('season_id', 0);
	$submit		= (!empty($_POST['submit'])) ? true : false;

	// Are we submitting a form?
	if ($submit)
	{
		// Yes, handle the form.
		$season_name	= utf8_normalize_nfc(request_var('season_name', '', true));

		$sql	= "UPDATE " . SEASONS_TABLE . " SET season_name = '$season_name' WHERE season_id = " . $season_id;
		$db->sql_query($sql);

		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=manage_seasons");
		meta_refresh(2, $redirect_url);
		trigger_error('SEASON_EDITTED');
	}
	else
	{
		// Get the season's information.
		$sql	= "SELECT * FROM " . SEASONS_TABLE . " WHERE season_id = " . $season_id;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Assign the variables to the template.
		$template->assign_vars(array(
			'U_ACTION'		=> $u_action,
			'SEASON_ID'		=> $season_id,
			'SEASON_NAME'	=> $row['season_name'])
		);
	}
}

?>