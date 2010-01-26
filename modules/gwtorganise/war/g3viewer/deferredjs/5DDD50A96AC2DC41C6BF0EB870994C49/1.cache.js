function YR(){}
function iS(){return MO}
function mS(){var a;while(bS){a=bS;bS=bS.c;!bS&&(cS=null);io(a.b)}}
function jS(){eS=true;dS=(gS(),new YR);ay((Zx(),Yx),1);!!$stats&&$stats(Gy(cqb,nhb,null,null));dS.$b();!!$stats&&$stats(Gy(cqb,dqb,null,null))}
function io(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(bqb);e.decode(a.b);d=e.width;c=e.height;f=~~(d/a.c.c);b=~~(c/a.c.b);if(f>b){if(f>1){e.resize(a.c.c,~~(c/f));Yu(a.d,e.encode())}}else{if(b>1){e.resize(~~(d/b),a.c.b);Yu(a.d,e.encode())}}}
var eqb='AsyncLoader1',bqb='beta.canvas',cqb='runCallbacks1';_=YR.prototype=new ZR;_.gC=iS;_.$b=mS;_.tI=0;var MO=R2(Unb,eqb);jS();