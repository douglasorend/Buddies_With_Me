<?php
/**********************************************************************************
* Subs-Followers.php - PHP implementation of the Followers Mod
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE .
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

// Function for followers table maintanence...
function ResyncFollowers2()
{
	global $smcFunc;
	
	// Dump the contents of the followers table ONLY if we aren't starting from the beginning:
	$time = time();
	$pos = empty($_REQUEST['pos']) ? 1 : (int) $_REQUEST['pos'];
	if ($pos == 1)
		$request = $smcFunc['db_query']('', '
			TRUNCATE TABLE {db_prefix}followers');

	// Figure out how many members we have:
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) AS total
		FROM {db_prefix}members'
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$max = $row['total'];

	// Process EVERYBODY's buddy list:
	$request = $smcFunc['db_query']('', '
		SELECT id_member, buddy_list
		FROM {db_prefix}members
		ORDER BY id_member
		LIMIT {int:start}, {int:top}',
		array(
			'start' => (int) $pos - 1,
			'top' => (int) $max,
		)
	);
	$count = 0;
	$followers = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$pos++;
		if (!empty($row['buddy_list']))
		{
			// Process this member's buddy list for the array:
			$list = explode(',', $row['buddy_list']);
			foreach ($list as $buddy)
				$followers[] = array($row['id_member'], $buddy);

			// Are we at the end of members OR hit the 100th member?
			if (($pos % 100) == 0 || $pos == $max)
			{
				// Dump the array to the followers table:
				if (!empty($followers))
					$smcFunc['db_insert']('replace',
						'{db_prefix}followers',
						array('id_member' => 'int', 'follows' => 'text'),
						$followers,
						array('id_member', 'follows')
					);
				$followers = array();
			}
		}
				
		// Are we over the 10-second mark?
		if (time() > $time + 10)
		{
			// Dump the array to the followers table:
			if (!empty($followers))
				$smcFunc['db_insert']('replace',
					'{db_prefix}followers',
					array('id_member' => 'int', 'follows' => 'text'),
					$followers,
					array('id_member', 'follows')
				);
				
			// Setup everything for the next run:
			$context['sub_template'] = 'not_done';
			$context['continue_percent'] = (int) (($pos / $max) * 100);
			$context['continue_get_data'] = 'action=admin;area=maintain;sa=members;sa=resyncfollowers;pos=' . $pos .  ';' . $context['session_var'] . '=' . $context['session_id'];
			break;
		}
	}
	$smcFunc['db_free_result']($request);

	// If we haven't gone past the 10-second mark, redirect properly:
	if (time() <= $time + 10)
		redirectexit('action=admin;area=maintain;sa=members;done=resyncfollowers');
}

// Gather the followers into a list for the user...
function Followers()
{
	global $smcFunc, $memberContext, $user_info, $context;

	// Get all the users' followers...
	$followers = array();
	$result = $smcFunc['db_query']('', '
		SELECT f.id_member
		FROM {db_prefix}followers AS f
		INNER JOIN {db_prefix}members AS m ON (f.id_member = m.id_member)
		WHERE f.follows = {int:user_id}
			AND m.is_activated < 10',
		array(
			'user_id' => (int) $user_info['id'],
		)
	);
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$followers[] = $row['id_member'];
	$smcFunc['db_free_result']($result);

	$context['buddy_count'] = count($followers);

	// Load all the members following up.
	loadMemberData($followers, false, 'profile');

	// Setup the context for each buddy.
	loadTemplate('Followers');
	$context['followers'] = array();
	foreach ($followers as $buddy)
	{
		loadMemberContext($buddy);
		$context['followers'][$buddy] = $memberContext[$buddy];
	}
}

// Remove user in question from the followers table:
function removeFollower($user_id)
{
	global $smcFunc, $user_info;

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}followers
		WHERE id_member = {int:user_id}
			AND follows = {int:follows}',
		array(
			'user_id' => (int) $user_info['id'],
			'follows' => $user_id,
		)
	);
}

// Add user in question to the followers table:
function addFollower($user_id)
{
	global $smcFunc, $user_info;

	$smcFunc['db_insert']('replace',
		'{db_prefix}followers',
		array('id_member' => 'int', 'follows' => 'text'),
		array($user_info['id'], $user_id),
		array('id_member', 'follows')
	);
}

// Gets the whole followers list.
function ssi_getFollowers($include_banned = false, $output_method = 'echo', $user_id = -1)
{
	global $smcFunc, $user_info, $scripturl;

	$request = $smcFunc['db_query']('', '
		SELECT m.real_name, m.id_member
		FROM {db_prefix}followers AS f
		INNER JOIN {db_prefix}members AS m ON (f.id_member = m.id_member)
		WHERE f.follows = {int:user_id}' . (!$include_banned ? ' AND m.is_activated < 10' : '') . '
		ORDER BY m.real_name',
		array(
			'user_id' => (int) ($user_id <> -1 ? $user_id : $user_info['id']),
		)
	);
	$followers = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$followers[$row['id_member']] = $row['real_name'];
	$smcFunc['db_free_result']($request);
	
	if ($output_method <> 'echo')
		return $followers;
		
	foreach ($followers as $id => $name)
		$followers[$id] = '<a href="' . $scripturl . '/index.php?action=profile;u=' . $id . '">' . $name . '</a>';
	echo implode(',', $followers);
}

?>