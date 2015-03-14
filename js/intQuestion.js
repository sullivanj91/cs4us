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
var intQuestion={
            "ID": 1,
            "Question_Text": "Whaaaa?",
            "Company": "Fidelity Investments",
            "User":{
                  "ID": 1,
                  "Display_Name": "Evan"
            },
            "Year": "2013"
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
//var fakeJSON = $.parseJSON(intQuestion);
$(document).ready(function(){
    var id = (getParameterByName("id", $(location).attr('href')))
    console.log(intQuestion);
    console.log(comments);
    var i=0;
    var html="";
    $.ajax({
      url:"server-side/interview_questions.php/"+id,
      dataType:"json",
      async:false,
      success:function(data, textStatus, jqXHR){
        console.log(data);
        intQuestion = (data);
        intQuestion.user = $.parseJSON(intQuestion.user);
      },
        error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
                if(errorThrown == "Unauthorized"){
                        window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
                }
        }

    });
    comments=[];
    $.ajax({
      url:"server-side/interview_comments.php/"+id,
      dataType:"json",
      async:false,
      success:function(data, textStatus, jqXHR){
        console.log(data);
        for (i=0; i<data.length; i++) {
          var comment = $.parseJSON(data[i]);
          comment.user = $.parseJSON(comment.user);
          comments[i] = comment;
        }
      }
    });
    html+="<div class='question' id='question"+intQuestion.id+"'><h2>"+intQuestion.text+"</h2>";
    html+="<br/>Asked by "+intQuestion.user.display_name;
    html+="<br/>For " ;
    //CHange conditionals to existence of professor, course, and semester. Will be slightly more complicated.
    if (true) {
        html+= intQuestion.company;
    }
    if (true) {
        html+= " in " +intQuestion.year;
    }
    
    
    html+=".</div>";
        
    $('#question').html(html);
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
		url:"server-side/interview_comments.php/",
		type:"POST",
		data:{comment_text:$('#commentText').val(), user_id: uid, question_id:intQuestion.id},
		success:function(){
			location.reload();
		}
	});	
}
