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
 * Check if the user exist and if are the guest one.
 * In are invitation check if are already in the clan.
 * Made for clan join / invitation
 *
 * @param integer $userid
 * @param integer $clanid
 * @param boolean $invite
 * @return interger
 */
function validate_user4clan($userid, $clanid = 0, $invite = true)
{
	global $db;
	
	$sql	= "SELECT user_id FROM " . USERS_TABLE . " WHERE user_id = " . (int) $userid;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (empty($row['user_id']) || $row['user_id'] <= 1)
	{
		$result	= 0;
	}
	else
	{
		if ($invite == true)
		{
			// check if are already in the clan
			$sql2		= "SELECT user_id, group_id, user_pending FROM " . USER_CLAN_TABLE . " WHERE user_id = " . (int) $userid . " AND group_id = " . (int) $clanid;
			$result2	= $db->sql_query_limit($sql2, 1);
			$row2		= $db->sql_fetchrow($result2);
			$db->sql_freeresult($result2);
		
			if (empty($row2['user_id']))
			{
				// if the users ins't members of clan
				$result = 1;
			}
			else
			{
				// if the users is already in the clan
				$result = 0;
			}
		}
		else
		{
			$result = 1;
		}
	}

	return	$result;
}


/**
 * Check if the clan is part of ladder.
 *
 * @param integer $clanid
 * @param integer $ladderid
 * @param boolean $usertype
 * @return interger
 */
function check_clanladder($clanid, $ladderid, $usertype = false)
{
	global $db;
	
	if ($usertype == false)
	{
		$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_ladder = " . (int) $ladderid . " AND group_id = " . (int) $clanid;
		$resesplult	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	}
	else if ($usertype == true)
	{
		$sql	= "SELECT * FROM " . ONEVSONEDATA_TABLE . " WHERE 1vs1_ladder = " . (int) $ladderid . " AND user_id = " . (int) $clanid;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	}
	
	if (sizeof($row) == 0)
	{
		$result = 0;
	}
	else
	{
		$result = 1;
	}
	
	return	$result;
}


/**
 * Check if are the correct challenger and challenge for the match
 *
 * @param integer $challenger
 * @param integer $challenge
 * @param boolean $usertype
 */
function validate_opponents($challenger, $challenge, $usertype = false)
{
	global $phpbb_root_path, $phpEx, $user;
	
	if ($usertype == false)
	{
		$group	= new group();
		
		if (!in_array($group->data['group_id'], array($challenger, $challenge)))
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			redirect($redirect_url);
			
			break;
		}
	}
	else if ($usertype == true)
	{
		if ($user->data['user_id'] != $challenger && $user->data['user_id'] != $challenge)
		{
			$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
			redirect($redirect_url);
			
			break;
		}
	}
}


/**
 * Check if there are a double checkin
 *
 * @param array $accept
 * @param array $decline
 * @param string $mode
 */
function nodouble_check($accept, $decline, $mode = '')
{
	global $phpbb_root_path, $phpEx, $user;
	
	$doublearray	= array_intersect($accept, $decline);
	
	if (sizeof($doublearray) > 0)
	{
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "{$mode}");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['DOUBLE_CHECK'], '<a href="' . $redirect_url . '">', '</a>'));
		
		break;
	}
}


/**
 * Check if the group exist
 *
 * @param integer $groupid
 * @param string $clanname
 * @return interger
 */
function clan_check($groupid = 0, $clanname = '')
{
	global $db;
	
	$clannamefixed	= (string) $clanname;
	
	if (empty($clannamefixed) && $groupid > 0)
	{
		$sql	= "SELECT group_id FROM " . CLANS_TABLE . " WHERE group_id = " . (int) $groupid;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!empty($row['group_id']))
		{
			$result = 1;
		}
		else
		{
			$result = 0;
		}
	}
	else if (!empty($clannamefixed) && $groupid == 0)
	{
		$sql	= "SELECT group_name FROM " . CLANS_TABLE . " WHERE group_name " . $db->sql_like_expression($clannamefixed . $db->any_char);;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!empty($row['group_name']))
		{
			$result = 1;
		}
		else
		{
			$result = 0;
		}
	}
	
	return	$result;
}

/**
 * Check if the user is a good one
 *
 * @param integer $user_id
 * @param integer $banned_group
 * @param integer $min_post
 * @return bolean
 */
function validate_user($user_id, $banned_group = 0, $min_post = 0)
{
	global	$phpbb_root_path, $db;
	
	$sql	= "SELECT user_id, user_type, group_id, user_posts FROM " . USERS_TABLE . " WHERE user_id = " . (int) $user_id;
	$result	= $db->sql_query_limit($sql, 1);
	$row	= $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$banned	= ($banned_group == 0) ? 6 : (int) $banned_group;

	if (!empty($row['user_id']))
	{
		if ($row['user_type'] != 2 && $row['group_id'] != $banned && $row['user_posts'] >= $min_post)
		{
			$output	= true;
		}
		else
		{
			$output	= false;
		}
	}
	else
	{
		$output	= false;
	}
	
	return $output;
}

?>