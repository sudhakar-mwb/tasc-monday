@include('includes.header')
<style>
  .blurry {
  position: absolute;
  z-index: 100000;
  backdrop-filter: blur(8px);
}
main.px-3.pt-5{
  padding-left: 0 !important;
  padding-right: 0 !important;
  padding-top: 20px !important
}
.cover-container.p-3{
  padding: 0px !important
}
</style>
<main class="px-3 pt-5">
    @include('admin.headtitle')
    <div style="margin:0px; height:75vh;" style="position: relative" id="iframe-signup">
      <div id="loader" class="blurry w-100" style="height: 100%;"></div>
     <?php echo $embed_code  ?>
    </div>

</main>
<script>
      $(document).ready(function() {
        let embed=  '{{ $embed_code }}'
        let decodedEmbed = $("<div/>").html(embed).text()
        var viewportWidth = $(window).width();
        if(viewportWidth<767)
     {
console.log({decodedEmbed})
$("#iframe-signup").html(decodedEmbed)
     }
        setTimeout(() => {
            $('#loader').hide()
        }, 4000);
    })

</script>
@include('includes.footer')
