<?php
/**
*
* @package acp
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
 * phpRivalsMOD acp main file
 */

class acp_rivals
{
	var	$u_action;

	function main($id, $mode)
	{
		global	$db, $user, $config, $template;
		global	$phpbb_root_path, $phpbb_admin_path, $phpEx;

		// Include Rivals' classes.
		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_group.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_tournament.' . $phpEx);
		include($phpbb_root_path . 'rivals/classes/class_ladder.' . $phpEx);
		include($phpbb_root_path . 'rivals/functions.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
		

		// Setup the language.
		$user->add_lang('mods/lang_rivals');

		// Switch between the modes to manage Rivals.
		switch($mode)
		{
			case 'main' :
				$this->tpl_name		= 'rivals/acp_rivals_main';
				$this->page_title	= 'ACP_RIVALS';
				
				// VERSION CHECK
				$errstr		= '';
				$errno		= 0;
				$remotefile	= get_remote_file("nipponart.org", '', 'modversion.txt', $errstr, $errno);
				if ($remotefile !== false)
				{
					preg_match('/rivalsmod=(.*?)__/msu', $remotefile, $rvmname);					
					if ($config['rivalsmod_version'] != $rvmname[1])
					{
						$template->assign_vars(array(
							'TUAV'		=> $config['rivalsmod_version'],
							'LATESTV'	=> $rvmname[1],
							'SHOWALARM' => true,
							'CHKFAILED'	=> (!empty($rvmname[1])) ? false : true
						));
					}
				}

				// Get the number of groups.
				$result	= $db->sql_query("SELECT COUNT(group_id) AS the_groups FROM " . CLANS_TABLE);
				$groups	= (int) $db->sql_fetchfield('the_groups');
				$db->sql_freeresult($result);
				
				$result	= $db->sql_query("SELECT COUNT(group_id) AS active_groups FROM " . CLANS_TABLE . " WHERE clan_closed = 0 AND clan_rep_time > 1");
				$actvgr	= (int) $db->sql_fetchfield('active_groups');
				$db->sql_freeresult($result);
				
				$result	= $db->sql_query("SELECT COUNT(group_id) AS closed_groups FROM " . CLANS_TABLE . " WHERE clan_closed = 1");
				$closgr	= (int) $db->sql_fetchfield('closed_groups');
				$db->sql_freeresult($result);

				// Get the challenges.
				$result		= $db->sql_query("SELECT COUNT(challenge_id) AS the_challenges FROM " . CHALLENGES_TABLE);
				$challenges	= (int) $db->sql_fetchfield('the_challenges');
				$db->sql_freeresult($result);
				
				$result		= $db->sql_query("SELECT COUNT(1vs1_id) AS the_1vs1challenges FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_accepted = 0");
				$onechallg	= (int) $db->sql_fetchfield('the_1vs1challenges');
				$db->sql_freeresult($result);


				// Get the on-going matches.
				$result		= $db->sql_query("SELECT COUNT(match_id) AS the_ogmatches FROM " . MATCHES_TABLE . " WHERE match_finishtime = 0");
				$ogmatches	= (int) $db->sql_fetchfield('the_ogmatches');
				$db->sql_freeresult($result);
				
				$result		= $db->sql_query("SELECT COUNT(1vs1_id) AS the_1vs1ogmatches FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_accepted = 1 AND 1vs1_confirmer = 0");
				$Uogmatches	= (int) $db->sql_fetchfield('the_1vs1ogmatches');
				$db->sql_freeresult($result);
				

				// Get the finished matches.
				$result		= $db->sql_query("SELECT COUNT(match_id) AS the_matches FROM " . MATCHES_TABLE . " WHERE match_finishtime > 0");
				$matches	= (int) $db->sql_fetchfield('the_matches');
				$db->sql_freeresult($result);
				
				$result		= $db->sql_query("SELECT COUNT(1vs1_id) AS the_1vs1matches FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_accepted = 1 AND 1vs1_confirmer > 0");
				$Umatches	= (int) $db->sql_fetchfield('the_1vs1matches');
				$db->sql_freeresult($result);
				
				
				// Get the contested matches.
				$result			= $db->sql_query("SELECT COUNT(match_id) AS the_matches FROM " . MATCHES_TABLE . " WHERE match_finishtime = 0 AND match_status = 2");
				$contmatches	= (int) $db->sql_fetchfield('the_matches');
				$db->sql_freeresult($result);
				
				$result			= $db->sql_query("SELECT COUNT(1vs1_id) AS the_1vs1contestedmatches FROM " . ONEVSONE_MATCH_DATA . " WHERE 1vs1_accepted = 1 AND 1vs1_confirmer = 0 AND 1vs1_contestested > 0");
				$Ucontmatches	= (int) $db->sql_fetchfield('the_1vs1contestedmatches');
				$db->sql_freeresult($result);
				
				
				// Get on-going tournaments
				$result			= $db->sql_query("SELECT COUNT(tournament_id) AS ongoin_tournaments FROM " . TOURNAMENTS_TABLE . " WHERE tournament_status = 1");
				$ongtournaments	= (int) $db->sql_fetchfield('ongoin_tournaments');
				$db->sql_freeresult($result);

				
				// Assign the other variables to the template
				$template->assign_vars(array(
					'BYE_GROUP'			=> $config['rivals_byegroup'],
					'CHALLENGES'		=> (!$challenges) ? 0 : $challenges,
					'ONGOING_MATCHES'	=> (!$ogmatches) ? 0 : $ogmatches,
					'FINISHED_MATCHES'	=> (!$matches) ? 0 : $matches,
					'RIVAL_LOGO'		=> $phpbb_admin_path . 'style/rivals/phpRivalMod-logo.png',
					'RIVAL_DONATE'		=> $phpbb_root_path . 'rivals/images/paypal_donate.gif',
					'GROUPS'			=> (!$groups) ? 0 : $groups,
					'ACTIVE_GROUPS'		=> (!$actvgr) ? 0 : $actvgr,
					'CLOSED_GROUPS'		=> (!$closgr) ? 0 : $closgr,
					'CHALLENGES_USER'	=> (!$onechallg) ? 0 : $onechallg,
					'ONGOING_MATCHES_U' => (!$Uogmatches) ? 0 : $Uogmatches,
					'USER_MATCHES_FINS'	=> (!$Umatches) ? 0 : $Umatches,
					'CONTESTED_MATCHES'	=> (!$contmatches) ? 0 : '<span class="error">' . $contmatches . '</span>',
					'USER_CONT_MATCHES'	=> (!$Ucontmatches) ? 0 : '<span class="error">' . $Ucontmatches . '</span>',
					'ONGOING_TOURNAMNT'	=> (!$ongtournaments) ? 0 : $ongtournaments
				));
				
			break;
			case 'add_ladder' :
				$this->tpl_name		= 'rivals/acp_rivals_add_ladder';
				$this->page_title	= 'ACP_RIVALS_ADD_LADDER';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_add_ladder.' . $phpEx);
				acp_rivals_add_ladder($id, $mode, $this->u_action);
			break;
			case 'add_platform' :
				$this->tpl_name		= 'rivals/acp_rivals_add_platform';
				$this->page_title	= 'ACP_RIVALS_ADD_PLATFORM';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_add_platform.' . $phpEx);
				acp_rivals_add_platform($id, $mode, $this->u_action);
			break;
			case 'add_tournament' :
				$this->tpl_name		= 'rivals/acp_rivals_add_tournament';
				$this->page_title	= 'ACP_RIVALS_ADD_TOURNAMENT';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_add_tournament.' . $phpEx);
				acp_rivals_add_tournament($id, $mode, $this->u_action);
			break;
			case 'configure' :
				$this->tpl_name		= 'rivals/acp_rivals_configure';
				$this->page_title	= 'ACP_RIVALS_CONFIGURE';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_configure.' . $phpEx);
				acp_rivals_configure($id, $mode, $this->u_action);
			break;
			case 'edit_brackets' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_brackets';
				$this->page_title	= 'ACP_RIVALS_EDIT_BRACKETS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_brackets.' . $phpEx);
				acp_rivals_edit_brackets($id, $mode, $this->u_action);
			break;
			case 'edit_groups' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_groups';
				$this->page_title	= 'ACP_RIVALS_EDIT_GROUPS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_groups.' . $phpEx);
				acp_rivals_edit_groups($id, $mode, $this->u_action);
			break;
			case 'edit_mvp' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_mvp';
				$this->page_title	= 'ACP_RIVALS_EDIT_MVP';

				// Include the file for this mode.
			    include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_mvp.' . $phpEx);
				acp_rivals_edit_mvp($id, $mode, $this->u_action);
			break;
			case 'edit_mvp_list' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_mvp_list';
				$this->page_title	= 'ACP_RIVALS_EDIT_MVP_LIST';
				
				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_mvp_list.' . $phpEx);
				acp_rivals_edit_mvp_list($id, $mode, $this->u_action);
			break;
			case 'add_mvp_list' :
				$this->tpl_name		= 'rivals/acp_rivals_add_mvp_list';
				$this->page_title	= 'ACP_RIVALS_ADD_MVP_LIST';
				
				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_add_mvp_list.' . $phpEx);
				acp_rivals_add_mvp_list($id, $mode, $this->u_action);
			break;
			case 'edit_rules' :
			$dettaglio	= request_var('rules', '');
				if (!empty($dettaglio))
				{
					$this->tpl_name		= 'rivals/acp_rivals_edit_rules_detail';
				}
				else
				{
					$this->tpl_name		= 'rivals/acp_rivals_edit_rules';
				}	
				$this->page_title	= 'ACP_RIVALS_EDIT_RULES';
				
				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_rules.' . $phpEx);
				acp_rivals_edit_rules($id, $mode, $this->u_action);
			break;
			case 'edit_match' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_match';
				$this->page_title	= 'ACP_RIVALS_EDIT_MATCH';
				
				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_match.' . $phpEx);
				acp_rivals_edit_match($id, $mode, $this->u_action);
			break;
			case 'edit_match_user' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_match_user';
				$this->page_title	= 'ACP_RIVALS_EDIT_MATCH_USER';
				
				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_match_user.' . $phpEx);
				acp_rivals_edit_match_user($id, $mode, $this->u_action);
			break;
			case 'edit_match_tourn' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_match_tourn';
				$this->page_title	= 'ACP_RIVALS_EDIT_MATCH_TOURNAMENT';
				
				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_match_tourn.' . $phpEx);
				acp_rivals_edit_match_tourn($id, $mode, $this->u_action);
			break;
			case 'edit_ladders' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_ladders';
				$this->page_title	= 'ACP_RIVALS_EDIT_LADDERS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_ladders.' . $phpEx);
				acp_rivals_edit_ladders($id, $mode, $this->u_action);
			break;
			case 'edit_ladder' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_ladder';
				$this->page_title	= 'ACP_RIVALS_EDIT_LADDER';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_ladder.' . $phpEx);
				acp_rivals_edit_ladder($id, $mode, $this->u_action);
			break;
			case 'edit_subladder' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_subladder';
				$this->page_title	= 'ACP_RIVALS_EDIT_SUBLADDER';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_subladder.' . $phpEx);
				acp_rivals_edit_subladder($id, $mode, $this->u_action);
			break;
			case 'edit_platforms' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_platforms';
				$this->page_title	= 'ACP_RIVALS_EDIT_PLATFORMS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_platforms.' . $phpEx);
				acp_rivals_edit_platforms($id, $mode, $this->u_action);
			break;
			case 'edit_tournaments' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_tournaments';
				$this->page_title	= 'ACP_RIVALS_EDIT_TOURNAMENTS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_tournaments.' . $phpEx);
				acp_rivals_edit_tournaments($id, $mode, $this->u_action);
			break;
			/*
			case 'report_match' :
				$this->tpl_name		= 'rivals/acp_rivals_report_match';
				$this->page_title	= 'ACP_RIVALS_REPORT_MATCH';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_report_match.' . $phpEx);
				acp_rivals_report_match($id, $mode, $this->u_action);
			break;
			*/
			case 'seed_tournament' :
				$this->tpl_name		= 'rivals/acp_rivals_seed_tournament';
				$this->page_title	= 'ACP_RIVALS_SEED_TOURNAMENT';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_seed_tournament.' . $phpEx);
				acp_rivals_seed_tournament($id, $mode, $this->u_action);
			break;
			case 'edit_tournament' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_tournament';
				$this->page_title	= 'ACP_RIVALS_EDIT_TOURNAMENT';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_tournament.' . $phpEx);
				acp_rivals_edit_tournament($id, $mode, $this->u_action);
			break;
			case 'manage_seasons' :
				$this->tpl_name		= 'rivals/acp_rivals_manage_seasons';
				$this->page_title	= 'ACP_RIVALS_MANAGE_SEASONS';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_manage_seasons.' . $phpEx);
				acp_rivals_manage_seasons($id, $mode, $this->u_action);
			break;
			case 'edit_random' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_random';
				$this->page_title	= 'ACP_RIVALS_EDIT_RANDOM';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_random.' . $phpEx);
				acp_rivals_edit_random($id, $mode, $this->u_action);
			break;
			case 'add_season' :
				$this->tpl_name		= 'rivals/acp_rivals_add_season';
				$this->page_title	= 'ACP_RIVALS_ADD_SEASON';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_add_season.' . $phpEx);
				acp_rivals_add_season($id, $mode, $this->u_action);
			break;
			case 'edit_season' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_season';
				$this->page_title	= 'ACP_RIVALS_EDIT_SEASON';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_season.' . $phpEx);
				acp_rivals_edit_season($id, $mode, $this->u_action);
			break;
			case 'edit_decerto' :
				$this->tpl_name		= 'rivals/acp_rivals_edit_decerto';
				$this->page_title	= 'ACP_RIVALS_EDIT_DECERTO';

				// Include the file for this mode.
				include($phpbb_root_path . 'includes/acp/rivals/acp_rivals_edit_decerto.' . $phpEx);
				acp_rivals_edit_decerto($id, $mode, $this->u_action);
			break;
		}
	}
}

?>