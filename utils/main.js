$(document).ready(function(){
    $("#gfx3_execute").click(function(){
      $.post('execute.php', 
		{ code: $("#gfx3_code").val()},
		function(data) {
		  $('#gfx3_generatedcode').html(data);
		});
    });
  });
