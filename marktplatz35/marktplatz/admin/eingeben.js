/* bbCode in Anlehnung an phpBB */
var form_name='mpEingabe';
var clientPC=navigator.userAgent.toLowerCase();
var clientVer=parseInt(navigator.appVersion);
var is_ie=((clientPC.indexOf('msie')!=-1)&&(clientPC.indexOf('opera')==-1));
var is_win=((clientPC.indexOf('win')!=-1)||(clientPC.indexOf('16bit')!=-1));
var baseHeight;
var theSelection=false;

function initInsertions(text_name){
 var textarea=document.forms[form_name].elements[text_name];
 if(is_ie&&typeof(baseHeight)!='number'){
  textarea.focus();
  baseHeight=document.selection.createRange().duplicate().boundingHeight;
 }
}

function bbTag(text_name,bbopen,bbclose){
 theSelection=false;
 var textarea=document.forms[form_name].elements[text_name];
 textarea.focus();
 if((clientVer>=4)&&is_ie&&is_win){
  theSelection=document.selection.createRange().text;
  if(theSelection){
   document.selection.createRange().text=bbopen+theSelection+bbclose;
   document.forms[form_name].elements[text_name].focus();
   theSelection='';
   return;
  }
 }else if(document.forms[form_name].elements[text_name].selectionEnd&&(document.forms[form_name].elements[text_name].selectionEnd-document.forms[form_name].elements[text_name].selectionStart>0)){
  mozWrap(document.forms[form_name].elements[text_name],bbopen,bbclose);
  document.forms[form_name].elements[text_name].focus();
  theSelection='';
  return;
 }
 var caret_pos=getCaretPosition(textarea).start;
 var new_pos=caret_pos+bbopen.length;
 insert_text(text_name,bbopen+bbclose);
 if(!isNaN(textarea.selectionStart)){
  textarea.selectionStart=new_pos;
  textarea.selectionEnd=new_pos;
 }else if(document.selection){
  var range=textarea.createTextRange();
  range.move('character',new_pos);
  range.select();
  storeCaret(textarea);
 }
 textarea.focus();
 return;
}

function insert_text(text_name,text){
 var textarea;
 textarea=document.forms[form_name].elements[text_name];
 if(!isNaN(textarea.selectionStart)){
  var sel_start=textarea.selectionStart;
  var sel_end=textarea.selectionEnd;
  mozWrap(textarea,text,'')
  textarea.selectionStart=sel_start+text.length;
  textarea.selectionEnd=sel_end+text.length;
 }else if(textarea.createTextRange&&textarea.caretPos)	{
  if(baseHeight!=textarea.caretPos.boundingHeight){
   textarea.focus();
   storeCaret(textarea);
  }
  var caret_pos=textarea.caretPos;
  caret_pos.text=caret_pos.text.charAt(caret_pos.text.length-1)==' '?caret_pos.text+text+' ':caret_pos.text+text;
 }else textarea.value=textarea.value+text;
 textarea.focus();
}

function mozWrap(txtarea,open,close){
 var selLength=(typeof(txtarea.textLength)=='undefined')?txtarea.value.length:txtarea.textLength;
 var selStart=txtarea.selectionStart;
 var selEnd=txtarea.selectionEnd;
 var scrollTop=txtarea.scrollTop;
 if(selEnd==1||selEnd==2) selEnd=selLength;
 var s1=(txtarea.value).substring(0,selStart);
 var s2=(txtarea.value).substring(selStart,selEnd)
 var s3=(txtarea.value).substring(selEnd,selLength);
 txtarea.value=s1+open+s2+close+s3;
 txtarea.selectionStart=selStart+open.length;
 txtarea.selectionEnd=selEnd+open.length;
 txtarea.focus();
 txtarea.scrollTop=scrollTop;
 return;
}

function storeCaret(textEl){
 if(textEl.createTextRange)
  textEl.caretPos=document.selection.createRange().duplicate();
}

function caretPosition(){var start=null; var end=null;}

function getCaretPosition(txtarea){
 var caretPos=new caretPosition();
 if(txtarea.selectionStart||txtarea.selectionStart==0){
  caretPos.start=txtarea.selectionStart;
  caretPos.end=txtarea.selectionEnd;
 }else if(document.selection){
  var range=document.selection.createRange();
  var range_all=document.body.createTextRange();
  range_all.moveToElementText(txtarea);
  var sel_start;
  for(sel_start=0;range_all.compareEndPoints('StartToStart',range)<0;sel_start++)
   range_all.moveStart('character',1);
  txtarea.sel_start=sel_start;
  caretPos.start=txtarea.sel_start;
  caretPos.end=txtarea.sel_start;
 }
 return caretPos;
}