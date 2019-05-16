<!DOCTYPE HTML>
<html>
    <head>
        <title>Weekendr</title>

        @include('layouts.frontend-meta')

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/frontend.css" />
        <link rel='shortcut icon' type='image/x-icon' href='/images/logo.png' />


        <!-- Facebook Pixel Code -->
            <script>
              !function(f,b,e,v,n,t,s)
              {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
              n.callMethod.apply(n,arguments):n.queue.push(arguments)};
              if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
              n.queue=[];t=b.createElement(e);t.async=!0;
              t.src=v;s=b.getElementsByTagName(e)[0];
              s.parentNode.insertBefore(t,s)}(window, document,'script',
              'https://connect.facebook.net/en_US/fbevents.js');
              fbq('init', '350003382461731');
              fbq('track', 'PageView');
            </script>
            <noscript><img height="1" width="1" style="display:none"
              src="https://www.facebook.com/tr?id=350003382461731&ev=PageView&noscript=1"
            /></noscript>
        <!-- End Facebook Pixel Code -->
    </head>
    <body class="is-preload">
        <!-- Nav -->
        <div id="navigation">
            <nav>
                <ul>
                    <li><a href="#menu">Menu</a></li>
                </ul>
            </nav>
        </div>
        <!-- Menu -->
            <nav id="menu">
                <h2>Menu</h2>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/how-it-works">How It Works</a></li>
                    <li><a href="/travel-tips">Travel Tips</a></li>
                </ul>
            </nav>

            @yield('frontend-content')

        <!-- Scripts -->
            <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
            <script src="/js/frontend.js"></script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-80504881-4"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', 'UA-80504881-4');
            </script>
    </body>
</html>
