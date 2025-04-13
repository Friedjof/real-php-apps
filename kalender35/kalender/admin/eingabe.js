/* JavaScript Kalender-Eingabe */
aTag=new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[center]','[/center]','[right]','[/right]','[list]','[/list]','[list=o]','[/list]','[img]','[/img]','[url]','[/url]');

function fInsertTag(sItem,sTagB,sTagE){
 var txtarea=document.forms['kalEingabe'].elements['kal_F'+sItem];
 txtarea.focus();
 if(typeof document.selection!='undefined'){ //IE
  var range=document.selection.createRange();
  var insText=range.text;
  range.text=sTagB+insText+sTagE;
  range=document.selection.createRange();
  if(insText.length==0) range.move('character',-sTagE.length);
  else range.moveStart('character',sTagB.length+insText.length+sTagE.length);
  range.select();
 }else if(typeof txtarea.selectionStart!='undefined'){ //Geko
  var start=txtarea.selectionStart; var end=txtarea.selectionEnd; var pos;
  var insText=txtarea.value.substring(start,end);
  txtarea.value=txtarea.value.substr(0,start)+sTagB+insText+sTagE+txtarea.value.substr(end);
  if(insText.length==0) pos=start+sTagB.length;
  else pos=start+sTagB.length+insText.length+sTagE.length;
  txtarea.selectionStart=pos; txtarea.selectionEnd=pos;
 }else txtarea.value=txtarea.value+sTagB+sTagE; //sonstige Browser
}

function fFmt(sItem,nTag){
 fInsertTag(sItem,aTag[nTag],aTag[nTag+1]);
}

function fCol(sItem,sCol){
 if(!sCol) return;
 fInsertTag(sItem,'[color='+sCol+']','[/color]');
}

function fSiz(sItem,sSiz){
 if(!sSiz) return;
 fInsertTag(sItem,'[size='+sSiz+']','[/size]');
}

function fSelWt(bVal,nPeriode){
 if(bVal!=false) document.forms['kalEingabe'].elements['kal_Periode'][nPeriode].checked=true;
}