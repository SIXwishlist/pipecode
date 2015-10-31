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

print_header("Archive");
beg_main();
writeln('<h1>Archive</h1>');

$items_per_page = 50;
list($item_start, $page_footer) = page_footer("story", $items_per_page);

writeln('<table class="zebra">');
$row = sql("select title, slug, publish_time from story order by publish_time desc limit $item_start, $items_per_page");
for ($i = 0; $i < count($row); $i++) {
	$date = gmdate("Y-m-d", $row[$i]["publish_time"]);

	writeln('	<tr>');
	writeln('		<td><a href="/story/' . $date . "/" . $row[$i]["slug"] . '">' . $row[$i]["title"] . '</a></td>');
	writeln('		<td style="text-align: right; white-space: nowrap;">' . $date . '</td>');
	writeln('	</tr>');
}
writeln('</table>');

writeln($page_footer);

end_main();
print_footer();
