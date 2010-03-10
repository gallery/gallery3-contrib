function HT(){}
function TT(){return WP}
function XT(){var a;while(MT){a=MT;MT=MT.c;!MT&&(NT=null);yo(a.b)}}
function UT(){PT=true;OT=(RT(),new HT);Py((My(),Ly),2);!!$stats&&$stats(tz(Asb,yjb,null,null));OT.ac();!!$stats&&$stats(tz(Asb,xsb,null,null))}
function yo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(zsb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Qv(a.d,e.encode());return}Qv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Qv(a.d,e.encode());return}Qv(a.d,a.b)}}
var Bsb='AsyncLoader2',zsb='beta.canvas',Asb='runCallbacks2';_=HT.prototype=new IT;_.gC=TT;_.ac=XT;_.tI=0;var WP=R4(jqb,Bsb);UT();