function nS(){}
function zS(){return bP}
function DS(){var a;while(sS){a=sS;sS=sS.c;!sS&&(tS=null);jo(a.b)}}
function AS(){vS=true;uS=(xS(),new nS);ky((hy(),gy),1);!!$stats&&$stats(Qy(Sqb,aib,null,null));uS.$b();!!$stats&&$stats(Qy(Sqb,Tqb,null,null))}
function jo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Rqb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));qv(a.d,e.encode());return}}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);qv(a.d,e.encode());return}}}
var Uqb='AsyncLoader1',Rqb='beta.canvas',Sqb='runCallbacks1';_=nS.prototype=new oS;_.gC=zS;_.$b=DS;_.tI=0;var bP=b3(Iob,Uqb);AS();