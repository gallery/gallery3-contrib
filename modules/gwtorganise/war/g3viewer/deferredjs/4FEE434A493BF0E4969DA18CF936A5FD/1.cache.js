function hS(){}
function tS(){return RO}
function xS(){var a;while(mS){a=mS;mS=mS.b;!mS&&(nS=null);io(a.a)}}
function uS(){pS=true;oS=(rS(),new hS);dy((ay(),_x),1);!!$stats&&$stats(Jy(erb,$hb,null,null));oS.Zb();!!$stats&&$stats(Jy(erb,frb,null,null))}
function io(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(drb);e.decode(a.a);d=e.width;c=e.height;f=~~(d/a.b.b);b=~~(c/a.b.a);if(f>b){if(f>1){e.resize(a.b.b,~~(c/f));Yu(a.c,e.encode())}}else{if(b>1){e.resize(~~(d/b),a.b.a);Yu(a.c,e.encode())}}}
var grb='AsyncLoader1',drb='beta.canvas',erb='runCallbacks1';_=hS.prototype=new iS;_.gC=tS;_.Zb=xS;_.tI=0;var RO=K3(Pob,grb);uS();