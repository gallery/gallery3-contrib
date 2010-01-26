function zS(){}
function LS(){return fP}
function PS(){var a;while(ES){a=ES;ES=ES.b;!ES&&(FS=null);ko(a.a)}}
function MS(){HS=true;GS=(JS(),new zS);gy((dy(),cy),1);!!$stats&&$stats(My(zsb,Oib,null,null));GS.Zb();!!$stats&&$stats(My(zsb,Asb,null,null))}
function ko(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(ysb);e.decode(a.a);d=e.width;c=e.height;f=~~(d/a.b.b);b=~~(c/a.b.a);if(f>b){if(f>1){e.resize(a.b.b,~~(c/f));$u(a.c,e.encode())}}else{if(b>1){e.resize(~~(d/b),a.b.a);$u(a.c,e.encode())}}}
var Bsb='AsyncLoader1',ysb='beta.canvas',zsb='runCallbacks1';_=zS.prototype=new AS;_.gC=LS;_.Zb=PS;_.tI=0;var fP=z4(gqb,Bsb);MS();