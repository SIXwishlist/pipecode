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

include("clean.php");
include("story.php");
include("publish.php");

require_editor();

$pipe = item_request(TYPE_PIPE);
$zid = $pipe["author_zid"];

$title = clean_subject();
list($clean_body, $dirty_body) = clean_body(true, "story");
//$icon = http_post_string("icon", array("len" => 50, "valid" => "[a-z][0-9]-_"));
$keywords = http_post_string("keywords", array("required" => false, "len" => 100, "valid" => "[A-Z][a-z][0-9]+-_. "));
$keywords = strtolower($keywords);
$topic_id = http_post_int("topic_id");
$time = time();

if (http_post("publish")) {
	if ($pipe["closed"] == 1) {
		fatal("This pipe is already closed");
	}
	$pipe["closed"] = 1;
	$pipe["edit_zid"] = $auth_zid;
	db_set_rec("pipe", $pipe);

	$story = db_new_rec("story");
	$story["story_id"] = create_short(TYPE_STORY);
	$story["author_zid"] = $pipe["author_zid"];
	$story["body"] = $clean_body;
	$story["edit_time"] = $time;
	$story["edit_zid"] = $auth_zid;
	//$story["icon"] = $icon;
	$story["keywords"] = $keywords;
	$story["image_id"] = 0;
	$story["pipe_id"] = $pipe["pipe_id"];
	$story["publish_time"] = $time;
	$story["slug"] = clean_url($title);
	$story["topic_id"] = $topic_id;
	$story["title"] = $title;
	$story["tweet_id"] = 0;
	db_set_rec("story", $story);

	header("Location: /pipe/{$pipe["short_code"]}");
	die();
}

print_publish_box($pipe["pipe_id"], $topic_id, $keywords, $title, $clean_body, $dirty_body, $zid);
