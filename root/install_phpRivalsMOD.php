<?php
/**
*
* @package umil
* @version $Id otopicindex_install.php
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}
/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
$version_config_name = 'rivalsmod_version';

$language_file = 'mods/rivals_umil';

// The name of the mod to be displayed during installation.
$mod_name = 'ACP_PHPRIVALS_MOD';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	// Version 2.0.1
	'2.0.1'	=> array(
	//Add table
		'table_column_add' => array(
			array("phpbb_rivals_decerto_cat", 'active', array('TINT:1', 0)),
		),
		
		'table_add' => array(
			array('phpbb_rivals_rosters', array(
				'COLUMNS'	=> array(
					'roster_id'			=> array('UINT', NULL, 'auto_increment'),
					'clan_id'			=> array('INT:11', 0),
					'roster_name'		=> array('VCHAR:255', ''),
					'roster_members'	=> array('TEXT_UNI', ''),
					'roster_leader'		=> array('INT:11', 0),
				),
				'PRIMARY_KEY'	=> 'roster_id',
				),
			),
		),
		
		//Clear cache
		'cache_purge' => array(''),

	/*//Add module
		'module_add' => array(
			// Now we will add the modules manually
            array('ucp', 'UCP_CAT_RIVALS', array(
					'module_basename'   => 'rivals',
					'module_langname'   => 'UCP_RIVALS_SET_ROSTER',
					'module_mode'       => 'set_roster',
					'module_auth'       => '',
				),
			),
		),	*/
	), // end of array 2.0.1
	
	// Version 2.0.0
	'2.0.0'	=> array(
	// Add config values
		'config_add' => array(
			array('rivals_byegroup', 0),
			array('rivals_frost_cost', 10),
			array('rivals_inactiv_penality', 12),
			array('rivals_kickout_day', 3),
			array('rivals_modstraight', 0),
			array('rivals_ticketreceiver', 2),			
			array('rivals_maxreporthours', 24),
			array('rivals_minpost', 0),
			array('rivals_bannedgroup', 0),
		),
		
	// Add permission data
		'permission_add' => array(
			array('a_rivals', true),
			array('m_rivals', true),
		),
		
	// Set permission to admin
		'permission_set' => array(
			// Global Group permissions
			//array('ADMINISTRATORS', 'a_rivals', 'group'),/* Removed cose with UMIL 1.0.3 this affect the mods too. */
			//array('ADMINISTRATORS', 'm_rivals', 'group'),/* Removed cose with UMIL 1.0.3 this affect the mods too. */
			// Global Role permissions for admins
			array('ROLE_ADMIN_FULL', 'a_rivals'),
			array('ROLE_ADMIN_FULL', 'm_rivals'),
		),
		
	//Add table
		'table_column_add' => array(
			array("phpbb_users", 'group_session', array('INT:11', 0)),
			array("phpbb_users", 'user_mvp', array('INT:11', 0)),
			array("phpbb_users", 'gamer_name', array('VCHAR_UNI:255', '')),
			array("phpbb_users", 'user_tournaments', array('TEXT_UNI', '')),
			array("phpbb_users", 'user_ladder_level', array('TINT:1', 0)),
			array("phpbb_users", 'user_ladder_value', array('VCHAR_UNI:255', '0')),
			array("phpbb_users", 'user_exp', array('VCHAR_UNI:20', '0')), /* i do not use a integer for that there are more: it was cose those numbers can be so bit or with . and , */
			array("phpbb_users", 'user_round_wins', array('INT:11', 0)),
			array("phpbb_users", 'user_round_losses', array('INT:11', 0)),
			array("phpbb_users", 'user_chicken', array('INT:11', 0)),
			array("phpbb_users", 'user_powns', array('INT:11', 0)),
			array("phpbb_users", 'rep_value', array('INT:11', 5)),
			array("phpbb_users", 'rep_time', array('INT:11', 1)),
		),	

	//Add module
		'module_add' => array(
			array('acp', false, 'ACP_CAT_RIVALS'),
			array('acp', 'ACP_CAT_RIVALS', 'ACP_RIVALS'),
			array('acp', 'ACP_RIVALS',
				array('module_basename'	=> 'rivals'),
			),
			
			array('ucp', false, 'UCP_CAT_RIVALS'),
			array('ucp', 'UCP_CAT_RIVALS',
				array('module_basename'	=> 'rivals'),
			),
			
			array('mcp', false, 'MCP_RIVALS'),
			array('mcp', 'MCP_RIVALS',
				array('module_basename'	=> 'rivals'),
			),
		),
		
	// Add a table
		'table_add' => array(
			array('phpbb_rivals_1vs1_machdata', array(
				'COLUMNS'	=> array(
					'1vs1_id'				=> array('UINT', NULL, 'auto_increment'),
					'1vs1_ladder'			=> array('INT:11', 0),
					'1vs1_challanger'		=> array('INT:11', 0),
					'1vs1_challangee'		=> array('INT:11', 0),
					'1vs1_challanger_ip'	=> array('VCHAR:40', ''),
					'1vs1_challangee_ip'	=> array('VCHAR:40', ''),
					'1vs1_unranked'			=> array('TINT:1', 0),
					'1vs1_challanger_score'	=> array('VCHAR:20', '0'),
					'1vs1_challangee_score'	=> array('VCHAR:20', '0'),
					'1vs1_winner'			=> array('INT:11', 0),
					'1vs1_mappa1'			=> array('VCHAR_UNI:255', '-'),
					'1vs1_mappa2'			=> array('VCHAR_UNI:255', '-'),
					'1vs1_mappa3'			=> array('VCHAR_UNI:255', '-'),
					'mode1'					=> array('VCHAR_UNI:255', ''),
					'mode2'					=> array('VCHAR_UNI:255', ''),
					'mode3'					=> array('VCHAR_UNI:255', ''),
					'mode1_score_er'		=> array('INT:11', 0),
					'mode2_score_er'		=> array('INT:11', 0),
					'mode3_score_er'		=> array('INT:11', 0),
					'mode1_score_ee'		=> array('INT:11', 0),
					'mode2_score_ee'		=> array('INT:11', 0),
					'mode3_score_ee'		=> array('INT:11', 0),
					'1vs1_challanger_team'	=> array('VCHAR_UNI:255', ''),
					'1vs1_challangee_team'	=> array('VCHAR_UNI:255', ''),
					'1vs1_details'			=> array('MTEXT_UNI', ''),
					'1vs1_accepted'			=> array('TINT:1', 0),
					'1vs1_reporter'			=> array('INT:11', 0),
					'1vs1_confirmer'		=> array('INT:11', 0),
					'1vs1_contestested'		=> array('TINT:1', 0),
					'start_time'			=> array('TIMESTAMP', 0),
					'rep_time'				=> array('TIMESTAMP', 0),
					'end_time'				=> array('TIMESTAMP', 0),
					'er_feedback'			=> array('TINT:1', 5),
					'ee_feedback'			=> array('TINT:1', 5),
				),
				'PRIMARY_KEY'	=> '1vs1_id',
				),
			),
			
			array('phpbb_rivals_1vs1_userdata', array(
				'COLUMNS'	=> array(
					'user_id'			=> array('UINT', 0),
					'1vs1_ladder'		=> array('INT:11', 0),
					'user_wins'			=> array('INT:11', 0),
					'user_losses'		=> array('INT:11', 0),
					'user_pari'			=> array('INT:11', 0),
					'user_score'		=> array('INT:11', 0),
					'user_lastscore'	=> array('INT:11', 0),
					'user_streak'		=> array('MTEXT_UNI', ''),
					'user_current_rank'	=> array('INT:11', 0),
					'user_last_rank'	=> array('INT:11', 0),
					'user_worst_rank'	=> array('INT:11', 0),
					'user_best_rank'	=> array('INT:11', 0),
					'user_goals_fatti'	=> array('INT:11', 0),
					'user_goals_subiti'	=> array('INT:11', 0),
					'user_ratio'		=> array('VCHAR:20', '0'),
					'powns_award'		=> array('INT:11', 0),
					'user_frosted'		=> array('TINT:1', 0),
					'frosted_time'		=> array('INT:11', 0),
				),
				'KEYS'	=> array(
					'user_id'		=> array('INDEX', 'user_id'),
					'1vs1_ladder'	=> array('INDEX', '1vs1_ladder'),
				),
			)),
			
			array('phpbb_rivals_challange_rth', array(
				'COLUMNS'	=> array(
					'group_id'	=> array('INT:11', 0),
					'ladder_id'	=> array('INT:11', 0),
					'oneone'	=> array('TINT:1', 0),
				),
				'KEYS'	=> array(
					'group_id'	=> array('INDEX', 'group_id'),
				),
			)),
			
			array('phpbb_rivals_challenges', array(
				'COLUMNS'	=> array(
					'challenge_id'			=> array('UINT', NULL, 'auto_increment'),
					'challenge_ladder'		=> array('INT:11', 0),
					'challenger'			=> array('INT:11', 0),
					'challenger_ip'			=> array('VCHAR:40', ''),
					'challengee'			=> array('INT:11', 0),
					'challenge_unranked'	=> array('TINT:1', 0),
					'challenge_posttime'	=> array('TIMESTAMP', 0),
					'challenge_details'		=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> 'challenge_id',
				),
			),
			
			array('phpbb_rivals_clans', array(
				'COLUMNS'	=> array(
					'group_id'				=> array('UINT', NULL, 'auto_increment'),
					'group_name'			=> array('VCHAR_UNI:255', ''),
					'group_desc'			=> array('MTEXT_UNI', ''),
					'clan_logo_name'		=> array('VCHAR_UNI:255', 'nologo.jpg'),
					'clan_logo_ext'			=> array('VCHAR:5', '.jpg'),
					'clan_logo_width'		=> array('INT:11', 100),
					'clan_logo_height'		=> array('INT:11', 100),
					'group_tournaments'		=> array('TEXT_UNI', ''),
					'group_sito'			=> array('VCHAR_UNI:255', '#'),
					'clan_alltime_wins'		=> array('INT:11', 0),
					'clan_alltime_losses'	=> array('INT:11', 0),
					'clan_alltime_pareggi'	=> array('INT:11', 0),
					'clan_level'			=> array('TINT:1', 0),
					'clan_creation_date'	=> array('TIMESTAMP', 0),
					'clan_target_10streak'	=> array('TINT:1', 0),
					'clan_target_ladderwin'	=> array('TINT:1', 0),
					'clan_favouritemap'		=> array('MTEXT_UNI', ''),
					'clan_favouriteteam'	=> array('MTEXT_UNI', ''),
					'guid'					=> array('VCHAR:8', ''),
					'uac'					=> array('INT:6', 0),
					'clan_closed'			=> array('TINT:1', 0),
					'rth_chicken'			=> array('INT:11', 0),
					'rth_powner'			=> array('INT:11', 0),
					'clan_rep_value'		=> array('INT:11', 5),
					'clan_rep_time'			=> array('INT:11', 1),
				),
				'PRIMARY_KEY'	=> 'group_id',
				),
			),
			
			array('phpbb_rivals_clansmsg', array(
				'COLUMNS'	=> array(
					'smsg_id'			=> array('UINT', NULL, 'auto_increment'),
					'group_id'			=> array('UINT', 0),
					'matchcomm_message'	=> array('MTEXT_UNI', ''),
					'matchcomm_time'	=> array('TIMESTAMP', 0),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_options'	=> array('INT:4', 0),
				),
				'PRIMARY_KEY'	=> 'smsg_id',
				'KEYS'			=> array(
					'group_id'	=> array('INDEX', 'group_id'),
				),
			)),
			
			array('phpbb_rivals_decerto_cat', array(
				'COLUMNS'	=> array(
					'id_decerto'		=> array('UINT', NULL, 'auto_increment'),
					'nome_gioco'		=> array('VCHAR_UNI:255', ''),
					'nome_corto'		=> array('VCHAR_UNI:20', ''),
					'decerto_interid'	=> array('TINT:1', 1),
					'decerto_mode'		=> array('VCHAR_UNI:255', ''),
					'cpc'				=> array('TINT:1', 1),
				),
				'PRIMARY_KEY'	=> 'id_decerto',
				),
			),
			
			array('phpbb_rivals_decerto_map', array(
				'COLUMNS'	=> array(
					'id_mappa_decerto'	=> array('UINT', NULL, 'auto_increment'),
					'nome_corto'		=> array('VCHAR_UNI:20', ''),
					'decerto_interid'	=> array('TINT:1', 1),
					'decerto_mappa'		=> array('VCHAR_UNI:255', ''),
					'decerto_cpc'		=> array('TINT:1', 1),
				),
				'PRIMARY_KEY'	=> 'id_mappa_decerto',
				),
			),
			
			array('phpbb_rivals_groupdata', array(
				'COLUMNS'	=> array(
					'group_id'				=> array('UINT', 0),
					'group_wins'			=> array('INT:11', 0),
					'group_losses'			=> array('INT:11', 0),
					'group_score'			=> array('INT:11', 0),
					'group_streak'			=> array('MTEXT_UNI', ''),
					'group_current_rank'	=> array('INT:11', 0),
					'group_ladder'			=> array('INT:11', 0),
					'group_last_rank'		=> array('INT:11', 0),
					'group_best_rank'		=> array('INT:11', 0),
					'group_lastscore'		=> array('INT:11', 0),
					'group_worst_rank'		=> array('INT:11', 0),
					'group_pari'			=> array('INT:11', 0),
					'group_goals_fatti'		=> array('INT:11', 0),
					'group_goals_subiti'	=> array('INT:11', 0),
					'group_ratio'			=> array('VCHAR:20', '0'),
					'powns_award'			=> array('INT:11', 0),
					'group_frosted'			=> array('TINT:1', 0),
					'group_frosted_time'	=> array('INT:11', 0),
				),
				'KEYS'	=> array(
					'group_id'		=> array('INDEX', 'group_id'),
					'group_ladder'	=> array('INDEX', 'group_ladder'),
				),
			)),
			
			array('phpbb_rivals_ladders', array(
				'COLUMNS'	=> array(
					'ladder_id'			=> array('UINT', NULL, 'auto_increment'),
					'ladder_name'		=> array('VCHAR_UNI:255', ''),
					'ladder_desc'		=> array('MTEXT_UNI', ''),
					'ladder_rules'		=> array('MTEXT_UNI', ''),
					'ladder_order'		=> array('INT:11', 0),
					'ladder_rm'			=> array('INT:11', 0),
					'subladder_order'	=> array('INT:11', 0),
					'ladder_locked'		=> array('INT:8', 0),
					'ladder_cl'			=> array('INT:8', 1),
					'ladder_ranking'	=> array('INT:11', 0),
					'ladder_parent'		=> array('INT:11', 0),
					'ladder_platform'	=> array('INT:11', 0),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_options'	=> array('INT:4', 0),
					'ladder_style'		=> array('TINT:1', 0),
					'shortname'			=> array('VCHAR_UNI:20', ''),
					'ladder_mvp'		=> array('TINT:1', 0),
					'ladder_logo'		=> array('VCHAR_UNI:255', 'nologo.jpg'),
					'ladder_logo_w'		=> array('INT:11', 800),
					'ladder_logo_h'		=> array('INT:11', 150),
					'ladder_advstat'	=> array('TINT:1', 0),
					'ladder_win_system'	=> array('TINT:1', 0),
					'ladder_mod'		=> array('INT:11', 0),
					'ladder_limit'		=> array('TINT:1', 0),
					'ladder_oneone'		=> array('TINT:1', 0),
				),
				'PRIMARY_KEY'	=> 'ladder_id',
				),
			),
			
			array('phpbb_rivals_ladders_userstats', array(
				'COLUMNS'	=> array(
					'ladder_id'		=> array('UINT', 0),
					'user_id'		=> array('UINT', 0),
					'ranking'		=> array('VCHAR:60', '0'),
					'kills'			=> array('VCHAR:60', '0'),
					'deads'			=> array('VCHAR:60', '0'),
					'assists'		=> array('VCHAR:60', '0'),
					'goalf'			=> array('VCHAR:60', '0'),
					'goala'			=> array('VCHAR:60', '0'),
					'mvps'			=> array('VCHAR:60', '0'),
					'match_played'	=> array('INT:11', 0),
				),
				'KEYS'	=> array(
					'ladder_id'	=> array('INDEX', 'ladder_id'),
					'user_id'	=> array('INDEX', 'user_id'),
				),
			)),
			
			array('phpbb_rivals_ladder_rules', array(
				'COLUMNS'	=> array(
					'rules_id'				=> array('UINT', NULL, 'auto_increment'),
					'rules_ladder'			=> array('INT:11', 0),
					'rules_platform'		=> array('INT:11', 0),
					'requisiti_iscrizione'	=> array('MTEXT_UNI', ''),
					'regole_generali'		=> array('MTEXT_UNI', ''),
					'configurazione'		=> array('MTEXT_UNI', ''),
					'divieti'				=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> 'rules_id',
				),
			),
			
			array('phpbb_rivals_matchchat', array(
				'COLUMNS'	=> array(
					'id_chat'			=> array('UINT', NULL, 'auto_increment'),
					'id_match'			=> array('INT:11', 0),
					'id_writer'			=> array('INT:11', 0),
					'id_clan'			=> array('INT:11', 0),
					'chat_flag'			=> array('TINT:1', 0),
					'tposition'			=> array('INT:11', 0),
					'tround'			=> array('INT:11', 0),
					'chat_time'			=> array('TIMESTAMP', 0),
					'chat_text'			=> array('MTEXT_UNI', ''),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_options'	=> array('INT:4', 0),
				),
				'PRIMARY_KEY'	=> 'id_chat',
				'KEYS'			=> array(
					'id_match'	=> array('INDEX', 'id_match'),
				),
			)),
			
			array('phpbb_rivals_matches', array(
				'COLUMNS'	=> array(
					'match_id'						=> array('UINT', NULL, 'auto_increment'),
					'match_ladder'					=> array('INT:11', 0),
					'match_challenger'				=> array('INT:11', 0),
					'match_challengee'				=> array('INT:11', 0),
					'match_challenger_ip'			=> array('VCHAR:40', ''),
					'match_challengee_ip'			=> array('VCHAR:40', ''),
					'match_status'					=> array('TINT:1', 0),
					'match_unranked'				=> array('TINT:1', 0),
					'match_finishtime'				=> array('TIMESTAMP', 0),
					'match_reptime'					=> array('TIMESTAMP', 0),
					'match_posttime'				=> array('TIMESTAMP', 0),
					'match_details'					=> array('MTEXT_UNI', ''),
					'match_winner'					=> array('INT:11', 0),
					'match_loser'					=> array('INT:11', 0),
					'match_challanger_score'		=> array('INT:11', 0),
					'match_challangee_score'		=> array('INT:11', 0),
					'match_challanger_score_mode1'	=> array('INT:11', 0),
					'match_challangee_score_mode1'	=> array('INT:11', 0),
					'match_challanger_score_mode2'	=> array('INT:11', 0),
					'match_challangee_score_mode2'	=> array('INT:11', 0),
					'match_challanger_score_mode3'	=> array('INT:11', 0),
					'match_challangee_score_mode3'	=> array('INT:11', 0),
					'mappa_mode1'					=> array('VCHAR_UNI:255', 'na'),
					'mappa_mode2'					=> array('VCHAR_UNI:255', 'na'),
					'mappa_mode3'					=> array('VCHAR_UNI:255', 'na'),
					'mode1'							=> array('VCHAR_UNI:255', '-'),
					'mode2'							=> array('VCHAR_UNI:255', '-'),
					'mode3'							=> array('VCHAR_UNI:255', '-'),
					'challenger_team'				=> array('VCHAR_UNI:255', ''),
					'challengee_team'				=> array('VCHAR_UNI:255', ''),
					'match_reported'				=> array('INT:11', 0),
					'match_confirmed'				=> array('INT:11', 0),
					'mvp1'							=> array('INT:11', 0),
					'mvp2'							=> array('INT:11', 0),
					'mvp3'							=> array('INT:11', 0),
					'challanger_rep'				=> array('TINT:1', 5),
					'challangee_rep'				=> array('TINT:1', 5),
				),
				'PRIMARY_KEY'	=> 'match_id',
				),
			),
			
			array('phpbb_rivals_matchfinder', array(
				'COLUMNS'	=> array(
					'match_id'			=> array('UINT', NULL, 'auto_increment'),
					'match_groupid'		=> array('INT:11', 0),
					'match_ladder'		=> array('INT:11', 0),
					'match_time'		=> array('INT:11', 0),
					'match_unranked'	=> array('TINT:1', 0),
					'match_initaltime'	=> array('TIMESTAMP', 0),
				),
				'PRIMARY_KEY'	=> 'match_id',
				),
			),
			
			array('phpbb_rivals_match_stats_temp', array(
				'COLUMNS'	=> array(
					'id_match'	=> array('INT:11', 0),
					'id_ladder'	=> array('INT:11', 0),
					'user_id'	=> array('UINT', 0),
					'kills'		=> array('INT:11', 0),
					'deads'		=> array('INT:11', 0),
					'assists'	=> array('INT:11', 0),
					'goal_f'	=> array('INT:11', 0),
					'goal_a'	=> array('INT:11', 0),
				),
				'KEYS'	=> array(
					'id_match'	=> array('INDEX', 'id_match'),
					'user_id'	=> array('INDEX', 'user_id'),
				),
			)),
			
			array('phpbb_rivals_mvp', array(
				'COLUMNS'	=> array(
					'mvp_id'			=> array('UINT', NULL, 'auto_increment'),
					'nome_mvp'			=> array('VCHAR_UNI:255', ''),
					'descrizione_mvp'	=> array('TEXT_UNI', ''),
					'platform_mvp'		=> array('INT:11', 0),
					'ladder_mvp'		=> array('INT:11', 0),
				),
				'PRIMARY_KEY'	=> 'mvp_id',
				),
			),
			
			array('phpbb_rivals_platforms', array(
				'COLUMNS'	=> array(
					'platform_id'			=> array('UINT', NULL, 'auto_increment'),
					'platform_name'			=> array('VCHAR_UNI:255', ''),
					'platform_logo'			=> array('VCHAR_UNI:255', 'nologo.jpg'),
					'platform_logo_w'		=> array('INT:5', 400),
					'platform_logo_h'		=> array('INT:5', 100),
				),
				'PRIMARY_KEY'	=> 'platform_id',
				),
			),
			
			array('phpbb_rivals_random', array(
				'COLUMNS'	=> array(
					'random_id'		=> array('UINT', NULL, 'auto_increment'),
					'gioco'			=> array('VCHAR_UNI:255', ''),
					'short_name'	=> array('VCHAR:20', ''),
					'tempo'			=> array('TIMESTAMP', 0),
					'img_mappa'		=> array('VCHAR_UNI:255', ''),
				),
				'PRIMARY_KEY'	=> 'random_id',
				),
			),
			
			array('phpbb_rivals_random_imgs', array(
				'COLUMNS'	=> array(
					'randimg_id'			=> array('UINT', NULL, 'auto_increment'),
					'randimg_short_name'	=> array('VCHAR:20', ''),
					'randimg_img'			=> array('VCHAR_UNI:255', ''),
				),
				'PRIMARY_KEY'	=> 'randimg_id',
				),
			),
			
			array('phpbb_rivals_seasondata', array(
				'COLUMNS'	=> array(
					'group_id'				=> array('UINT', 0),
					'season_id'				=> array('INT:11', 0),
					'group_wins'			=> array('INT:11', 0),
					'group_losses'			=> array('INT:11', 0),
					'group_score'			=> array('INT:11', 0),
					'group_streak'			=> array('MTEXT_UNI', ''),
					'group_current_rank'	=> array('INT:11', 0),
					'group_last_rank'		=> array('INT:11', 0),
					'group_best_rank'		=> array('INT:11', 0),
					'group_ladder'			=> array('INT:11', 0),
					'group_worst_rank'		=> array('INT:11', 0),
					'group_pari'			=> array('INT:11', 0),
					'group_goals_fatti'		=> array('INT:11', 0),
					'group_goals_subiti'	=> array('INT:11', 0),
					'group_ratio'			=> array('VCHAR:20', '0'),
					'powns_award'			=> array('INT:11', 0),
					'group_frosted'			=> array('TINT:1', 0),
				),
				'KEYS'	=> array(
					'group_id'	=> array('INDEX', 'group_id'),
					'season_id'	=> array('INDEX', 'season_id'),
				),
			)),
			
			array('phpbb_rivals_seasons', array(
				'COLUMNS'	=> array(
					'season_id'		=> array('UINT', NULL, 'auto_increment'),
					'season_name'	=> array('VCHAR_UNI:255', ''),
					'season_ladder'	=> array('INT:11', 0),
					'season_status'	=> array('TINT:1', 1),
				),
				'PRIMARY_KEY'	=> 'season_id',
				),
			),
			
			array('phpbb_rivals_tdecerto', array(
				'COLUMNS'	=> array(
					'id_torneo'	=> array('INT:11', 0),
					'round'		=> array('INT:6', 0),
					'modi'		=> array('VCHAR_UNI:255', ''),
					'map1'		=> array('VCHAR_UNI:255', ''),
					'map2'		=> array('VCHAR_UNI:255', ''),
					'map3'		=> array('VCHAR_UNI:255', ''),
				),
				'KEYS'	=> array(
					'id_torneo'	=> array('INDEX', 'id_torneo'),
				),
			)),
			
			array('phpbb_rivals_tgroups', array(
				'COLUMNS'	=> array(
					'group_tournament'		=> array('INT:11', 0),
					'group_id'				=> array('INT:11', 0),
					'roster_id'				=> array('INT:11', 0),
					'group_bracket'			=> array('INT:8', 0),
					'group_position'		=> array('INT:8', 0),
					'group_loser'			=> array('INT:11', 0),
					'group_position_temp'	=> array('INT:8', 0),
					'group_reported'		=> array('INT:11', 0),
					'loser_confirm'			=> array('TINT:1', 0),
					'group_uid'				=> array('VCHAR:100', '0'),
					'group_time'			=> array('TIMESTAMP', 0),
					'reputation'			=> array('TINT:1', 5),
				),
				'KEYS'	=> array(
					'group_tournament'	=> array('INDEX', 'group_tournament'),
					'group_id'			=> array('INDEX', 'group_id'),
				),
			)),
			
			array('phpbb_rivals_tmatches', array(
				'COLUMNS'	=> array(
					'group_uid'		=> array('VCHAR:100', '0'),
					'id_torneo'		=> array('INT:11', 0),
					'group1'		=> array('INT:11', 0),
					'group2'		=> array('INT:11', 0),
					'punti1'		=> array('INT:11', 0),
					'punti2'		=> array('INT:11', 0),
					'vincitore'		=> array('INT:11', 0),
					'mvp1'			=> array('INT:11', 0),
					'mvp2'			=> array('INT:11', 0),
					'mvp3'			=> array('INT:11', 0),
					'conferma1'		=> array('TINT:1', 0),
					'conferma2'		=> array('TINT:1', 0),
					'first_home'	=> array('INT:11', 0),
					'home_punti1'	=> array('INT:11', 0),
					'home_punti2'	=> array('INT:11', 0),
					'match_problem'	=> array('TINT:1', 0),
				),
				'KEYS'	=> array(
					'group_uid'	=> array('INDEX', 'group_uid'),
					'id_torneo'	=> array('INDEX', 'id_torneo'),
				),
			)),
			
			array('phpbb_rivals_tournaments', array(
				'COLUMNS'	=> array(
					'tournament_id'				=> array('UINT', NULL, 'auto_increment'),
					'tournament_name'			=> array('VCHAR_UNI:255', ''),
					'tournament_logo'			=> array('VCHAR_UNI:255', 'nologo.jpg'),
					'tournament_brackets'		=> array('INT:11', 0),
					'tournament_status'			=> array('TINT:1', 0),
					'tournament_type'			=> array('TINT:1', 0),
					'tournament_tipo'			=> array('TINT:1', 0),
					'tournament_decerto'		=> array('TINT:1', 0),
					'shorty'					=> array('VCHAR_UNI:50', ''),
					'tournament_finishedgroups'	=> array('TEXT_UNI', ''),
					'tournament_info'			=> array('TEXT_UNI', ''),
					'tournament_invite'			=> array('TEXT_UNI', ''),
					'tournament_time'			=> array('TIMESTAMP', 0),
					'tournament_startdate'		=> array('TIMESTAMP', 0),
					'tournament_licence'		=> array('TINT:1', 0),
					'tournament_advstats'		=> array('TINT:1', 0),
					'tournament_userbased'		=> array('TINT:1', 0),
					'tournament_stricted'		=> array('TINT:1', 0),
					'tournament_minuser'		=> array('INT:11', 0),
					'tournament_maxuser'		=> array('INT:11', 0),
					'league_cycle'				=> array('INT:4', 1),
					'bbcode_uid'				=> array('VCHAR:8', ''),
					'bbcode_bitfield'			=> array('VCHAR:255', ''),
					'bbcode_options'			=> array('INT:4', 0),
				),
				'PRIMARY_KEY'	=> 'tournament_id',
				),
			),
			
			array('phpbb_rivals_treports', array(
				'COLUMNS'	=> array(
					'report_id'		=> array('UINT', NULL, 'auto_increment'),
					'reported'		=> array('INT:11', 0),
					'tournament_id'	=> array('INT:11', 0),
					'group_1'		=> array('INT:11', 0),
					'group_2'		=> array('INT:11', 0),
					'group_winner'	=> array('INT:11', 0),
				),
				'PRIMARY_KEY'	=> 'report_id',
				),
			),
			
			array('phpbb_rivals_tuserdata', array(
				'COLUMNS'	=> array(
					'group_uid'		=> array('VCHAR:100', '0'),
					'user_id'		=> array('UINT', 0),
					'tournament_id'	=> array('INT:11', 0),
					'group_id'		=> array('INT:11', 0),
					'kills'			=> array('INT:11', 0),
					'morti'			=> array('INT:11', 0),
					'assist'		=> array('INT:11', 0),
					'conferma1'		=> array('TINT:1', 0),
					'conferma2'		=> array('TINT:1', 0),
				),
				'KEYS'	=> array(
					'group_uid'		=> array('INDEX', 'group_uid'),
					'user_id'		=> array('INDEX', 'user_id'),
					'tournament_id'	=> array('INDEX', 'tournament_id'),
				),
			)),
			
			array('phpbb_rivals_user_clan', array(
				'COLUMNS'	=> array(
					'group_id'		=> array('UINT', 0),
					'user_id'		=> array('UINT', 0),
					'group_leader'	=> array('TINT:1', 0),
					'user_pending'	=> array('TINT:1', 1),
					'mvp_utente'	=> array('INT:11', 0),
					'kills'			=> array('INT:11', 0),
					'deads'			=> array('INT:11', 0),
					'assists'		=> array('INT:11', 0),
					'agoals'		=> array('INT:11', 0),
					'fgoals'		=> array('INT:11', 0),
				),
				'KEYS'	=> array(
					'group_id'	=> array('INDEX', 'group_id'),
					'user_id'	=> array('INDEX', 'user_id'),
				),
			)),
			
		),
		
	//Clear cache
		'cache_purge' => array(''),
		
	), // end of array 2.0
);

// Check if the new modules exist if not add it manually
// If i put in array when i do a fresh install i get a error
$Regaction	= request_var('action', 'install');
$Regconfirm	= (!empty($_POST['confirm'])) ? true : false;
if ($Regconfirm && $Regaction == 'update')
{
	include($phpbb_root_path . 'umil/umil.' . $phpEx);
	$umil = new umil;
	
	if (!$umil->module_exists('ucp', 'UCP_CAT_RIVALS', 'UCP_RIVALS_SET_ROSTER'))
	{
		$umil->module_add('ucp', 'UCP_CAT_RIVALS', array(
			'module_basename'   => 'rivals',
			'module_langname'   => 'UCP_RIVALS_SET_ROSTER',
			'module_mode'       => 'set_roster',
			'module_auth'       => '',
		));
	}
}

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>