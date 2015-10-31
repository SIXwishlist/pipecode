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

include("story.php");
include("image.php");

$topic = $s2;
if (strlen($topic) > 0 && !string_uses($topic, "[a-z][0-9]-")) {
	fatal("Invalid topic");
}

if ($topic == "") {
	print_header("Topics", [], [], [], ["Topic"], ["/topic/"]);
} else {
	print_header(ucwords($topic), [], [], [], ["Topic", ucwords($topic)], ["/topic/", "/topic/$topic"]);
}

if ($topic != "") {
	//print_user_nav("topics");
//} else {
	print_user_nav($topic);
}

beg_main("cell");
if ($topic == "") {
	writeln('<h1>Topics</h1>');

	$row = sql("select distinct topic from journal where zid = ? order by topic", $zid);
	for ($i = 0; $i < count($row); $i++) {
		$topic = $row[$i]["topic"];
		if (fs_is_file("$doc_root/www/images/$topic-64.png") && fs_is_file("$doc_root/www/images/$topic-128.png")) {
			$icon = $topic;
		} else {
			$icon = "news";
		}
		writeln('<a href="/topic/' . $topic . '"><div class="topic-box ' . $icon . '-64">' . $topic . '</div></a>');
	}
} else {
	$items_per_page = 10;
	if ($zid == $auth_zid) {
		list($item_start, $page_footer) = page_footer("journal", $items_per_page, array("zid" => $zid, "topic" => $topic));
		$row = sql("select journal_id from journal where zid = ? and topic = ? order by publish_time desc limit $item_start, $items_per_page", $zid, $topic);
	} else {
		list($item_start, $page_footer) = page_footer("journal", $items_per_page, array("zid" => $zid, "published" => 1, "topic" => $topic));
		$row = sql("select journal_id from journal where zid = ? and published = 1 and topic = ? order by publish_time desc limit $item_start, $items_per_page", $zid, $topic);
	}
	for ($i = 0; $i < count($row); $i++) {
		print_journal($row[$i]["journal_id"]);
	}

	writeln($page_footer);
}
end_main();

print_footer();
