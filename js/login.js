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
function createUser(){
            $('#post').dialog({
                        title:"New Account",
                        height: 600,
                        width:600,
                        buttons: {
                                    "Close": function() {
                                                $( this ).dialog( "close" );
                                    },
                                    "Submit": function() {
                                        console.log($('#myUser').serialize());
if($('#pw1').val() == $('#pw2').val()){
    $.ajax({
        url:"server-side/users.php",
        type:"POST",
        async:false,
        data:$('#myUser').serialize(),
        success:function(){
	//e.stopPropagation();
	//e.preventDefault();
	$.ajax('server-side/login.php',
	       {type: 'GET',
		data: {username:$('#uname2').val(), password: $('#pw2').val()},
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
        },
        error:function(jqXHR, textStatus, errorThrown){
                if(errorThrown.message == "Unexpected token Q"){
                        //location.reload();
                }
                console.log(errorThrown);
        }
    });
}
else{
	alert("Your passwords don't match!");
}
                                    }
                        }           
            });
}

$(document).ready(function () {
    if(getParameterByName('status', $(location).attr('href')) == 'login'){
	alert("Please login before attemting to access CS4US");
    }
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

    $('#secret_form').on('submit', function (e) {
	e.stopPropagation();
	e.preventDefault();

	$.ajax('secret.php',
	       {type: 'GET',
		cache: false,
		success: function (data, status, jqxhr) {
		    alert(data);
		},
		error: function(jqxhr, status, error) {
		    alert("Couldn't get secret");
		}
	       });
    });
});
