/* JavaScript Testfragen-Eingabe*/
var theSelection=false;
var clientPC=navigator.userAgent.toLowerCase();
var clientVer=parseInt(navigator.appVersion);
var is_ie=((clientPC.indexOf("msie")!=-1)&&(clientPC.indexOf("opera")==-1));
var is_nav=((clientPC.indexOf('mozilla')!=-1)&&(clientPC.indexOf('spoofer')==-1)&&(clientPC.indexOf('compatible')==-1)&&(clientPC.indexOf('opera')==-1)&&(clientPC.indexOf('webtv')==-1)&&(clientPC.indexOf('hotjava')==-1));
var is_moz=0;
var is_win=((clientPC.indexOf("win")!=-1)||(clientPC.indexOf("16bit")!=-1));
var is_mac=(clientPC.indexOf("mac")!=-1);

function mozWrap(txtarea,open,close){
 var selLength=txtarea.textLength; var selStart=txtarea.selectionStart; var selEnd=txtarea.selectionEnd;
 if(selEnd==1 || selEnd==2) selEnd=selLength;
 var s1=(txtarea.value).substring(0,selStart);
 var s2=(txtarea.value).substring(selStart,selEnd)
 var s3=(txtarea.value).substring(selEnd,selLength);
 txtarea.value=s1+open+s2+close+s3;
 return;
}

aTag=new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[center]','[/center]','[right]','[/right]','[list]','[/list]','[list=o]','[/list]','[img]','[/img]','[url]','[/url]','[sup]','[/sup]','[sub]','[/sub]','[youtube]','[/youtube]','[video]','[/video]','[audio]','[/audio]');

function fFmt(sItem,nTag){
 var txtarea=document.forms['fraEingabe'].elements[sItem];
 txtarea.focus();
 theSelection=false;
 if((clientVer>=4)&& is_ie && is_win){
  theSelection=document.selection.createRange().text;
  if(theSelection){
   document.selection.createRange().text=aTag[nTag]+theSelection+aTag[nTag+1];
   txtarea.focus();
   theSelection='';
   return;
  }
 }else if(txtarea.selectionEnd && (txtarea.selectionEnd-txtarea.selectionStart>0)){
  mozWrap(txtarea,aTag[nTag],aTag[nTag+1]);
  return;
 }
 if (txtarea.createTextRange) txtarea.caretPos=document.selection.createRange().duplicate();
}

function fCol(sItem,sCol){
 if(!sCol) return;
 var txtarea=document.forms['fraEingabe'].elements[sItem];
 txtarea.focus();
 theSelection=false;
 if((clientVer>=4) && is_ie && is_win){
  theSelection=document.selection.createRange().text;
  if(!theSelection){
   txtarea.value+='[color='+sCol+']'+'[/color]';
   txtarea.focus();
   theSelection='';
   return;
  }
  document.selection.createRange().text='[color='+sCol+']'+theSelection+'[/color]';
  txtarea.focus();
  return;
 }else if(txtarea.selectionEnd &&(txtarea.selectionEnd-txtarea.selectionStart>0)){
  mozWrap(txtarea,'[color='+sCol+']','[/color]');
  return;
 }else{
  txtarea.value+='[color='+sCol+']'+'[/color]';
  txtarea.focus();
 }
 if(txtarea.createTextRange) txtarea.caretPos=document.selection.createRange().duplicate();
}

function fSiz(sItem,sSiz){
 if(!sSiz) return;
 var txtarea=document.forms['fraEingabe'].elements[sItem];
 txtarea.focus();
 theSelection=false;
 if((clientVer>=4) && is_ie && is_win){
  theSelection=document.selection.createRange().text;
  if(!theSelection){
   txtarea.value+='[size='+sSiz+']'+'[/size]';
   txtarea.focus();
   theSelection='';
   return;
  }
  document.selection.createRange().text='[size='+sSiz+']'+theSelection+'[/size]';
  txtarea.focus();
  return;
 }else if(txtarea.selectionEnd &&(txtarea.selectionEnd-txtarea.selectionStart>0)){
  mozWrap(txtarea,'[size='+sSiz+']','[/size]');
  return;
 }else{
  txtarea.value+='[size='+sSiz+']'+'[/size]';
  txtarea.focus();
 }
 if(txtarea.createTextRange) txtarea.caretPos=document.selection.createRange().duplicate();
}