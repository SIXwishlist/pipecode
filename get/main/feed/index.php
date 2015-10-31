<?
//
// Pipecode - distributed social network
// Copyright (C) 2014-2015 Bryan Beicker <bryan@pipedot.org>
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

include("feed.php");

print_header();
print_main_nav("feed");
beg_main("cell");

$zid = "bryan@$server_name";
print_feed_page($zid);

if ($auth_zid === "") {
	box_center("This is a sample feed page. <a href=\"" . ($https_enabled ? "https" : $protocol ) . "://$server_name/login\">Login</a> to create your own.");
} else {
	box_center("This is a sample feed page. <a href=\"" . user_link($auth_zid) . "feed/edit\">Create</a> your own <a href=\"" . user_link($auth_zid) . "\">feed page</a>.");
}

end_main();
print_footer();