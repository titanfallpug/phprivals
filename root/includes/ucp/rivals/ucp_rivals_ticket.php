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
 * Issue a Ticket
 * Called from ucp_rivals with mode == 'ticket'
 */
function ucp_rivals_ticket($id, $mode, $u_action)
{
	global	$config, $db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$ulad		= (!empty($user->data['group_session'])) ? (string) utf8_normalize_nfc(request_var('ulad', 'false', true)) : 'true';
	$match_id	= (int) request_var('match_id', 0);
	$userm		= (int) request_var('userm', 0);
	$submit		= (!empty($_POST['submit'])) ? true : false;
	$thematch1	= (!empty($match_id)) ? $match_id : $user->lang['MATCHID_NONDEF'];
	
	if ($ulad == 'true')
	{
		// Get the match information.
		$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $match_id;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$thematch	= (!empty($match_id)) ? $row['1vs1_id'] : $user->lang['MATCHID_NONDEF'];
		$tipoulad	= $user->lang['MATCHID_TIPOONEONE'];
		$cher		= getusername($row['1vs1_challanger']);
		$chee		= getusername($row['1vs1_challangee']);
		
		$template->assign_vars(array(
			'FROMMATCH'		=> (!empty($match_id)) ? true : false,
			'CHALLENGER'	=> $cher,
			'CHALLENGEE'	=> $chee,
			'USERLAD'		=> 1,
			'MATCHID'		=> $match_id
		));
	}
	else
	{
		if (empty($user->data['group_session']))
		{
			// They are not apart of a ladder. Deny them.
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(4, $redirect_url);
			trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
		}
		else if (empty($group->data['group_ladders']))
		{
			// They are not apart of a ladder. Deny them.
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(4, $redirect_url);
			trigger_error(sprintf($user->lang['GROUP_NOTIN_LADDER'], '<a href="' . $redirect_url . '">', '</a>'));
		}
		
		// Get the match information.
		$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $match_id;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$thematch	= (!empty($match_id)) ? $row['match_id'] : $user->lang['MATCHID_NONDEF'];
		$tipoulad	= '';
		$cher		= $group->data('group_name', $row['match_challenger']);
		$chee		= $group->data('group_name', $row['match_challengee']);
		
		$template->assign_vars(array(
			'FROMMATCH'		=> (!empty($match_id)) ? true : false,
			'CHALLENGER'	=> $cher,
			'CHALLENGEE'	=> $chee,
			'USERLAD'		=> 0,
			'MATCHID'		=> $match_id
		));
	}

	if ($match_id > 0)
	{
		if ($userm === 1)
		{
			// Are we submitting a form?
			if ($submit)
			{
				// Yes, handle the form.
				$attachment	= $_FILES['attachment'];
				$type		= request_var('type', '');
				$ticket		= utf8_normalize_nfc(request_var('ticket', '', true));
				$tipoulad	= $user->lang['MATCHID_TIPOONEONE'];
				$opponents	= "{$cher} VS {$chee} ({$tipoulad})";

				if (!empty($_FILES['attachment']['type']))
				{
					// Get the file's info for reference and checking.
					include ($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
					$upload = new fileupload('RIVALS_');
					
					$upload->set_allowed_extensions(array('jpg', 'jpeg', 'png', 'tiff', 'pdf', 'gif', 'swf', 'mpeg', 'mpg', 'wmv', 'wav', 'avi', 'bmp'));
					
					$file = $upload->form_upload('attachment');
					$file->clean_filename('unique_ext');
					$destination = "{$phpbb_root_path}rivals/uploads/";
					$file->move_file($destination, false, false, CHMOD_READ);

					// Check if it was uploaded.
					if (!$file->is_uploaded())
					{
						// Did not upload. Let the user know.
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=mode=ticket&amp;ulad=true&amp;match_id={$match_id}");
						meta_refresh(3, $redirect_url);
						trigger_error('RIVALS_FILE_NOT_UPLOADED');
					}

					// Send a PM to the ticket receiver. (url / user id / username / match id / match opponents/ quote / board / file)
					$boardurl	= generate_board_url();
					$subject	= $user->lang['PMTICKET'] . ' (' . $type . ')';
					$message	= sprintf($user->lang['PMTICKETTXT_USER'], $boardurl, $user->data['user_id'], $user->data['username'], $thematch, $opponents, $ticket, $boardurl, $file->realname);
				}
				else
				{
					// Send a PM to the ticket receiver.
					$boardurl	= generate_board_url();
					$subject	= $user->lang['PMTICKET'] . ' (' . $type . ')';
					$message	= sprintf($user->lang['PMTICKETTXT_NOATTACH_USER'], $boardurl, $user->data['user_id'], $user->data['username'], $thematch, $opponents, $ticket);
				}

				$moderatore	= ($ladder->data['ladder_mod'] == 0) ? $config['rivals_ticketreceiver'] : $ladder->data['ladder_mod'];
				insert_pm($moderatore, $user->data, $subject, $message);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
				meta_refresh(3, $redirect_url);
				trigger_error('TICKET_SENT');
			}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END USER LADDER MOD
		}
		else
		{
			// Are we submitting a form?
			if ($submit)
			{
				// Yes, handle the form.
				$attachment	= $_FILES['attachment'];
				$type		= request_var('type', '');
				$ticket		= utf8_normalize_nfc(request_var('ticket', '', true));
				$tipoulad	= '';
				$opponents	= "{$cher} VS {$chee}";

				if (!empty($_FILES['attachment']['type']))
				{
					// Get the file's info for reference and checking.
					include ($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
					$upload = new fileupload('RIVALS_');
					
					$upload->set_allowed_extensions(array('jpg', 'jpeg', 'png', 'tiff', 'pdf', 'gif', 'swf', 'mpeg', 'mpg', 'wmv', 'wav', 'avi', 'bmp'));
					
					$file = $upload->form_upload('attachment');
					$file->clean_filename('unique_ext');
					$destination = "{$phpbb_root_path}rivals/uploads/";
					$file->move_file($destination, false, false, CHMOD_READ);

					// Check if it was uploaded.
					if (!$file->is_uploaded())
					{
						// Did not upload. Let the user know.
						$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=mode=ticket&amp;match_id={$match_id}");
						meta_refresh(3, $redirect_url);
						trigger_error('RIVALS_FILE_NOT_UPLOADED');
					}

					// Send a PM to the ticket receiver. (url / clan id / clan name / match id / match opponents/ quote / board / file)
					$boardurl	= generate_board_url();
					$subject	= $user->lang['PMTICKET'] . ' (' . $type . ')';
					$message	= sprintf($user->lang['PMTICKETTXT'], $boardurl, $group->data['group_id'], $group->data['group_name'], $thematch, $opponents, $ticket, $boardurl, $file->realname);
				}
				else
				{
					// Send a PM to the ticket receiver.
					$boardurl	= generate_board_url();
					$subject	= $user->lang['PMTICKET'] . ' (' . $type . ')';
					$message	= sprintf($user->lang['PMTICKETTXT_NOATTACH'], $boardurl, $group->data['group_id'], $group->data['group_name'], $thematch, $opponents, $ticket);
				}

				$moderatore	= ($ladder->data['ladder_mod'] == 0) ? $config['rivals_ticketreceiver'] : $ladder->data['ladder_mod'];
				insert_pm($moderatore, $user->data, $subject, $message);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
				meta_refresh(3, $redirect_url);
				trigger_error('TICKET_SENT');
			}
		}
	}
	else
	{
		//Standard ticket without ma relative match
		// Are we submitting a form?
		if ($submit)
		{
			// Yes, handle the form.
			$attachment	= $_FILES['attachment'];
			$type		= request_var('type', '');
			$ticket		= utf8_normalize_nfc(request_var('ticket', '', true));
			
			if (!empty($_FILES['attachment']['type']))
			{
				// Get the file's info for reference and checking.
				include ($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
				$upload = new fileupload('RIVALS_');
				
				$upload->set_allowed_extensions(array('jpg', 'jpeg', 'png', 'tiff', 'pdf', 'gif', 'swf', 'mpeg', 'mpg', 'wmv', 'wav', 'avi', 'bmp'));
				
				$file = $upload->form_upload('attachment');
				$file->clean_filename('unique_ext');
				$destination = "{$phpbb_root_path}rivals/uploads/";
				$file->move_file($destination, false, false, CHMOD_READ);

				// Check if it was uploaded.
				if (!$file->is_uploaded())
				{
					// Did not upload. Let the user know.
					$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=mode=ticket&amp;match_id={$match_id}");
					meta_refresh(3, $redirect_url);
					trigger_error('RIVALS_FILE_NOT_UPLOADED');
				}

				// Send a PM to the ticket receiver. (url / clan id / clan name / match id / match opponents/ quote / board / file)
				$boardurl	= generate_board_url();
				$subject	= $user->lang['PMTICKET'] . ' (' . $type . ')';
				$message	= sprintf($user->lang['PMTICKETTXT_NOMATCH'], $boardurl, $user->data['user_id'], $user->data['username'], $ticket, $boardurl, $file->realname);
			}
			else
			{
				// Send a PM to the ticket receiver.
				$boardurl	= generate_board_url();
				$subject	= $user->lang['PMTICKET'] . ' (' . $type . ')';
				$message	= sprintf($user->lang['PMTICKETTXT_NOATTCHMATCH'], $boardurl, $user->data['user_id'], $user->data['username'], $ticket);
			}

			$moderatore	= ($ladder->data['ladder_mod'] == 0) ? $config['rivals_ticketreceiver'] : $ladder->data['ladder_mod'];
			insert_pm($moderatore, $user->data, $subject, $message);

			// Completed. Let the user know.
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			meta_refresh(3, $redirect_url);
			trigger_error('TICKET_SENT');
		}
	}
	
	// Assign the other variables to the template.
	$template->assign_vars(array('U_ACTION' => $u_action));
}
?>