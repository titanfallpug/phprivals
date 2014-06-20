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

$ladder	= new ladder();

// Get the platforms.
$sql	= "SELECT * FROM " . PLATFORMS_TABLE;
$result	= $db->sql_query($sql);

$i	= 1;
while($row = $db->sql_fetchrow($result))
{
	// Assign each platform to the template.
	$template->assign_block_vars('block_platforms', array(
		'U_ACTION' 		=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $row['platform_id']),
		'BG_COLOR'		=>($i % 2) ? 'bg1' : 'bg2',
		'ROW_COLOR'		=>($i % 2) ? 'row1' : 'row2',
		'COUNT'			=> $i,
		'NUM_LADDERS'	=> get_totladder($row['platform_id']),
		'PLATFORM_LOGO'	=> $row['platform_logo'],
		'PLATFORM_NAME'	=> $row['platform_name']
	));
	$i++;
}

$db->sql_freeresult($result);

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['PLATFORMS'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=platforms'))
);

$template->set_filenames(array('body' => 'rivals/platforms.html'));

?>