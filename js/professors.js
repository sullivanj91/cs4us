var professors=[{
  "ID": 1,
  "Name": "Ketan Meyer-Patel",
  "Bio": "Sample Bio"
},
{
  "ID": 2,
  "Name": "Mike Reiter",
  "Bio": "Sample Bio"
},
{
  "ID": 3,
  "Name": "Kevin Jeffay",
  "Bio": "Sample Bio"
},
];
//var fakeJSON = $.parseJSON(jsonObj);
$(document).ready(function(){
    //console.log(getParameterByName("id", "http://www.cs.unc.edu/Professors/comp426-f13/snydere/cs4us/questionDetail.html?id=1"))
    console.log(professors);
    var i=0;
    var html="";
    professors = [];
    $.ajax({
	url:"server-side/professors.php",
	async:false,
	dataType:"json",
	success:function(data,textStatus, jqXHR){
		console.log(data);
		for(i=0; i<data.length; i++){
			professors[i] = $.parseJSON(data[i]);
		}
		console.log(professors);
	},
        error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
                if(errorThrown == "Unauthorized"){
                        window.location = 'http://wwwp.cs.unc.edu/Courses/comp426-f13/snydere/cs4us/login.html?status=login';
                }
        }
    });
    $.ajax({
	url:"server-side/courses.php/1",
	async:false,
	dataType:"json",
	success:function(data,textStatus,jqXHR){
		console.log(data);
	}
    });
    html="";
    for (i=0;i<professors.length;i++) {
      html+="<div class='professor' id='professor'"+professors[i].id+"'>";
      html+="<h3><a href='professorDetail.html?id="+professors[i].id+"'>" + professors[i].name+"</a></h3>";
      html+="</div>";
    }
    $('#professors').html(html);
});
