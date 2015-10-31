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

var comments;
var reasons = new Array("Normal", "Offtopic", "Flamebait", "Troll", "Redundant", "Insightful", "Interesting", "Informative", "Funny", "Overrated", "Underrated", "Spam");
var protocol = get_protocol();
var server_name = get_server_name();
var current;


// jquery checkbox toggle on table row click
$(document).ready(function() {
	$('.hover').click(function(event) {
		if (event.target.type !== 'checkbox') {
			$(':checkbox', this).trigger('click');
		}
	});
});


function get_protocol()
{
	var a;

	a = document.URL.split(":");

	return a[0];
}


function get_server_name()
{
	var a;

	a = document.URL.split("/");
	a = a[2].split(".");

	return a[a.length - 2] + "." + a[a.length - 1];
}


function moderate(e, comment_id)
{
	var s;

	data = "reason=" + e.value;
	$.post("/moderate/" + comment_id, data, function(data) {
		if (data.indexOf("error:") != -1) {
			alert(data);
		} else {
			var json = $.parseJSON(data);
			s = json.score;
			if (json.reason != "") {
				s += ", " + json.reason;
			}
			$("#score_" + json.code).html(s);
		}
	});
}


function reply(comment_code)
{
	var s;
	var subject;
	var re = false;

	if ($("#reply_" + comment_code).is(":visible")) {
		$("#reply_" + comment_code).hide();
	} else {
		if ($("#reply_" + comment_code).html() == "") {
			subject = $("#subject_" + comment_code).html();
			if (subject.length >= 4) {
				if (subject.substring(0, 4) == "Re: ") {
					re = true;
				}
			}
			if (!re) {
				subject = "Re: " + $("#subject_" + comment_code).html();
			}

			s = "<div class=\"dialog-title\">Post Comment</div>\n";
			s += "<div class=\"dialog-body\">\n";
			s += "<table>\n";
			s += "	<tr>\n";
			s += "		<td>Subject</td>\n";
			s += "		<td colspan=\"2\"><input id=\"reply_subject_" + comment_code + "\" type=\"text\" value=\"" + subject + "\"></td>\n";
			s += "	</tr>\n";
			s += "	<tr>\n";
			s += "		<td>Comment</td>\n";
			s += "		<td colspan=\"2\"><textarea id=\"reply_body_" + comment_code + "\"></textarea></td>\n";
			s += "	</tr>\n";
			s += "	<tr>\n";
			s += "		<td></td>\n";
			s += "		<td><label><input id=\"reply_coward_" + comment_code + "\" type=\"checkbox\">Post Anonymously</label></td>\n";
			s += "		<td><input type=\"button\" value=\"Post\" onclick=\"post_reply('" + comment_code + "')\"></td>";
			s += "	</tr>\n";
			s += "</table>\n";
			s += "</div>\n"

			$("#reply_" + comment_code).html(s);
		}
		$("#reply_" + comment_code).show();

		if (wysiwyg_enabled) {
			var editor = CKEDITOR.replace("reply_body_" + comment_code,
			{
				resize_enabled: false,
				enterMode: CKEDITOR.ENTER_BR,
				toolbar :
				[
					["Bold","Italic","Underline","Strike","Subscript","Superscript"],
					["NumberedList","BulletedList","Blockquote"],
					["Link","Unlink"]
				]
			});
			editor.on('change', function(evt) {
				editor.updateElement();
			});
		}

		$('#reply_' + comment_code)[0].scrollIntoView(false);
	}
}


function post_reply(comment_code)
{
	var subject;
	var body;
	var coward;
	var data;
	var s;

	current = comment_code;
	subject = $("#reply_subject_" + comment_code).val();
	subject = encodeURIComponent(subject);
	subject = subject.replace("%20", "+");
	body = $("#reply_body_" + comment_code).val();
	body = encodeURIComponent(body);
	body = body.replace("%20", "+");
	coward = $("#reply_coward_" + comment_code).is(':checked');

	data = "subject=" + subject + "&body=" + body + "&coward=" + coward;
	$.post("/reply/" + comment_code, data, function(json) {
		if (data.indexOf("error:") != -1) {
			alert(data);
		} else {
			json = $.parseJSON(json);
			s = render(json);
			$("#reply_" + current).before(s);
			$("#reply_" + current).html("");
			$("#reply_" + current).hide();
		}
	});
}


function update_hide_slider()
{
	hide_value = $("#slider_hide").prop("value");
	$("#label_hide").html(hide_value);
	if (expand_value < hide_value) {
		expand_value = hide_value;
		$("#slider_expand").prop("value", expand_value);
		$("#label_expand").html(expand_value);
	}
	render_page();
}


function update_expand_slider()
{
	expand_value = $("#slider_expand").prop("value");
	$("#label_expand").html(expand_value);
	if (hide_value > expand_value) {
		hide_value = expand_value;
		$("#slider_hide").prop("value", hide_value);
		$("#label_hide").html(hide_value);
	}
	render_page();
}


function get_comments(type, short_code)
{
	var uri;

	uri = "/" + type + "/" + short_code + "/comments";
	$.getJSON(uri, function(json) {
		comments = json;
		render_page();
	});
}


function render_page()
{
	s = render(comments);
	$('#comment_box').html(s);
}


function show_comment(comment_id)
{
	$('#collapse_' + comment_id).hide();
	$('#title_' + comment_id).show();
	$('#subtitle_' + comment_id).show();
	$('#body_' + comment_id).show();
}


function render(c)
{
	var i;
	var s = "";
	var d;
	var t;
	var hide = false;
	var collapse = false;
	var by;
	var seen;
	var score;

	if (c === undefined) {
		return "";
	}

	if (c.zid !== undefined) {
		d = new Date(c.time * 1000);
		t = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
		score = c.score;
		if (c.reason != "") {
			score += ", " + c.reason;
		}

		if (c.score < hide_value) {
			hide = true;
		} else {
			if (c.score < expand_value) {
				collapse = true;
			}
		}
		//console.log("code [" + c.code + "] score [" + c.score + "] hide [" + hide + "] collapse [" + collapse + "] hide_value [" + hide_value + "] expand_value [" + expand_value + "]");
		if (c.zid == "") {
			by = "Anonymous Coward";
		} else {
			by = "<a href=\"" + protocol + "://" + c.zid.replace("@", ".") + "/\">" + c.zid + "</a>";
		}

		if (hide) {
			s += "<div>";
		} else {
			if (c.junk > 0) {
				seen = "h3 class=\"color-junk\"";
			} else if (c.time > last_seen) {
				seen = "h3 class=\"color-new\"";
			} else {
				seen = "h3 class=\"color-old\"";
			}
			s += "<article class=\"comment\">";
			if (collapse) {
				s += "<h5 id=\"collapse_" + c.code + "\" onclick=\"show_comment('" + c.code + "')\"><b>" + c.subject + ":</b> " + c.body + "</h4>";

				s += "<" + seen + " id=\"title_" + c.code + "\" style=\"display: none\"><span id=\"subject_" + c.code + "\">" + c.subject + "</span> (Score: <span id=\"score_" + c.code + "\">" + score + "</span>)</" + seen + ">";
				s += "<h4 id=\"subtitle_" + c.code + "\" style=\"display: none\">by " + by + " on " + t + " (<a href=\"" + protocol + "://" + server_name + "/" + c.code + "\">#" + c.code + "</a>)</h3>";
				s += "<div class=\"comment-outline\">";
				s += "<div id=\"body_" + c.code + "\" style=\"display: none\">";
			} else {
				s += "<" + seen + " id=\"title_" + c.code + "\"><span id=\"subject_" + c.code + "\">" + c.subject + "</span> (Score: <span id=\"score_" + c.code + "\">" + score + "</span>)</" + seen + ">";
				s += "<h4 id=\"subtitle_" + c.code + "\">by " + by + " on " + t + " (<a href=\"" + protocol + "://" + server_name + "/" + c.code + "\">#" + c.code + "</a>)</h3>";
				s += "<div class=\"comment-outline\">";
				s += "<div id=\"body_" + c.code + "\">";
			}
			s += "<div class=\"comment-body\">" + c.body + "</div>";
			if (inline_reply) {
				s += "<footer><div><a href=\"javascript:reply('" + c.code + "')\">Reply</a>";
			} else {
				s += "<footer><div><a rel=\"nofollow\" href=\"" + protocol + "://" + server_name + "/post/" + c.code + "\">Reply</a>";
			}
			if (c.zid != auth_zid) {
				s += "<select name=\"s_" + c.code + "\" onchange=\"moderate(this, '" + c.code + "')\">";

				for (i = 0; i < reasons.length; i++) {
					if (reasons[i] == c.vote) {
						s += "<option selected=\"selected\">" + reasons[i] + "</option>";
					} else {
						s += "<option>" + reasons[i] + "</option>";
					}
				}
				s += "</select>";
			} else {
				s += "</div>";
				s += "<div>";
				s += "<a class=\"icon-16 notepad-16\" href=\"" + protocol + "://" + server_name + "/comment/" + c.code + "/edit\">Edit</a>";
			}
			s += "</div>";
			s += "</footer>";
			s += "</div>";
		}
	}

	for (i = 0; i < c.reply.length; i++) {
		s += render(c.reply[i]);
	}

	if (c.zid !== undefined) {
		s += "<div id=\"reply_" + c.code + "\" class=\"reply\" style=\"display: none\"></div>";
		s += "</div>";
		s += "</article>";
	}

	return s;
}


function stream_vote(code, value)
{
	data = "code=" + code + "&value=" + value;
	$.post("/vote", data, function(data) {
		if (data.indexOf("error:") != -1) {
			alert(data);
		} else {
			var json = $.parseJSON(data);
			$("#score_" + json.code).html(json.score);
			if (json.value == -1) {
				$('#plus_' + code).attr("class", "vote-button down-16");
				$('#plus_' + code).attr("title", "You Voted Down");
				$('#minus_' + code).attr("class", "vote-button undo-16");
				$('#minus_' + code).attr("title", "Undo Vote");
			} else if (json.value == 1) {
				$('#plus_' + code).attr("class", "vote-button up-16");
				$('#plus_' + code).attr("title", "You Voted Up");
				$('#minus_' + code).attr("class", "vote-button undo-16");
				$('#minus_' + code).attr("title", "Undo Vote");
			} else {
				$('#plus_' + code).attr("class", "vote-button plus-16");
				$('#plus_' + code).attr("title", "Vote Up");
				$('#minus_' + code).attr("class", "vote-button minus-16");
				$('#minus_' + code).attr("title", "Vote Down");
			}
		}
	});
}
