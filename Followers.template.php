<?php
/**********************************************************************************
* Followers.template.php - Template for the Followers mod
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE .
**********************************************************************************/

function template_Followers()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
		<div class="title_bar">
			<h3 class="titlebg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/icons/online.gif" alt="" class="icon" />', $txt['Followers_List'], '</span>
			</h3>
		</div>
		<table border="0" width="100%" cellspacing="1" cellpadding="4" class="table_grid" align="center">
			<tr class="catbg">
				<th class="first_th lefttext" scope="col" width="20%">', $txt['name'], '</th>
				<th scope="col">', $txt['status'], '</th>
				<th scope="col">', $txt['email'], '</th>
				<th scope="col">', $txt['icq'], '</th>
				<th scope="col">', $txt['aim'], '</th>
				<th scope="col">', $txt['yim'], '</th>' . (isset($txt['msn']) ? '
				<th scope="col">', $txt['msn'], '</th>' : '') . '
			</tr>';

	// If they don't have any followers don't list them!
	if (empty($context['followers']))
		echo '
			<tr class="windowbg2">
				<td colspan="8" align="center"><strong>', $txt['Followers_Nobody'], '</strong></td>
			</tr>';

	// Now loop through each follower showing info on each.
	$alternate = false;
	foreach ($context['followers'] as $follower)
	{
		echo '
			<tr class="', $alternate ? 'windowbg' : 'windowbg2', '">
				<td>', $follower['link'], '</td>
				<td align="center"><a href="', $follower['online']['href'], '"><img src="', $follower['online']['image_href'], '" alt="', $follower['online']['label'], '" title="', $follower['online']['label'], '" /></a></td>
				<td align="center">', ($follower['show_email'] == 'no' ? '' : '<a href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $follower['id'] . '" rel="nofollow"><img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . ' ' . $follower['name'] . '" /></a>'), '</td>
				<td align="center">', $follower['icq']['link'], '</td>
				<td align="center">', $follower['aim']['link'], '</td>
				<td align="center">', $follower['yim']['link'], '</td>' . (isset($txt['msn']) ? '
				<td align="center">', $follower['msn']['link'], '</td>' : '') . '
			</tr>';

		$alternate = !$alternate;
	}

	echo '
		</table>';
}

?>