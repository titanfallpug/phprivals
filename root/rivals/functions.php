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
 * Adds the English ordinal suffixes. Used for ranks.
 * From php.net
 *
 * @param integer $number
 * @return mixed
 */
function ordinal($number)
{
	$test_c	= abs($number) % 10;
    $ext	= ((abs($number) %100 < 21 && abs($number) %100 > 4) ? 'th'
			: (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1)
			? 'th' : 'st' : 'nd' : 'rd' : 'th'));

	return	$number . '<sup>' . $ext . '</sup>';
}

/**
 * Calculate the ELO constant based on the group's score.
 *
 * @param integer $score
 * @return integer
 */
function determine_constant($score)
{
	if ($score < 2000)
	{
		$constant	= 30;
	}
	else if ($score >= 2000 && $score < 2400)
	{
		$constant	= 20;
	}
	else
	{
		$constant	= 10;
	}

	return	$constant;
}

/**
 * Calculate the ELO rating (scoring in this case).
 *
 * @param integer $group1_score
 * @param integer $group2_score
 * @param boolean $group1_win
 * @return integer
 */
function calculate_elo($group1_score, $group2_score, $group1_win)
{
	$outcome			= ($group1_win == true) ? 1 : 0;
	$difference			= $group1_score - $group2_score;
	$exponent			= -$difference / 400;
	$expected_outcome	= 1 / (1 + pow(10, $exponent));

	$constant	= determine_constant($group1_score);
	$new_score	= round($group1_score + $constant * ($outcome - $expected_outcome));

	return	$new_score;
}

/**
 * From phpBB code. Re-order the ladders
 *
 * @param string $move
 * @param integer $ladder_id
 */
function re_order($move, $ladder_id)
{
	global	$db;

	// Get the ladder information.
	$sql	= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $ladder_id;
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);

	if ($row['ladder_parent'] == '' || $row['ladder_parent'] == '0')
	{
		// This is ladder.
		$field	= 'ladder_order';

		// Get the ladder information.
		$sql	= "SELECT * FROM " . LADDERS_TABLE . "  WHERE ladder_parent = 0 ORDER BY ladder_order ASC";
		$result	= $db->sql_query($sql);
	}
	else
	{
		// This is a sub-ladder.
		$field	= 'subladder_order';

		// Get the ladder information.
		$sql	= "SELECT * FROM " . LADDERS_TABLE . "  WHERE ladder_parent = {$row['ladder_parent']} ORDER BY subladder_order ASC";
		$result	= $db->sql_query($sql);
	}

	// Put the ladder IDs and ladder order into an array.
	$order	= array();
	while ($row = $db->sql_fetchrow($result))
	{
		$order[]	= array('0' => $row['ladder_id'], '1' => $row[$field]);
	}

	$db->sql_freeresult($result);

	$i	= 0;
	foreach ($order AS $key => $value)
	{
		foreach ($value AS $key_2 => $value_2)
		{
			if ($order[$i][0] == $ladder_id)
			{
				$current	= $order[$i][1];

				if ($move == 'up')
				{
					// We are moving up.
					$new	= $order[$i - 1][1];
					$from	= $order[$i - 1][0];
				}
				else
				{
					// We are moving down.
					$new	= $order[$i + 1][1];
					$from	= $order[$i + 1][0];
				}

				// Switch the two ladder orders.
				$sql	= "UPDATE " . LADDERS_TABLE . " SET $field = $new WHERE ladder_id = " . $ladder_id;
				$db->sql_query($sql);

				$sql	= "UPDATE " . LADDERS_TABLE . " SET $field = $current WHERE ladder_id = " . $from;
				$db->sql_query($sql);
			}
		}

		$i++;
	}
}

/**
 * Sends a PM to a user.
 *
 * @param integer $to
 * @param integer $from
 * @param string $subject
 * @param string $message
 * @return array
 */
function insert_pm($to, $from, $subject, $message)
{
	global	$phpbb_root_path, $phpEx;
	$uid	= $bitfield = $options = 1;

	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

	$data	= array(
		'address_list' => array ('u' => array ($to => 'to')),
		'from_user_id' => $from['user_id'],
		'from_username' => $from['username'],
		'icon_id' => 0,
		'from_user_ip' => $from['user_ip'],
		'enable_bbcode' => true,
		'enable_smilies' => true,
		'enable_urls' => true,
		'enable_sig' => true,
		'message' => $message,
		'bbcode_bitfield' => $bitfield,
		'bbcode_uid' => $uid
	);

	submit_pm('post', $subject, $data, false);
}

/**
 * Get username by ID by Soshen.
 *
 * @param integer $user_id
 * @return string
 */
function getusername($user_id)
{
	global	$db;
	
	$sql	= "SELECT user_id, username FROM " . USERS_TABLE . " WHERE user_id = " . (int) $user_id;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$username = $row['username'];

	return $username;
}

/**
 * Get gamername by ID by Soshen.
 *
 * @param integer $user_id
 * @return string
 */
function getgamername($user_id)
{
	global	$db;
	
	$sql	= "SELECT user_id, gamer_name FROM " . USERS_TABLE . " WHERE user_id = " . (int) $user_id;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$gamername = (!empty($row['gamer_name'])) ? $row['gamer_name'] : '';

	return $gamername;
}

/**
 * Get getuserdata by ID by Soshen.
 *
 * @param string $field
 * @param integer $user_id
 * @return string
 */
function getuserdata($field = '', $user_id)
{
	global	$db;
	
	$sql	= "SELECT user_id, {$field} FROM " . USERS_TABLE . " WHERE user_id = " . (int) $user_id;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row["{$field}"];
}

/**
 * Calculate user Ranking by Soshen
 *
 * @param integer $match_played
 * @param integer $ok_points
 * @param integer $ko_points
 * @return integer
 */
function getuser_rank($match_played, $ok_points, $ko_points)
{
	$ratio	= ($ko_points == 0) ? $ok_points : $ok_points / $ko_points;
	$ratio	= ($ratio == 0) ? 1 : $ratio;
	
	$rank1	= ($ratio * sqrt($match_played / $ratio));
	$rank2	= (1 / 10) * ($ok_points / (100 / (5 * $ratio)));
	$newrank = round($rank1 + $rank2 + ($ratio / 5),4);

	return $newrank;
}

/**
 * Get the images from RivalsMOD imageset pack
 *
 * @param string $imgname
 * @param integer $dimx
 * @param integer $dimy
 * @return string
 */
function getimg_button($imgname, $altname = '', $dimx = 50, $dimy = 25)
{
	global $phpbb_root_path, $user;
	
	if (file_exists("{$phpbb_root_path}rivals/images/imageset/{$user->data['user_lang']}/"))
	{
		$imgpath = "{$phpbb_root_path}rivals/images/imageset/{$user->data['user_lang']}/{$imgname}";
	}
	else
	{
		$imgpath = "{$phpbb_root_path}rivals/images/imageset/en/{$imgname}";
	}
	
	$thealt	= $user->lang['' . $altname . ''];
	$theimg	= '<img src="' . $imgpath . '.png" alt="' . $thealt . '" title="' . $thealt . '" width="' . $dimx . '" class="rivabaseimg" /><img src="' . $imgpath . '_over.png" alt="' . $thealt . '" title="' . $thealt . '" width="' . $dimx . '" class="rivaloverimg" />';

	return $theimg;
}

/**
 * Get the Original Clan ID
 *
 * @param integer $userid
 * @param integer $userladder
 * @return integer
 */
function getoriginalclan_id($userid, $userladder)
{
	global $db;
	
	$sql	= "SELECT grt.group_id, grt.group_ladder FROM " . GROUPDATA_TABLE . " AS grt 
			LEFT JOIN " . USER_CLAN_TABLE . " AS uct ON uct.group_id = grt.group_id 
			WHERE grt.group_ladder = " . (int) $userladder . " AND uct.user_id = " . (int) $userid;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$clanid	= $row['group_id'];

	return $clanid;
}

/**
 * Get the ladder activity status
 *
 * @param integer $clanid
 * @param integer $ladderid
 * @param boolean $usertype
 * @return string
 */
function getactivity_status($clanid, $ladderid, $usertype = false)
{
	global $db, $phpbb_root_path;	
	if ($usertype == true)
	{
		$sql	= "SELECT 1vs1_challanger, 1vs1_ladder, 1vs1_challangee, end_time FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_ladder = {$ladderid} AND (1vs1_challanger = {$clanid} OR 1vs1_challangee = {$clanid}) ORDER BY end_time DESC";
		$result	= $db->sql_query_limit($sql, 1);
        $row	= $db->sql_fetchrow($result);
		$time_max	= $row['end_time'];
	    $db->sql_freeresult($result);
		
		$sql	= "SELECT user_id , 1vs1_ladder, user_frosted FROM " . ONEVSONEDATA_TABLE . " WHERE user_id = {$clanid} AND 1vs1_ladder  = {$ladderid}";
		$result	= $db->sql_query_limit($sql, 1);
        $row	= $db->sql_fetchrow($result);
		$frosted	= $row['user_frosted'];
	    $db->sql_freeresult($result);
	}
	else
	{
		$sql	= "SELECT match_challenger, match_ladder, match_challengee, match_finishtime FROM " . MATCHES_TABLE . " WHERE match_ladder = {$ladderid} AND (match_challenger = {$clanid} OR match_challengee = {$clanid}) ORDER BY match_finishtime DESC";
		$result	= $db->sql_query_limit($sql, 1);
        $row	= $db->sql_fetchrow($result);
		$time_max	= $row['match_finishtime'];
	    $db->sql_freeresult($result);
		
		$sql	= "SELECT group_id, group_ladder, group_frosted FROM " . GROUPDATA_TABLE . " WHERE group_id = {$clanid} AND group_ladder  = {$ladderid}";
		$result	= $db->sql_query_limit($sql, 1);
        $row	= $db->sql_fetchrow($result);
		$frosted	= $row['group_frosted'];
	    $db->sql_freeresult($result);
	}
	
	$current_time	= time();
	$daystime		= 2*24*60*60;
	
	if ($frosted == 0)
	{
		if ($current_time <= ($time_max + 2*$daystime))
		{
			$status = '<img src="' . $phpbb_root_path . 'rivals/images/verdepieno.png" class="status_icon" alt="status" />';
		}
		else if (($current_time > ($time_max + 2*$daystime)) && ($current_time <= ($time_max + 4*$daystime)))
		{
			$status = '<img src="' . $phpbb_root_path . 'rivals/images/verdemezzo.png" class="status_icon" alt="status" />';
		}
		else if (($current_time > ($time_max + 4*$daystime)) && ($current_time <= ($time_max + 7*$daystime)))
		{
			$status = '<img src="' . $phpbb_root_path . 'rivals/images/giallo.png" class="status_icon" alt="status" />';
		}
		else if (($current_time > ($time_max + 7*$daystime)) && ($current_time <= ($time_max + 15*$daystime)))
		{
			$status = '<img src="' . $phpbb_root_path . 'rivals/images/rosso.png" class="status_icon" alt="status" />';
		}
		else if ($current_time > ($time_max + 15*$daystime))
		{
			$status = '<img src="' . $phpbb_root_path . 'rivals/images/vuoto.png" class="status_icon" alt="status" />';
		}
	}
	else
	{
		$status = '<img src="' . $phpbb_root_path . 'rivals/images/frosted.png" class="status_icon" alt="status" />';
	}
	
	return $status;
}

/**
 * Get the Current Clan score for ladder
 *
 * @param integer $clanid
 * @param integer $ladderid
 * @param boolean $usertype
 * @return integer
 */
function current_clanscore($clanid, $ladderid, $usertype = false)
{
	global $db;
	
	if ($usertype == true)
	{
		$sql	= "SELECT user_id, user_score, 1vs1_ladder FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . (int) $ladderid . " AND user_id = " . (int) $clanid;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		$thescore	= $row['user_score'];
	}
	else
	{
		$sql	= "SELECT group_id, group_score, group_ladder FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . (int) $ladderid . " AND group_id = " . (int) $clanid;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		$thescore	= $row['group_score'];
	}

	return $thescore;
}

/**
 * Get if a user is hibernated in a ladder
 *
 * @param integer $userid
 * @param integer $ladderid
 * @return integer
 */
function user_frosted($userid, $ladderid)
{
	global $db;
	
	$sql	= "SELECT user_id, user_frosted, 1vs1_ladder FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . (int) $ladderid . " AND user_id = " . (int) $userid;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$frosted	= $row['user_frosted'];

	return $frosted;
}

/**
 * Get the tournament round winner
 *
 * @param integer $tournamentid
 * @param integer $round
 * @return integer
 */
function get_roundwinner($tournamentid, $round, $position)
{
	global $db;
	
	$pos1	= (int) $position;
	$pos2	= (int) ($position + 1);
	
	$sql	= "SELECT * FROM " . TGROUPS_TABLE . " 
			WHERE (group_position = {$pos1} OR group_position = {$pos2}) AND  
			group_tournament = " . (int) $tournamentid . " AND group_bracket = " . (int) $round . " AND group_loser != 1 AND loser_confirm > 0";
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!empty($row['group_id']))
	{
		$output = $row['group_id'];
	}
	else
	{
		$output = 0;
	}

	return $output;
}

/**
 * Get the users advanced stats for tournament
 *
 * @param string $thestat
 * @param integer $group_uid
 * @param integer $userid
 * use: KILLS, DEADS, ASSISTS
 * @return integer
 */
function get_tadvstats($thestat, $group_uid, $userid)
{
	global $db;
	
	$sql	= "SELECT user_id, group_uid, kills, morti, assist FROM " . TUSER_DATA . " WHERE group_uid = " . (int) $group_uid . " AND user_id = " . (int) $userid;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	switch ($thestat)
	{
		case 'KILLS':
			$output	= $row['kills'];
		break;
		case 'DEADS':
			$output	= $row['morti'];
		break;
		case 'ASSISTS':
			$output	= $row['assist'];
		break;
	}

	return $output;
}

/**
 * Get the tournament round winner
 *
 * @param integer $tournamentid
 * @param integer $tournament_uid
 * @param integer $groupid
 * @param boolean $homeaway
 * @return integer
 */
function get_tgrpoint($tournamentid, $tournament_uid, $groupid, $homeaway = false)
{
	global $db;
	
	$tuid		= (int) $tournament_uid;
	$tid		= (int) $tournamentid;
	$group_id	= (int) $groupid;
	
	$sql	= "SELECT * FROM " . TMATCHES . " 
			WHERE group_uid = {$tuid} AND id_torneo = {$tid} AND (group1 = {$group_id} OR group2 = {$group_id}) AND conferma1 = 1 AND conferma2 = 1";
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!empty($row['group_uid']) && $row['group_uid'] != '69')
	{
		if ($homeaway == false)
		{
			$gpoint	= ($row['group1'] == $group_id) ? $row['punti1'] : $row['punti2'];
		}
		else
		{
			$pointA	 = $row['punti1'] + $row['home_punti1'];
			$pointB	 = $row['punti2'] + $row['home_punti2'];
			
			if ($pointA == $pointB)
			{
				if ($row['first_home'] == $row['group1']) /* group1 was the one that play first match in home */
				{
					$gpoint	= $row['home_punti1'] + ($row['punti1']*2); /* the away points will have double value */
				}
				else /* is the first the away match */
				{
					$gpoint	= ($row['home_punti1']*2) + $row['punti1'];
				}
			}
			else
			{
				$gpoint	= ($row['group1'] == $group_id) ? $pointA : $pointB;
			}
		}
	}
	else if ($row['group_uid'] == '69')
	{
		$gpoint	= ($row['group_loser'] == 1) ? 0 : 3;
	}
	else
	{
		$gpoint	= 0;
	}
	
	return $gpoint;
}

/**
 * Get the max total value for graph
 *
 * @param integer $latestmid
 * @param integer $time
 * @param integer $group_id
 * @param boolena $onlywinratio
 * @return integer
 */
function get_totalmath4graph($latestmid, $time = 0, $group_id, $onlywinratio = false)
{
	global $db;
	
	if ($onlywinratio == false)
	{
		$result	= $db->sql_query("SELECT COUNT(match_id) as total_matches FROM " . MATCHES_TABLE . " WHERE match_id < {$latestmid} AND (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_confirmed > 0");
		$theA	= (int) $db->sql_fetchfield('total_matches');
		$db->sql_freeresult($result);
		
		$result	= $db->sql_query("SELECT COUNT(tm.group_uid) as theB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo LEFT JOIN " . TGROUPS_TABLE . " AS tg ON tm.group_uid = tg.group_uid AND tg.group_id = {$group_id} WHERE (tm.group1 = {$group_id} OR tm.group2 = {$group_id}) AND tm.conferma2 = 1 AND tt.tournament_userbased = 0 AND tg.group_time <= {$time}");
		$theB	= (int) $db->sql_fetchfield('theB');
		$db->sql_freeresult($result);
		
		$thetot	= $theA + $theB;
	}
	else if ($onlywinratio == true)
	{
		$result	= $db->sql_query("SELECT COUNT(match_id) as winA FROM " . MATCHES_TABLE . " WHERE match_id < {$latestmid} AND match_winner = {$group_id} AND match_confirmed > 0");
		$winA	= (int) $db->sql_fetchfield('winA');
		$db->sql_freeresult($result);
		
		$result	= $db->sql_query("SELECT COUNT(tm.group_uid) as winB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo LEFT JOIN " . TGROUPS_TABLE . " AS tg ON tm.group_uid = tg.group_uid AND tg.group_id = {$group_id} WHERE tm.vincitore = {$group_id} AND tm.conferma2 = 1 AND tt.tournament_userbased = 0 AND tg.group_time <= {$time}");
		$winB	= (int) $db->sql_fetchfield('winB');
		$db->sql_freeresult($result);
		
		$thewin	= $winA + $winB;
		
		
		$result	= $db->sql_query("SELECT COUNT(match_id) as losA FROM " . MATCHES_TABLE . " WHERE match_id < {$latestmid} AND match_loser = {$group_id} AND match_confirmed > 0");
		$losA	= (int) $db->sql_fetchfield('losA');
		$db->sql_freeresult($result);
		
		$result	= $db->sql_query("SELECT COUNT(tm.group_uid) as losB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo LEFT JOIN " . TGROUPS_TABLE . " AS tg ON tm.group_uid = tg.group_uid AND tg.group_id = {$group_id} WHERE (tm.group1 = {$group_id} OR tm.group2 = {$group_id}) AND tm.vincitore <> {$group_id} AND tm.conferma2 = 1 AND tt.tournament_userbased = 0 AND tg.group_time <= {$time}");
		$losB	= (int) $db->sql_fetchfield('losB');
		$db->sql_freeresult($result);
		
		$thelos	= $losA + $losB;
		
		
		$result	= $db->sql_query("SELECT COUNT(match_id) as drwA FROM " . MATCHES_TABLE . " WHERE match_id < {$latestmid} AND (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_winner = '9999999' AND match_confirmed > 0");
		$drwA	= (int) $db->sql_fetchfield('drwA');
		$db->sql_freeresult($result);
		
		$result	= $db->sql_query("SELECT COUNT(tm.group_uid) as drwB FROM " . TMATCHES . " AS tm LEFT JOIN " . TOURNAMENTS_TABLE . " AS tt ON tt.tournament_id = tm.id_torneo LEFT JOIN " . TGROUPS_TABLE . " AS tg ON tm.group_uid = tg.group_uid AND tg.group_id = {$group_id} WHERE (tm.group1 = {$group_id} OR tm.group2 = {$group_id}) AND tm.vincitore = '9999999' AND tm.conferma2 = 1 AND tt.tournament_userbased = 0 AND tg.group_time <= {$time}");
		$drwB	= (int) $db->sql_fetchfield('drwB');
		$db->sql_freeresult($result);
		
		$thedrw	= $drwA + $drwB;
		
		// count
		$dividendo	= (($thelos + $thedrw) == 0) ? 1 : ($thelos + $thedrw);
		$thetot 	= ($thewin == 0) ? 0 : ($thewin / $dividendo);
	}

	return $thetot;
}

/**
 * Get the time of latest match played
 *
 * @param integer $group_id
 * @param boolean $userbased
 * @return integer
 */
function get_lastmatchtime($group_id, $userbased = false)
{
	global $db;
	
	if ($userbased == false)
	{	
		$result	= $db->sql_query_limit("SELECT match_challenger, match_challengee, match_finishtime, match_confirmed FROM " . MATCHES_TABLE . " WHERE (match_challenger = {$group_id} OR match_challengee = {$group_id}) AND match_confirmed > 0 ORDER BY match_finishtime DESC", 1);
		$TimeA	= (int) $db->sql_fetchfield('match_finishtime');
		$db->sql_freeresult($result);
		
		$result	= $db->sql_query_limit("SELECT group_id, group_time, loser_confirm FROM " . TGROUPS_TABLE . " WHERE group_id = {$group_id} AND loser_confirm = 1 ORDER BY group_time DESC", 1);
		$TimeB	= (int) $db->sql_fetchfield('group_time');
		$db->sql_freeresult($result);
		
		$output	= ($TimeA >= $TimeB) ? $TimeA : $TimeB;
	}
	else
	{
		$result	= $db->sql_query_limit("SELECT 1vs1_challanger, 1vs1_challangee, end_time, 1vs1_confirmer FROM " . ONEVSONE_MATCH_DATA . " WHERE (1vs1_challanger = {$group_id} OR 1vs1_challangee = {$group_id}) AND 1vs1_confirmer > 0 ORDER BY end_time DESC", 1);
		$TimeA	= (int) $db->sql_fetchfield('match_finishtime');
		$db->sql_freeresult($result);
		
		$result	= $db->sql_query_limit("SELECT group_id, group_time, loser_confirm FROM " . TGROUPS_TABLE . " WHERE group_id = {$group_id} AND loser_confirm = 1 ORDER BY group_time DESC", 1);
		$TimeB	= (int) $db->sql_fetchfield('group_time');
		$db->sql_freeresult($result);
		
		$output	= ($TimeA >= $TimeB) ? $TimeA : $TimeB;	
	}

	return $output;
}

/**
 * Get random image file
 *
 * @param string $shortname
 * @return string
 */
function get_mapofday($shortname)
{
	global $db;
	
	$sql	= "SELECT * FROM " . RANDOM_IMGS_TABLE . " WHERE randimg_short_name = '{$shortname}' ORDER BY RAND()";
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row['randimg_img'];
}

/**
 * Count the number of clans joined to a ladder
 *
 * @param integer $ladder_id
 * @param boolean $usersystem
 * @return integer
 */
function get_totaluserladder($ladder_id, $usersystem = false)
{
	global $db;
	
	if ($usersystem == true)
	{
		$result	= $db->sql_query("SELECT COUNT(*) AS num_users FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . $ladder_id);
		$output	= (int) $db->sql_fetchfield('num_users');
		$db->sql_freeresult($result);
	}
	else
	{
		$result	= $db->sql_query("SELECT COUNT(*) AS num_groups FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . $ladder_id);
		$output	= (int) $db->sql_fetchfield('num_groups');
		$db->sql_freeresult($result);
	}

	return $output;
}

/**
 * Count the number of ladders in a platform
 *
 * @param integer $platform_id
 * @return integer
 */
function get_totladder($platform_id)
{
	global $db;
	
	$result	= $db->sql_query("SELECT COUNT(*) AS num_ladders FROM " . LADDERS_TABLE . " WHERE ladder_platform = " . $platform_id);
	$output	= (int) $db->sql_fetchfield('num_ladders');
	$db->sql_freeresult($result);

	return $output;
}

/**
 * RECalculate the general user exp for all rivals system, single player and clan's player
 *
 * @param integer $user_id
 */
function recalculate_totalEXP($user_id)
{
	global $db;
	
	$sqlu1		= "SELECT user_id, user_mvp, user_exp, user_powns, user_chicken, user_ladder_value FROM " . USERS_TABLE . " WHERE user_id = " . (int) $user_id;
	$resultu1	= $db->sql_query_limit($sqlu1, 1);
	$rowu1		= $db->sql_fetchrow($resultu1);
	$db->sql_freeresult($resultu1);
	
	$oneone_gexp	= (int) ceil($rowu1['user_exp']*30);
	$checkined		= (int) $rowu1['user_chicken'];
	$themvps		= (int) $rowu1['user_mvp'];
	$thepowns		= (int) $rowu1['user_powns'];
	
	$sql	= "SELECT SUM(kills) AS total_kills, SUM(deads) AS total_deads, SUM(assists) AS total_assists, SUM(agoals) AS total_agoals, SUM(fgoals) AS total_fgoals, user_id, user_pending 
			FROM " . USER_CLAN_TABLE . " WHERE user_id  = " . (int) $user_id . " AND user_pending = 0 GROUP BY user_id";
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!empty($row['user_id']))
	{
		$ex_kills	= (int) $row['total_kills'];
		$ex_deads	= (int) $row['total_deads'];
		$ex_assists	= (int) $row['total_assists'];
		$ex_agoals	= (int) $row['total_agoals'];
		$ex_fgoals	= (int) $row['total_fgoals'];
	}
	else
	{
		$ex_kills	= 0;
		$ex_deads	= 0;
		$ex_assists	= 0;
		$ex_agoals	= 0;
		$ex_fgoals	= 0;
	}
	
	// tournament data
	$sqlT		= "SELECT SUM(kills) AS total_killsT, SUM(morti) AS total_deadsT, SUM(assist) AS total_assistsT, user_id, conferma2
				FROM " . TUSER_DATA . " WHERE user_id  = " . (int) $user_id . " AND conferma2 > 0 GROUP BY user_id";
	$resultT	= $db->sql_query($sqlT);
	$rowT		= $db->sql_fetchrow($resultT);
	$db->sql_freeresult($resultT);
	
	if (!empty($rowT['user_id']))
	{
		$ex_killsT		= (int) $rowT['total_killsT'];
		$ex_deadsT		= (int) $rowT['total_deadsT'];
		$ex_assistsT	= (int) $rowT['total_assistsT'];
	}
	else
	{
		$ex_killsT		= 0;
		$ex_deadsT		= 0;
		$ex_assistsT	= 0;
	}
	//
	
	$newpositive	= ($ex_kills + $ex_fgoals + $ex_killsT) * 3;
	$newnegative	= ($ex_deads + $ex_agoals + $ex_deadsT) * 3;
	$newmvps		= $themvps * 20;
	$newchickened	= $checkined * 50;
	$newpowns		= $thepowns * 35;
	
	$finalexp	= ($newpositive + $ex_assists + $ex_assistsT + $newmvps + $oneone_gexp + $newpowns) - ($newchickened + $newnegative);

	$sql_array	= array(
		'user_ladder_value'	=> $finalexp
	);
	$sql = "UPDATE " . USERS_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE user_id = " . (int) $user_id;
	$db->sql_query($sql);
}

/**
 * Get roster total EXP
 *
 * @param integer $roster_id
 * @return string
 */
function get_roster_exp($roster_id)
{
	global $db;
	
	$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE roster_id = " . (int) $roster_id;
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$totalpos	= 0;
	$totalneg	= 0;
	$roster_mbrs	= explode('|', $row['roster_members']);
	
	foreach ($roster_mbrs AS $member)
	{
		$userlevel		= getuserdata('user_ladder_value', $member);
		$userlevenint	= abs(getuserdata('user_ladder_value', $member));
		
		if (substr($userlevel, 0, 1) == '-')
		{
			$totalneg	= $totalneg + $userlevenint;
			$totalpos	= $totalpos + 0;
		}
		else
		{
			$totalneg	= $totalneg + 0;
			$totalpos	= $totalpos + $userlevenint;
		}
	}
	return (string) ($totalpos - $totalneg);
}

/**
 * Get roster total EXP
 *
 * @param integer $roster_id
 * @return string
 */
function get_roster_name($roster_id)
{
	global $db;
	
	$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE roster_id = " . (int) $roster_id;
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	return (string) $row['roster_name'];
}

/**
 * Get roster members list
 *
 * @param integer $roster_id
 * @return array
 */
function get_roster_members($roster_id)
{
	global	$db;
	
	$rosters	= array();

	$sql	= "SELECT * FROM " . RIVAL_ROSTERS . " WHERE roster_id = " . (int) $roster_id;
	$result	= $db->sql_query($sql);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!empty($row['roster_members']))
	{
		$rostering	= explode('|', $row['roster_members']);
		
		if (sizeof($rostering) > 0)
		{
			foreach ($rostering AS $ilroster)
			{
				$rosters[]	= $ilroster;
			}
		}
	}
			
	return $rosters;
}

/**
 * Validate a decerto mode.
 *
 * @param string $short_name
 * @return boolean
 */
function validate_decerto($short_name)
{
	global	$db;

	$sql	= "SELECT * FROM " . DECERTO_CAT . " WHERE nome_corto = '{$short_name}' AND active = 0";
	$result	= $db->sql_query_limit($sql,1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!empty($row['id_decerto']))
	{
		$output = false;
	}
	else
	{
		$output = true;
	}
			
	return $output;
}
?>