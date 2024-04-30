@include('includes.header')

<main class="px-3">
    @include('admin.headtitle')
    <div class="w-100 mt-5" style="position: relative">
        <div id="loader" class="blurry w-100" style="height: 100vh;"></div>
        <div style="margin:0px; height:130vh;position: relative"class="w-100" id="iframe-chart">
            <?php echo $embed_code; ?>
            <div class="w-100 bottom-blur" style="height: 50px"></div>
        </div>
</main>
<style>
    body,
    #hider {
        /* background: #f6f7fb; */
    }

    .blurry {
        position: absolute;
        z-index: 100000;
        backdrop-filter: blur(10px);
    }

    .bottom-blur {
        backdrop-filter: blur(10px);
        height: 50px;
        position: absolute;
        bottom: 6px
    }

    #hider {
        border-radius: 0px 0px 89px 0px;
        width: calc(100% - 100px);
    }

    main>.animation-container {
        min-height: 105px !important;
    }

    #iframe-chart>iframe {
        border: 0;
        border-radius: 0.5rem;
        box-shadow: 5px 5px 56px 0px rgba(0, 0, 0, 0.25);
        width: 100% !important;
        height: 100% !important;
        transform: scale(1) !important;

    }
</style>
<script>
    $(document).ready(function() {
        setTimeout(() => {
            $('#loader').hide()
        }, 4000);
    })
</script>

</div>
{{-- </html> --}}
@include('includes.footer')
