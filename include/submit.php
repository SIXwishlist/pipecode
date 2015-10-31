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

function print_submit_box($title, $dirty_body, $body, $topic_id, $preview)
{
	global $auth_user;
	global $auth_zid;
	global $protocol;
	global $server_name;

	print_header("Submit Story");
	print_main_nav("pipe");
	beg_main("cell");

	if ($preview) {
		writeln('<h1>Preview</h1>');
		writeln('<p>Check your links before you post!</p>');

		$story["zid"] = $auth_zid;
		$story["time"] = time();
		$topic = db_get_rec("topic", $topic_id);
		$a["body"] = $body;
		$a["title"] = $title;
		$a["info"] = content_info($story, $topic);
		$a["image"] = content_image($topic);
		$a["view"] = "<b>0</b> comments";
		print_content($a);
	}

	beg_form();
	writeln('<div class="dialog-title">Submit Story</div>');
	writeln('<div class="dialog-body">');

	writeln('<table class="fill" style="padding: 0px">');
	writeln('	<tr>');
	writeln('		<td style="width: 80px">Title</td>');
	writeln('		<td colspan="2"><input name="title" type="text" value="' . $title . '" required></td>');
	writeln('	</tr>');
	writeln('	<tr>');
	writeln('		<td style="width: 80px">Topic</td>');
	writeln('		<td colspan="2">');
	writeln('			<select name="topic_id">');
	$topics = db_get_list("topic", "topic");
	$k = array_keys($topics);
	for ($i = 0; $i < count($topics); $i++) {
		$topic = $topics[$k[$i]];
		if ($topic["topic_id"] == $topic_id) {
			writeln('				<option value="' . $topic["topic_id"] . '" selected="selected">' . $topic["topic"] . '</option>');
		} else {
			writeln('				<option value="' . $topic["topic_id"] . '">' . $topic["topic"] . '</option>');
		}
	}
	writeln('			</select>');
	writeln('		</td>');
	writeln('	</tr>');
	writeln('	<tr>');
	writeln('		<td style="width: 80px; vertical-align: top; padding-top: 12px">Story</td>');
	writeln('		<td colspan="2"><textarea name="story" style="height: 200px" required>' . $dirty_body . '</textarea></td>');
	writeln('	</tr>');
	writeln('	<tr>');
	if ($auth_zid === "") {
		$question = captcha_challenge();
		writeln('		<td>Captcha</td>');
		writeln('		<td><table><tr><td>' . $question . '</td><td><input name="answer" type="text" style="margin-left: 8px; width: 100px"></td></tr></table></td>');
		writeln('		<td class="right"><input type="submit" value="Submit"> <input name="preview" type="submit" value="Preview"></td>');
	} else {
		writeln('		<td colspan="3" class="right"><input type="submit" value="Submit"> <input name="preview" type="submit" value="Preview"></td>');
	}
	writeln('	</tr>');
	writeln('</table>');
	writeln('</div>');

	end_form();
	end_main();
	print_footer();
}

