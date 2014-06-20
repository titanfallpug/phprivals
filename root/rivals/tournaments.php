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
 * Called from rivals with action == 'tournaments'
 */

$group		= new group();
$tournament	= new tournament();
$ladder		= new ladder();

$start			= (int) request_var('start', 0);
$season_id		= (int) request_var('season_id', 0);
$tournament_id	= (int) request_var('tournament_id', 0);

/*
* SHOW TOURNAMENT DETAIL
*/
if (!empty($tournament_id))
{
	if ($tournament->get_take_tslots($tournament_id) >= 1)
	{
		$math	= $tournament->data['tournament_brackets'] - $tournament->get_take_tslots($tournament_id);
	}
	else
	{
		$math	= $tournament->data['tournament_brackets'];
	}

	// Setup the BBcode for the tournament info.
	$info		= nl2br(generate_text_for_display($tournament->data('tournament_info', $tournament_id) , $tournament->data('bbcode_uid', $tournament_id), $tournament->data('bbcode_bitfield', $tournament_id), $tournament->data('bbcode_options', $tournament_id)));
	$backurl	= append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments');
	trigger_error(sprintf($user->lang['TOURNAMENT_SPOTS'], $math, $info, $backurl));
}

// Get all the public tournaments.
$sql	= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_status <> 3 ORDER BY tournament_time DESC";
$result	= $db->sql_query($sql);

$i				= 0;
while ($row = $db->sql_fetchrow($result))
{	
	if ($row['tournament_userbased'] == 0)
	{
		// THE CLAN BASED TOURNAMENT
		if (!empty($group->data['group_id']) && $row['tournament_status'] < 2) /* status 2 is for ongoin tournaments */
		{
			if ($tournament->check_ifsignedup($row['tournament_id'], false) == true)
			{
				$show_signup	= ($row['tournament_stricted'] == 1) ? true : false;
				$show_remove	= ($row['tournament_status'] == 1) ? true : false;
			}
			else
			{
				if ($row['tournament_startdate'] >= time())
				{
					if ($tournament->get_totaltclan($row['tournament_id']) >= $row['tournament_brackets']) /* if we have no free slot */
					{
						$show_signup	= false;
						$show_remove	= false;
					}
					else
					{
						if ($row['tournament_type'] == 2 && !in_array($group->data['group_id'], explode("\n", unserialize($row['tournament_invite'])))) /* check for invite only */
						{
							$show_signup	= false;
							$show_remove	= false;
						}
						else if ($row['tournament_type'] == 2 && in_array($group->data['group_id'], explode("\n", unserialize($row['tournament_invite']))))
						{
							$show_signup	= true;
							$show_remove	= false;
						}
						else if ($row['tournament_type'] != 2)
						{
							$show_signup	= true;
							$show_remove	= false;
						}
					}
				}
				else
				{
					$show_signup	= false;
					$show_remove	= false;
				}
			}
		}
		else
		{
			$show_signup	= false;
			$show_remove	= false;
		}
	}
	else if ($row['tournament_userbased'] == 1)
	{
		// THE USER BASED TOURNAMENT
		if ($user->data['user_id'] > ANONYMOUS)
		{
			if ($tournament->check_ifsignedup($row['tournament_id'], true) == true)
			{
				$show_signup	= false;
				$show_remove	= ($row['tournament_status'] == 1) ? true : false;
			}
			else
			{
				if ($row['tournament_startdate'] >= time())
				{
					if ($tournament->get_totaltclan($row['tournament_id']) >= $row['tournament_brackets'])
					{
						$show_signup	= false;
						$show_remove	= false;
					}
					else
					{
						if ($row['tournament_type'] == 2 && !in_array($group->data['group_id'], unserialize((array) $row['tournament_invite'])))
						{
							$show_signup	= false;
							$show_remove	= false;
						}
						else
						{
							$show_signup	= true;
							$show_remove	= false;
						}
					}
				}
				else
				{
					$show_signup	= false;
					$show_remove	= false;
				}
			}
		}
		else
		{
			$show_signup	= false;
			$show_remove	= false;
		}
	}

	//SLOT OCCUPATI
	$slotpresi	= $tournament->get_take_tslots($row['tournament_id']);
	
	switch ($row['tournament_tipo'])
	{
		case 1:
			$Ttipo	= $user->lang['TOURNAMENT_DIRECTELIM'];
		break;
		case 2:
			$Ttipo	= $user->lang['TOURNAMENT_HOMEAWAY_SHORT'];
		break;
	}
	
	// licences
	switch ($row['tournament_licence'])
	{
		case 0:
			$licence	= $user->lang['APERTA_A_C'];
		break;
		case 1:
			$licence	= $user->lang['APERTA_B_A'];
		break;
		case 2:
			$licence	= $user->lang['APERTA_A'];
		break;
	}
	
	// Assign each tournament to the template
	$template->assign_block_vars('block_tournaments', array(
		'U_ACTION'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments_brackets&amp;tournament_id=' . $row['tournament_id']),
		'U_ACTION2'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments&amp;tournament_id=' . $row['tournament_id']),
		'U_ACTION3'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments_signup&amp;tournament_id=' . $row['tournament_id']),
		'U_ACTION4'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments_signup&amp;remove=1&amp;tournament_id=' . $row['tournament_id']),
		'TOURNAMENT_NAME'	=> $row['tournament_name'],
		'TOURNAMENT_LOGO'	=> $row['tournament_logo'],
		'LICENZA'			=> $licence,
		'USERBASED'			=> ($row['tournament_userbased'] == 1) ? '<img src="' . $phpbb_root_path . 'rivals/images/1vs1.gif" alt="1vs1" class="ladderimg" />' : '',
		'SHOW_SIGNUP'		=> $show_signup,
		'SHOW_REMOVE'		=> $show_remove,
		'SHOW_INVITE'		=> ($tournament->data['tournament_type'] == 2) ? ((!in_array($group->data['group_id'], (array) unserialize((array) $row['tournament_invite']))) ? true : false) : false,
		'TIME'				=> $user->format_date($row['tournament_startdate'], 'm/d/Y'),
		'TOURNAMENT_STARTS'	=> $user->format_date($row['tournament_startdate']),
		'SLOTS'				=> ($row['tournament_brackets'] - $slotpresi) . " (" . $row['tournament_brackets'] . ")",
		'TIPO'				=> $Ttipo,
		'BG_COLOR'			=> ($i % 2) ? 'bg1' : 'bg2',
		'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2'
	));

	$i++;
}
$db->sql_freeresult($result);

// Get all archived tournaments.
$sql	= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_status = 3 ORDER BY tournament_time DESC";
$result	= $db->sql_query_limit($sql, 10, $start);
$y	= 0;
while ($row = $db->sql_fetchrow ($result))
{
	// Assign each tournament to the template
	$template->assign_block_vars('block_archivedtournaments', array(
		'U_ACTION'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments_brackets&amp;tournament_id=' . $row['tournament_id']),
		'TOURNAMENT_NAME'	=> $row['tournament_name'],
		'TIME'				=> $user->format_date($row['tournament_startdate'], 'm/d/Y'),
		'BG_COLOR'			=> ($i % 2) ? 'bg1' : 'bg2',
		'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2')
	);

	$i++;
}
$db->sql_freeresult($result);


// Setup the pagination.
$sql	= "SELECT COUNT(tournament_id) AS total FROM " . TOURNAMENTS_TABLE . " WHERE tournament_status = 3";
$result	= $db->sql_query($sql);
$total	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Generate the pagination.
$pagination	= generate_pagination(append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments'), $total['total'], 15, $start);

// Get the season name.
$season_name	= '';
if ($season_id != 0)
{
	$sqls		= "SELECT * FROM " . SEASONS_TABLE . " WHERE season_id = " . $season_id;
	$results	= $db->sql_query($sqls);
	$rows		= $db->sql_fetchrow($results);
	$db->sql_freeresult($results);
	$season_name = '(' . $rows['season_name'] . ')';
}

// Assign the other variables to the template.
$template->assign_vars(array(
	'U_ACTION'		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=tournaments'),
	'SEASON_NAME'	=> $season_name,
	'PAGINATION'	=> $pagination,
	'PAGE_NUMBER'	=> on_page($total['total'], 15, $start))
);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['TOURNAMENT'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=tournaments"))
);

$template->set_filenames(array('body' => 'rivals/tournaments.html'));

?>