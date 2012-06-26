$(function() {
	$("#sortable").sortable({
		placeholder: 'ui-state-highlight',
		handle: $('.i-move'),
		axis: 'y',
		/*cursorAt: 'top',*/
		delay: 200,
		revert: true,
		update: function(event, ui) {
			var result = $('#sortable').sortable('toArray');
			var id = ui['item'][0]['id'];
			var index = jQuery.inArray(id,result);
			$('#tx-sort-input-sort-uid').val(id.split('_')[1]);
			$('#tx-sort-input-sort-sort').val(index);
			$('#tx-sort-form').ajaxSubmit();
		}
	});
});