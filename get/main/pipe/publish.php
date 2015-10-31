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
$clean_body = $pipe["body"];
$dirty_body = dirty_html($clean_body);

print_publish_box($pipe["pipe_id"], $pipe["topic_id"], $pipe["keywords"], $pipe["title"], $clean_body, $dirty_body, $pipe["author_zid"]);
