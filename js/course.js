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
var course={
  "ID": 1,
  "Name": "COMP 550: Algorithms and analysis",
  "Description": "Learn about algorithms AND analysis of them!"
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
//var fakeJSON = $.parseJSON(course);
$(document).ready(function(){
    console.log($(location).attr('href'));
    var id = getParameterByName("id", $(location).attr('href'));
    console.log(course);
    console.log(questions);
    var i =0;
    $.ajax({
	url:'server-side/courses.php/'+id,
	async:false,
	dataType:"json",
	success:function(data, textStatus, jqXHR){
		console.log(data);
		course = data;
	},
        error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
                if(errorThrown == "Unauthorized"){
                        window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
                }
        }


    });
    questions = [];
    $.ajax({
	url:'server-side/questions.php',
	dataType:"json",
	async:false,
	success: function(data, textStatus, jqXHR){
		for(i=0; i<data.length; i++){
			var jsonRull = $.parseJSON(data[i]);
			jsonRull.course = $.parseJSON(jsonRull.course);
			console.log(jsonRull);
			if(jsonRull.course != null){
			if(jsonRull.course.id == id){
			jsonRull.professor = $.parseJSON(jsonRull.professor);
			jsonRull.user = $.parseJSON(jsonRull.user);
			questions.push(jsonRull);
			}
			}
		}
	}
    });
    var html="";
    html+="<h2>"+course.name+"</h2>";
    html+="<br/>Brief description of the course: "+course.description;       
    html+=".";
        
    $('#course').html(html);
    html="";
    for (i=0;i<questions.length;i++) {
      html+="<div class='question' id='question"+questions[i].id+"'><a href='questionDetail.html?id="+questions[i].id+"'>"+questions[i].text+"</a>";
        html+="<br/>Asked by "+questions[i].user.display_name;
	        if(questions[i].professor != null || questions[i].course!=null || (questions[i].semester !=null && questions[i].semester != "")){
        html+="<br/>In " ;
        if (questions[i].professor != null) {
            html+= questions[i].professor.name + "'s ";
        }
        if(questions[i].course != null){
            html+= questions[i].course.name;
        }
        html+= " course";
        if (questions[i].semester != null && questions[i].semester != "") {
            html+= " during " +questions[i].semester;
        }
        }
 
    }
    $('#questionList').html(html);
});
