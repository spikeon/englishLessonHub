$(window).load(function() {
	var availctr = 0;
	$('#availabilityadmin').each(function(){
		var $admin = $(this);
		$admin.find('.removeavail').click(function(){
			$(this).parents('.row').first().remove();
		});
		$admin.find('.newavail').click(function(){
			var $new = $admin.find('.availtemplate').clone(true,true).show().removeClass('availtemplate');
			availctr++;
			$new.find('.day').attr('name', 'new['+availctr+'][day]');
			$new.find('.start').attr('name', 'new['+availctr+'][start]');
			$new.find('.end').attr('name', 'new['+availctr+'][end]');
			$admin.append($new);
		});

	});
});
