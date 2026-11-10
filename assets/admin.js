/* Image picker for Denis B fields. */
(function ($) {
	$(document).on('click', '.denisb-image-pick', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.denisb-image');
		var frame = wp.media({ title: wp.media.view.l10n.chooseImage || 'Choose image', multiple: false, library: { type: 'image' } });
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			var url = (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);
			wrap.find('input').val(a.id);
			wrap.find('img').attr('src', url).show();
		});
		frame.open();
	});
	$(document).on('click', '.denisb-image-clear', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.denisb-image');
		wrap.find('input').val('');
		wrap.find('img').attr('src', '').hide();
	});
})(jQuery);
