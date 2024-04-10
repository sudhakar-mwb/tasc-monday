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
    <div style="margin:0px; height:250vh;" id="iframe-signup">
        <?php echo $embed_code; ?>
    </div>
    <style>
        #iframe-signup>iframe {
            height: 100%;
            width: 800px !important;
            border: 0px !important;
        }
    </style>
</main>
{{-- </html> --}}

@include('includes.footer')
