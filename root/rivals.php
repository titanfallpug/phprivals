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
define('IN_PHPBB', true);
$phpbb_root_path	= (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx				= substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Initilize the phpBB sessions.
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/lang_rivals');

// Include Rivals' classes.
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
include($phpbb_root_path . 'rivals/classes/class_group.' . $phpEx);
include($phpbb_root_path . 'rivals/classes/class_tournament.' . $phpEx);
include($phpbb_root_path . 'rivals/classes/class_ladder.' . $phpEx);
include($phpbb_root_path . 'rivals/functions.' . $phpEx);

// Start the output.
$action	= request_var('action', '');

// Set page title
switch ($action)
{
	case 'random':
		$addtitle = ' - ' . $user->lang['TITOLO_PAGINA_RANDOM'];
	break;
	case 'group_profile':
		$addtitle = ' - ' . $user->lang['PROFILE_GROUP'];
	break;
	case 'clan_full':
	case 'group_list':
		$addtitle = ' - ' . $user->lang['CLAN_LIST'];
	break;
	case 'platforms':
		$addtitle = ' - ' . $user->lang['PLATFORM'];
	break;
	case 'ladders':
		$addtitle = ' - ' . $user->lang['LADDERS'];
	break;
	case 'subladders':
		$addtitle = ' - ' . $user->lang['SUBLADDER'];
	break;
	case 'ladder_rules':
		$addtitle = ' - ' . $user->lang['LADDER_RULES'];
	break;
	case 'add_group':
		$addtitle = ' - ' . $user->lang['ADD_GROUP'];
	break;
	case 'latest_war':
		$addtitle = ' - ' . $user->lang['LATEST_WAR_TITLE'];
	break;
	case 'uleadrboard':
		$addtitle = ' - ' . $user->lang['USER_LEADERBOARD'];
	break;
	case 'tournaments':
	case 'tournaments_brackets':
		$addtitle = ' - ' . $user->lang['TOURNAMENTS'];
	break;
	
	case 'mvp':
		$addtitle = ' - ' . $user->lang['TITOLO_PAGINA_MVP'];
	break;
	case 'mvp_chart':
		$addtitle = ' - ' . $user->lang['TITOLO_PAGINA_MVP_CHART'];
	break;
	default:
		$addtitle = '';
	break;
}
page_header($user->lang['RIVALS_TITLE'] . $addtitle);

// Include the file for the action called.
$basename	= basename($action, '.' . $phpEx);
include($phpbb_root_path . 'rivals/' . $basename . '.' . $phpEx);


// Setup main page call
if (empty($action))
{
	redirect(append_sid("{$phpbb_root_path}rivals.$phpEx", "action=platforms"));
}

page_footer();
?>