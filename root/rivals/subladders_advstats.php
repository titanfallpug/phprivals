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

$ladder_id	= (int) request_var('ladder_id', 0);

$sql	= "SELECT ustat.*, user.user_id, user.username, user.gamer_name
		FROM " . USER_LADDER_STATS . " ustat, " . USERS_TABLE . " user
		WHERE ustat.ladder_id = {$ladder_id} AND user.user_id = ustat.user_id
		ORDER BY ustat.ranking DESC";
$result = $db->sql_query_limit($sql, 30);
$i		= 0;
while($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('user_chart', array(
		'POS'		=> $i+1,
		'USERNAME'	=> $row['username'],
		'GAMERNAME'	=> $row['gamer_name'],
		'U_USER'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u={$row['user_id']}"),
		'EXP'		=> $row['ranking'],
		'KILLS'		=> $row['kills'],
		'DEADS'		=> $row['deads'],
		'RATIO'		=> ($row['deads'] == 0) ? '1.000' : round($row['kills']/$row['deads'],3),
		'RATIO_FOT'	=> ($row['goala'] == 0) ? '1.000' : round($row['goalf']/$row['goala'],3),
		'ASSISTS'	=> $row['assists'],
		'GOAL_F'    => $row['goalf'],
		'GOAL_S'	=> $row['goala'],			
		'MVPS'		=> $row['mvps'],
		'ROW_COLOR' =>($i % 2) ? 'row1' : 'row2'
	));
	$i++;
}
$db->sql_freeresult($result);

?>