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
 * Add a Tournament
 * Called from acp_rivals with mode == 'add_tournament'
 */
function acp_rivals_add_tournament($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$tournament	= new tournament();
	$group		= new group();
	$errore		= array();
	$error		= array();

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		// Yes, handle the form.
		$tournament_name		= (string) utf8_normalize_nfc(request_var('tourn_name', '', true));
		$tournament_info		= (string) utf8_normalize_nfc(request_var('tourn_info', '', true));
		$tournament_brackets	= (int) request_var('tourn_brackets', 0);
		$tournament_tipo	    = (int) request_var('tourn_tipo', 0);
		$tournament_type		= (int) request_var('tourn_type', 0);
		$tournament_dir			= (int) request_var('tourn_dir', 0);
		$tournament_startdate	= request_var('tourn_startdate', array(0 => 0));
		$tournament_invite		= request_var('tourn_invite', '');
		$tournament_decerto		= (string) utf8_normalize_nfc(request_var('shorty', '', true));
		$tournament_dectyp  	= (int) request_var('tourn_decerto', 0);
		$ladder_limit			= (int) request_var('ladder_limit', 0);
		$tourn_advstats			= (int) request_var('tourn_advstats', 0);
		$tourn_userb			= (int) request_var('tourn_userb', 0);
		$tourn_stricted			= (int) request_var('tourn_stricted', 0);
		$tourn_minuser			= (int) request_var('tourn_minuser', 0);
		$tourn_maxuser			= (int) request_var('tourn_maxuser', 0);
		
		// Setup the BBcode for the ladder rules.
		$uid			= $bitfield = $options = '';
		$allow_bbcode	= $allow_urls = $allow_smilies = true;
		generate_text_for_storage($tournament_info, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

		// Convert the start date to a UNIX timestamp.
		$start_date	= mktime($tournament_startdate[3], $tournament_startdate[4], 0, $tournament_startdate[0], $tournament_startdate[1], $tournament_startdate[2]);
		
		// tournament logo
		if (!empty($_FILES['tournament_logo']['type']))
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
		}
		else
		{
			$tlogo_name		= 'nologo.jpg';
		}
		
		// check for errors
		if (empty($tournament_name))
		{
			$error[] = $user->lang['TOURNAMENT_NAME_EMPTY'];
		}
		if ($tourn_stricted == 1 && $tourn_minuser == 0)
		{
			$error[] = $user->lang['TOURNAMENT_MINUSER_0'];
		}
		if ($tourn_stricted == 1 && $tourn_minuser == 0)
		{
			$error[] = $user->lang['TOURNAMENT_MAXUSER_0'];
		}
		if ($tourn_stricted == 1 && $tourn_userb == 1)
		{
			$error[] = $user->lang['TOURNAMENT_USER_NOT_STRICTED'];
		}
		
		if (!sizeof($errore) && !sizeof($error))
		{
			// Check if this is a invitational tournament.
			if (!empty($tournament_invite))
			{
				$board_path = generate_board_url() . '/';
				$invitati	= array();
				$invitati	= explode("\n", $tournament_invite);
				foreach ($invitati AS $value)
				{
					// Send a PM to the group leader telling them they were invited.
					$subject	= $user->lang['PM_TOURNAMENTINVITE'];
					$message	= sprintf($user->lang['PM_TOURNAMENTINVITETXT'], $tournament_name, "{$board_path}rivals.php?action=tournaments");
					
					if ($tourn_userb == 1)
					{
						insert_pm($value, $user->data, $subject, $message);
					}
					else
					{
						insert_pm($group->data('user_id', $value), $user->data, $subject, $message);
					}
				}
				$tournament_invite	= serialize($tournament_invite);
			}
			
			// Run the query to add the tournament.
			$sql_array	= array(
				'tournament_status'			=> 1,
				'tournament_name'			=> $tournament_name,
				'tournament_logo'			=> $tlogo_name,
				'tournament_brackets'		=> $tournament_brackets,
				'tournament_info'			=> $tournament_info,
				'tournament_type'			=> $tournament_type,
				'tournament_tipo'			=> $tournament_tipo,
				'tournament_decerto'		=> $tournament_dectyp,
				'shorty'					=> $tournament_decerto,
				'tournament_time'			=> time(),
				'tournament_invite'			=> $tournament_invite,
				'tournament_finishedgroups'	=> '',
				'tournament_startdate'		=> $start_date,
				'tournament_licence'		=> $ladder_limit,
				'tournament_advstats'		=> $tourn_advstats,
				'tournament_userbased'		=> $tourn_userb,
				'tournament_stricted'		=> $tourn_stricted,
				'tournament_minuser'		=> $tourn_minuser,
				'tournament_maxuser'		=> $tourn_maxuser,
				'league_cycle'				=> 1,
				'bbcode_uid'				=> $uid,
				'bbcode_options'			=> $options,
				'bbcode_bitfield'			=> $bitfield
			);
			$sql	= "INSERT INTO " . TOURNAMENTS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_tournament");
			meta_refresh(2, $redirect_url);
			trigger_error('TOURNAMENT_ADDED');
		}
		else
		{
			$fixa = (sizeof($error)) ? implode('<br />', $error) : '';
			
			$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=add_tournament");
			meta_refresh(2, $redirect_url);
			trigger_error("{$fixa}");
			
			$template->assign_vars(array(
				'ERRORE' => $fixa
			));
		}
	}
	else
	{

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
			// Assign the month to the template.
			$template->assign_block_vars('block_months', array(
				'MONTH'			=> ($key + 1),
				'MONTH_NAME'	=> $value
			));
		}

		// Set the days for the start date.
		for ($i = 1; $i <= 31; $i++)
		{
			// Assign the day to the template.
			$template->assign_block_vars('block_days', array(
				'DAY' => (strlen($i) == 1) ? 0 . $i : $i
			));
		}

		// Set the years for the start date.
		for ($i = date('Y'); $i < (date('Y') + 3); $i++)
		{
			// Assign the year to the template.
			$template->assign_block_vars('block_years', array(
				'YEAR' => $i
			));
		}

		// Set the hours for the start date.
		for ($i = 1; $i <= 24; $i++)
		{
			// Assign the hour to the template.
			$template->assign_block_vars('block_hours', array(
				'HOUR' => (strlen($i) == 1) ? 0 . $i : $i
			));
		}

		// Set the minutes for the start date.
		for ($i = 0; $i <= 55; $i += 5)
		{
			// Assign the minute to the template.
			$template->assign_block_vars('block_minutes', array(
				'MINUTE' => (strlen($i) == 1) ? 0 . $i : $i
			));
		}
		
		// decerto
		$sql_43		= "SELECT * FROM " . DECERTO_CAT . " GROUP BY nome_corto ORDER BY nome_gioco";
		$result_43	= $db->sql_query($sql_43);
		$juju = 0;
		while ($row_43 = $db->sql_fetchrow($result_43))
		{
			// Assign each platform to the template.
			if (validate_decerto($row_43['nome_corto']) === true)
			{
				$template->assign_block_vars('block_decerto', array(
					'NOMECORTO'	=> $row_43['nome_corto'],
					'NOMELUNGO'	=> $row_43['nome_gioco'],
					'TIPO'		=> ($row_43['cpc'] == 1) ? $user->lang['DECERTO'] : $user->lang['CPC']
				));
				
				$juju++;
			}
		}
         $db->sql_freeresult($result_43);
		 
		// Assign the other variables to the template.
		$template->assign_vars(array(
			'ERRORE'	=> (sizeof($error)) ? implode('<br />', $error) : '',
			'U_ACTION'	=> $u_action,
			'S_DECERTO'	=> ($juju > 0) ? true : false
		));
	}
}

?>