var courses=[{
  "ID": 1,
  "Name": "COMP 550: Algorithms and analysis",
  "Description": "Learn about algorithms AND analysis of them!"
},
{
  "ID": 2,
  "Name": "COMP 426: Web programming",
  "Description": "How to www"
},
{
  "ID": 3,
  "Name": "COMP 535: Introduction to web security",
  "Description": "Keep it locked down."
},
];
//var fakeJSON = $.parseJSON(jsonObj);
$(document).ready(function(){
    //console.log(getParameterByName("id", "http://www.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/questionDetail.html?id=1"))
    var i=0;
    courses = [];
    $.ajax({
	url:"server-side/courses.php",
	dataType:"json",
	async:false,
	success:function(data,textStatus,jqXHR){
		for(i=0;i<data.length;i++){
			var course = $.parseJSON(data[i]);
			courses[i] = course;
		}
		console.log(data);
	},
        error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
                if(errorThrown == "Unauthorized"){
                        window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
                }
        }
    });

    console.log(courses);
    var html="";
    html="";
    for (i=0;i<courses.length;i++) {
      html+="<div class='course' id='course'"+courses[i].id+"'>";
      html+="<h3><a href='courseDetail.html?id="+courses[i].id+"'>" + courses[i].name+"</a></h3>";
      html+="</div>";
    }
    $('#courses').html(html);
});
