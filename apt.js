var myVar = setInterval(function(){ myTimer() }, 1000);
$(window).load(function(){
	$('.msg').hide();
});
var checked_in = false;
var not_alone = false;
function check_partner(){

	if(!checked_in){
		$.getJSON(
			'ajax.php',
			{
				query : 'check_in',
				uid : $('div.info').data('tid'),
				cid	: $('div.info').data('cid'),
				type : $('div.info').data('type')
			},
			function(data){

			}
		);
		checked_in = true;
	}
	if(!not_alone) {
		$.getJSON(
			'ajax.php',
			{
				query: 'alone',
				uid: $('div.info').data('tid'),
				cid: $('div.info').data('cid'),
				type: $('div.info').data('type')
			},
			function (data) {
				if (data.alone) {
					$('div.info').hide();
					$('div.before').hide();
					$('div.after').hide();
					$('div.wait').show();
				}
				else {
					$('div.info').show();
					$('div.before').hide();
					$('div.wait').hide();
					$('div.after').hide();
					$('div.hide').show();
					not_alone = true;

				}
			}
		);

	}

}

function rate(){
	$('.rate').each(function(){
		for(var i=1; i <= 5; i++) {
			$(this).append($("<span class='glyphicon glyphicon-star-empty' data-value='"+i+"' style='color:#ccc;'></span>").hover(function(){
				$(this).removeClass('glyphicon-star-empty').addClass('glyphicon-star').css({'color':'gold'});
				$(this).prevAll().removeClass('glyphicon-star-empty').addClass('glyphicon-star').css({'color':'gold'});
			}, function(){
				$(this).removeClass('glyphicon-star').addClass('glyphicon-star-empty').css({'color':'#ccc'});
				$(this).prevAll().removeClass('glyphicon-star').addClass('glyphicon-star-empty').css({'color':'#ccc'});
			}).click(function(){
				$(this).parent().find('span').unbind('mouseenter mouseleave');
				$(this).removeClass('glyphicon-star-empty').addClass('glyphicon-star').css({'color':'gold'});
				$(this).prevAll().removeClass('glyphicon-star-empty').addClass('glyphicon-star').css({'color':'gold'});
				var value = $(this).data('value');
				$.getJSON(
					'ajax.php',
					{
						query : 'rate',
						uid : $('div.info').data('tid'),
						cid	: $('div.info').data('cid'),
						rating : value
					},
					function(data){

					}
				);



			}));
		}
		$(this).css({'font-size': '24px'});



	});
}
var finished = false;
function myTimer() {
	var starttime = $('div.info').data('starttime');
	var endtime = $('div.info').data('endtime');

	if(Date.now() < starttime){
		$('div.info').hide();
		$('div.before').show();

		var seconds = Math.round( (starttime - Date.now()) / 1000 );
		var minutes = Math.floor(seconds / 60);
		var hours = Math.floor(minutes / 60);
		var days = Math.floor(hours / 24);
		seconds = seconds - (60 * minutes);
		minutes = minutes - (60 * hours);
		hours = hours - ( 24 * days );

		$('.before .days').text( days );
		$('.before .hours').text( hours );
		$('.before .minutes').text( minutes );
		$('.before .seconds').text( seconds );
	}
	else if(Date.now() > endtime){
		if(!finished) {
			finished = true;
			$('div.info').hide();
			$('div.after').show();
			if ($('div.info').data('type') == 'teacher' && not_alone) {

				if($('div.info').data('free') * 1 != 1) {
					$('.after .message').text("Thank you for teaching! Your payment has been processed.");
					$.getJSON(
						'ajax.php',
						{
							query: 'payout',
							tid: $('div.info').data('tid'),
							cid: $('div.info').data('cid')
						},
						function (data) {

						}
					);
				}
				else $('.after .message').text("Thank you for teaching!");

			}
			if ($('div.info').data('type') == 'teacher' && !not_alone) {
				if($('div.info').data('free') * 1 != 1) {
					$('.after .message').text("Thank you for Waiting! It appears that the student has missed their lesson.  Your payment has been processed.");
					$.getJSON(
						'ajax.php',
						{
							query: 'payout',
							tid: $('div.info').data('tid'),
							cid: $('div.info').data('cid')
						},
						function (data) {

						}
					);
				}
				else $('.after .message').text("Thank you for Waiting! It appears that the student has missed their lesson.");
			}
			if ($('div.info').data('type') == 'student' && not_alone) {
				$('.after .message').html("Please rate your Lesson!<br><br><div style='text-align: center;' class='rate'></div></div>");
				rate();
			}
			if ($('div.info').data('type') == 'student' && !not_alone) {
				$('.after .message').text("We see the teacher didn't show up.  We are sorry for the inconvenience and have issued a refund to you and given the teacher a zero star rating for this lesson.");
				$.getJSON(
					'ajax.php',
					{
						query : 'rate',
						uid : $('div.info').data('tid'),
						cid	: $('div.info').data('cid'),
						rating : 0
					},
					function(data){

					}
				);
				if($('div.info').data('free') * 1 != 1) {
					$.getJSON(
						'ajax.php',
						{
							query: 'refund',
							txnid: $('div.info').data('txnid'),
							tid: $('div.info').data('tid'),
							cid: $('div.info').data('cid')
						},
						function (data) {

						}
					);
				}
			}
		}
	}
	else {
		check_partner();
	}


	if($('div.blackboard').length == 1){
		$.getJSON(
			'ajax.php',
			{
				query : 'get_blackboard',
				cid	: $('div.blackboard').data('cid'),
				lastchange : $('div.blackboard').data('lastchanged')
			},
			function(data){
				if(data.changed != false) {
					$('div.blackboard').data('lastchanged', data.data.timestamp);
					$('div.blackboard').html(data.data.content);
				}
			}
		);
	}else{

		if(! CKEDITOR.instances.blackboard.checkDirty()) return;
		$.post(
			'ajax.php?query=set_blackboard',
			{
				cid			: $('#blackboard').data('cid'),
				data		: CKEDITOR.instances.blackboard.getData()
			},
			function(data){
				console.log(data);
				CKEDITOR.instances.blackboard.resetDirty();
			},
			'json'
		);

	}
}