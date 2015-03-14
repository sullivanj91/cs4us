
function logout(){
	$.ajax({
		url:"server-side/logout.php",
		success:function(){
			window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=logout';

		}
	});
}
