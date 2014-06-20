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
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * Match Chat
 * Called from ucp_rivals with mode == 'match_chat'
 */
function ucp_rivals_match_chat($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$tournament	= new tournament();
	$submit		= (!empty($_POST['submit'])) ? true : false;
	$userm		= (int) request_var('uwar', 0);
	$match		= (int) request_var('mid', 0);
	$turnmatch	= (int) request_var('twar', 0);
	$lightbox	= (int) request_var('lb', 0);
	$idgruppo	= (!empty($user->data['group_session'])) ? $group->data['group_id'] : 0;
	
	if ($userm == 1 && $turnmatch == 0)
	{
		$xflag = 1;
	}
	else if ($userm == 1 && $turnmatch == 1)
	{
		$xflag = 3;
	}
	else if ($userm == 0 && $turnmatch == 1)
	{
		$xflag = 2;
	}
	else
	{
		$xflag = 0;
	}
	
	if ($match > 0)
	{
		// Get the position for tournament.
		if ($xflag == 3)
		{
			$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$match} AND group_id = {$user->data['user_id']} AND loser_confirm = 0";
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$clan2	 	= $tournament->get_vsclan($row['group_tournament'], $row['group_id'], $row['group_bracket'], false);
			if (!empty($clan2))
			{
				$modtpos	= $row['group_id'] . $clan2 . '_mod';
				$modtpos2	= $clan2 . $row['group_id'] . '_mod';
				$posfixed	= ($row['group_position'] & 1) ? $row['group_position'] + 1 : $row['group_position'] - 1;
				$poslimit	= " AND (tposition = {$row['group_position']} OR tposition = {$posfixed} OR tposition = '{$modtpos}' OR tposition = '{$modtpos2}')";
				$poxround	= $row['group_bracket'];
			}
			else
			{
				$poslimit	= " AND tposition = 0";
				$poxround	= 1;
			}
		}
		else if ($xflag == 2)
		{
			$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$match} AND group_id = {$group->data['group_id']} AND loser_confirm = 0";
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$clan2	 	= $tournament->get_vsclan($row['group_tournament'], $row['group_id'], $row['group_bracket'], false);
			if (!empty($clan2))
			{
				$modtpos	= $row['group_id'] . $clan2 . '_mod';
				$modtpos2	= $clan2 . $row['group_id'] . '_mod';
				$posfixed	= ($row['group_position'] & 1) ? $row['group_position'] + 1 : $row['group_position'] - 1;
				$poslimit	= " AND (tposition = {$row['group_position']} OR tposition = {$posfixed} OR tposition = '{$modtpos}' OR tposition = '{$modtpos2}')";
				$poxround	= $row['group_bracket'];
			}
			else
			{
				$poslimit	= " AND tposition = 0";
				$poxround	= 1;
			}
		}
		else
		{
			$poslimit	= ''; /* no tournament */
			$poxround	= 1;
		}
		
		// MATCH CHAT
		$sql7		= "SELECT * FROM " . RIVALS_MATCH_CHAT . " WHERE id_match = {$match} AND chat_flag = {$xflag}{$poslimit} AND tround = {$poxround} ORDER BY chat_time DESC";
		$result7	= $db->sql_query($sql7);
		$i7	= 0;
		while($row7 = $db->sql_fetchrow($result7))
		{		
			$template->assign_block_vars('block_match_chat', array(
				'POSTER_URL'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row7['id_writer']),
				'POSTER_NAME'	=> getusername($row7['id_writer']),
				'CLAN_NAME'		=> ($userm == 0) ? (($row7['id_clan'] > 0) ? $group->data('group_name', $row7['id_clan']) : $user->lang['LADDER_STAFF']) : ((strpos($row7['tposition'], '_mod') !== false) ? $user->lang['LADDER_STAFF'] : ''),
				'CLAN_URL'		=> ($row7['id_clan'] > 0) ? append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $row7['id_clan']) : '#',
				'CHAT_TIME'		=> $user->format_date($row7['chat_time']),
				'CHAT_TEXT'		=> generate_text_for_display($row7['chat_text'], $row7['bbcode_uid'], $row7['bbcode_bitfield'], $row7['bbcode_options'])
			));
			$i7++;
		}
		$db->sql_freeresult($result7);
	}
	else
	{	
		if (!empty($user->data['group_session']))
		{
			// GET current ongoing matches list for clan
			$sql 	= "SELECT * FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$group->data['group_id']} OR match_challengee = {$group->data['group_id']}) 
					AND match_confirmed = 0 ORDER BY match_id DESC";
			$result = $db->sql_query($sql);
			$i		= 0;
			while ($row = $db->sql_fetchrow($result))
			{		
				$ladder_data	= $ladder->get_roots($row['match_ladder']);
				
				$template->assign_block_vars('block_match', array(
					'EDIT_URL'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;mid={$row['match_id']}"),
					'CHALLANGER'	=> $group->data('group_name', $row['match_challenger']),
					'CHALLANGEE'	=> $group->data('group_name', $row['match_challengee']),
					'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
					'LADDER'		=> $ladder_data['LADDER_NAME'],
					'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME']
				));
				$i++;
			}
			$db->sql_freeresult($result);
			
			// GET current ongoing tournaments match CLAN
			$sql3		= "SELECT * FROM " . TGROUPS_TABLE . " AS tg LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tg.group_tournament = tt.tournament_id
						WHERE tg.group_id = {$group->data['group_id']} AND tt.tournament_userbased = 0 AND tg.loser_confirm = 0 ORDER BY tg.group_tournament DESC";
			$result3	= $db->sql_query($sql3);
			$y3		= 0;
			while ($chapa3 = $db->sql_fetchrow($result3))
			{				
				$clan1name	= $group->data('group_name', $chapa3['group_id']);
				$clan2	 	= $tournament->get_vsclan($chapa3['group_tournament'], $chapa3['group_id'], $chapa3['group_bracket'], false);
				$clan2name	= $group->data('group_name', $clan2);
				
				if (!empty($clan2))
				{
					$template->assign_block_vars('block_match_tournament', array(
						'EDIT_URL'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;twar=1&amp;mid={$chapa3['group_tournament']}"),
						'CHALLANGER'	=> $clan1name,
						'CHALLANGEE'	=> $clan2name,
						'TOURNAMENT'	=> $tournament->data('tournament_name', $chapa3['group_tournament'])
					));
				}
				$y3++;
			}
			$db->sql_freeresult($result3);
		}
		
		// GET current ongoing matches list for users
		$sql 	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$user->data['user_id']} OR 1vs1_challangee = {$user->data['user_id']}) 
				AND 1vs1_confirmer = 0 AND 1vs1_accepted > 0 ORDER BY 1vs1_id DESC";
		$result = $db->sql_query($sql);
		$i		= 0;
		while ($row = $db->sql_fetchrow($result))
		{		
			$ladder_data	= $ladder->get_roots($row['1vs1_ladder']);
			
			$template->assign_block_vars('block_match', array(
				'EDIT_URL'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;uwar=1&amp;mid={$row['1vs1_id']}"),
				'CHALLANGER'	=> getusername($row['1vs1_challanger']),
				'CHALLANGEE'	=> getusername($row['1vs1_challangee']),
				'PLATFORM'		=> $ladder_data['PLATFORM_NAME'],
				'LADDER'		=> $ladder_data['LADDER_NAME'],
				'SUBLADDER'		=> $ladder_data['SUBLADDER_NAME']
			));
			$i++;
		}
		$db->sql_freeresult($result);
		
		// GET current ongoing tournaments match USER
		$sql2		= "SELECT * FROM " . TGROUPS_TABLE . " AS tg LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tg.group_tournament = tt.tournament_id
					WHERE tg.group_id = {$user->data['user_id']} AND tt.tournament_userbased = 1 AND tg.loser_confirm = 0 ORDER BY tg.group_tournament DESC";
		$result2	= $db->sql_query($sql2);
		$y		= 0;
		while ($chapa = $db->sql_fetchrow($result2))
		{				
			$clan1name	= getusername($chapa['group_id']);
			$clan2	 	= $tournament->get_vsclan($chapa['group_tournament'], $chapa['group_id'], $chapa['group_bracket'], false);
			$clan2name	= getusername($clan2);
			
			if (!empty($clan2))
			{
				$template->assign_block_vars('block_match_tournament', array(
					'EDIT_URL'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;twar=1&amp;uwar=1&amp;mid={$chapa['group_tournament']}"),
					'CHALLANGER'	=> $clan1name,
					'CHALLANGEE'	=> $clan2name,
					'TOURNAMENT'	=> $tournament->data('tournament_name', $chapa['group_tournament'])
				));
			}
			$y++;
		}
		$db->sql_freeresult($result2);
	}
	
	
	// CHAT insert message
	if ($submit)
	{
		$textchat	= (string) utf8_normalize_nfc(request_var('chat_text', '', true));
		$lightbox	= request_var('lb', 0);
		
		$uid = $bitfield = $options = '';
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($textchat, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
		
		if ($userm == 1 && $turnmatch == 0)
		{
			// Get the match information.
			$sql	= "SELECT * FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_id = " . $match;
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$destinatario	= ($user->data['user_id'] == $row['1vs1_challanger']) ? $row['1vs1_challangee'] : $row['1vs1_challanger'];
			$destname		= $user->data['username'];
			$chatflag		= 1;
			$urlflag		= '&amp;uwar=1';
			$tposition		= 0;
			$tround			= 1;
		}
		else if ($userm == 1 && $turnmatch == 1)
		{
			// Get the match tournament information.
			$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = {$user->data['user_id']} AND group_tournament = {$match} AND loser_confirm = 0 ORDER BY group_bracket DESC";
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$destinatario	= $tournament->get_vsclan($row['group_tournament'], $row['group_id'], $row['group_bracket'], false);
			$destname		= $user->data['username'];
			$chatflag		= 3;
			$urlflag		= '&amp;twar=1&amp;uwar=1';
			$tposition		= $row['group_position'];
			$tround			= $row['group_bracket'];
		}
		else if ($userm == 0 && $turnmatch == 1)
		{
			// Get the match tournament information.
			$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_id = {$group->data['group_id']} AND group_tournament = {$match} AND loser_confirm = 0 ORDER BY group_bracket DESC";
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$opponent_id	= $tournament->get_vsclan($row['group_tournament'], $row['group_id'], $row['group_bracket'], false);
			$destinatario	= $group->data('user_id', $opponent_id);
			$destname		= $group->data['group_name'];
			$chatflag		= 2;
			$urlflag		= '&amp;twar=1';
			$tposition		= $row['group_position'];
			$tround			= $row['group_bracket'];
		}
		else
		{
			// Get the match information.
			$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $match;
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$destinatario	= ($group->data['group_id'] == $row['match_challenger']) ? $group->data('user_id', $row['match_challengee']) : $group->data('user_id', $row['match_challenger']);
			$destname		= $group->data['group_name'];
			$chatflag		= 0;
			$urlflag		= '';
			$tposition		= 0;
			$tround			= 1;
		}
		
		$sql_array	= array(
			'id_match'			=> $match,
			'id_writer'			=> $user->data['user_id'],
			'id_clan'			=> ($userm == 0) ? $group->data['group_id'] : 0,
			'chat_flag'			=> $chatflag,
			'chat_text'			=> $textchat,
			'tposition'			=> $tposition,
			'tround'			=> $tround,
			'chat_time'			=> time(),
			'bbcode_uid'		=> $uid,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_options'	=> $options			
		);
		$sql	= "INSERT INTO " . RIVALS_MATCH_CHAT . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);
		
		// send pm to clans leader
		$subject	= $user->lang['MOD_CHATWRITE_USER'];
		$message	= sprintf($user->lang['MOD_CHATWRITETEXT_USER'], $destname);
		insert_pm($destinatario, $user->data, $subject, $message);
		
		$coda = ($lightbox == 1) ? '&amp;lb=1' : '';
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat{$urlflag}&amp;mid={$match}" . $coda);
		redirect($redirect_url);
	}
	
	
	// Assign the other variables to the template.
	$template->assign_vars(array(
		'MATCHDET'	=> ($match == 0) ? false : true,
		'ID_MATCH'	=> $match,
		'ID_TMATCH'	=> $turnmatch,
		'ID_UMATCH'	=> $userm,
		'LIGHTBOX'	=> $lightbox,
		'U_ACTION'	=> $u_action
	));
}

?>