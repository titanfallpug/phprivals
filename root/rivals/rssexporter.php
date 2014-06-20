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
if(!defined('IN_PHPBB'))
{
	exit;
}

/**
* Export Clan data like rss feed
**/

$group		= new group();
$ladder		= new ladder();

$clan			= (int) request_var('clan', 0);
$season_id		= (int) request_var('season_id', 0);
$season_ladder	= (int) request_var('season_ladder', 0);
$clan_data		= $group->data('*', $clan);

$board 	= generate_board_url();
$url 	= "{$board}/rivals.$phpEx?action=group_profile&group_id=" . $clan;


// CREO XML
header("Content-type: application/xml");

echo("<?xml version=\"1.0\"?>\n");


echo("<team>\n");
echo("<name><![CDATA[{$clan_data['group_name']}]]></name>\n");
echo("<url><![CDATA[{$url}]]></url>\n");

// ALL TIME STATS
$sql_2		= "SELECT group_id, SUM(group_wins) AS vittorie, SUM(group_losses) AS sconfitte, SUM(group_pari) AS pareggi FROM " . GROUPDATA_TABLE . " WHERE group_id = {$clan} GROUP BY group_id ";
$result_2	= $db->sql_query($sql_2);
$row_2		= $db->sql_fetchrow($result_2);
$db->sql_freeresult($result_2);
		
$sql_3		= "SELECT group_id, SUM(group_wins) AS vittorie2, SUM(group_losses) AS sconfitte2, SUM(group_pari) AS pareggi2 FROM " . SEASONDATA_TABLE . " WHERE group_id = {$clan} GROUP BY group_id ";
$result_3	= $db->sql_query($sql_3);
$row_3		= $db->sql_fetchrow($result_3);
$db->sql_freeresult($result_3);
		
echo("<alltimewins>" . ($row_2['vittorie'] + $row_3['vittorie2']) . "</alltimewins>\n");
echo("<alltimelosses>" . ($row_2['sconfitte'] + $row_3['sconfitte2']) . "</alltimelosses>\n");
echo("<alltimepareg>" . ($row_2['pareggi'] + $row_3['pareggi2']) . "</alltimepareg>\n\n");		


// DETAILED CURRENT STATS
$sql	= "SELECT * FROM " . GROUPDATA_TABLE . " WHERE group_id = " . $clan;
$result	= $db->sql_query($sql);

while($row = $db->sql_fetchrow($result))
{
	$ladder_data	= $ladder->get_roots($row['group_ladder']);

	echo("<LadderStats>\n");
	echo("<platform>{$ladder_data['PLATFORM_NAME']}</platform>\n");
	echo("<ladder>{$ladder_data['LADDER_NAME']}</ladder>\n");
	echo("<subladder>{$ladder_data['SUBLADDER_NAME']}</subladder>\n");

	echo("<wins>{$row['group_wins']}</wins>\n");
	echo("<losses>{$row['group_losses']}</losses>\n");
	echo("<pareg>{$row['group_pari']}</pareg>\n");
	echo("<streak>{$row['group_streak']}</streak>\n");
	echo("<score>{$row['group_score']}</score>\n");
	echo("<current_rank>{$row['group_wins']}</current_rank>\n");
	echo("</LadderStats>\n\n");	
}
$db->sql_freeresult($result);

echo("</team>\n");

$template->set_filenames(array('body' => 'rivals/rssexporter.html'));
?>