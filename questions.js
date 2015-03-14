
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
//var fakeJSON = $.parseJSON(jsonObj);
$(document).ready(function(){
    console.log(jsonObj);
    var i=0;
    var html="";
    for (i=0; i<jsonObj.length; i++) {
        html+="<div class='question' id='question"+jsonObj[i].ID+"'><h3><a href='questionDetail.html?id="+jsonObj[i].ID+"'>"+jsonObj[i].Question_Text+"</a></h3>";
        html+="<br/>Asked by "+jsonObj[i].User.Display_Name;
        html+="<br/>In " ;
        //CHange conditionals to existence of professor, course, and semester. Will be slightly more complicated.
        if (true) {
            html+= jsonObj[i].Professor + "'s ";
        }
        if(true){
            html+= jsonObj[i].Course.Name + " course";
        }
        if (true) {
            html+= " during " +jsonObj[i].Semester;
        }
        
        
        html+=".</div>";
        
    }
    $('#questions').html(html);
});