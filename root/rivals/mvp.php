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

$mvp_list	= array();

// Load MVP Chart
$sql		= "SELECT * FROM " . RIVAL_MVP . " ORDER BY mvp_id ASC";
$result		= $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result))
{
	$mvp_list[] = $row;
}
$db->sql_freeresult($result);

if (!empty($mvp_list))
{
	$i = 0;
	foreach ($mvp_list as $mvp_data)
	{
		$numero = $i + 1;
		// Assign the member's data to the template.
		$template->assign_block_vars('block_mvp', array(
			'U_MVP'				=> append_sid ("{$phpbb_root_path}rivals.$phpEx", 'action=mvp_chart&amp;ladder_mvp=' . $mvp_data['ladder_mvp']),
			'NOME_MVP'			=> $mvp_data['nome_mvp'],
			'DESCRIZIONE_MVP'	=> trim($mvp_data['descrizione_mvp']),
			'NUM'				=> $numero,
			'ROW_COLOR'			=> ($i % 2) ? 'row1' : 'row2'
		));
		$i++;
	}
}

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['MVP'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=mvp'))
);

$template->set_filenames(array('body' => 'rivals/mvp.html'));
?>