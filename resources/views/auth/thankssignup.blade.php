@include('auth.header')

<main class="px-3 pt-5">
    <h2 class="align-center text-success ">
        <?php 
        if(!empty($status)&&$status){
        ?>
        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-check2-circle"
            viewBox="0 0 16 16">
            <path
                d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
            <path
                d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
        </svg>
        <?php
        }else{?>
        <div class="text-warning " style="font-size: 100px">
            <i class="bi bi-exclamation-circle"></i>
        </div>

        <?php }?>
    </h2>

    @include('admin.headtitle')
    <a class="btn btn-secondary btn-gradiant mt-2" href="/">
        <span class="fs-6">Back</span> &nbsp;
        <span style="float:right;transform: rotate(180deg);"><svg height="25px" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100"
                enable-background="new 0 0 100 100" xml:space="preserve" style="max-width:100%">
                <g style="" fill="currentColor">
                    <path
                        d="M16,50.02C16.001,68.787,31.214,84,49.979,84c18.767,0,33.98-15.213,33.98-33.98c0-18.765-15.214-33.978-33.981-33.979   C31.213,16.041,16.001,31.255,16,50.02z M72.114,52.356c-0.033,0.044-0.065,0.088-0.102,0.13c-0.064,0.078-0.135,0.156-0.212,0.232   c-0.001,0.003-0.005,0.006-0.007,0.008L54.815,69.705c-1.494,1.494-3.914,1.494-5.408,0c-1.492-1.493-1.492-3.914,0-5.406   l10.457-10.455H30.866c-2.111,0-3.824-1.712-3.824-3.824c0-2.11,1.713-3.822,3.824-3.822h28.998L49.409,35.742   c-1.494-1.492-1.494-3.914,0-5.408c1.492-1.491,3.912-1.491,5.404,0l16.982,16.983c0.082,0.081,0.153,0.16,0.22,0.243   c0.03,0.033,0.058,0.069,0.085,0.104c0.021,0.027,0.045,0.058,0.062,0.087c0.472,0.635,0.752,1.416,0.754,2.269   c-0.002,0.854-0.283,1.638-0.755,2.274C72.147,52.315,72.13,52.335,72.114,52.356z"
                        style="" fill="currentColor"></path>
                </g>
            </svg></span>
          </a>

</main>
@include('includes.footer')
