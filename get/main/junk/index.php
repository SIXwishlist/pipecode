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

require_editor();

$junk = true;

print_header("Junk");
beg_main();

writeln('<h1>Junk</h1>');

$row = sql("select comment_vote.comment_id, count(*) as votes, article_id, subject, edit_time, body, junk_status, comment.zid from comment_vote inner join comment on comment_vote.comment_id = comment.comment_id where reason = 'Spam' and junk_status = 0 group by comment_id");
if (count($row) == 0) {
	writeln('<p>No unmarked junk comments</p>');
} else {
	beg_form();
	for ($i = 0; $i < count($row); $i++) {
		print_comment($row[$i], true);
	}

	box_two('<a href="?default=spam">Default to Spam</a>', "Save");
	end_form();
}

box_center('<a href="anonymous">Show All Anonymous</a>');

end_main();
print_footer();
