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
 * Edit Groups
 * Called from acp_rivals with mode == 'edit_mvp'
 */
function acp_rivals_edit_mvp($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$group		= new group();
	$ladder		= new ladder();
	$submit		= (!empty($_POST['submit'])) ? true : false;
	$group_id	= (int) request_var('group_id', 0);
	
	// LOAD CLANS FOR SELECT
	$sql	= "SELECT * FROM " . CLANS_TABLE . " WHERE clan_closed = 0 ORDER BY group_name ASC";
	$result	= $db->sql_query($sql);
	$j 		= 0;
	while ($row = $db->sql_fetchrow($result))
	{
		// Assign it to the template.
		$template->assign_block_vars('clan_select', array(
			'CLAN_NAME'	=> $row['group_name'],
			'CLAN_ID'	=> $row['group_id']
		));
	$j++;
	}
	$db->sql_freeresult($result);
	
	if (!empty($group_id))
	{
		// find the ladder where the clan plays
		$sql	= "SELECT group_id, group_ladder FROM " . GROUPDATA_TABLE . " WHERE group_id = " . $group_id;
		$result	= $db->sql_query($sql);
		
		$ladders	= array();
		while ($row = $db->sql_fetchrow($result))
		{
			$ladders[]	= $row['group_ladder'];
		}
		$db->sql_freeresult();
		$i	= 0;
		if(sizeof($ladders) > 0)
		{
			foreach($ladders AS $ladderid)
			{
				
				$ladder_data	= $ladder->get_roots($ladderid);
				//Template ladder
				$template->assign_block_vars('block_ladders', array(
					'PLATFORM'	=> $ladder_data['PLATFORM_NAME'],
					'LADDER' 	=> $ladder_data['LADDER_NAME'],
					'SUBLADDER' => $ladder_data['SUBLADDER_NAME'],
					'GROUPID'	=> $group_id,
					'LADDERID'	=> $ladderid
				));
				
				//get member for that ladder
				$members	= (array) $group->members('get_members', $group_id);
				$m	= 0;
				foreach($members AS $value)
				{
					
					$sql5		= " SELECT * FROM " . USER_LADDER_STATS . " WHERE ladder_id = {$ladderid} AND user_id = {$value}
								ORDER BY user_id DESC ";
					$result5	= $db->sql_query_limit($sql5, 1);
					$row5		= $db->sql_fetchrow($result5);
					$db->sql_freeresult($result5);
					
					// Assign the member's data to the template.
					$template->assign_block_vars('block_ladders.block_members', array(
						'U_MEMBERPROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $value),
						'MEMBER_NAME'		=> getusername($value),
						'MEMBER_ID'			=> $value,
						'MEMBER_MVP'		=> $row5['mvps'],
						'MEMBER_KILLS'		=> $row5['kills'],
						'MEMBER_DEADS'		=> $row5['deads'],
						'MEMBER_ASSISTS'	=> $row5['assists'],
						'MEMBER_GOALA'		=> $row5['goala'],
						'MEMBER_GOALF'		=> $row5['goalf'],
						'MEMBER_PLAYED'		=> $row5['match_played']
					));
					$m++;
				}
				$i++;
			}
		}
	}
	// action
	if ($submit)
	{
		$advstats	= isset($_POST['stats']) ? $_POST['stats'] : array();
		$xgroup_id	= (int) request_var('xgroup_id', 0);
		$xladder_id	= (int) request_var('xladder_id', 0);
		
		foreach ($advstats as $ID_utente => $values)
		{
			$mpayed	= (!empty($values['mpayed'])) ? $values['mpayed'] : 0;
			$xmvp	= (!empty($values['mvps'])) ? $values['mvps'] : 0;
			$xkill	= (!empty($values['kills'])) ? $values['kills'] : 0;
			$xdeads	= (!empty($values['morti'])) ? $values['morti'] : 0;
			$xasist = (!empty($values['assist'])) ? $values['assist'] : 0;
			$xgoalf = (!empty($values['goalf'])) ? $values['goalf'] : 0;
			$xgoala = (!empty($values['goals'])) ? $values['goals'] : 0;
						
			//CHECK FOR NUMBERS ENTRIES
			if (!is_numeric($xkill)
			|| !is_numeric($xmvp)
			|| !is_numeric($mpayed)
			|| !is_numeric($xdeads)
			|| !is_numeric($xasist)
			|| !is_numeric($xgoalf)
			|| !is_numeric($xgoala))
			{
				$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_mvp");
				meta_refresh(4, $redirect_url);
				trigger_error(sprintf($user->lang['STATS_SOLO_NUMERI'], '<a href="' . $redirect_url . '">', '</a>'));
			}
						
			
			
	// GET THE DIFFERENCE FOR ADD OR REMOVE VALUES AND UPDATE THE USER_CLAN_TABLE IF NEED
			$sql_m		= "SELECT * FROM " . USER_LADDER_STATS . " WHERE ladder_id = {$xladder_id} AND user_id = {$ID_utente}";
			$result_m	= $db->sql_query_limit($sql_m, 1);
			$row_m		= $db->sql_fetchrow($result_m);
			$db->sql_freeresult($result_m);
			
			if ($xmvp > $row_m['mvps']) // if new values if biggest -> sum
			{
				$mvp_diff	= $xmvp - $row_m['mvps'];
				// update general user stats
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET mvp_utente = mvp_utente + {$mvp_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
				// mvp need update the user table too
				$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp + {$mvp_diff} WHERE user_id = {$ID_utente}";
				$db->sql_query($sql);
			}
			else if ($xmvp < $row_m['mvps']) // if new values if biggest -> diff
			{
				$mvp_diff	= $row_m['mvps'] - $xmvp;
				// update general user stats
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET mvp_utente = mvp_utente - {$mvp_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
				// mvp need update the user table too
				$sql = "UPDATE " . USERS_TABLE . " SET user_mvp = user_mvp - {$mvp_diff} WHERE user_id = {$ID_utente}";
				$db->sql_query($sql);
			}
		/*-----------------*/	
			if ($xkill > $row_m['kills'])
			{
				$kill_diff	= $xkill - $row_m['kills'];
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET kills = kills + {$kill_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
			else if ($xkill < $row_m['kills'])
			{
				$kill_diff	= $row_m['kills'] - $xkill;
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET kills = kills - {$kill_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
		/*-----------------*/	
			if ($xdeads > $row_m['deads'])
			{
				$dead_diff	= $xdeads - $row_m['deads'];
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET deads = deads + {$dead_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
			else if ($xdeads < $row_m['deads'])
			{
				$dead_diff	= $row_m['deads'] - $xdeads;
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET deads = deads - {$dead_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
		/*-----------------*/	
			if ($xasist > $row_m['assists'])
			{
				$ass_diff	= $xasist - $row_m['assists'];
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET assists = assists + {$ass_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
			else if ($xasist < $row_m['assists'])
			{
				$ass_diff	= $row_m['assists'] - $xasist;
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET assists = assists - {$ass_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
		/*-----------------*/	
			if ($xgoalf > $row_m['goalf'])
			{
				$golf_diff	= $xgoalf - $row_m['goalf'];
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET fgoals = fgoals + {$golf_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
			else if ($xgoalf < $row_m['goalf'])
			{
				$golf_diff	= $row_m['goalf'] - $xgoalf;
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET fgoals = fgoals - {$golf_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
		/*-----------------*/	
			if ($xgoala > $row_m['goala'])
			{
				$gola_diff	= $xgoala - $row_m['goala'];
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET agoals = agoals + {$gola_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}
			else if ($xgoala < $row_m['goala'])
			{
				$gola_diff	= $row_m['goala'] - $xgoala;
				$sql = "UPDATE " . USER_CLAN_TABLE . " SET agoals = agoals - {$gola_diff} WHERE user_id = {$ID_utente} AND group_id = {$xgroup_id}";
				$db->sql_query($sql);
			}

			// update the ladder user stats table
			$positivep	= $xkill + $xgoalf;
			$negativep	= $xdeads + $xgoala;
			$played		= $mpayed;
			
			$sql_array4	= array(
				'ranking'	=> getuser_rank($played, $positivep, $negativep),
				'mvps'		=> $xmvp,
				'kills'		=> $xkill,
				'deads'		=> $xdeads,
				'assists'	=> $xasist,
				'goalf'		=> $xgoalf,
				'goala'		=> $xgoala
			);
			$sql = "UPDATE " . USER_LADDER_STATS . " SET " . $db->sql_build_array('UPDATE', $sql_array4) . " WHERE ladder_id = {$xladder_id} AND user_id = {$ID_utente}";
			$db->sql_query($sql);
			
		}
		
		// let the user know that is finished
		$redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=rivals&amp;mode=edit_mvp");
		meta_refresh(2, $redirect_url);
		trigger_error(sprintf($user->lang['STATS_UPDATED'], '<a href="' . $redirect_url . '">', '</a>'));
	}
}
?>