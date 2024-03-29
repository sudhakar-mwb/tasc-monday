@include('includes.header')
{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TASC FORM</title>
</head> --}}
<main class="px-3 pt-5">
@include('admin.headtitle')
<div style="margin:0px; height:140vh;" class="mt-3"  id="iframe-chart">
    <iframe style="transform:scale(1)"
        src="https://view.monday.com/embed/1352607400-32fca21e6641da6a4438eb10aeff06ed?r=euc1"  width="100%"
        height="100%"
        style="border: 0; box-shadow: 5px 5px 56px 0px rgba(0,0,0,0.25);"></iframe>
</main>
<style>
    body{
        background: #f6f7fb;
    }
</style>
</div>
{{-- </html> --}}
@include('includes.footer')
