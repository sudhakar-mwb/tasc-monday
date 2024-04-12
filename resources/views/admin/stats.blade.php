@include('includes.header')
{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TASC FORM</title>
</head> --}}
<main class="px-3 pt-5" >
    @include('admin.headtitle')
  <div class="w-100 mt-5" style="position: relative">
    <div style="margin:0px; height:140vh;position: absolute;z-index:-1; top:0px" class="w-100" id="iframe-chart"  >
      <?php echo $embed_code; ?>
        {{-- <iframe style="transform:scale(1)" id="iframe-chart"
            src="https://view.monday.com/embed/1352607400-32fca21e6641da6a4438eb10aeff06ed?r=euc1" width="100%"
            height="100%" style="border: 0; box-shadow: 5px 5px 56px 0px rgba(0,0,0,0.25);"></iframe> --}}
  </div>
</main>
<style>
    body,#hider {
        background: #f6f7fb;
    }
    #hider{
        border-radius : 0px 0px 89px 0px;
       width:calc(100% - 100px);
    }
    main>.animation-container{
        min-height: 105px !important;
    }
    #iframe-chart>iframe{
      border: 0; box-shadow: 5px 5px 56px 0px rgba(0,0,0,0.25);
      width: 100% !important;
      height: 100% !important;
      transform:scale(1) !important;

    }
</style>


</div>
{{-- </html> --}}
@include('includes.footer')
