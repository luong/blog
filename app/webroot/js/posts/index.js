$(function() {
	$('#PostIndexForm').submit(function(e) {
		location = '/posts/index/keyword:' + $(this).find('#PostKeyword').val();
		return false;
	});
});