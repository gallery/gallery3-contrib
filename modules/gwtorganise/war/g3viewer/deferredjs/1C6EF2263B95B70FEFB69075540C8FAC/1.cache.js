function qS(){}
function CS(){return dP}
function GS(){var a;while(vS){a=vS;vS=vS.c;!vS&&(wS=null);ko(a.b)}}
function DS(){yS=true;xS=(AS(),new qS);my((jy(),iy),1);!!$stats&&$stats(Sy(irb,oib,null,null));xS.Zb();!!$stats&&$stats(Sy(irb,jrb,null,null))}
function ko(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(hrb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));sv(a.d,e.encode());return}}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);sv(a.d,e.encode());return}}}
var krb='AsyncLoader1',hrb='beta.canvas',irb='runCallbacks1';_=qS.prototype=new rS;_.gC=CS;_.Zb=GS;_.tI=0;var dP=q3(Zob,krb);DS();