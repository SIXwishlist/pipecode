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

require_mine();

print_header("Notifications", [], [], [], ["Notifications"], ["/notification/"]);
beg_main();
beg_form();

$items_per_page = 10;
list($item_start, $page_footer) = page_footer("notification", $items_per_page, ["zid" => $zid]);

$row = sql("select notification_id, item_id, parent_id, type_id from notification where zid = ? order by time desc limit $item_start, $items_per_page", $auth_zid);
for ($i = 0; $i < count($row); $i++) {
	print_notification_row($row[$i]["notification_id"], $row[$i]["item_id"], $row[$i]["parent_id"], $row[$i]["type_id"], true);
}

if (count($row) == 0) {
	writeln("No notifications yet!");
} else {
	box_right('<a class="icon-16 broom-16" href="clear">Clear All</a>');
}

writeln($page_footer);

end_form();
end_main();
print_footer();
