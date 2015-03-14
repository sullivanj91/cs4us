function getParameterByName( name,href )
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( href );
  if( results == null )
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}
var uid;
function changePw(){
	if($('#pw1').val() == $('#pw2').val()){
		$.ajax({
			url:"server-side/users.php/"+uid,
			type:"POST",
			async:false,
			data:{password:$('#pw1').val()},
			success:function(data){alert('Password Successfully Changed!')}
		});
	}
	else{
		alert("Your passwords don't match!");
	}
}
function submit(){
    $.ajax({
        url:"server-side/users.php/"+uid,
        type:"POST",
        async:false,
        data:$('#myUser').serialize(),
        success:function(){
	//e.stopPropagation();
	//e.preventDefault();
	/*$.ajax('server-side/login.php',
	       {type: 'GET',
		data: {username:$('#uname2').val(), password: $('#pw2').val()},
		cache: false,
		success: function () {
		    alert('Update Successfull');
		    location.refresh();
		},
		error: function (jqxhr, status, error) {
		    alert(error);
		    //alert('Login Failed');
		    }
	       });*/
		alert('update successful!');
		location.reload();
        },
        error:function(jqXHR, textStatus, errorThrown){
                if(errorThrown.message == "Unexpected token Q"){
                        //location.reload();
                }
                console.log(errorThrown);
        }
    });
}

$(document).ready(function () {
    if(getParameterByName('status', $(location).attr('href')) == 'login'){
	alert("Please login before attemting to access CS4US");
    }
    $.ajax({
	url:"server-side/getUID.php",
	dataType:"json",
	async:false,
	success:function(data){
		uid = data;
	}
    });
    var user;
    $.ajax({
	url:"server-side/users.php/"+uid,
	dataType:"json",
	async:false,
	success:function(data){
		user=data;
		console.log(data);
	}
    });

	$('#uname').val(user.username);
	$('#dname').val(user.display_name);
	$('#email').val(user.email);
	
    $('#login_form').on('submit', function (e) {
	e.stopPropagation();
	e.preventDefault();
	$.ajax('server-side/login.php',
	       {type: 'GET',
		data: {username:$('#uname').val(), password: $('#pw').val()},
		cache: false,
		success: function () {
		    alert('Login Successful');
		    window.location= 'questions.html';
		},
		error: function (jqxhr, status, error) {
		    alert(error);
		    //alert('Login Failed');
		    }
	       });
    });

});
