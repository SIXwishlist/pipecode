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

print_header("Banned Users");
beg_main();

writeln('<h1>' . get_text("Banned Users") . '</h1>');

$items_per_page = 50;
list($item_start, $page_footer) = page_footer("ban_user", $items_per_page);

$row = sql("select zid, short_id, time, editor_zid from ban_user order by time desc limit $item_start, $items_per_page");

beg_tab();
writeln('	<tr>');
writeln('		<th>' . get_text("Time") . '</th>');
writeln('		<th class="center">' . get_text("User") . '</th>');
writeln('		<th class="center">' . get_text("Example") . '</th>');
writeln('		<th class="right">' . get_text("Editor") . '</th>');
writeln('	</tr>');
if (count($row) == 0) {
	writeln('	<tr><td colspan="4">(' . get_text("none") . ')</td></tr>');
}
for ($i = 0; $i < count($row); $i++) {
	$short_code = crypt_crockford_encode($row[$i]["short_id"]);

	writeln('	<tr>');
	writeln('		<td>' . date("Y-m-d H:i", $row[$i]["time"]) . '</td>');
	writeln('		<td class="center">' . user_link($row[$i]["zid"], ["tag" => true]) . '</td>');
	writeln('		<td class="center"><a href="/comment/' . $short_code . '">#' . $short_code . '</a></td>');
	writeln('		<td class="right">' . user_link($row[$i]["editor_zid"], ["tag" => true]) . '</td>');
	writeln('	</tr>');
}
end_tab();

writeln($page_footer);

end_main();
print_footer();
