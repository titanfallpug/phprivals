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
define('IN_PHPBB', true);
$phpbb_root_path	= (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx				= substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Initilize the phpBB sessions.
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/lang_rivals');

// Include Rivals' classes.
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'rivals/classes/class_group.' . $phpEx);
include($phpbb_root_path . 'rivals/functions.' . $phpEx);

// Start the output.
page_header($user->lang['RIVALS_TITLE']);

$theclans	= array();
$themembers	= array();
$siteimgid	= $config['avatar_salt'];

// check that is the first time that you import clan in 2.0
$result		= $db->sql_query("SELECT COUNT(group_id) as clans FROM " . CLANS_TABLE);
$justclans	= (int) $db->sql_fetchfield('clans');
$db->sql_freeresult($result);

// check for permission acl
if (!$auth->acl_getf_global('a_'))
{
	trigger_error('NOT_AUTHORISED');
}
else if ($auth->acl_getf_global('a_') && $justclans > 0)
{
	trigger_error('ALREADY_CLAN_IN_NEW_TABLE');
}
else
{
	$submit	= (!empty($_POST['submit'])) ? true : false;
	if ($submit)
	{
		// Load in a array all old clan data
		$sql	= "SELECT * FROM " . GROUPS_TABLE . " WHERE group_type = 2";
		$result	= $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (strpos($row['group_name'], '::') !== false)
			{
				$theclans[] = array($row['group_id'], $row['group_name'], $row['group_desc'], $row['group_desc_bitfield'], $row['group_desc_options'], $row['group_desc_uid'], $row['group_avatar'], $row['group_avatar_type'], $row['group_avatar_width'], $row['group_avatar_height'], $row['group_sito']);
				$realclans[] = $row['group_id'];
			}
		}
		$db->sql_freeresult($result);
			
			
		// process each clan for copying
		foreach ($theclans AS $clan)
		{
			// copy the clan logo to new folder if there are one
			if (!empty($clan[6]))
			{
				$ext 	= substr(strrchr($clan[6], '.'), 1);
				$logo	= ("{$phpbb_root_path}images/avatars/upload/{$siteimgid}_g{$clan[0]}.{$ext}");
				
				if (is_file($logo))
				{
					$newdir	= ("{$phpbb_root_path}images/rivals/clanlogo/logofclan{$clan[0]}.{$ext}");
					rename($logo, $newdir);
				}
			}
			else
			{
				$ext 	= 'jpg';
			}
			
			// GET STATS
			$result			= $db->sql_query("SELECT COUNT(match_id) as total_winsA, match_winner, match_confirmed FROM " . MATCHES_TABLE . " WHERE match_winner = {$clan[0]} AND match_confirmed > 0");
			$total_wins		= (int) $db->sql_fetchfield('total_winsA');
			$db->sql_freeresult($result);

			$result			= $db->sql_query("SELECT COUNT(match_id) as total_lossesA, match_loser, match_confirmed FROM " . MATCHES_TABLE . " WHERE match_loser = {$clan[0]} AND match_confirmed > 0");
			$total_losses	= (int) $db->sql_fetchfield('total_lossesA');
			$db->sql_freeresult($result);

			$result			= $db->sql_query("SELECT COUNT(match_id) as total_drawsA, match_winner, match_confirmed FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$clan[0]} OR match_challengee = {$clan[0]}) AND match_winner = '9999999' AND match_confirmed > 0");
			$total_draws	= (int) $db->sql_fetchfield('total_drawsA');
			$db->sql_freeresult($result);
			
			
			$fixname	= explode('::', $clan[1]);
			
			// insert clan in the new table
			$sql_array	= array(
				'group_id'				=> $clan[0],
				'group_name'			=> $fixname[1],
				'group_desc'			=> $clan[2],
				'clan_logo_name'		=> (!empty($clan[6])) ? "logofclan{$clan[0]}.{$ext}" : "nologo.jpg",
				'clan_logo_ext'			=> $ext,
				'clan_logo_width'		=> $clan[8],
				'clan_logo_height'		=> $clan[9],
				'group_tournaments'		=> 'N/A',
				'group_sito'			=> $clan[10],
				'clan_alltime_wins'		=> $total_wins,
				'clan_alltime_losses'	=> $total_losses,
				'clan_alltime_pareggi'	=> $total_draws,
				'clan_level'			=> 0,
				'clan_creation_date'	=> time(),
				'clan_target_10streak'	=> 0,
				'clan_target_ladderwin'	=> 0,
				'clan_favouritemap'		=> '',
				'clan_favouriteteam'	=> '',
				'clan_closed'			=> 0,
				'rth_chicken'			=> 0,
				'rth_powner'			=> 0,
				'clan_rep_value'		=> 5,
				'clan_rep_time'			=> 1,
			);
			// Add it.
			$sql	= "INSERT INTO " . CLANS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);	
		}

		/**
		*	Import the clan members
		**/

		// Load in a array all old clan data
		$sql	= "SELECT * FROM " . USER_GROUP_TABLE . " WHERE group_id > 7"; /* groups from 1 to 7 are all default system groups */
		$result	= $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (in_array($row['group_id'], $realclans)) /* process only groups that are clan for sure */
			{
				$themembers[] = array($row['group_id'], $row['user_id'], $row['group_leader'], $row['user_pending'], $row['mvp_utente']);	
			}
		}
		$db->sql_freeresult($result);
			
		foreach ($themembers AS $member)
		{
			// insert member in new clan-user table
			$sql_array	= array(
				'group_id'		=> $member[0],
				'user_id'		=> $member[1],
				'group_leader'	=> $member[2],
				'user_pending'	=> $member[3],
				'mvp_utente'	=> $member[4],
				'kills'			=> 0,
				'deads'			=> 0,
				'assists'		=> 0,
				'agoals'		=> 0,
				'fgoals'		=> 0,
			);
			// Add it.
			$sql	= "INSERT INTO " . USER_CLAN_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
			$db->sql_query($sql);
		}
		
/*************************************
*** CONVERT MATCHES
********************************/

		$sql	= "SELECT * FROM " . MATCHES_TABLE . " ORDER BY match_id ASC";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			//1) C&amp;D 2) Cat. Bandiera 3) Demolizione
			$modes[]	= explode(") ", $row['ordine_modi']);
			$mode1		= str_replace('1', '', $modes[1]);
			$mode2		= str_replace('2', '', $modes[2]);
			$mode3		= str_replace('3', '', $modes[3]);
			
			$sql_array2	= array(
				'match_challanger_score'		=> ($row['match_winner'] == $row['match_challenger']) ? $row['match_winnerscore'] : $row['match_loserscore'],
				'match_challangee_score'		=> ($row['match_winner'] == $row['match_challengee']) ? $row['match_winnerscore'] : $row['match_loserscore'],
				'match_challanger_score_mode1'	=> ($row['match_winner'] == $row['match_challenger']) ? $row['match_winnerscore_ced'] : $row['match_loserscore_ced'],
				'match_challangee_score_mode1'	=> ($row['match_winner'] == $row['match_challengee']) ? $row['match_winnerscore_ced'] : $row['match_loserscore_ced'],		
				'match_challanger_score_mode2'	=> ($row['match_winner'] == $row['match_challenger']) ? $row['match_winnerscore_dom'] : $row['match_loserscore_dom'],
				'match_challangee_score_mode2'	=> ($row['match_winner'] == $row['match_challengee']) ? $row['match_winnerscore_dom'] : $row['match_loserscore_dom'],		
				'match_challanger_score_mode3'	=> ($row['match_winner'] == $row['match_challenger']) ? $row['match_winnerscore_flag'] : $row['match_loserscore_flag'],
				'match_challangee_score_mode3'	=> ($row['match_winner'] == $row['match_challengee']) ? $row['match_winnerscore_flag'] : $row['match_loserscore_flag'],		
				'mappa_mode1'					=> $row['mappa_ced'],
				'mappa_mode2'					=> $row['mappa_dom'],
				'mappa_mode3'					=> $row['mappa_flag'],		
				'mode1'							=> $mode1,
				'mode2'							=> $mode2,
				'mode3'							=> $mode3,		
				'match_reported'				=> (!empty($row['conferma_win'])) ? $row['conferma_win'] : $row['match_challenger'],
				'match_confirmed'				=> (!empty($row['conferma_los'])) ? $row['conferma_los'] : $row['match_challengee'],
			);
			$sql = "UPDATE " . MATCHES_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE match_id = " . $row['match_id'];
			$db->sql_query($sql);	
		}
		$db->sql_freeresult($result);
		
		$url	= append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=main");
		trigger_error(sprintf($user->lang['IMPORTATION_FINISHED'], $url));
	}
}
	
$template->assign_vars(array(
	'U_ACTION' => '',
));


$template->set_filenames(array('body' => 'rivals/rivals_clan_convertor.html'));
page_footer();
?>