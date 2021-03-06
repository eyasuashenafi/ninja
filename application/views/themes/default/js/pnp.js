$(document).ready(function () {
	$('#iframe').load(function() {
		$(this).focus();
		$(this.contentDocument).find('.ui-widget-header tr').each(function () {
			$(this).append('<td align="right"><a href="#" class="default" title="Make default graph" alt="Make default graph"><img src="' + _site_domain + '/application/views/themes/default/icons/16x16/shield-ok.png"/></a></td>');
			$(this).find('.default').click(function() {
				var src = $(this).closest('div').next('div').find('img').attr('src');
				var match = src.match(/\?(.*)&view=(\d)&source=(\d)/);
				$.ajax(
					_site_domain + _index_page + '/ajax/pnp_default/',
					{
						data: {
							param: match[1],
							view: match[2],
							source: match[3]
						},
						complete: function () {
							$.jGrowl(_settings_msg, {header: _success_header});
						},
						type: 'POST'
					}
				);
				return false;
			});
		});
	});
});
