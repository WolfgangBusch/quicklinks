/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
/*   P o p u p   M e n u s    f o r    Q u i c k l i n k s   */
var pop='pop'; var box='box';
var delayclean;
function clean(anz) {
    for(var k=1; k<=anz; k=k+1) {
        document.getElementById(pop+k).style.visibility='hidden';
        }
    }
function quickMenu(j,anz) {
             /*alert("mouseonmenu: " + anz);*/
    if(delayclean){
       cleartimer();
       }
    menu(j,anz);
    }
function quickCleanDelay(anz) {
    delayclean=setTimeout("clean(" + anz + ")",250);
    }
function cleartimer() {
           /*alert ("cleantimer");*/
    clearTimeout(delayclean);
    }
function menu (i,anz) {
             /*alert("menu: " + anz);*/
    for(var k=1; k<=anz; k=k+1) {
        if(k!=i) {
            document.getElementById(pop+k).style.visibility='hidden';
            } else {
            document.getElementById(pop+k).style.visibility='visible';
            }
        }
    }
/*   H i n t e r g r u n d f a r b e    e i n e r    Q u i c k l i n k - Z e l l e    b e i    m o u s e o v e r   */
function getBgColor(idName) {
   /* Rueckgabe der Hintergrundfarbe einer <li>-Box
      id = 1 / 2 / 3 / ...: Nummer der betreffenden <li>-Box,
   */
   var col = window.getComputedStyle(document.getElementById(idName),"").backgroundColor;
   return col;
   }
function stdBgColor(id) {
   /* Rueckgabe der Standard-Hintergrundfarbe der <li>-Boxes
      (gerade nicht veraenderte Hintergrundfarbe 'box1' oder 'box2')
      id = 1 / 2 / 3 / ...: Nummer der <li>-Box,
      deren Hintergrundfarbe gerade veraendert wurde
      benutzte function: getBgColor(idName);
   */
   var neuId = 1;
   if(id==neuId) neuId = 2;
   var bgBox=getBgColor(box+neuId);
   return bgBox;
   }
function quickStdStyle(id) {
   /* Rueckaenderung der Hintergrundfarbe einer <li>-Box auf die Standardfarbe
      id = 1 / 2 / 3 / ...: Nummer der <li>-Box,
      deren Hintergrundfarbe veraendert werden soll
      benutzte function: stdBgColor(id);
   */
   var idName= box + id;
   var bgColor = stdBgColor(id);
   document.getElementById(idName).style.backgroundColor=bgColor;
   }
function quickNewStyle(id) {
   /* Aenderung der Hintergrundfarbe einer <li>-Box,
      und zwar auf die Farbe der Popup-Menues
      id = 1 / 2 / 3 / ...: Nummer der <li>-Box,
      deren Hintergrundfarbe veraendert werden soll
      benutzte function: getBgColor(idName);
   */
   var bgColor = getBgColor(pop+'1');
   var idName = box + id;
   document.getElementById(idName).style.backgroundColor=bgColor;
   }
