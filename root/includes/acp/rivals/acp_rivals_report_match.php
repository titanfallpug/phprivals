<?php
##############################################################
# FILENAME  : acp_rivals_report_match.php
# COPYRIGHT : (c) 2010, Soshen <nipponart.org>
# http://opensource.org/licenses/gpl-license.php GNU Public License
##############################################################
if (!defined ('IN_PHPBB'))
{
	exit;
}

/**
 * Report a Match
 * Called from acp_rivals with mode == 'report_match'
 */
function acp_rivals_report_match ($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

	$group	= new group ();
	$ladder	= new ladder ();

	$submit		= request_var('submit', '');
	$ladder_id	= request_var('ladder_id', 0);

	// Are we submitting a form?
	if (!empty($submit))
	{
		// Yes, handle the form
		$group_winner	= request_var('group_winner', 0);
		$group_loser	= request_var('group_loser', 0);
		
		// DECERTO MOD


$win_point = isset($_POST['win_point']) ? intval($_POST['win_point']) : $row['win_point'];
$los_point = isset($_POST['los_point']) ? intval($_POST['los_point']) : $row['los_point'];

$ordine_modi = isset($_POST['ordine_modi']) ? trim($_POST['ordine_modi']) : $row['ordine_modi'];
$mappa_ced = isset($_POST['mappa_ced']) ? trim($_POST['mappa_ced']) : $row['mappa_ced'];
$mappa_dom = isset($_POST['mappa_dom']) ? trim($_POST['mappa_dom']) : $row['mappa_dom'];
$mappa_flag = isset($_POST['mappa_flag']) ? trim($_POST['mappa_flag']) : $row['mappa_flag'];

                $win_point_ced = isset($_POST['win_point_ced']) ? intval($_POST['win_point_ced']) : $row['win_point_ced'];
				$los_point_ced = isset($_POST['los_point_ced']) ? intval($_POST['los_point_ced']) : $row['los_point_ced'];
				$win_point_dom = isset($_POST['win_point_dom']) ? intval($_POST['win_point_dom']) : $row['win_point_dom'];
				$los_point_dom = isset($_POST['los_point_dom']) ? intval($_POST['los_point_dom']) : $row['los_point_dom'];
				$win_point_flag = isset($_POST['win_point_flag']) ? intval($_POST['win_point_flag']) : $row['win_point_flag'];
				$los_point_flag = isset($_POST['los_point_flag']) ? intval($_POST['los_point_flag']) : $row['los_point_flag'];

		// Insert the match into the database.
		$sql_array	= array(
			'match_challenger' => $group_winner,
			'match_challengee' => $group_loser,
			'match_finishtime' => time (),
			'match_ladder' => $ladder_id,
			'match_winner' => $group_winner,
			'match_loser' => $group_loser,
			'match_details' => '',
			'match_winnerscore' => $win_point,
			'match_loserscore' => $los_point,
			'ordine_modi' => $ordine_modi,
			'mappa_ced' => $mappa_ced,
			'mappa_dom' => $mappa_dom,
			'mappa_flag' => $mappa_flag,
			'match_winnerscore_ced' => $win_point_ced,
			'match_loserscore_ced' => $los_point_ced,
			'match_winnerscore_dom' => $win_point_dom,
			'match_loserscore_dom' => $los_point_dom,
			'match_winnerscore_flag' => $win_point_flag,
			'match_loserscore_flag' => $los_point_flag
		);
		
		$sql		= "INSERT INTO " . MATCHES_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
		$db->sql_query($sql);
		$match_id	= $db->sql_nextid ();

		// ELO scoring system.
		$score_winner	= calculate_elo ($group->data ('group_score', $group_winner, $ladder_id), $group->data ('group_score', $group_loser, $ladder_id), true);
		$score_loser	= calculate_elo ($group->data ('group_score', $group_loser, $ladder_id), $group->data ('group_score', $group_winner, $ladder_id), false);

		// Calculate the new streaks.
		$streak_winner	= ($group->data ('group_streak', $group_winner, $ladder_id) < 0) ? 'group_streak = 0' : 'group_streak = group_streak + 1';
		$streak_loser	= ($group->data ('group_streak', $group_winner, $ladder_id) < 0) ? 'group_streak = group_streak - 1' : 'group_streak = 0';

		$sql	= "UPDATE " . GROUPDATA_TABLE . " SET group_wins = group_wins + 1, group_lastscore = group_score, group_score = $score_winner, $streak_winner WHERE group_id = $group_winner AND group_ladder = " . $ladder_id;
		$db->sql_query($sql);

		$sql	= "UPDATE " . GROUPDATA_TABLE . " SET group_losses = group_losses + 1, group_lastscore = group_score, group_score = $score_loser, $streak_loser WHERE group_id = $group_loser AND group_ladder = " . $ladder_id;
		$db->sql_query($sql);

		// Get the match information.
		$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE match_id = " . $match_id;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Now, update the ranks. Swap if needed.
		$ladder	= new ladder ();
		$ladder->update_ranks ($row['match_winner'], $row['match_loser'], $row['match_ladder']);

		// Completed. Let the user know.
		trigger_error('MATCH_REPORTED');
	}
	else
	{
		// Load the group data?
		if (!empty($ladder_id))
		{
			// Yes. A ladder was selected.
			// Get the groups for the ladder_id.
			$sql	= "SELECT cd.*, c.* FROM " . GROUPDATA_TABLE . " cd, " . GROUPS_TABLE . " c WHERE cd.group_ladder = $ladder_id AND cd.group_id = c.group_id ORDER BY c.group_name ASC";
			$result	= $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				// Assign the groups to the template.
				$template->assign_block_vars('block_groups', array(
					'GROUP_NAME' => $group->data ('group_name', $row['group_id']),
					'GROUP_ID' => $row['group_id'])
				);
			}

			$db->sql_freeresult($result);

			$template->assign_vars(array('LADDER_ID' => $ladder_id));
		}
		else
		{
			// No. No ladder was selected.
			$has_ladderid	= false;

			// Loop through the ladders.
			$sql	= "SELECT l.*, p.* FROM " . LADDERS_TABLE . " l, " . PLATFORMS_TABLE . " p WHERE l.ladder_platform = p.platform_id";
			$result	= $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				// Assign it to the template.
				$template->assign_block_vars('block_ladders', array(
					'LADDER_NAME' => $row['ladder_name'],
					'PLATFORM' => $row['platform_name'])
				);

				$sql_2		= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_parent = " . $row['ladder_id'];
				$result_2	= $db->sql_query($sql_2);

				// Loop through the ladder's sub-ladders.
				while ($row_2 = $db->sql_fetchrow($result_2))
				{
					// Assign them to the template.
					$template->assign_block_vars('block_ladders.block_subladders', array(
						'LADDER_ID' => $row_2['ladder_id'],
						'LADDER_NAME' => $row_2['ladder_name'])
					);
				}

				$db->sql_freeresult($result_2);
			}

			$db->sql_freeresult($result);
		}

		// Assign the other variables to the template.
		$template->assign_vars(array(
			'U_ACTION' => $u_action,
         )
		);
	}
}

?>
