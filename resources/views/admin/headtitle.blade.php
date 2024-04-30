<?php
$settings = Session::get('settings');

?>
<div class="animation-container my-3 mt-5 onboarding-margin-top-0 onboarding-min-height-120" style="min-height: <?php echo $subheading == '' ? '100px' : '160px'; ?>">
    <div class="animation-content" style="transition: transform 1s ease, opacity 2s ease;">
        <h1 class="header-heading1 mt-2 fw-bold ff-ws onboarding-font-size-30" style="color:{{ $settings->head_title_color ?? null }}">
            {{ $heading }}</h1>
        <div class="w-100 d-flex flex-column justify-content-center align-items-center secondaryHeading">
            @if ($subheading ?? false)
                <p class="secondry-heading header-heading3 mb-0 text-secondary"
                    style="max-width:800px;font-size:19px"><?php echo $subheading; ?></p>
            @endif
            @if ($secondaryHeading ?? false)
                <p class="header-heading3 mt-0 fs-6 text-secondary" style="width:90vw;max-width:800px">
                    {{ $secondaryHeading }}</p>
            @endif
        </div>
    </div>
</div>
