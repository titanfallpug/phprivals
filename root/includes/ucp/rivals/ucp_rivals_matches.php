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
if(!defined('IN_PHPBB'))
{
	exit;
}

/**
 * Manage Matches
 * Called from ucp_rivals with mode == 'matches'
 */
function ucp_rivals_matches($id, $mode, $u_action)
{
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;
	
	$group	= new group();
	$ladder	= new ladder();
	
	// Check if the group is apart of a ladder yet.
	if (empty($user->data['group_session']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['LOGIN_TO_TEAM'], '<a href="' . $redirect_url . '">', '</a>'));
	}
	else if (empty($group->data['group_ladders']))
	{
		// They are not apart of a ladder. Deny them.
		$redirect_url = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=main");
		meta_refresh(4, $redirect_url);
		trigger_error(sprintf($user->lang['GROUP_NOTIN_LADDER'], '<a href="' . $redirect_url . '">', '</a>'));
	}

	// Get all the unreported matches only.
	$sql	= "SELECT * FROM " . MATCHES_TABLE . " WHERE (match_challengee = {$group->data['group_id']} OR match_challenger = {$group->data['group_id']}) AND match_reported = 0 AND match_confirmed = 0";
	$result	= $db->sql_query($sql);
    $i		= 0;
		
	while ($row = $db->sql_fetchrow($result))
	{
		$other	= ($row['match_challenger'] == $group->data['group_id']) ? $row['match_challengee'] : $row['match_challenger'];

		if ($row['match_finishtime'] == 0)
		{	
			// CARICO INFO LADDER	
			$ladder_data = $ladder->get_roots($row['match_ladder']);
				
			// STARDARD E CPC.
			$template->assign_block_vars('block_unreported', array(
				'U_OPPONENT' 	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=group_profile&amp;group_id=' . $other),
				'OPPONENT' 		=> $group->data('group_name', $other),
				'OPPONENT_ID' 	=> $other,
				'TIME' 			=> $user->format_date($row['match_posttime']),
				'MATCH_ID' 		=> $row['match_id'],
				'MATCHDESC' 	=> $row['match_details'],
				'MAP1' 			=> $row['mappa_mode1'],
				'MAP2' 			=> $row['mappa_mode2'],
				'MAP3' 			=> $row['mappa_mode3'],
				'DECERTO' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
				'MODE1' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode1'] : '',
				'MODE2' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode2'] : '',
				'MODE3' 		=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? $row['mode3'] : '',
				'CALCIO' 		=> ($ladder_data['SUBLADDER_STYLE'] == 3) ? true : false,
				'LADDER_ICON'	=> ($ladder_data['SUBLADDER_STYLE'] > 0) ? '<img src="' . $phpbb_root_path .'rivals/images/iconlad' . $ladder_data['SUBLADDER_STYLE'] . '.gif" alt="' . $user->lang['ICON_LADDER'] . '" title="' . $user->lang["{$ladder_data['SUBLADDER_STYLE']}ICON_LADDER"] . '" />' : '',
				'IMG_ADVSTATS'	=> ($ladder_data['SUBLADDER_ADVSTAT'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/statsicon.gif" alt="' . $user->lang['ICON_ADVSTATS'] . '" title="' . $user->lang['ICON_ADVSTATS'] . '" />' : '',
				'IMG_MVP'		=> ($ladder_data['SUBLADDER_MVP'] == 1) ? '<img src="' . $phpbb_root_path .'rivals/images/mvpicon.gif" alt="' . $user->lang['ICON_MVP'] . '" title="' . $user->lang['ICON_MVP'] . '" />' : '',
				'IMG_RTH'		=> ($ladder_data['SUBLADDER_RAKING'] == 2) ? '<img src="' . $phpbb_root_path .'rivals/images/rth.gif" alt="' . $user->lang['RTH_LADDER'] . '" title="' . $user->lang['RTH_LADDER'] . '" /> ' : '',
				'PLATFORM' 		=> $ladder_data['PLATFORM_NAME'],
				'LADDER' 		=> $ladder_data['LADDER_NAME'],
				'SUBLADDER' 	=> $ladder_data['SUBLADDER_NAME'],
				'SCORE_RELATED'	=> ($ladder_data['SUBLADDER_WINSYS'] == 0) ? true : false,
				'TUOGRUPPO'		=> $group->data['group_name'],
				'TUAID'			=> $group->data['group_id'],
				'REPORTLINK'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=matchmvp&amp;mid={$row['match_id']}"),
				'REPORTBUTTON'	=> getimg_button('reportmatch', 'REPORT_MATCH', 116, 25),
				'CLASSIFICATA'	=> ($row['match_unranked'] == 1) ? $user->lang['NONCLASSIFICATA'] : $user->lang['CLASSIFICATA'],
				'MATCH_CHAT'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=rivals&amp;mode=match_chat&amp;mid={$row['match_id']}&amp;lb=1"),
				'BG_COLOR'		=> ($i % 2) ? 'bg1' : 'bg2',
				'ROW_COLOR'		=> ($i % 2) ? 'row1' : 'row2'
			));
		}
		$i++;
		
		$template->assign_vars(array(
			'DECERTO'	=> ($ladder_data['SUBLADDER_STYLE'] == 1) ? true : false,
		));	
	}
	$db->sql_freeresult($result);

	// Assign the other variables to the template.
	$template->assign_vars(array(
		'U_ACTION'	=> $u_action
	));
}

?>