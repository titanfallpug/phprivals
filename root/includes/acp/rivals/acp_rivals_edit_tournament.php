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
 * Edit a Tournament
 * Called from acp_rivals with mode == 'edit_tournament'
 */
function acp_rivals_edit_tournament($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$group		= new group();
	$tournament	= new tournament();
	$errore		= array();

	$tournament_id	= (int) request_var('tournament_id', 0);
	$submit			= (!empty($_POST['submit'])) ? true : false;
	$remove_group	= (int) request_var('remove_group', 0);

	// Are we submitting a form?
	if ($submit)
	{
		// Yes, handle the form.
		$tournament_name		= (string) utf8_normalize_nfc(request_var('tourn_name', '', true));
		$tournament_info		= (string) utf8_normalize_nfc(request_var('tourn_info', '', true));
		$tournament_brackets	= (int) request_var('tourn_brackets', 0);
		$tournament_startdate	= request_var('tourn_startdate', array(0 => 0));
		$tournament_tipo	    = (int) request_var('tourn_tipo', 0);
		$ladder_limit			= (int) request_var('ladder_limit', 0);
		$tourn_advstats			= (int) request_var('tourn_advstats', 0);
		$logo_delete			= (int) request_var('delete', 0);
		$tourn_stricted			= (int) request_var('tourn_stricted', 0);
		$tourn_minuser			= (int) request_var('tourn_minuser', 0);
		$tourn_maxuser			= (int) request_var('tourn_maxuser', 0);

		// Setup the BBcode for the tournament info.
		$uid			= $bitfield = $options = '';
		$allow_bbcode	= $allow_urls = $allow_smilies = true;
		generate_text_for_storage($tournament_info, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
		
		// tournament logo
		if (!empty($_FILES['tournament_logo']['type']) && $logo_delete == 0)
		{
			include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
			$upload	= new fileupload('LOGOMEN_', array('jpg', 'jpeg', 'gif', 'png'), 163840, 200, 80, 200, 80, explode('|', $config['mime_triggers']));
				
			$foto	= $upload->form_upload('tournament_logo');
			$foto->clean_filename('real', '', $user->data['user_id']);
				
			$destination	= "{$phpbb_root_path}images/rivals/tournamentlogo/";
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
			$tlogo_name		= $foto->get('realname');
				
			if (sizeof($foto->error))
			{
				$foto->remove();
				$errore = array_merge($errore, $foto->error);
			}
			else
			{
				$shalla	= "{$phpbb_root_path}images/rivals/tournamentlogo/";
				chmod($shalla . $tlogo_name, 0644);
			}
			
			// delete old logo if there was one
			if ($tournament->data['tournament_logo'] != 'nologo.jpg')
			{
				@unlink($phpbb_root_path . "images/rivals/tournamentlogo/" . $tournament->data['tournament_logo']);
			}
		}
		else if (empty($_FILES['tournament_logo']['name']) && $logo_delete == 1)
		{
			// remove logo
			@unlink($phpbb_root_path . "images/rivals/tournamentlogo/" . $tournament->data['tournament_logo']);
			$tlogo_name		= 'nologo.jpg';
		}
		else
		{
			$tlogo_name		= $tournament->data['tournament_logo'];
		}

		// Convert the start date to a UNIX timestamp.
		$start_date	= mktime($tournament_startdate[ 3 ], $tournament_startdate[ 4 ], 0, $tournament_startdate[ 0 ], $tournament_startdate[ 1 ], $tournament_startdate[ 2 ]);

		// check for errors
		if (empty($tournament_name))
		{
			$errore[] = $user->lang['TOURNAMENT_NAME_EMPTY'];
		}
		if ($tourn_stricted == 1 && $tourn_minuser == 0)
		{
			$errore[] = $user->lang['TOURNAMENT_MINUSER_0'];
		}
		if ($tourn_stricted == 1 && $tourn_minuser == 0)
		{
			$errore[] = $user->lang['TOURNAMENT_MAXUSER_0'];
		}

		// Run the query to update the tournament.
		if (!sizeof($errore))
		{
			$sql_array	= array(
				'tournament_name'			=> $tournament_name,
				'tournament_brackets'		=> ($tournament_brackets & 1) ? $tournament_brackets + 1 : $tournament_brackets,
				'tournament_info'			=> $tournament_info,
				'tournament_tipo'			=> $tournament_tipo,
				'tournament_time'			=> time(),
				'tournament_finishedgroups'	=> '',
				'tournament_logo'			=> $tlogo_name,
				'tournament_licence'		=> $ladder_limit,
				'tournament_advstats'		=> $tourn_advstats,
				'tournament_startdate'		=> $start_date,
				'tournament_stricted'		=> $tourn_stricted,
				'tournament_minuser'		=> $tourn_minuser,
				'tournament_maxuser'		=> $tourn_maxuser,
				'bbcode_uid'				=> $uid,
				'bbcode_options'			=> $options,
				'bbcode_bitfield'			=> $bitfield
			);
			$sql = "UPDATE " . TOURNAMENTS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE tournament_id = " . $tournament_id;
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournaments");
			meta_refresh(2, $redirect_url);
			trigger_error('TOURNAMENT_UPDATED');
		}
		else
		{
			$fixa = (sizeof($errore)) ? implode('<br />', $errore) : '';
			
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_tournament");
			meta_refresh(2, $redirect_url);
			trigger_error("{$fixa}");
			
			$template->assign_vars(array(
				'ERRORE' => $fixa
			));
		}
	}
	
	if ($remove_group != 0)
	{
		// Remove the signed up group.
		$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_id = " . $remove_group;
		$db->sql_query($sql);

		// Completed. Let the user know.
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_tournaments");
		meta_refresh(2, $redirect_url);
		trigger_error('GROUP_REMOVED_TOURNAMENT');
		break;
	}

	// Break down the tournament's start date.
	$month	= date('m', $tournament->data['tournament_startdate']);
	$day	= date('d', $tournament->data['tournament_startdate']);
	$year	= date('Y', $tournament->data['tournament_startdate']);
	$hour	= date('H', $tournament->data['tournament_startdate']);
	$minute	= date('i', $tournament->data['tournament_startdate']);

	// Set the months for the start date.
	$months	= array(
		$user->lang['datetime']['January'],
		$user->lang['datetime']['February'],
		$user->lang['datetime']['March'],
		$user->lang['datetime']['April'],
		$user->lang['datetime']['May'],
		$user->lang['datetime']['June'],
		$user->lang['datetime']['July'],
		$user->lang['datetime']['August'],
		$user->lang['datetime']['September'],
		$user->lang['datetime']['October'],
		$user->lang['datetime']['November'],
		$user->lang['datetime']['December']
	);

	foreach ($months AS $key => $value)
	{
		$fixed_month	= $key + 1;
		// Assign the month to the template.
		$template->assign_block_vars('block_months', array(
			'MONTH'			=> $fixed_month,
			'MONTH_NAME'	=> $value,
			'SELECTED'		=> ($fixed_month == $month) ? 'selected="selected"' : ''
		));
	}

	// Set the days for the start date.
	for ($i = 1; $i <= 31; $i++)
	{
		$fixed_day	= (strlen($i) == 1) ? 0 . $i : $i;

		// Assign the day to the template.
		$template->assign_block_vars('block_days', array(
			'DAY'		=> $fixed_day,
			'SELECTED'	=> ($fixed_day == $day) ? 'selected="selected"' : ''
		));
	}

	// Set the years for the start date.
	for ($i = date('Y'); $i < (date('Y') + 3); $i++)
	{
		// Assign the year to the template.
		$template->assign_block_vars('block_years', array(
			'YEAR'		=> $i,
			'SELECTED'	=> ($i == $year) ? 'selected="selected"' : ''
		));
	}

	// Set the hours for the start date.
	for ($i = 1; $i <= 24; $i++)
	{
		$fixed_hour	= (strlen($i) == 1) ? 0 . $i : $i;

		// Assign the hour to the template.
		$template->assign_block_vars('block_hours', array(
			'HOUR'		=> $fixed_hour,
			'SELECTED'	=> ($fixed_hour == $hour) ? 'selected="selected"' : ''
		));
	}

	// Set the minutes for the start date.
	for ($i = 0; $i <= 55; $i += 5)
	{
		$fixed_minute	= (strlen($i) == 1) ? 0 . $i : $i;

		// Assign the minute to the template.
		$template->assign_block_vars('block_minutes', array(
			'MINUTE'	=> $fixed_minute,
			'SELECTED'	=> ($fixed_minute == $minute) ? 'selected="selected"' : ''
		));
	}

	// Get the groups signed up for this tournament (if its unstarted).
	if ($tournament->data['tournament_status'] == 1)
	{
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = 1";
		$result	= $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('block_groups', array(
				'U_REMOVE' 		=> $u_action . '&amp;remove_group=' . $row['group_id'] . '&amp;tournament_id=' . $tournament_id,
				'GROUP_NAME'	=> ($tournament->data['tournament_userbased'] == 0) ? $group->data('group_name', $row['group_id']) : getusername($row['group_id'])
			));
		}
	}

	// Assign the other variables to the template.
	decode_message($tournament->data['tournament_info'], $tournament->data['bbcode_uid']);
	$template->assign_vars(array(
		'U_ACTION'				=> $u_action . '&amp;tournament_id=' . $tournament_id,
		'S_CLAN_BASED'			=> ($tournament->data['tournament_userbased'] == 0) ? true : false,
		'TOURNAMENT_STRICTED'	=> ($tournament->data['tournament_stricted'] == 1) ? ' checked="selected"' : '',
		'TOURNAMENT_MIN_LIMIT'	=> $tournament->data['tournament_minuser'],
		'TOURNAMENT_MAX_LIMIT'	=> $tournament->data['tournament_maxuser'],
		'COMO'					=> ($tournament->data['tournament_stricted'] == 1) ? 'block' : 'none',
		'TOURNAMENT_NAME'		=> $tournament->data['tournament_name'],
		'TOURNAMENT_INFO'		=> $tournament->data['tournament_info'],
		'TOURNAMENT_BRACKETS1'	=> ($tournament->data['tournament_brackets'] == 2) ? 'selected="selected"' : '',
		'TOURNAMENT_BRACKETS2'	=> ($tournament->data['tournament_brackets'] == 4) ? 'selected="selected"' : '',
		'TOURNAMENT_BRACKETS3'	=> ($tournament->data['tournament_brackets'] == 8) ? 'selected="selected"' : '',
		'TOURNAMENT_BRACKETS4'	=> ($tournament->data['tournament_brackets'] == 16) ? 'selected="selected"' : '',
		'TOURNAMENT_BRACKETS5'	=> ($tournament->data['tournament_brackets'] == 32) ? 'selected="selected"' : '',
		'TOURNAMENT_BRACKETS6'	=> ($tournament->data['tournament_brackets'] == 64) ? 'selected="selected"' : '',
		'TOURNAMENT_BRACKETS7'	=> ($tournament->data['tournament_brackets'] == 128) ? 'selected="selected"' : '',
		'TOURNAMENT_LIMIT0'		=> ($tournament->data['tournament_licence'] == 0) ? 'selected="selected"' : '',
		'TOURNAMENT_LIMIT1'		=> ($tournament->data['tournament_licence'] == 1) ? 'selected="selected"' : '',
		'TOURNAMENT_LIMIT2'		=> ($tournament->data['tournament_licence'] == 2) ? 'selected="selected"' : '',
		'TOURNAMENT_TIPO1'		=> ($tournament->data['tournament_tipo'] == 1) ? 'selected="selected"' : '',
		'TOURNAMENT_TIPO2'		=> ($tournament->data['tournament_tipo'] == 2) ? 'selected="selected"' : '',
		'TOURNAMENT_ASVSTAT'	=> ($tournament->data['tournament_advstats'] == 1) ? 'checked="selected"' : '',
		'TOURNAMENT_LOGO'		=> $phpbb_root_path . "images/rivals/tournamentlogo/" . $tournament->data['tournament_logo']
	));
}

?>