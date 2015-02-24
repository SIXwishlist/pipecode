<?
//
// Pipecode - distributed social network
// Copyright (C) 2014 Bryan Beicker <bryan@pipedot.org>
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

print_header("Articles");
beg_main();

writeln('<h1>Articles</h1>');

$row = sql("select * from article order by publish_time desc limit 0, 50");
beg_tab();
writeln('	<tr>');
writeln('		<th>Title</th>');
writeln('		<th class="right">Time</th>');
writeln('	</tr>');
for ($i = 0; $i < count($row); $i++) {
	$short_code = crypt_crockford_encode($row[$i]["article_id"]);

	writeln('	<tr>');
	writeln('		<td><a href="/article/' . $short_code . '">' . $row[$i]["title"] . '</a></td>');
	writeln('		<td class="right">' . date("Y-m-d H:i", $row[$i]["publish_time"]) . '</td>');
	writeln('	</tr>');
}
end_tab();

end_main();
print_footer();
