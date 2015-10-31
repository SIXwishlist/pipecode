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

require_admin();

print_header("Create Poll");
print_main_nav("poll");
beg_main("cell");

writeln('<h1>Create Poll</h1>');

beg_form();

beg_tab();
print_row(array("caption" => "Question", "text_key" => "question"));
print_row(array("caption" => "Type", "option_key" => "type_id", "option_keys" => array(1, 2, 3), "option_list" => array("Multiple Choice", "Approval Voting", "Borda Count")));
end_tab();

$li = '<li><div class="icon-16 vsort-16" title="Drag to Reorder"></div><div><input type="text" name="answer[]" value=""></div><div><a class="icon-16 minus-16" href="javascript:remove_answer()">Remove</a></div></li>';

writeln('<h2>Answers</h2>');
writeln('<ul id="sortable" class="poll-sortable">');
writeln($li);
writeln($li);
writeln($li);
writeln('</ul>');

box_right('<a class="icon-16 plus-16" href="javascript:add_answer()">Add</a>');
box_right("Publish");

?>
<script type="text/javascript">

function add_answer()
{
	$("#sortable").append('<?= $li ?>');
}


function remove_answer()
{
	// FAKE
}


$("#sortable").sortable();

$('.minus-16').live('click', function() {
	$(this).closest('li').remove();
});

</script>
<?

end_form();
end_main();
print_footer();
