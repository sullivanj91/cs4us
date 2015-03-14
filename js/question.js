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
var jsonObj={
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
            };
var comments=[{
  "ID": 1,
  "Question_ID": 1,
  "User":{
    "ID": 2,
    "Display_Name": "Jeremy"
  },
  "Text": "Yeah, I know right?!"
},
{
  "ID": 6,
  "Question_ID": 1,
  "User":{
    "ID": 3,
    "Display_Name": "Ben"
  },
  "Text": "I couldn't believe it!"
}
];
var id;
function edit(){
	$('#submission').css('display', 'block');
}
function update(){
	alert('subbmitting!');
	console.log({text:$('#newText').val(), course: jsonObj.course.id, user:jsonObj.user.id, semester: jsonObj.semester, professor:jsonObj.professor});
	$.ajax({
		url:'server-side/questions.php/'+id,
		type:"POST",
		data:{text:$('#newText').val(), course: jsonObj.course.id, user:jsonObj.user.id, semester: jsonObj.semester, professor:jsonObj.professor.id},
		success:function(){
			alert('success!');
			//location.reload();
		},
        error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
                if(errorThrown == "Unauthorized"){
                        window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
                }
        }
	});
}
//var fakeJSON = $.parseJSON(jsonObj);
$(document).ready(function(){
    id =(getParameterByName("id", $(location).attr('href')))
    console.log(jsonObj);
    console.log(comments);
    var i=0;
    var html="";

    $.ajax({
	url:"server-side/questions.php/"+id,
	dataType:"json",
	async:false,
	success:function(data, textStatus, jqXHR){
		jsonObj =data;
		jsonObj.professor = $.parseJSON(jsonObj.professor);
		jsonObj.course = $.parseJSON(jsonObj.course);
		jsonObj.user = $.parseJSON(jsonObj.user);
	}
    });
     var uid = -1;
     $.ajax({
	url:"server-side/getUID.php",
	dataType:"json",
	async:false,
	success:function(data, textStatus, jqXHR){
		uid= ($.parseJSON(data));
	}
    });
    comments=[];
    $.ajax({
	url:"server-side/comments.php/"+id,
	dataType:"json",
	async:false,
	success:function(data){
		console.log(data);
		for (i=0; i<data.length; i++) {
		  var comment = $.parseJSON(data[i]);
		  comment.user = $.parseJSON(comment.user);
		  comments[i] = comment;
		}

	}
    });

    html+="<h2>"+jsonObj.text+"</h2>";
    if(uid == jsonObj.user.id){
	html+="<button style='display:inline;' value='Edit' onclick='edit()'>Edit</button>";
    }
    html+="<br/>Asked by "+jsonObj.user.display_name;

        if(jsonObj.professor != null || jsonObj.course!=null || (jsonObj.semester !=null && jsonObj.semester != "")){
        html+="<br/>In " ;
        if (jsonObj.professor != null) {
            html+= jsonObj.professor.name + "'s ";
        }
        if(jsonObj.course != null){
            html+= jsonObj.course.name;
        }
        html+= " course";
        if (jsonObj.semester != null && jsonObj.semester != "") {
            html+= " during " +jsonObj.semester;
        }
        }       
    html+=".";
        
    $('#question').prepend(html);
    html="";
    if(comments.length == 0){
        html="No comments yet, click the button to add one!";
    }
    else{
    for (i=0;i<comments.length;i++) {
      html+="<div class='comment' id='comment'"+comments[i].id+"'>";
      html+="Comment by " + comments[i].user.display_name;
      html+="<br/>"+comments[i].comment_text;
      html+="</div>";
    }
    }
    $('#comments').html(html);
});
function newComment(){
        $('#newComment').toggleClass('hideMe');
}
function submitComment(){
        var uid;
        $.ajax({
                url:"server-side/getUID.php",
                async:false,
                dataType:"json",
                success:function(data){
                        uid=data;
                }
        });
        $.ajax({
                url:"server-side/comments.php/",
                type:"POST",
                data:{comment_text:$('#commentText').val(), user_id: uid, question_id:jsonObj.id},
                success:function(){
                        location.reload();
                }
        });
}
