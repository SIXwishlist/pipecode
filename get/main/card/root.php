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

include("image.php");

$card = item_request(TYPE_CARD);

if ($auth_zid === "") {
	print_header("Card");
} else {
	print_header("Card", ["Share"], ["share"], [user_link($auth_zid) . "stream/share"]);
}
beg_main();
writeln('<h1>' . get_text("Card") . '</h1>');

print_card($card["card_id"], "large");
print_comments(TYPE_CARD, $card);

end_main();
print_footer();


