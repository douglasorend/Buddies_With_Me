<?php
/**********************************************************************************
* Subs-BuddiesWithMe.php - Implementation of the Buddies With Me mod
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

/**********************************************************************************
* Function to display the members that are Buddies With Me:
**********************************************************************************/
function BuddiesWithMe()
{
	global $context, $txt, $sourcedir, $scripturl, $user_info;

	// Build the array required for "createList" function:
	$list_options = array(
		'id' => 'buddies_with_me',
		'title' => $txt['BWM'],
		'items_per_page' => 30,
		'base_href' => $scripturl . '?action=profile;area=lists;sa=BWM;u=' . $user_info['id'],
		'default_sort_col' => 'member_name',
		'get_items' => array(
			'function' => 'BWM_get_data',
		),
		'get_count' => array(
			'function' => 'BWM_get_count',
		),
		'no_items_label' => $txt['BWM_Nobody'],
		'columns' => array(
			'profile' => array(
				'header' => array(
					'value' => '#',
				),
				'data' => array(
					'db' => 'profile',
					'style' => 'text-align: right; width: 5%;',
				),
				'sort' =>  array(
					'default' => 'm.id_member',
					'reverse' => 'tag DESC',
				),
			),
			'member_name' => array(
				'header' => array(
					'value' => $txt['username'],
				),
				'data' => array(
					'db' => 'link',
					'style' => 'width: 50%;',
				),
				'sort' =>  array(
					'default' => 'm.member_name',
					'reverse' => 'tag DESC',
				),
			),
			'status' => array(
				'header' => array(
					'value' => $txt['status'],
				),
				'data' => array(
					'db' => 'status',
					'style' => 'text-align: center;',
				),
			),
			'email' => array(
				'header' => array(
					'value' => $txt['email'],
				),
				'data' => array(
					'db' => 'email',
					'style' => 'text-align: center;',
				),
			),
			'icq' => array(
				'header' => array(
					'value' => $txt['icq'],
				),
				'data' => array(
					'db' => 'icq',
					'style' => 'text-align: center;',
				),
			),
			'aim' => array(
				'header' => array(
					'value' => $txt['aim'],
				),
				'data' => array(
					'db' => 'aim',
					'style' => 'text-align: center;',
				),
			),
			'yim' => array(
				'header' => array(
					'value' => $txt['yim'],
				),
				'data' => array(
					'db' => 'yim',
					'style' => 'text-align: center;',
				),
			),
		),
	);
	if (isset($txt['msn']))
	{
		$list_options['columns']['msn'] = array(
			'header' => array(
				'value' => $txt['msn'],
			),
			'data' => array(
				'db' => 'msn',
				'style' => 'text-align: center;',
			),
		);
	}
	if (empty($user_info['is_admin']))
		unset($list_options['columns']['id']);

	// Let's build the list now:
	loadTemplate('Profile');
	$context['page_title'] = $txt['BWM'];
	$context['sub_template'] = 'BuddiesWithMe';
	require_once($sourcedir . '/Subs-List.php');
	createList($list_options);
}

/**********************************************************************************
* Helper Functions to manage the gather information for the list:
**********************************************************************************/
function BWM_get_count()
{
	global $smcFunc, $user_info;

	$result = $smcFunc['db_query']('', '
		SELECT COUNT(*)
		FROM {db_prefix}buddies AS b
			INNER JOIN {db_prefix}members AS m ON (b.id_member = m.id_member)
		WHERE b.is_buddies_with = {int:user_id}
			AND m.is_activated < 10',
		array(
			'user_id' => (int) $user_info['id'],
		)
	);
	list ($count) = $smcFunc['db_fetch_row']($result);
	$smcFunc['db_free_result']($result);
	return $count;
}

function BWM_get_data($start, $items_per_page, $sort)
{
	global $txt, $scripturl, $settings, $smcFunc, $memberContext, $user_info;

	// Get all members who are buddies with this user:
	$result = $smcFunc['db_query']('', '
		SELECT b.id_member
		FROM {db_prefix}buddies AS b
			INNER JOIN {db_prefix}members AS m ON (b.id_member = m.id_member)
		WHERE b.is_buddies_with = {int:user_id}
			AND m.is_activated < 10
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:per_page}',
		array(
			'user_id' => (int) $user_info['id'],
			'sort' => $sort,
			'start' => $start,
			'per_page' => $items_per_page,
		)
	);
	$buddies = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$buddies[] = $row['id_member'];
	$smcFunc['db_free_result']($result);

	// Load all the members following up:
	loadMemberData($buddies, false, 'profile');

	// Setup a small subset of data for each buddy:
	$info = array();
	foreach ($buddies as $buddy)
	{
		loadMemberContext($buddy);
		$row = $memberContext[$buddy];
		$info[] = array(
			'profile' => '<a href="' . $row['href'] . '">' . $row['id'] . '</a>',
			'link' => $row['link'],
			'status' => '<a href="' . $row['online']['href'] . '"><img src="' . $row['online']['image_href'] . '" alt="' . $row['online']['label'] . '" title="' . $row['online']['label'] . '" /></a>',
			'email' => ($row['show_email'] == 'no' ? '' : '<a href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $row['id'] . '" rel="nofollow"><img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . ' ' . $row['name'] . '" /></a>'),
			'icq' => $row['icq']['link'],
			'aim' => $row['aim']['link'],
			'yim' => $row['yim']['link'],
			'msn' => (isset($row['msn']['link']) ? $row['msn']['link'] : ''),
		);
	}
	return $info;
}

/**********************************************************************************
* Functions to add/remove user in question to/from the Buddies With Me table:
**********************************************************************************/
function BWM_Remove($dest_id)
{
	global $smcFunc, $user_info;

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}buddies
		WHERE id_member = {int:user_id}
			AND is_buddies_with = {int:is_buddies_with}',
		array(
			'user_id' => (int) $user_info['id'],
			'is_buddies_with' => (int) $dest_id,
		)
	);
}

function BWM_Add($dest_id)
{
	global $smcFunc, $user_info;

	// Get how many times the "user id/buddy id" has been stored:
	$result = $smcFunc['db_query']('', '
		SELECT COUNT(id_member) AS count
		FROM {db_prefix}buddies
		WHERE id_member = {int:user_id}
			AND is_buddies_with = {int:is_buddies_with}',
		array(
			'user_id' => (int) $user_info['id'],
			'is_buddies_with' => (int) $dest_id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
	
	// If we can't find an instance, then store it in the table:
	if (empty($row['count']))
	{
		$smcFunc['db_insert']('replace',
			'{db_prefix}buddies',
			array('id_member' => 'int', 'is_buddies_with' => 'text'),
			array((int) $user_info['id'], (int) $dest_id),
			array('id_member', 'is_buddies_with')
		);
	}
}

/**********************************************************************************
* Function for resyncing the Buddies With Me table:
**********************************************************************************/
function BWM_Resync2()
{
	global $smcFunc;
	
	// Dump the contents of the buddies table ONLY if we aren't starting from the beginning:
	$time = time();
	$pos = empty($_REQUEST['pos']) ? 1 : (int) $_REQUEST['pos'];
	if ($pos == 1)
		$result = $smcFunc['db_query']('', '
			TRUNCATE TABLE {db_prefix}buddies');

	// Figure out how many members we have:
	$result = $smcFunc['db_query']('', '
		SELECT COUNT(*) AS total
		FROM {db_prefix}members'
	);
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
	$max = $row['total'];

	// Process EVERYBODY's buddy list:
	$result = $smcFunc['db_query']('', '
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
	$buddies = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$pos++;
		if (!empty($row['buddy_list']))
		{
			// Process this member's buddy list for the array:
			$list = explode(',', $row['buddy_list']);
			foreach ($list as $buddy)
				$buddies[] = array((int) $row['id_member'], (int) $buddy);

			// Are we at the end of members OR hit the 100th member?
			if (!empty($buddies))
			{
				$smcFunc['db_insert']('replace',
					'{db_prefix}buddies',
					array('id_member' => 'int', 'is_buddies_with' => 'int'),
					$buddies,
					array('id_member', 'is_buddies_with')
				);
				$buddies = array();
			}
		}

		// Are we over the 10-second mark?
		if (time() > $time + 10)
		{
			// Setup everything for the next run:
			$context['sub_template'] = 'not_done';
			$context['continue_percent'] = (int) (($pos / $max) * 100);
			$context['continue_get_data'] = 'action=admin;area=maintain;sa=members;sa=BWM_Resync;pos=' . $pos .  ';' . $context['session_var'] . '=' . $context['session_id'];
			break;
		}
	}
	$smcFunc['db_free_result']($result);

	// If we haven't gone past the 10-second mark, redirect properly:
	if (time() <= $time + 10)
		redirectexit('action=admin;area=maintain;sa=members;done=BWM_Resync');
}

?>