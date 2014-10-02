$(function() {
	$('textarea').each(function() {
		CKEDITOR.replace(this.id, {
			contentsCss : '/css/ckeditor.css',
			height : '500px'
		});
	});
});