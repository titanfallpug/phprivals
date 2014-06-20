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

define('MOVE_RIGHT', 1);
define('MOVE_LEFT', 2);
define('MOVE_UP', 3);
define('MOVE_DOWN', 4);

define('DIRECT_ELIM', 1);
define('DOUBLE_ELIM', 2);
define('ROUNDROBIN', 3);
define('LEAGUE', 4);
/**
 * Tournament Class
 * This does all the complex calculations for generating brackets and displaying them.
 */
class tournament
{
	/**
	 * Contains the tournament's information.
	 *
	 * @var array
	 */
	var $data;

	/**
	 * Contains the tournament's table widths.
	 *
	 * @var array
	 */
	var $rounds_widths;

	/**
	 * Contains the tournament's round names.
	 *
	 * @var array
	 */
	var $rounds_list;

	/**
	 * Contains the tournament's round numbers.
	 *
	 * @var array
	 */
	var $rounds_array;

	/**
	 * Inits the class, populating the data object.
	 */
	function tournament()
	{
		// Populate the data object.
		$this->data	= $this->data();
	}

	/**
	 * Populates the array of data based on information from the arguments.
	 *
	 * @param string $grab
	 * @param integer $tournament_id
	 * @return array
	 */
	function data($grab = '', $tournament_id = 0)
	{
		global	$db;

		// Are we dealing with a request or default request?
		if ($tournament_id != 0)
		{
			// Request.
			$type	= $tournament_id;
		}
		else
		{
			$check	= (int) request_var('tournament_id', 0);
			if ($check != 0)
			{
				// Default.
				$type	= $check;
			}
			else
			{
				$type	= 0;
			}
		}

		// Get the tournament's information.
		$sql	= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_id = " . $type;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);

		return (!empty($grab)) ? $row[$grab] : $row;
	}
	
	/*
	* Get the tournament taken slots
	*/
	function get_take_tslots($tournament_id = 0)
	{
		global	$db;
		
		if ($tournament_id != 0)
		{
			$sql	= "SELECT COUNT(*) AS presi FROM " . TGROUPS_TABLE . " WHERE group_bracket = 1 AND group_tournament = " . $tournament_id;
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		
		return $row['presi'];
	}
	
	/*
	* Get the clan abbinate to &1 one
	*/
	function get_vsclan($tournament_id = 0, $group_id = 0, $group_bracket = 1, $temp = false)
	{
		global	$db;
		
		if ($tournament_id != 0 && $group_id != 0)
		{			
			$sql	= "SELECT group_tournament, group_id, group_position, group_position_temp FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = {$group_bracket} AND group_id = " . $group_id;
			$result	= $db->sql_query_limit($sql, 1);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			if ($row['group_position'] & 1)
			{
				$vspos	= ($temp == false) ? "group_position = {$row['group_position']} + 1" : "group_position = {$row['group_position_temp']} + 1";
			}
			else
			{
				$vspos	= ($temp == false) ? "group_position = {$row['group_position']} - 1" : "group_position = {$row['group_position_temp']} - 1";
			}
			
			$sql2		= "SELECT group_tournament, group_id, group_position, group_position_temp FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = {$group_bracket} AND " . $vspos;
			$result2	= $db->sql_query_limit($sql2, 1);
			$row2		= $db->sql_fetchrow($result2);
			$db->sql_freeresult($result2);
			
			$opponent	= $row2['group_id'];
		}
		else
		{
			$opponent	= 0;
		}
		
		return $opponent;
	}
	
	/*
	* Get total clan signed up
	*/
	function get_totaltclan($tournament_id = 0)
	{
		global	$db;
		
		if ($tournament_id != 0)
		{			
			$sql	= "SELECT COUNT(*) AS num_groups FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = 1";
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		
		return $row['num_groups'];
	}
	
	/*
	* Get tournament users mvps
	*/
	function get_tusermvps($tournament_id = 0, $userid = 0)
	{
		global	$db;
		
		if ($tournament_id != 0 && $userid != 0)
		{			
			//1)
			$sql_m1		= " SELECT count(mvp1) as kimp1 FROM " . TMATCHES . " WHERE id_torneo = {$tournament_id} AND mvp1 = {$userid} AND conferma1 = 1 AND conferma2 = 1";
			$result_m1	= $db->sql_query($sql_m1);
			$row_m1		= $db->sql_fetchrow($result_m1);
			$xp1		= $row_m1['kimp1'];
			$db->sql_freeresult($result_m1);
			//2)
			$sql_m2		= " SELECT count(mvp2) as kimp2 FROM " . TMATCHES . " WHERE id_torneo = {$tournament_id} AND mvp2 = {$userid} AND conferma1 = 1 AND conferma2 = 1";
			$result_m2	= $db->sql_query($sql_m2);
			$row_m2		= $db->sql_fetchrow($result_m2);
			$xp2		= $row_m2['kimp2'];
			$db->sql_freeresult($result_m2);
			//3)
			$sql_m3		= " SELECT count(mvp3) as kimp3 FROM " . TMATCHES . " WHERE id_torneo = {$tournament_id} AND mvp3 = {$userid} AND conferma1 = 1 AND conferma2 = 1";
			$result_m3	= $db->sql_query ($sql_m3);
			$row_m3		= $db->sql_fetchrow ($result_m3);
			$xp3		= $row_m3['kimp3'];
			$db->sql_freeresult ($result_m3);
			
			$totmvp = $xp1 + $xp2 + $xp3;
		}
		else
		{
			$totmvp = 0;
		}
		
		return $totmvp;
	}
	
	/*
	* Check if a clan or a user are signed up in a tournament
	*/
	function check_ifsignedup($tournament_id = 0, $userbased = false)
	{
		global	$db;
		
		if ($tournament_id != 0 && $userbased == false)
		{			
			$group = new group();
			
			$sql	= "SELECT COUNT(*) AS num_groups FROM " . TGROUPS_TABLE . " WHERE group_id = {$group->data['group_id']} AND group_tournament = " . $tournament_id;
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		else if ($tournament_id != 0 && $userbased == true)
		{
			global	$user;
			
			$sql	= "SELECT COUNT(*) AS num_groups FROM " . TGROUPS_TABLE . " WHERE group_id = {$user->data['user_id']} AND group_tournament = " . $tournament_id;
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		$output	= (!empty($row['num_groups'])) ? true : false;
		
		return $output;
	}

	/**
	 * This generates both the single and double elimination brackets.
	 *
	 * @param boolean $double_elimination
	 */
	function generate_brackets($double_elimination = false)
	{
		global	$user;

		// If double_elimination is true, then take 1 away from the total brackets.
		$brackets		= ($double_elimination == true) ? ($this->data['tournament_brackets'] - 1) : $this->data['tournament_brackets'];
		$spots			= $brackets * 2 - 1;
		$temp_rows		= $brackets;

		// Keep counting until the rows/columes are filled.
		$rows		= 0;
		$row_count	= 1;
		while ($temp_rows > 1)
		{
			$row_count++;

			// Add a new colume.
			$temp_rows	= ($temp_rows / 2);
			$rows		= $row_count;
		}

		$counter	= $brackets;
		$finals		= ($rows - 1);
		$winner		= $rows;
		$round		= 0;

		$rounds_array	= array();
		$rounds_list	= array();
		while ($counter > 1)
		{
			if ($round > 0)
			{
				// Starts a new bracket essentially.
				$counter	= $counter / 2;
			}

			$round++;
			if ($round == $winner)
			{
				// This is the last round (Winners Round).
				$round_games	= $user->lang['WINNER_ROUND'];
			}
			else if ($round == $finals)
			{
				// This is the second last round (Finals Round).
				$round_games	= $user->lang['FINAL_ROUND'];
			}
			else
			{
				// This is a regular round.
				$round_games	= sprintf($user->lang['ROUND'], $round);
			}

			// Add the rounds and round names to the arrays for processing;
			$rounds_list[]					= $round_games;
			$rounds_array[$round_games]	= array();

			$position	= 0;
			while ($counter > $position)
			{
				$position++;

				// Give each bracket a ID.
				$rounds_array[$round_games][]	= $round . '_' . $position;
			}
		}

		// Inserts the "Game x" into the proper position in the brackets.
		// Thanks to Thomas Jollans for this :).
		$x	= 1;
		foreach ($rounds_array AS $key => $value)
		{
			$i	= 1;
			while ($i < sizeof($rounds_array[$key]))
			{
				array_splice($rounds_array[$key], $i, 0, 'Game ' . $x);
				$i	+= 3;
				$x++;
			}
		}

		$this->rounds_list	= $rounds_list;
		$this->rounds_array	= $rounds_array;
	}

	/**
	 * Fixes up the brackets to make them run smooth, using BYE groups.
	 *
	 * @param integer $tournament_id
	 * @return null
	 */
	function add_byes($tournament_id)
	{
		global	$db, $config;

		// check the tournament type
		$sql_8		= "SELECT * FROM " . TOURNAMENTS_TABLE . " WHERE tournament_id = " . $tournament_id;
		$result_8	= $db->sql_query_limit($sql_8, 1);
		$row_8		= $db->sql_fetchrow($result_8);
		$db->sql_freeresult($result_8);
		
		$thebye	= ($row_8['tournament_userbased'] == 0) ? $config['rivals_byegroup'] : 1;
		
		// Get the groups for this tournament.
		$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = " . $tournament_id;
		$result	= $db->sql_query ($sql);

		// Setup the group array. Loop through the signed-up groups.
		$groups	= array();
		while ($row = $db->sql_fetchrow($result))
		{
			$groups[]	= $row['group_id'];
		}
		shuffle($groups);
		$db->sql_freeresult($result);

		/*Upscales the group array to a power of 2 by inserting BYE groups. This fixes up the brackets to make them run smoothly. Thanks to Thomas Jollans for the math help :)*/
		$i	= 1;
		while ($i < sizeof($groups))
		{
			$exponent	= log(sizeof($groups), 2);
			if ((float)(int)($exponent) != $exponent)
			{
				// Add a bye.
				array_splice($groups, $i, 0, $thebye);
			}

			// Jump two positions to add the next bye.
			$i	+= 2;
		}
		
		// Add new
		$y	= 1;
		foreach($groups AS $value)
		{
			if ($value != $thebye)
			{
				// Those are the real clan/user subscrived
				$sql_array2 = array(
					'group_position' => $y,
				);
				$sql = "UPDATE " . TGROUPS_TABLE  . " SET " . $db->sql_build_array('UPDATE', $sql_array2) . " WHERE group_tournament = {$tournament_id} AND group_id = {$value} AND group_bracket = 1";
				$db->sql_query($sql);
			}
			else
			{
				// add the bye to db
				$sql_array	= array(
					'group_tournament'	 	=> $tournament_id,
					'group_id'				=> $value,
					'roster_id'				=> 0,
					'group_bracket'			=> 1,
					'group_position'		=> $y,
					'group_loser'			=> 0,
					'group_position_temp'	=> 0,
					'group_reported'		=> 0,
					'loser_confirm'			=> 0,
					'group_uid'				=> 0,
					'group_time'			=> 0,
					'reputation'			=> 5
				);
				$sql = "INSERT INTO " . TGROUPS_TABLE . " " . $db->sql_build_array('INSERT', $sql_array);
				$db->sql_query($sql);
			}			
			$y++;
		}
	}

	/**
	 * Checks if a group has a matchup in a tournament.
	 * If the group's position in the bracket is odd,
	 * it gets the position ahead. If the group's
	 * position is even, it gets the position behind.
	 *
	 * @param integer $bracket
	 * @param integer $position
	 * @param integer $tournament_id
	 * @param boolean $loser
	 * @return integer
	 */
	function get_matchup($bracket, $position, $tournament_id = 0, $loser = false)
	{
		global	$db;

		$loser			= ($loser == true) ? " AND group_loser = 1" : " AND group_loser != 1";
		$position		= ($position % 2) ? ($position + 1) : ($position - 1);
		$tournament_id	= ($tournament_id != 0) ? $tournament_id : $this->data['tournament_id'];

		$sql	= "SELECT group_id FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$tournament_id} AND group_bracket = {$bracket} AND group_position = " . $position . $loser;
		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return(!empty($row['group_id']) && $row['group_id'] != 0) ? $row['group_id'] : false;
	}

	/**
	 * This takes the data from generate_brackets
	 * and puts it into a graphical perspective
	 * for both admins and users.
	 *
	 * @param $admin boolean
 	 * @param $u_action object
	 * @return object
	 */
	function generate_tournament($admin = false, $u_action = '')
	{
		global	$db;
		global	$user;
		global	$template;
		global	$phpEx;
		global	$phpbb_root_path;

		$group		= new group();
		$fieltour	= new tournament();

		// Do the calculations.
		$this->generate_brackets();

		$i			= 0;
		$rounds		= 0;
		$move_on	= '';
		
		foreach ($this->rounds_array AS $round)
		{
			// Add a new colume/round with the table widths, round name and round number.
			$template->assign_block_vars('block_rounds', array(
				'S_ROUND'	=> $this->rounds_list[$i],
				'S_FINAL'	=> ($this->rounds_list[$i] == $user->lang['WINNER_ROUND']) ? false : true
			));
			$rounds++;

			$x	= 0;
			foreach ($round AS $round_value)
			{
				$x++;
				// Is this a group or game?
				if (empty($round_value) || strstr($round_value, 'Game'))
				{
					// Game, italics it.
					$template->assign_block_vars('block_rounds.block_data', array(
						'S_DATA'	=> '<em>' . $round_value . '</em>',
						'S_COLOR'	=> 'trasparent',
						'S_COLOR2'	=> 'rowX'
					));
				}
				else
				{
					// Get the group for this bracket and position.
					$bap	= explode('_', $round_value);

					$sql	= "SELECT * FROM " . TGROUPS_TABLE . " WHERE group_tournament = {$this->data['tournament_id']} AND group_bracket = {$bap[0]} AND group_position = {$bap[1]}";
					$result	= $db->sql_query($sql);
					$row	= $db->sql_fetchrow($result);
					
					$gposition = $bap[1];

					// Admin mode?
					if ($admin == true)
					{
						// Test and see if they have a matchup.
						$test		= $this->get_matchup($bap[0], $bap[1]);
						$move_on	= ($row['group_position'] != 1) ? '(<a href="' . $u_action . '&amp;group_id=' . $row['group_id'] . '&amp;bracket=' . $bap[0] . '&amp;position=' . $bap[1] . '&amp;move=' . MOVE_UP . '&amp;where=1&amp;tournament_id=' . $this->data['tournament_id'] . '&amp;submit=1"><img src="' . $phpbb_root_path . 'rivals/images/icon_up.gif" /></a> | <a href="' . $u_action . '&amp;group_id=' . $row['group_id'] . '&amp;bracket=' . $bap[0] . '&amp;position=' . $bap[1] . '&amp;move=' . MOVE_DOWN . '&amp;where=1&amp;tournament_id=' . $this->data['tournament_id'] . '&amp;submit=1"><img src="' . $phpbb_root_path . 'rivals/images/icon_down.gif" /></a>) <a href="' . $u_action . '&amp;remove_group=' . $row['group_id'] . '&amp;tournament_id=' . $this->data['tournament_id'] . '&amp;submit=1">' . $user->lang['DELETE'] . '</a>' : '(<a href="' . $u_action . '&amp;group_id=' . $row['group_id'] . '&amp;bracket=' . $bap[0] . '&amp;position=' . $bap[1] . '&amp;move=' . MOVE_DOWN . '&amp;where=1&amp;tournament_id=' . $this->data['tournament_id'] . '&amp;submit=1"><img src="' . $phpbb_root_path . 'rivals/images/icon_down.gif" /></a>) <a href="' . $u_action . '&amp;remove_group=' . $row['group_id'] . '&amp;tournament_id=' . $this->data['tournament_id'] . '&amp;submit=1">' . $user->lang['DELETE'] . '</a>';
					}
					
					$winnerid	= get_roundwinner($this->data['tournament_id'], $bap[0], $gposition);
					
					if ($fieltour->data('tournament_userbased', $this->data['tournament_id']) == 0)
					{
						$winnername	= ($winnerid != 0) ? $group->data('group_name', $winnerid) : '';
					}
					else
					{
						$winnername	= ($winnerid != 0) ? getusername($winnerid) : '';
					}
					
					$switcherkk	= ($fieltour->data('tournament_tipo', $this->data['tournament_id']) == 2) ? true : false;
					
					// Load the correct name between clan and user
					$correctname	= ($fieltour->data('tournament_userbased', $this->data['tournament_id']) == 0) ? $group->data('group_name', $row['group_id']) : getusername($row['group_id']);
					$correcturl		= ($fieltour->data('tournament_userbased', $this->data['tournament_id']) == 0) ? append_sid($phpbb_root_path . 'rivals.' . $phpEx, 'action=group_profile&amp;group_id=' . $row['group_id']) : append_sid($phpbb_root_path . 'memberlist.' . $phpEx, 'mode=viewprofile&amp;u=' . $row['group_id']);
					$roster			= ($row['roster_id'] != 0) ? '<br />(' . $user->lang['LINEUP_SHORT'] . ': ' . get_roster_name($row['roster_id']) . ') ' : '';
					// group, bold it.
					$S_points		= ($row['group_id'] != 0) ? get_tgrpoint($this->data['tournament_id'], $row['group_uid'], $row['group_id'], $switcherkk) : '&nbsp;';
					
					$template->assign_block_vars('block_rounds.block_data', array(
						'S_DATA'	=> ($row['group_id'] != 0) ? '<strong><a href="' . $correcturl . '">' . $correctname . $roster . '</a></strong>' : '' . $user->lang['VUOTO'] . '',
						'S_MOVEON'	=> (!empty($row['group_id'])) ? $move_on : '',
						'S_POINTS'	=> ($row['group_uid'] == 69) ? (($row['group_loser'] == 0) ? 3 : 0) : $S_points,
						'S_COLOR'	=> 'row2',
						'S_COLOR2'	=> 'rowF',
						'S_BGCOLOR'	=> 'bg2',
						'SEPARE'	=> ($gposition % 2) ? '<td rowspan="3" class="rowGR"><img src="' . $phpbb_root_path . 'rivals/images/tourlimt.png" alt="" /></td>' : '',
						'THEWINNER'	=> ($gposition % 2) ? '<td rowspan="3" class="rowGZ">' . $winnername . '</td>' : ''
					));
				}

				// This adds a "spacer" to beautify the brackets.
				// count ($round) = number of groups in this bracket.
				if (($x % 3 == 0) && $x != count($round))
				{
					switch ($rounds)
					{
						case 1:
							$spacer = '';
						break;
						case 2:
							$spacer = str_repeat('<br />', 8);
						break;
						case 3:
							$spacer = str_repeat('<br />', 24);
						break;
						case 4:
							$spacer = str_repeat('<br />', 54);
						break;
						case 5:
							$spacer = str_repeat('<br />', 108);
						break;
						case 6:
							$spacer = str_repeat('<br />', 216);
						break;
						case 7:
							$spacer = str_repeat('<br />', 432);
						break;
						default:
							$spacer = ($rounds != 1) ? str_repeat('<br />', ($rounds * 6)) : '';
						break;
					}
					// Assign dummy data to the template. S_DATA will contain a spacer, to make the brackets look nice.
					$template->assign_block_vars ('block_rounds.block_data', array(
						'S_DATA'	=> $spacer,
						'S_COLOR'	=> 'trasparent',
						'S_COLOR2'	=> 'trasparent',
						'S_BGCOLOR'	=> ''
					));
				}
			}

			$i++;
		}
	}

	/**
	 * Removes the group from a tournament.
	 *
	 * @param integer $group_id
	 * @param integer $tournament_id
	 * @return null
	 */
	function remove_group($group_id, $tournament_id = 0)
	{
		global	$db;
		global	$user;
		global	$group;
		global	$template;
		global	$phpEx;
		global	$phpbb_root_path;

		$tournament_id	= ($tournament_id == 0) ? $this->data['tournament_id'] : $tournament_id;

		// Remove the group from the brackets.
		$sql	= "DELETE FROM " . TGROUPS_TABLE . " WHERE group_id = {$group_id} AND group_tournament = " . $tournament_id;
		$db->sql_query ($sql);

		$group	= new group();

		$tournaments	= unserialize($group->data('group_tournaments', $group_id));
		foreach ($tournaments AS $key => $value)
		{
			if ($value == $tournament_id)
			{
				// Tournament IDs match, remove it.
				unset ($tournaments[ $key ]);
			}
		}

		// Re-serialize the array for the database.
		$tournaments	= serialize($tournaments);

		// Update the group's data.
		$sql	= "UPDATE " . CLANS_TABLE . " SET group_tournaments = '{$tournaments}' WHERE group_id = " . $group_id;
		$db->sql_query ($result);
	}
}

?>