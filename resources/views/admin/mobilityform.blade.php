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
    <div style="margin:0px; height:250vh;" >
        <iframe name="iframe-signup" id="iframe-signup"
            src="https://forms.monday.com/forms/embed/595289733aa77c20d595ad8aeb7fc7c9?r=euc1" width="800px"
            height="100%" style="border: 0;"></iframe>
    </div>
</main>
{{-- </html> --}}

@include('includes.footer')
