@include('includes.header')
<main class="px-3 pt-5">
    @include('admin.headtitle')
    <div style="margin:0px; height:150vh;" style="position: relative" id="iframe-signup">
      <div id="loader" class="blurry w-100" style="height: 100%;"></div>
        <?php echo $embed_code; ?>
    </div>
    <style>
        #iframe-signup>iframe {
            height: 100%;
            width: 1000px !important;
            border: 0px !important;
            box-shadow: none !important
        }
        .blurry {
        position: absolute;
        z-index: 100000;
        backdrop-filter: blur(8px);
    }
    </style>
</main>
<script>
      $(document).ready(function() {
        setTimeout(() => {
            $('#loader').hide()
        }, 4000);
    })
</script>
@include('includes.footer')
