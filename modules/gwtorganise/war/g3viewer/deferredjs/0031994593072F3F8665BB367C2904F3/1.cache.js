function sS(){}
function ES(){return dP}
function IS(){var a;while(xS){a=xS;xS=xS.c;!xS&&(yS=null);qo(a.b)}}
function FS(){AS=true;zS=(CS(),new sS);jy((gy(),fy),1);!!$stats&&$stats(Py(irb,kib,null,null));zS.ac();!!$stats&&$stats(Py(irb,jrb,null,null))}
function qo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(hrb);e.decode(a.b);d=e.width;c=e.height;f=~~(d/a.c.c);b=~~(c/a.c.b);if(f>b){if(f>1){e.resize(a.c.c,~~(c/f));fv(a.d,e.encode())}}else{if(b>1){e.resize(~~(d/b),a.c.b);fv(a.d,e.encode())}}}
var krb='AsyncLoader1',hrb='beta.canvas',irb='runCallbacks1';_=sS.prototype=new tS;_.gC=ES;_.ac=IS;_.tI=0;var dP=P3(Xob,krb);FS();