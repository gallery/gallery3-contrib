function yS(){}
function KS(){return gP}
function OS(){var a;while(DS){a=DS;DS=DS.b;!DS&&(ES=null);jo(a.a)}}
function LS(){GS=true;FS=(IS(),new yS);ny((ky(),jy),1);!!$stats&&$stats(Ty(Urb,Nib,null,null));FS.Zb();!!$stats&&$stats(Ty(Urb,Vrb,null,null))}
function jo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Trb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));qv(a.c,e.encode());return}}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);qv(a.c,e.encode());return}}}
var Wrb='AsyncLoader1',Trb='beta.canvas',Urb='runCallbacks1';_=yS.prototype=new zS;_.gC=KS;_.Zb=OS;_.tI=0;var gP=W3(Dpb,Wrb);LS();