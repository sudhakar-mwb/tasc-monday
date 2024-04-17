<?php
    $settings = Session::get('settings');
    ?>
<div class="animation-container" style="min-height: 170px; ">
    <div class="animation-content" style="transition: transform 1s ease, opacity 2s ease;">
        <h1 class="header-heading1 mt-2 fw-bold " style="color:{{ $settings->head_title_color??null}}">{{ $heading }}</h1>
      <div class="w-100 d-flex flex-column justify-content-center align-items-center secondaryHeading">
        <p class="header-heading3 mb-0 fs-6  text-secondary" style="width:90vw;max-width:800px" >{{ $subheading }}</p>   
        @if($secondaryHeading??false)
        <p class="header-heading3 mt-0 fs-6 text-secondary" style="width:90vw;max-width:800px" >{{ $secondaryHeading }}</p>   
         @endif
    </div>  
    </div>
</div>