/* Bilder vor dem Upload vor-verkleinern */

 var sPostUrl=''; // wird im Hauptprogramm gesetzt
 var nBildBreit=1; var nBildHoch=1; var nThumbBreit=1; var nThumbHoch=1;

 var bJSSend=false; // Formular per JavaScript senden (nur falls Bilder verkleinert wurden)
 var blobs=new Array(); blobs[0]=true;
 var nFldNr=0;

 var bAutoOrientation=false; // kann der Browser BildOrientierung verarbeiten?
 var tstImgURL='data:image/jpeg;base64,/9j/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAYAAAAAAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAEAAgMBEQACEQEDEQH/xABKAAEAAAAAAAAAAAAAAAAAAAALEAEAAAAAAAAAAAAAAAAAAAAAAQEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8H//2Q==';
 var tstImg=document.createElement('img');
 tstImg.onload=function(){
  bAutoOrientation=(tstImg.width===1&&tstImg.height===2); // black 2x1 JPEG mit EXIF Orientation 6 (Rotated 90° CCW) wurde automatisch gedreht
 }
 tstImg.src=tstImgURL;

 // Bibliothek canvas-to-blob.min.js
 !function(t){"use strict";var e=t.HTMLCanvasElement&&t.HTMLCanvasElement.prototype,o=t.Blob&&function(){try{return Boolean(new Blob)}catch(t){return!1}}(),n=o&&t.Uint8Array&&function(){try{return 100===new Blob([new Uint8Array(100)]).size}catch(t){return!1}}(),r=t.BlobBuilder||t.WebKitBlobBuilder||t.MozBlobBuilder||t.MSBlobBuilder,a=/^data:((.*?)(;charset=.*?)?)(;base64)?,/,i=(o||r)&&t.atob&&t.ArrayBuffer&&t.Uint8Array&&function(t){var e,i,l,u,c,f,b,d,B;if(!(e=t.match(a)))throw new Error("invalid data URI");for(i=e[2]?e[1]:"text/plain"+(e[3]||";charset=US-ASCII"),l=!!e[4],u=t.slice(e[0].length),c=l?atob(u):decodeURIComponent(u),f=new ArrayBuffer(c.length),b=new Uint8Array(f),d=0;d<c.length;d+=1)b[d]=c.charCodeAt(d);return o?new Blob([n?b:f],{type:i}):((B=new r).append(f),B.getBlob(i))};t.HTMLCanvasElement&&!e.toBlob&&(e.mozGetAsFile?e.toBlob=function(t,o,n){var r=this;setTimeout(function(){t(n&&e.toDataURL&&i?i(r.toDataURL(o,n)):r.mozGetAsFile("blob",o))})}:e.toDataURL&&i&&(e.toBlob=function(t,e,o){var n=this;setTimeout(function(){t(i(n.toDataURL(e,o)))})})),"function"==typeof define&&define.amd?define(function(){return i}):"object"==typeof module&&module.exports?module.exports=i:t.dataURLtoBlob=i}(window);

 function loadDatFile(inputField){
  nFldNr=parseInt(inputField.name.substr(5));
  blobs[nFldNr]=inputField.files[0];
 }

 function loadImgFile(inputField){
  nFldNr=parseInt(inputField.name.substr(5));
  if(window.FileReader){ // alte Browser ausschliessen
   file=inputField.files[0];
   reader=new FileReader();
   reader.onload=function(readerEvent){
    var exifOrientation=1; if(!bAutoOrientation) exifOrientation=getExifOrientation(readerEvent.target.result);
    var img=new Image();
    img.onload=function(imgEvent){resizeImg(img,exifOrientation)};
    img.src=readerEvent.target.result;
   }
   reader.readAsDataURL(file);
  }else bJSSend=false;
 }

 function getExifOrientation(dataStr){
  var p=dataStr.substr(0,80).indexOf("base64,");
  if(p>0){
   var str=atob(dataStr.substr(p+7,2500)); var strLen=str.length;
   var buf=new ArrayBuffer(strLen); var bufView=new Uint8Array(buf); // 1 byte for each char
   for(var i=0;i<strLen;i++) bufView[i]=str.charCodeAt(i);
   var view=new DataView(buf); var length=view.byteLength;
   if(view.getUint16(0,false)!=0xFFD8) return -2; // kein Bild
   var offset=2;
   while(offset<length){
    if(view.getUint16(offset+2,false)<=8) return -1;
    var marker=view.getUint16(offset,false);
    offset+=2;
    if(marker==0xFFE1){ // Start EXIF
     if(view.getUint32(offset+=2,false)!=0x45786966) return -1;
     var little=view.getUint16(offset+=6,false)==0x4949;
     offset+=view.getUint32(offset+4,little);
     var tags=Math.min(view.getUint16(offset,little),200); // maximal 200 Tags in 2500 Byte
     offset+=2;
     for(var i=0;i<tags;i++) if(view.getUint16(offset+(i*12),little)==0x0112) return view.getUint16(offset+(i*12)+8,little);
    }else if((marker & 0xFF00)!=0xFF00){
     break;
    }else offset+=view.getUint16(offset,false);
   }
   return -1;
  }else return -1;
 }

 function resizeImg(img,exifOrientation){
  var canvas=document.createElement("canvas");
  if(exifOrientation<5 || exifOrientation>8){
   canvas.width=img.width; canvas.height=img.height;
  }else{
   canvas.width=img.height; canvas.height=img.width; // 90°
  }
  if(canvas.width>0 && canvas.getContext){
   var ctx=canvas.getContext("2d");
   switch(exifOrientation){ // transform context before drawing image
    case 1: break;
    case 2: ctx.transform(-1,0,0,1,img.width,0); break;
    case 3: ctx.transform(-1,0,0,-1,img.width,img.height); break;
    case 4: ctx.transform(1,0,0,-1,0,img.height); break;
    case 5: ctx.transform(0,1,1,0,0,0); break;
    case 6: ctx.transform(0,1,-1,0,img.height,0); break;
    case 7: ctx.transform(0,-1,-1,0,img.height,img.width); break;
    case 8: ctx.transform(0,-1,1,0,0,img.width); break;
    default: break;
   }
   ctx.webkitImageSmoothingEnabled=false; ctx.msImageSmoothingEnabled=false; ctx.imageSmoothingEnabled=false;
   ctx.drawImage(img,0,0);

   var maxW=2*nBildBreit; var maxH=2*nBildHoch; var w=canvas.width; var h=canvas.height; var f;
   if(w>maxW){f=maxW/w; w=maxW; h=Math.ceil(f*h);}
   if(h>maxH){f=maxH/h; h=maxH; w=Math.ceil(f*w);}
   resample_single(canvas,w,h,true);
   canvas.toBlob(function(blob){blobs[nFldNr]=blob; bJSSend=true;},"image/jpeg"); // jedes Bild als BLOB zwischenspeichern

   maxW=nThumbBreit; maxH=nThumbHoch; w=canvas.width; h=canvas.height; // zusaetzlich Thumbnail erzeugen
   if(w>maxW){f=maxW/w; w=maxW; h=Math.ceil(f*h);}
   if(h>maxH){f=maxH/h; h=maxH; w=Math.ceil(f*w);}
   canvas.toBlob(function(blob){ // Kontrollausgabe als Thumbnail
    var url=URL.createObjectURL(blob);
    var newImg=document.createElement("img");
    newImg.setAttribute("width", w);
    newImg.setAttribute("height",h);
    newImg.onload=function(){URL.revokeObjectURL(url);}
    newImg.src=url;
    var oLabel=document.getElementById("mpLabel"+nFldNr);
    oLabel.innerHTML=""; oLabel.appendChild(newImg);
   });
  }else bJSSend=false;
 }

 function resample_single(canvas,width,height,bRresize){
  var width_source=canvas.width; var height_source=canvas.height;
  width=Math.round(width); height=Math.round(height);
  var ratio_w=width_source/width; var ratio_h=height_source/height;
  var ratio_w_half=Math.ceil(ratio_w/2); var ratio_h_half=Math.ceil(ratio_h/2);
  var ctx=canvas.getContext("2d");
  var img=ctx.getImageData(0,0,width_source,height_source); var img2=ctx.createImageData(width,height);
  var data=img.data; var data2=img2.data;
  for(var j=0;j<height;j++){
   for(var i=0;i<width;i++){
    var x2=(i+j*width)*4;
    var weight=0; var weights=0; var weights_alpha=0;
    var gx_r=0; var gx_g=0; var gx_b=0; var gx_a=0;
    var center_y=(j+0.5)*ratio_h;
    var yy_start=Math.floor(j*ratio_h); var yy_stop=Math.ceil((j+1)*ratio_h);
    for(var yy=yy_start;yy<yy_stop;yy++){
     var dy=Math.abs(center_y-(yy + 0.5))/ratio_h_half;
     var center_x=(i+0.5)*ratio_w;
     var w0=dy*dy; //pre-calc part of w
     var xx_start=Math.floor(i*ratio_w); var xx_stop=Math.ceil((i+1)*ratio_w);
     for(var xx=xx_start;xx<xx_stop;xx++){
      var dx=Math.abs(center_x-(xx + 0.5))/ratio_w_half;
      var w=Math.sqrt(w0+dx*dx);
      if(w>=1) continue; //pixel too far
      //hermite filter
      weight=2*w*w*w-3*w*w+1;
      var pos_x=4*(xx+yy*width_source);
      //alpha
      gx_a+=weight*data[pos_x+3];
      weights_alpha+=weight;
      //colors
      if(data[pos_x+3]<255) weight=weight*data[pos_x+3]/250;
      gx_r+=weight*data[pos_x]; gx_g+=weight*data[pos_x+1]; gx_b+=weight*data[pos_x+2];
      weights+=weight;
     }
    }
    data2[x2]=gx_r/weights; data2[x2+1]=gx_g/weights; data2[x2+2]=gx_b/weights; data2[x2+3]=gx_a/weights_alpha;
   }
  }
  if(bRresize===true){ //clear and resize canvas
   canvas.width=width; canvas.height=height;
  }else ctx.clearRect(0,0,width_source,height_source);
  ctx.putImageData(img2,0,0); //draw
 }

 function formSend(){
  if(bJSSend){
   var formData=new FormData();
   formData.append("mp_JSSend","1");
   for(var j=0;document.mpEingabe.elements.length>j;j++){
    if(document.mpEingabe.elements[j].type=="checkbox"||document.mpEingabe.elements[j].type=="radio"){
     if(document.mpEingabe.elements[j].checked) formData.append(document.mpEingabe.elements[j].name,document.mpEingabe.elements[j].value);
    }else if(document.mpEingabe.elements[j].type!="file"){
     if(document.mpEingabe.elements[j].value>"") formData.append(document.mpEingabe.elements[j].name,document.mpEingabe.elements[j].value);
    }else if(document.mpEingabe.elements[j].value){ // Typ: file
     nFldNr=parseInt(document.mpEingabe.elements[j].name.substr(5));
     formData.append("mp_UpNa_"+nFldNr,document.mpEingabe.elements[j].value);
     formData.append(document.mpEingabe.elements[j].name,blobs[nFldNr],document.mpEingabe.elements[j].value);
   }}
   request.open("POST",sPostURL);
   request.send(formData);
   return false;
  }else return true; // normales POST-Ersatzsenden
 }

 var request=new XMLHttpRequest();
 request.onreadystatechange=function(){
  if(request.readyState===4){requestAnswer(request);}
 }

 function requestAnswer(request){
  document.close();
  document.write(request.response);
  document.close();
 }