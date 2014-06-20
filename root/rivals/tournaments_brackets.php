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
 * Tournaments list
 * Called from rivals with action == 'tournaments_brackets'
 */

$tournament		= new tournament();
$group			= new group();
$tournament_id	= (int) request_var('tournament_id', 0);

// Get the number of groups in the tournament.
$sql	= "SELECT COUNT(*) AS num_groups FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = 1";
$result	= $db->sql_query($sql);
$row	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Check if all the groups have signed up yet.
if ($tournament->data['tournament_status'] == 1)
{
	// Any groups signed up yet?
	if ($row['num_groups'] > 0)
	{
		// Show who signed up.
		$message	= $user->lang['BRACKETS_CANT_GENERATE'] . '</p><br />';
		
		$message	.= '<p>' . $user->lang['SUBSCRIPTED_AT_NOW'] . '</p><br /><ul style="margin-left:20px;">';

		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = 1";
		$result	= $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($tournament->data['tournament_userbased'] == 1)
			{
				$message	.= '<li><a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['group_id']) . '">' . getusername($row['group_id']) . '</a></li>';
			}
			else
			{
				// Show the group's name who signed up.
				$roster		= ($tournament->data('tournament_stricted', $tournament_id) == 1) ? '(' . $user->lang['ROSTERS'] . ': ' . get_roster_name($row['roster_id']) . ')' : '';
				$message	.= '<li><a href="' . append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $row['group_id']) . '">' . $group->data('group_name', $row['group_id']) . '</a> ' . $roster . '</li>';
			}
		}
		$db->sql_freeresult($result);

		$message	.= '</ul><p>';
	}
	else
	{
		// No one signed up, yet.
		$message	= $user->lang['BRACKETS_CANT_GENERATE'];
	}
	trigger_error($message);
}

// Generate the tournament.
$tournament->generate_tournament();

// Show maps and order for decerto tournament
$dece	= $tournament->data('tournament_decerto', $tournament_id);
$shorty = $tournament->data('shorty', $tournament_id);

$ricchione = '';
switch ($dece)
{
	case 1:
		$sql22		= "SELECT * FROM " . TDECERTO. " WHERE id_torneo = {$tournament_id} AND round = 1";
		$result22	= $db->sql_query($sql22);
		$row22		= $db->sql_fetchrow($result22);
		$db->sql_freeresult($result22);
		
		$ricchione = $user->lang['MODALITA'] . ': ' . $row22['modi'] . '<br />'. $user->lang['ORDINE_MAPPE'] . ': ' . $row22['map1'] . ", " . $row22['map2'] . ", " . $row22['map3'];
	break;
	case 2:
		$sql22		= "SELECT * FROM " . TDECERTO. " WHERE id_torneo = {$tournament_id} AND round = 1";
		$result22	= $db->sql_query($sql22);
		$row22		= $db->sql_fetchrow($result22);
		$db->sql_freeresult($result22);
		
		$ricchione = $user->lang['ORDINE_MAPPE'] . ': ' . $row22['map1'] . ", " . $row22['map2'] . ", " . $row22['map3'];
	break;
}

$template->assign_vars(array(
	'DECERTO' => $ricchione
));

/******************************************
* Tournaments advanced stats
*/
if ($tournament->data('tournament_advstats', $tournament_id) == 1 && $tournament->data['tournament_userbased'] == 0)
{
	include($phpbb_root_path . 'rivals/classes/funzioni_numeriche.' . $phpEx);
	$start	= (int) request_var('start', 0);
	$sql_tipo		= " SELECT *, SUM(kills) as totkill, SUM(morti) as totmorti, SUM(assist) as totassist FROM " . TUSER_DATA . " WHERE tournament_id = {$tournament_id} AND conferma1 = 1 AND conferma2 = 1 GROUP BY user_id ORDER BY kills DESC";
	$result_tipo	= $db->sql_query_limit($sql_tipo, 30, $start);
	$i = 0;
	$row_number = $start;
	while ($row_tipo = $db->sql_fetchrow($result_tipo))
	{
		$userid = $row_tipo['user_id'];
		$row_number++;
		
		$deads = ($row_tipo['totmorti'] == 0) ? 1 : $row_tipo['totmorti'];
				
		$template->assign_block_vars('adv_stats', array(
			'NUM'		=> $row_number,
			'GIOCATORE' => getusername($userid),
			'GAMERNAME' => (getgamername($userid) == 0) ? '' : getgamername($userid),
			'CLAN'		=> $group->data('group_name', $row_tipo['group_id']),
			'KILLS'		=> $row_tipo['totkill'],
			'MORTI'		=> $row_tipo['totmorti'],
			'RATIO'		=> round_up($row_tipo['totkill'] / $deads, 3),
			'ASSIST'	=> $row_tipo['totassist'],
			'USER_LINK'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $userid),
			'CLAN_LINK' => append_sid("{$phpbb_root_path}rivals.$phpEx", "action=group_profile&amp;group_id=" . $userid),
			'MVP'		=> $tournament->get_tusermvps($tournament_id, $userid),
			'ROW_COLOR' => ($i % 2) ? 'row1' : 'row2'
		));
		$i++;
	}
	$db->sql_freeresult($result_tipo);

	// Pagination
	$sql	= " SELECT * FROM " . TUSER_DATA . " WHERE tournament_id = {$tournament_id} AND conferma1 = 1 AND conferma2 = 1";
	$result	= $db->sql_query($sql);
	$total	= sizeof($db->sql_fetchrowset($result));
	$db->sql_freeresult($result);

	// Generate the pagination.
	$pagination	= generate_pagination(append_sid("{$phpbb_root_path}rivals.$phpEx", "action=tournaments_brackets&amp;tournament_id=$tournament_id"), $total, 30, $start);

	$template->assign_vars(array(
		'PAGINATION'	=> $pagination,
		'PAGE_NUMBER'	=> on_page($total, 30, $start)
	));
}
	
// set navlink
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['TOURNAMENT'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=tournaments")
));

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $tournament->data['tournament_name'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=tournaments_brackets&amp;tournament_id=" . $tournament_id)
));

		
$template->set_filenames(array('body' => 'rivals/tournaments_backets.html'));

?>