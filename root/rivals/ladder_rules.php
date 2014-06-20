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

$ladder			= new ladder();
$valueladder 	= request_var('rules_ladder', 0);
$ladder_data	= $ladder->get_roots($valueladder);

// CARICO I DATI DELLE SEZIONI RULES
$sql	= "SELECT * FROM " . RIVAL_LADDER_RULES . " AS r
			INNER JOIN " . LADDERS_TABLE . " as l ON l.ladder_id = r.rules_ladder
		WHERE rules_ladder = " . $valueladder;
$result	= $db->sql_query($sql);
$rules_list = array();
while ($row = $db->sql_fetchrow($result))
{
	$rules_list[] = $row;
}
$db->sql_freeresult($result);

$i = 0;
foreach ($rules_list as $rules_data)
{
	// Assign the member's data to the template.
	$template->assign_block_vars('block_rules', array(
		'REQUISITI' 		=> htmlspecialchars_decode($rules_data['requisiti_iscrizione']),
		'REGOLE_GENERALI' 	=> htmlspecialchars_decode($rules_data['regole_generali']),
		'CONFIGURAZIONE' 	=> htmlspecialchars_decode($rules_data['configurazione']),
		'DIVIETI' 			=> htmlspecialchars_decode($rules_data['divieti']),
		'NOME_LADDER' 		=> $ladder_data['SUBLADDER_NAME'],
		'MAIN_LADDER' 		=> $ladder_data['LADDER_NAME'],
		'PLATFORM' 			=> $ladder_data['PLATFORM_NAME'],
		'ROW_COLOR' 		=> ($i % 2) ? 'row1' : 'row2'
	));
	$i++;
}

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $ladder_data['PLATFORM_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $ladder_data['PLATFORM_ID']))
);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $ladder_data['LADDER_NAME'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=ladders&amp;platform=' . $ladder_data['PLATFORM_ID']))
);

$sql	= "SELECT * FROM " . LADDERS_TABLE . " WHERE ladder_id = " . $valueladder;
$result	= $db->sql_query($sql);
$row	= $db->sql_fetchrow($result);
$db->sql_freeresult($result);
if ($row['ladder_oneone'] == 1)
{
	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME'	=> $ladder_data['SUBLADDER_NAME'],
		'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=userssubladder&amp;ladder_id=' . $ladder_data['SUBLADDER_ID']))
	);
}
else
{
	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME'	=> $ladder_data['SUBLADDER_NAME'],
		'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=subladders&amp;ladder_id=' . $ladder_data['SUBLADDER_ID']))
	);
}

$template->set_filenames(array('body' => 'rivals/ladder_rules.html'));
?>