function zS(){}
function LS(){return jP}
function PS(){var a;while(ES){a=ES;ES=ES.c;!ES&&(FS=null);po(a.b)}}
function MS(){HS=true;GS=(JS(),new zS);hy((ey(),dy),1);!!$stats&&$stats(Ny(hrb,hib,null,null));GS.cc();!!$stats&&$stats(Ny(hrb,irb,null,null))}
function po(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(grb);e.decode(a.b);d=e.width;c=e.height;f=~~(d/a.c.c);b=~~(c/a.c.b);if(f>b){if(f>1){e.resize(a.c.c,~~(c/f));dv(a.d,e.encode())}}else{if(b>1){e.resize(~~(d/b),a.c.b);dv(a.d,e.encode())}}}
var jrb='AsyncLoader1',grb='beta.canvas',hrb='runCallbacks1';_=zS.prototype=new AS;_.gC=LS;_.cc=PS;_.tI=0;var jP=R3(Vob,jrb);MS();