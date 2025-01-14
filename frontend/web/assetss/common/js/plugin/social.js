// Facebook
(function (d, s, id) {
    'use strict';
    var js, fjs = d.getElementsByTagName(
            s)[0];
    if(d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js,fjs);
}(document, 'script', 'facebook-jssdk'));

function fbShare() {
    window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'facebook-share-dialog','width=626,height=436');
    return false
}

// Twitter
!function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0],
        p = /^http:/.test(d.location)?'http' : 'https';
    if(!d.getElementById(id)) {
        js = d.createElement(s);
        js.id = id;
        js.src = p+'://platform.twitter.com/widgets.js';
        fjs.parentNode.insertBefore(js,fjs);
	}
}(document, 'script', 'twitter-wjs');

// Google+
(function () {
    'use strict';
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src ='https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
})();