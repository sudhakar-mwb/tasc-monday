<?php
$links = [
    '1' => [
        'users list' => 'users',
        'board visibilty' => 'board-visiblilty',
        'settings' => 'settings',
        'create Admin' => 'create-admin',
    ],
    '2' => [
        'users list' => 'users',
        'board visibilty' => 'board-visiblilty',
    ],
];
$links = $links[auth()->user()->role];
$active_link = '';
if (!empty($active)) {
    $active_link = $active;
}
?>

<nav class="mt-1 mb-5" style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach ($links as $key => $val)
            <li class="breadcrumb-item active"> <a
                    class="inactive  {{ $val == $active_link ? 'link-primary' : 'link-secondary' }} text-decoration-none"
                    href="{{ $val }}"><u>
                        {{ ucwords($key) }}&nbsp;
                        <i class="bi bi-box-arrow-up-right"></i>
                    </u>
                </a>
            </li>
        @endforeach

    </ol>
</nav>
<style>
    @media (max-width: 767px) {
        .breadcrumb {
            justify-content: space-around;
            gap: 10px
        }

        .breadcrumb-item::before {
            display: none
        }

        .breadcrumb-item+.breadcrumb-item {
            padding-left: 0px
        }
    }
</style>
