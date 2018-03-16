function updateView(id){
    if(document.curID != undefined){
        document.getElementById(document.curID).style.display = "none";
    }
    if(id == "overview"){
    	document.getElementById("overviewBtn").style.display = "none";
       	document.getElementById("legend").style.display = "block";
    }else{
    	document.getElementById("overviewBtn").style.display = "block";
        document.getElementById("legend").style.display = "none";
    }
    var types = ['overview', 'medicaid','sehp','pic'];
    for(type in types){
        typestr = types[type]+'PDFBtn';
        document.getElementById(typestr).style.display = 'none';
    }
    document.getElementById(id+'PDFBtn').style.display = 'block';

    document.getElementById(id).style.display = "block";
    document.curID = id;
    //console.log(document.pdfForm.URL.value);
}
