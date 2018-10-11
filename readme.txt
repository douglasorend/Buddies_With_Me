[hr]
[center][color=red][size=16pt][b]FOLLOWERS v1.7[/b][/size][/color]
[url=http://www.simplemachines.org/community/index.php?action=profile;u=253913][b]By Dougiefresh[/b][/url] - [url=http://custom.simplemachines.org/mods/index.php?mod=3894]Link to Mod[/url]
[/center]
[hr]

[color=blue][b][size=12pt][u]Introduction[/u][/size][/b][/color]
This mod adds a page to the Profile area under [b]Buddies/Ignore List[/b] that lists everybody that lists you as a buddy.  Note that you cannot remove someone from their buddies list!

This mod also adds a resync function to the Admin area under [b]Forum Maintenance[/b], under [b]Members[/b], in the event the followers table ever becomes unsynced.

[color=blue][b][size=12pt][u]Related Discussion[/u][/size][/b][/color]
- [url=http://www.simplemachines.org/community/index.php?topic=524878.0]Easiest way to display a list of everyone whos added me as a buddy on my profile[/url]

[color=blue][b][size=12pt][u]Potential Issue[/u][/size][/b][/color]
After installation is successful, the script must call the resync function in order to build the table.  If this does not happen, then the proper results won't be seen.

[color=blue][b][size=12pt][u]Compatibility Notes[/u][/size][/b][/color]
This mod was tested on SMF 2.0.8, and may work on SMF 2.0 and later.  SMF 1.x is not and will not be supported!

DO NOT attempt to uninstall v1.1!!  Upgrade capabilities were mistakenly included in v1.1 and did not include any functionality changes from v1.0 (and 1.0a).  v1.2 fixes this!!!

[color=blue][b][size=12pt][u]Changelog[/u][/size][/b][/color]
[quote]
[u][b]v1.7 - August 6th, 2014[/b][/u]
o Modified resync code to remove buffering of member's buddies list while processing....
o Added some additional input validation code for adding and removing follower functions....

[u][b]v1.6 - August 4th, 2014[/b][/u]
o Modified SSI function to accept user ID as a parameter # 3....

[u][b]v1.5 - July 28th, 2014[/b][/u]
o Moved [b]ssi_getFollowers[/b] function from [b]SSI.php[/b] to [b]Subs-Followers.php[/b]....
o Modified [b]SSI.php[/b] to include [b]Subs-Followers.php[/b]...
o Fixed [b]ssi_getFollowers[/b] function - function is 1st parameter is wrongly inverted
o Fixed all dates in the changelog, as they said June instead of July...

[u][b]v1.4 - July 27th, 2014[/b][/u]
o Modified SSI function [b]ssi_getFollowers[/b] to echo out results if changed...
o Added example code for [b]ssi_getFollowers[/b] function in [b]ssi_examples.php[/b]...

[u][b]v1.3 - July 23th, 2014[/b][/u]
o Included SSI function to pull the list of followers

[u][b]v1.2 - July 21th, 2014[/b][/u]
o Placed the new functions for the Followers into their own seperate file to try to reduce memory requirements.

[u][b]v1.1 - July 20th, 2014[/b][/u]
o Fixed major issues with resync function
o Fixed major issue with adding/removing buddies
o Fixed some minor install issues with the Portuguese Brazil language files.
o Made some necessary alterations to set my changes off from the core buddy system.

[u][b]v1.0a - July 17th, 2014[/b][/u]
o Added Portuguese Brazil to the language mix.  No functionality change.  Thanks, [url=http://www.simplemachines.org/community/index.php?action=profile;u=341618]DSystem[/url]!!

[u][b]v1.0 - July 11th, 2014[/b][/u]
o Initial Release of the Mod
[/quote]

[hr]
[url=http://creativecommons.org/licenses/by/3.0][img]http://i.creativecommons.org/l/by/3.0/80x15.png[/img][/url]
This work is licensed under a [url=http://creativecommons.org/licenses/by/3.0]Creative Commons Attribution 3.0 Unported License[/url]
