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
var professor={
  "ID": 3,
  "Name": "Kevin Jeffay",
  "Bio": "Sample Bio"
};
var questions=[{
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
//var fakeJSON = $.parseJSON(professor);
$(document).ready(function(){
    var id = getParameterByName("id", $(location).attr('href'));
    console.log(professor);
    console.log(questions);
    $.ajax({
	url:"server-side/professors.php/"+id,
	dataType:"json",
	async:false,
	success:function(data, textStatus, jqXHR){
		console.log(data);
		professor = data;
	},
        error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
                if(errorThrown == "Unauthorized"){
                        window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
                }
        }

    });
    var i=0;
    questions = [];
    $.ajax({
	url:"server-side/questions.php",
	dataType:"json",
	async:false,
	success:function(data,textStatus, jqXHR){
		for(i=0; i<data.length; i++){
			var jsonRull = $.parseJSON(data[i]);
			jsonRull.professor = $.parseJSON(jsonRull.professor);
			if(jsonRull.professor != null){
			if(jsonRull.professor.id == id){
				jsonRull.course = $.parseJSON(jsonRull.course);
				jsonRull.user = $.parseJSON(jsonRull.user);
				questions.push(jsonRull);
			}
			}
		}
	}
    });
    var html="";
    html+="<h2>"+professor.name+"</h2>";
    html+="<br/>Brief description of the professor: "+professor.bio;       
    html+=".";
        
    $('#professor').html(html);
    html="";
    for (i=0;i<questions.length;i++) {
      html+="<div class='question' id='question"+questions[i].id+"'><a href='questionDetail.html?id="+questions[i].id+"'>"+questions[i].text+"</a>";
        html+="<br/>Asked by "+questions[i].user.display_name;

	var myQ = questions[i];
	if(myQ.professor != null || myQ.course != null || (myQ.semester != null && myQ.semester != "")){
        html+="<br/>In " ;
        //CHange conditionals to existence of professor, professor, and semester. Will be slightly more complicated.
        if (myQ.professor != null) {
            html+= questions[i].professor.name + "'s";
        }
        if(myQ.course != null){
            html+= questions[i].course.name;
        }
	html+= " course"
        if (myQ.semester != null && myQ.semester != "") {
            html+= " during " +questions[i].semester;
        }
	}
        
        
        html+=".</div>";
    }
    $('#questionList').html(html);
});
