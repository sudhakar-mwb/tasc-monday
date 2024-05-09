<script>
    window.intercomSettings = {
        api_base: "https://api-iam.intercom.io",
        app_id: "wk35gw8g",
        name: <?php
        if (isset(auth()->user()->name)) {
            echo json_encode((string) auth()->user()->name);
        } else {
            echo '';
        }
        ?>, // Full name
        user_id: <?php
        if (isset(auth()->user()->id)) {
            echo json_encode((string) auth()->user()->id);
        } else {
            echo '';
        }
        ?>, // a UUID for your user
        email: <?php
        if (isset(auth()->user()->email)) {
            echo json_encode((string) auth()->user()->email);
        } else {
            echo '';
        }
        ?>, // the email for your user
        created_at: <?php
        if (isset(auth()->user()->created_at)) {
            echo json_encode(strtotime(auth()->user()->created_at));
        } else {
            echo '';
        }
        ?> // Signup date as a Unix timestamp
    };
</script>
<script>
    // We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/wk35gw8g'
    (function() {
        var w = window;
        var ic = w.Intercom;
        if (typeof ic === "function") {
            ic('reattach_activator');
            ic('update', w.intercomSettings);
        } else {
            var d = document;
            var i = function() {
                i.c(arguments);
            };
            i.q = [];
            i.c = function(args) {
                i.q.push(args);
            };
            w.Intercom = i;
            var l = function() {
                var s = d.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'https://widget.intercom.io/widget/wk35gw8g';
                var x = d.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            };
            if (document.readyState === 'complete') {
                l();
            } else if (w.attachEvent) {
                w.attachEvent('onload', l);
            } else {
                w.addEventListener('load', l, false);
            }
        }
    })();
</script>
