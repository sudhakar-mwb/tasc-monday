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
    <button class="btn btn-primary btn-lg" onclick="window.history.back()"><svg xmlns="http://www.w3.org/2000/svg"
            width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
            <path fill-rule="evenodd"
                d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
        </svg> Back</button>
</main>
@include('includes.footer')
