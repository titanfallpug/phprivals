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
 * Configure Rivals
 * Called from acp_rivals with mode == 'configure'
 */
function acp_rivals_configure($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		// Yes, handle the form.
		$ticket_receiver	= (int) request_var('ticket_receiver', 0);
		$bye_group			= (int) request_var('bye_group', 0);
		$kicked_time		= (int) request_var('kicked_time', 0);
		$frost_cost			= (int) request_var('frost_cost', 0);
		$inactiv_penality	= (int) request_var('inactiv_penality', 0);
		$rivals_modstraight	= (int) request_var('modstraight', 0);
		$maxreporthours		= (int) request_var('maxreporthours', 0);
		$minpost			= (int) request_var('minpost', 0);
		$bannergroup		= (int) request_var('bannedgroup', 0);

		// Update options.
		set_config('rivals_ticketreceiver', $ticket_receiver);
		set_config('rivals_byegroup', $bye_group);
		set_config('rivals_modstraight', $rivals_modstraight);
		set_config('rivals_kickout_day', $kicked_time);
		set_config('rivals_frost_cost', $frost_cost);
		set_config('rivals_inactiv_penality', $inactiv_penality);		
		set_config('rivals_maxreporthours', $maxreporthours);
		set_config('rivals_minpost', $minpost);
		set_config('rivals_bannedgroup', $bannergroup);

		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=configure");
		meta_refresh(2, $redirect_url);
		trigger_error('CONFIGUREATION_UPDATED');
	}
	else
	{
		$template->assign_vars(array(
			'U_ACTION' 	=> $u_action,
			'CONFIG' 	=> $config['rivals_ticketreceiver'],
			'BYE_GROUP' => $config['rivals_byegroup'],
			'KICK_DAY'	=> $config['rivals_kickout_day'],
			'FROST_COS' => $config['rivals_frost_cost'],
			'INACT_PEN'	=> $config['rivals_inactiv_penality'],
			'MODSTRIGT'	=> ($config['rivals_modstraight'] == 1) ? 'checked="checked"' : '',
			'MAXHOURS'	=> $config['rivals_maxreporthours'],
			'MINPOST'	=> $config['rivals_minpost'],
			'SELECT0'	=> ($config['rivals_bannedgroup'] == 0) ? ' selected="selected"' : ''
		));
		
		// get corrent groups
		$sql = 'SELECT group_id, group_name, group_type FROM ' . GROUPS_TABLE . ' ORDER BY group_type ASC, group_name';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('groups', array(
				'GROUP_ID'		=> $row['group_id'],
				'GROUP_NAME'	=> ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'],
				'GROUP_SELECT'	=> ($config['rivals_bannedgroup'] == $row['group_id']) ? ' selected="selected"' : ''
			));
		}
		$db->sql_freeresult($result);
	}
}

?>