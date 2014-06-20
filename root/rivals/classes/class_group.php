<?php
##############################################################
# FILENAME  : class_group.php
# COPYRIGHT : (c) 2008, Tyler N. King <aibotca@yahoo.ca>
# http://opensource.org/licenses/gpl-license.php GNU Public License
# MOD FILE : (c) 2010, Soshen <nipponart.org>
##############################################################
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * Group Class
 * This takes group information from the user's group in session, or takes information from the DB for a specific group.
 */
class group
{
	/**
	 * Contains the group's information.
	 *
	 * @var array
	 */
	var $data;

	/**
	 * Contains the group's ID.
	 *
	 * @var integer
	 */
	var $group_id;

	/**
	 * Gets the user's group information.
	 */
	function group()
	{
		global	$db;
		global	$user;

		// Get the group the user is logged into and grab it's data.
		$sql		= "SELECT group_session FROM " . USERS_TABLE . " WHERE user_id = " . $user->data['user_id'];
		$result		= $db->sql_query($sql);
		$row		= $db->sql_fetchrow($result);

		$this->group_id	= intval($row['group_session']);
		$this->data		= $this->data();
	}

	/**
	 * Populates the array of data based on information from the arguments.
	 *
	 * @param string $feild
	 * @param integer $group_id
	 * @param integer $ladder_id
	 * @return array
	 */
	function data($field = '*', $group_id = 0, $ladder_id = 0)
	{
		global	$db;

		// Are we dealing with a request or default request?
		$type		= ($group_id != 0) ? $group_id : $this->group_id;
		$frosteds	= array();

		if ($ladder_id != 0)
		{
			// Get the group's information for a specific ladder.
			$sql	= "SELECT gd.*, ud.user_id FROM " . GROUPDATA_TABLE . " gd, " . USER_CLAN_TABLE . " ud WHERE gd.group_id = $type AND gd.group_ladder = $ladder_id AND gd.group_id = ud.group_id AND ud.group_leader != 0";
			$result	= $db->sql_query($sql);
			$row	= $db->sql_fetchrow($result);

			if ($field != '*')
			{
				// Return the specific feild.
				return	$row[($field != 'user_id') ? $field : 'user_id' ];
			}
			else
			{
				return	$row;
			}
		}
		else
		{
			if ($type != 0)
			{
				// Get the group's information.
				$sql		= "SELECT gd.*, ud.user_id FROM " . CLANS_TABLE . " gd, " . USER_CLAN_TABLE . " ud WHERE gd.group_id = {$type} AND ud.group_id = gd.group_id AND ud.group_leader != 0";
				$result		= $db->sql_query($sql);
				$row		= (array) $db->sql_fetchrow($result);

				if ($field != '*')
				{
					// Return the specific feild.
					return	$row[($field != 'user_id') ? $field : 'user_id'];
				}
				else
				{
					// Get the ladders the group is joined to as well.
					$sql	= "SELECT group_ladder, group_frosted FROM " . GROUPDATA_TABLE . " WHERE group_id = " . $type;
					$result	= $db->sql_query($sql);

					$ladders	= array();
					while ($rows = $db->sql_fetchrow($result))
					{
						$ladders[]	= $rows['group_ladder'];
						$frosteds[]	= ($rows['group_frosted'] == 1) ? $rows['group_ladder'] : 0;
					}
					$db->sql_freeresult($result);

					// Return the group's data and the group's ladder list.
					return	array_merge($row, array('group_ladders' => $ladders, 'group_frosteds' => $frosteds));
				}
			}
			else
			{
				// Return an empty array set.
				return	array();
			}
		}
	}


	/**
	 * Handles all the functions for the members.
	 *
	 * @param string $action
	 * @param integer $group_id
	 * @param integer $user_id
	 * @param integer $leader
	 * @return array
	 */
	function members($action, $group_id)
	{
		global	$db;

		// Switch between the actions.
		switch($action)
		{
			case 'get_members' :
				// Gets all members. No pending members.
				$sql	= "SELECT user_id FROM " . USER_CLAN_TABLE . " WHERE user_pending = 0 AND group_id = " . $group_id;
				$result	= $db->sql_query($sql);

				$users	= array();
				while ($row = $db->sql_fetchrow($result))
				{
					$users[]	= $row['user_id'];
				}
				$db->sql_freeresult();

				return	$users;
			case 'get_pending' :
				// Gets all pending members.
				$sql	= "SELECT user_id FROM " . USER_CLAN_TABLE . " WHERE user_pending = 1 AND group_id = " . $group_id;
				$result	= $db->sql_query($sql);

				$users	= array();
				while ($row = $db->sql_fetchrow($result))
				{
					$users[]	= $row['user_id'];
				}
				$db->sql_freeresult();

				return	$users;
			break;
		}
	}
	
	/**
	 * Check if a clan joinet at last a ladder.
	 *
	 * @param integer $group_id
	 * @return boolean
	 */
	 function active($group_id)
	{
		global	$db;
		
		$sql	= " SELECT group_id FROM " . GROUPDATA_TABLE . " WHERE group_id = " . (int) $group_id;
		$result	= $db->sql_query_limit($sql, 1);
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		return (!empty($row['group_id']) ? true : false);
	}
}

?>
