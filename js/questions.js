
var jsonObj=[{
            "ID": 1,
            "Question_Text": "Whaaaa?",
            "Course": {
                  "ID": 1,
                  "Name": "COMP 426",
                  "Description": "Silly Class"
              },
              "User":{
                  "ID": 1,
                  "Display_Name": "Evan"
              },
              "Semester": "Fall 2013",
              "Professor": "KMP"
            },
            {
            "ID": 2,
            "Question_Text": "How to things?",
            "Course": {
                  "ID": 2,
                  "Name": "COMP 535",
                  "Description": "Security"
              },
              "User":{
                  "ID": 1,
                  "Display_Name": "Evan"
              },
              "Semester": "Fall 2013",
              "Professor": "Mike Reiter"
            }];
var uid;
//var fakeJSON = $.parseJSON(jsonObj);
$(document).ready(function(){
    console.log(jsonObj);
    //lert($.session.get('userid'));
    $.ajax({
	url:"server-side/getUID.php",
	dataType:"json",
	success:function(data, textStatus, jqXHR){
		uid=($.parseJSON(data));
	}
    });
    var i=0;
	jsonObj = [];
    $.ajax({
	url:"server-side/questions.php",
	dataType:"json",
	async:false,
	success: function(data,textStatus, jqXHR){
		//console.log($.parseJSON(data[0]));
		var i=0;
		console.log(data);
		for(i=0; i<data.length; i++){
			var jsonRull= $.parseJSON(data[i]);
			jsonRull.course = $.parseJSON(jsonRull.course);
			jsonRull.professor = $.parseJSON(jsonRull.professor);
			jsonRull.user = $.parseJSON(jsonRull.user);
			jsonObj[i] = jsonRull
		}
		console.log(jsonRull);
	},
	error: function(jqXHR, textStatus, errorThrown){
		console.log(errorThrown);
		if(errorThrown == "Unauthorized"){
			window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
		}
	}
    });
    var html="";
    for (i=0; i<jsonObj.length; i++) {
        html+="<div class='question' id='question"+jsonObj[i].id+"'><h3><a href='questionDetail.html?id="+jsonObj[i].id+"'>"+jsonObj[i].text+"</a></h3>";
        html+="<br/>Asked by "+jsonObj[i].user.display_name;
        //CHange conditionals to existence of professor, course, and semester. Will be slightly more complicated.

	if(jsonObj[i].professor != null || jsonObj[i].course!=null || (jsonObj[i].semester !=null && jsonObj[i].semester != "")){
        html+="<br/>In " ;
        if (jsonObj[i].professor != null) {
            html+= jsonObj[i].professor.name + "'s ";
        }
        if(jsonObj[i].course != null){
            html+= jsonObj[i].course.name;
        }
	html+= " course";
        if (jsonObj[i].semester != null && jsonObj[i].semester != "") {
            html+= " during " +jsonObj[i].semester;
        }
        }
        
        html+=".</div>";
        
    }
    $('#questions').html(html);
    /*$.ajax({
	url:"server-side/questions.php",
	type:"POST",
	data:"text=Text&course=1&user=3&semester=Fall2013&professor=1",
	error:function(jqXHR, textStatus, errorThrown){
		console.log(errorThrown);
	}
    });*/
});
function postQuestion() {
	//autoload using user cookie
	$('#myUser').val(uid);
            $('#post').dialog({
                        title:"New Question",
                        height: 600,
                        width:600,
                        buttons: {
                                    "Close": function() {
                                                $( this ).dialog( "close" );
                                    },
                                    "Submit": function() {
					console.log($('#myQuestion').serialize());
    $.ajax({
	url:"server-side/questions.php",
	type:"POST",
	async:false,
	data:$('#myQuestion').serialize(),
	success:function(){
		location.reload();
	},
	error:function(jqXHR, textStatus, errorThrown){
		if(errorThrown.message == "Unexpected token Q"){
			location.reload();
		}
		console.log(errorThrown);
	}
    });
                                    }
                        }           
            });
}
function logout(){
	$.ajax({
		url:"server-side/logout.php",
		success:function(){
			window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=logout';

		}
	});
}
