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

$tempo = time();

$sql	= "SELECT * FROM " . RANDOM_TABLE;
$result	= $db->sql_query($sql);
while($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('block_ranmenu', array(
		'LINK'			=> append_sid("{$phpbb_root_path}rivals.$phpEx", "action=random#" . $row['short_name']),
		'NOME_GIOCO'	=> $row['gioco']
	));
}
$db->sql_freeresult($result);

/***********************
* GRAPHIC BLOCK
****************/

$sql_0		= "SELECT * FROM " . RANDOM_TABLE;
$result_0	= $db->sql_query($sql_0);
$i		= 0;
while ($row_t = $db->sql_fetchrow($result_0))
{
	$temposalvato	= $row_t['tempo'];
	$mappa 			= $row_t['img_mappa'];
	$gioco			= $row_t['gioco'];
	$short			= $row_t['short_name'];
	$mapofDay		= get_mapofday($short);
	
	if (!empty($mapofDay))
	{
		if (time() >= ($temposalvato + 86400)) // if pass a day
		{           
			$sql_array	= array(
				'tempo'		=> $tempo,
				'img_mappa'	=> $mapofDay
			);
			$sql = "UPDATE " . RANDOM_TABLE . " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE short_name = '{$short}'";
			$db->sql_query($sql);
			
			//reload page
			redirect(append_sid("{$phpbb_root_path}rivals.$phpEx", "action=random"));
		}
			
		$template->assign_block_vars('block_random', array(
			'HOMEURL' 		=> "{$phpbb_root_path}images/rivals/random/{$short}",
			'NOME_GIOCO'	=> $gioco,
			'ANCHOR'		=> $short,
			'MAPPA'			=> $mappa
		));
	}
	$i++;
}
$db->sql_freeresult($result_0);

// Set up the breadcrumb.
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['RANDOM_MAP'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rivals.$phpEx", 'action=random'))
);

$template->set_filenames(array('body' => 'rivals/random.html'));
?>