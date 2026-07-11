<!-- Facebook Pixel Code -->
@if(config('facebook.pixel_id'))
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ config('facebook.pixel_id') }}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ config('facebook.pixel_id') }}&ev=PageView&noscript=1"
/></noscript>
@endif

@if(config('facebook.verification_code'))
<meta name="facebook-domain-verification" content="{{ config('facebook.verification_code') }}" />
@endif

<!-- Facebook Messenger Chat Plugin -->
@if(config('facebook.page_id'))
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
    FB.init({
        xfbml            : true,
        version          : 'v18.0'
    });
};
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<div class="fb-customerchat"
    attribution="shop"
    page_id="{{ config('facebook.page_id') }}">
</div>
@endif
