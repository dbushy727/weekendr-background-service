@extends('layouts.frontend-layout')

@section('frontend-content')

    <!-- Header -->
    <header id="header">
        <h1>Weekendr</h1>

        <p>Friday to Sunday roundtrip tickets for under $300.</p>
        <p>Weekendr is a notification service that will let you know <br/>
        when we find weekend roundtrip tickets, under $300, leaving <br/>
        from your city so you can have a fun weekend somewhere new.</p>
        <p>Sign up now!</p>

        <div id="mc_embed_signup">
            <form action="/subscribe.php" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Email Address">
                <select class="js-data-example-ajax" id="mce-AIRPORT" name="AIRPORT">
                    <option value="">Aiport Code or City</option>
                </select>

                <input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="button">

                <div id="mce-responses" class="clear">
                <div class="response" id="mce-error-response" style="display:none"></div>
                    <div class="response" id="mce-success-response" style="display:none"></div>
                </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_b8208b298e182f511941c318d_03c2cc3ef1" tabindex="-1" value=""></div>
            </form>
        </div>
    </header>

    <!-- Footer -->
        <footer id="footer">
            <ul class="icons">
                <li><a target="_BLANK" href="https://twitter.com/realDanBush" class="fab fa-twitter custom-icon"><span class="label"></span></a></li>
                <li><a target="_BLANK" href="mailto:danny@weekendr.io" class="fas fa-envelope custom-icon"><span class="label"></span></a></li>
                <li><a target="_BLANK" href="/travel-tips"><span class="label">Travel Tips</span></a></li>
            </ul>
            <ul class="copyright">
                <li>&copy; Weekendr</li>
                <li><a href="/privacy-policy.html">Privacy Policy</a></li>
                <li>Credits: <a target="_BLANK" href="https://unsplash.com">Images from Unsplash.com</a></li>
            </ul>
        </footer>

@stop
