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
 * Edit a Ladder
 * Called from acp_rivals with mode == 'edit_ladder'
 */
function acp_rivals_edit_ladder($id, $mode, $u_action)
{
	global	$db, $user, $template, $config;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$ladder	= new ladder();
	$error	= array();

	$ladder_id	= (int) request_var('ladder_id', 0);
	$delete		= (int) request_var('delete', 0);
	$confirm	= (int) request_var('confirm', 0);

	// Are we submitting a form?
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit || $confirm > 0)
	{
		// Yes, handle the form.
		// Are we deleting?
		if (!empty($delete))
		{
			if ($confirm > 0)
			{
				// Yes. Delete the ladder.
				$sql	= "DELETE FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
				$db->sql_query($sql);
				
				// get all subladder of this ladder
				$sql	= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = " . $ladder_id;
				$result	= $db->sql_query($sql);
				$i	= 0;
				while ($row = $db->sql_fetchrow($result))
				{
					$sql	= "DELETE FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder  = " . $row['ladder_id']; /* 1vs1 */
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder  = " . $row['ladder_id'];
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . MATCHES_TABLE . " WHERE match_ladder  = " . $row['ladder_id']; /* clan */
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . CHALLENGES_TABLE . " WHERE challenge_ladder  = " . $row['ladder_id'];
					$db->sql_query($sql);
					$sql	= "DELETE FROM " . GROUPDATA_TABLE . " WHERE group_ladder  = " . $row['ladder_id'];
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . RIVAL_LADDER_RULES . " WHERE rules_ladder  = " . $row['ladder_id']; /* rules */
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . MATCHFINDER_TABLE . " WHERE match_ladder  = " . $row['ladder_id']; /* match finder */
					$db->sql_query($sql);
					
					$sql	= "DELETE FROM " . RIVAL_MVP . " WHERE ladder_mvp  = " . $row['ladder_id']; /* mvp chart */
					$db->sql_query($sql);
				}
				$db->sql_freeresult($result);
				
				// Now delete subladders too.
				$sql	= "DELETE FROM " . LADDERS_TABLE . " WHERE ladder_parent = " . $ladder_id; /*subladders*/
				$db->sql_query($sql);
				
				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_UPDATED');
				break;
			}
			else
			{
				// needs confirmation
				$no_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladder&amp;ladder_id= " . $ladder_id);
				$si_url	= append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladder&amp;ladder_id={$ladder_id}&amp;submit=1&amp;delete=1&amp;confirm=1");
				trigger_error(sprintf($user->lang['LADDER_DELETE_TXT'], '<a href="' . $no_url . '">', '</a>', '<a href="' . $si_url . '">', '</a>'));
				break;
			}
		}
		else
		{
			$ladder_name	= utf8_normalize_nfc(request_var('ladder_name', '', true));
			$ladder_rules	= utf8_normalize_nfc(request_var('ladder_rules', '', true));

			// Setup the BBcode for the ladder rules.
			$uid			= $bitfield = $options = '';
			$allow_bbcode	= $allow_urls = $allow_smilies = true;
			generate_text_for_storage($ladder_rules, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			// cover
			if (!empty($_FILES['ladder_logo']['type']))
			{
				include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
				$upload = new fileupload('LOGOMEN_', array('jpg', 'jpeg', 'gif', 'png'), 163840, 900, 150, 900, 150, explode('|', $config['mime_triggers']));
			
				$foto = $upload->form_upload('ladder_logo');
				$foto->clean_filename('real', '', $user->data['user_id']);
			
				$destination = "{$phpbb_root_path}images/rivals/ladderlogo/";
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
				$lad_logo_width 	= $foto->get('width');
				$lad_logo_height 	= $foto->get('height');
				
				if (sizeof($foto->error))
				{
					$foto->remove();
					$error = array_merge($error, $foto->error);
				}
				else
				{
					$shalla	= "{$phpbb_root_path}images/rivals/ladderlogo/";
					chmod($shalla . $lad_logo_name, 0644);
				}
				
				$sql_array	= array(
					'ladder_name'		=> $ladder_name,
					'ladder_rules'		=> $ladder_rules,
					'bbcode_uid'		=> $uid,
					'bbcode_bitfield'	=> $bitfield,
					'bbcode_options'	=> $options,
					'ladder_logo'		=> $lad_logo_name,
					'ladder_logo_w'		=> $lad_logo_width,
					'ladder_logo_h'		=> $lad_logo_height
				);
			}
			else
			{
				$sql_array	= array(
					'ladder_name'		=> $ladder_name,
					'ladder_rules'		=> $ladder_rules,
					'bbcode_uid'		=> $uid,
					'bbcode_bitfield'	=> $bitfield,
					'bbcode_options'	=> $options
				);
			}
			
			if (empty($ladder_name))
			{
				$error[] = $user->lang['NAME_LADDER_EMPTY'];
			}

			if (!sizeof($error))
			{
				// Update the ladder's data.
				$sql = "UPDATE " . LADDERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE ladder_id = " . $ladder_id;
				$db->sql_query($sql);

				// Completed. Let the user know.
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_ladders");
				meta_refresh(2, $redirect_url);
				trigger_error('LADDER_UPDATED');
			}
			
			$template->assign_vars(array(
				'ERRORE' => (sizeof($error)) ? implode('<br />', $error) : ''
			));
		}
	}

	// Get the ladder's information.
	$ladder_data	= $ladder->data('*', $ladder_id);
	decode_message($ladder_data['ladder_rules'], $ladder_data['bbcode_uid']);

	// Assign the information to the template.
	$template->assign_vars(array(
		'U_ACTION'		=> $u_action,
		'ERRORE' 		=> (sizeof($error)) ? implode('<br />', $error) : '',
		'LADDER_ID'		=> $ladder_id,
		'CURRENT_LOGO'	=> "{$phpbb_root_path}images/rivals/ladderlogo/{$ladder_data['ladder_logo']}",
		'LADDER_NAME'	=> $ladder_data['ladder_name'],
		'LADDER_RULES'	=> $ladder_data['ladder_rules'])
	);

}

?>