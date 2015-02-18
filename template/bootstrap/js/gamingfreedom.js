// run js code after html page loaded
$(document).ready(function() {	
	
	
	
	$('.modalButton').click(function() {
		var myhref = $(this).attr('href');
		var mytarget = $(this).attr('target');
		$.get(mytarget, function(data) {
			$(myhref).html(data);
			}
	);});
	
	
	$(".static-rating").jRating({
		isDisabled : true,
		length : 10,
		decimalLength : 0,
		rateMax : 100
	});
		
	$(".dynamic-rating").jRating({
		isDisabled : false,
		length : 10,
		decimalLength : 0,
		rateMax : 100,
		onSuccess : function(){ alert('Success : your rate has been saved!'); },
		onError : function(){ alert('Error : some error occured registering your vote, try again!'); }
	});

});
