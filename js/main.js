/*
Nicholas Osborne
*/

var query = "";

$(document).ready(function () {

	//Change thedefault submit	function for the search form
	$('form#search_form').submit(function () {
		search();
		return false;
	});

	setInterval(checkautoupdate, 3000);

});

//Auto searching if the user has typed in new characters into the search input field
function checkautoupdate() {
	if (($('#query_value').val() != "") && ($('#query_value').val() != query)) {
		search();
		query = $('#query_value').val();
	}
}

//Ajax Search Function
function search() {
	query = $('#query_value').val();
	
	if(query != ''){
	
		$('#submit_button').fadeOut(function(){
			$('#loader').css('display','inline');
		});
	
		$.post("ajax.php", {"function": "search","query": query},function (data) {
			$('#loader').css('display','none');
			$('#submit_button').fadeIn();
			if (data.status == "ok") {
				$('#results_area').html(data.content);
			} else if (data.status == "no_results") {
				$('#results_area').html('<span class="no_results">No results found</span>');
			}
		}, "json");

	}
}