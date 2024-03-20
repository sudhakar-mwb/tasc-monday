@include('includes.header')

<?php
function getClass($input)
{
    switch ($input) {
        case 'IN PROGRESS':
            return 'bg-light-progress';
        case 'COMPLETED':
            return 'bg-light-success';
        case 'STUCK':
            return 'bg-light-danger';
        default:
            return 'bg-light-progress';
    }
}
?>
<main class="px-3 pt-5">
    <div class="w-100 mt-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                        href="/monday"><u> {{ ucwords('Command Center') }}</u></a></li>
                        <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                            href="/monday/form/track-request"><u> {{ ucwords('Request Tracking') }}</u></a></li>
                   <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none" href="">
                        {{ ucwords('Ahmed Salem Al Mohammady') }}</a></li>

                        
            </ol>
        </nav>
    </div>
    <div class="w-100">
        <div class="d-flex mt-5 w-100" style="gap:20px">
            <div class="col-6 d-flex flex-column" style="gap:30px">
                <div class="d-flex mb-2" style="gap:16px">
                    <div class="rounded-circle bg-success p-4">
                        <div class="icon-size text-light" style="height: 50px;width:50px;">
                            <svg xmlns:x="http://ns.adobe.com/Extensibility/1.0/"
                                xmlns:i="http://ns.adobe.com/AdobeIllustrator/10.0/"
                                xmlns:graph="http://ns.adobe.com/Graphs/1.0/" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.2" baseProfile="tiny" x="0px"
                                y="0px" viewBox="0 0 100 100" xml:space="preserve" style="max-width:100%"
                                height="100%">
                                <metadata style="" fill="currentColor">
                                    <sfw xmlns="http://ns.adobe.com/SaveForWeb/1.0/" style="" fill="currentColor">
                                        <slices style="" fill="currentColor"></slices>
                                        <slicesourcebounds width="92.603" height="88.807" x="3.375" y="-94.057"
                                            bottomleftorigin="true" style="" fill="currentColor">
                                        </slicesourcebounds>
                                    </sfw>
                                </metadata>
                                <path
                                    d="M27.521,94c0.162-12,9.944-21.717,22.004-21.717C61.584,72.283,71.366,82,71.529,94h24.445c0,0,0.003,0.092,0.003,0.042  c0-25.572-20.73-46.206-46.301-46.206c-25.572,0-46.302,20.586-46.302,46.156C3.375,94.043,3.378,94,3.378,94H27.521z"
                                    style="" fill="currentColor" xmlns="http://www.w3.org/2000/svg"></path>
                                <circle cx="49.677" cy="24.856" r="19.607" style="" fill="currentColor">
                                </circle>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                        <h5 class="text-start m-0">Ahmed Salem Al Mohammady</h5>
                        <p class="profession m-0 text-start text-secondary" style="font-weight: 400">Software Engineer |
                            Expat Outside KSA</p>
                        <h6 class="status m-0 text-start text-success fw-bold">COMPLETED</h6>
                    </div>
                </div>
                <div class="w-100">
                    <div class="card border-0 border-1 p-4" style="background: rgba(171, 174, 190, 0.06)">
                        <h4 class="text-start head-color fw-bold pb-4 border-bottom">Candidate Information</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-flag-fill"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001" />
                                    </svg></span><span>Jordanian 🇯🇴🇯🇴 </span></li>
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                                        <path
                                            d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                                    </svg></span>Jordan 🇯🇴 <span></span></li>
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                                    </svg></span><span>(+971) 100 390 1790</span></li>
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-calendar4-range" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                                        <path
                                            d="M9 7.5a.5.5 0 0 1 .5-.5H15v2H9.5a.5.5 0 0 1-.5-.5zm-2 3v1a.5.5 0 0 1-.5.5H1v-2h5.5a.5.5 0 0 1 .5.5" />
                                    </svg></span><span>Joining Date | Jan 31, 2024</span></li>

                        </ul>
                    </div>
                    <div class="card border-0 border-1 p-4" style="background: rgba(171, 174, 190, 0.06)">
                        <h4 class="text-start head-color fw-bold pb-4 border-bottom">Onboarding Status</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-start border-0 text-start mb-1"
                                style="background: inherit;gap:10px">
                                <span style="width: 20px;height:20px" class="text-success mt-1">
                                    <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"
                                        style="max-width:100%" height="100%">
                                        <path
                                            d="M6 0c3.3137 0 6 2.6863 6 6s-2.6863 6-6 6-6-2.6863-6-6 2.6863-6 6-6zm2.6464 3.6464L5 7.293 3.3536 5.6464l-.7072.7072L5 8.707l4.3536-4.3535-.7072-.7072z"
                                            fill="currentColor" fill-rule="evenodd" style=""></path>
                                    </svg>
                                </span>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-secondary fs-5">Visa Issuance </span>
                                    <span class="text-secondary"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="16" height="16" fill="currentColor"
                                            class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                        </svg> Initiated | December 14, 2023 </span>
                                        <span class="text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                width="16" height="16" fill="currentColor"
                                                class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                            </svg>  Completed | January 6, 2024
                                        </span>
                                    </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start border-0 text-start mb-1"
                                style="background: inherit;gap:10px">
                                <span style="width: 20px;height:20px" class="text-success mt-1">
                                    <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"
                                        style="max-width:100%" height="100%">
                                        <path
                                            d="M6 0c3.3137 0 6 2.6863 6 6s-2.6863 6-6 6-6-2.6863-6-6 2.6863-6 6-6zm2.6464 3.6464L5 7.293 3.3536 5.6464l-.7072.7072L5 8.707l4.3536-4.3535-.7072-.7072z"
                                            fill="currentColor" fill-rule="evenodd" style=""></path>
                                    </svg>
                                </span>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-secondary fs-5">Visa / E-Wakala
                                    </span>
                                    <span class="text-secondary"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="16" height="16" fill="currentColor"
                                            class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                        </svg> Initiated | December 14, 2023 </span>
                                        <span class="text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                width="16" height="16" fill="currentColor"
                                                class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                            </svg>  Completed | January 6, 2024
                                        </span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start border-0 text-start mb-1"
                                style="background: inherit;gap:10px">
                                <span style="width: 20px;height:20px" class="text-success mt-1">
                                    <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"
                                        style="max-width:100%" height="100%">
                                        <path
                                            d="M6 0c3.3137 0 6 2.6863 6 6s-2.6863 6-6 6-6-2.6863-6-6 2.6863-6 6-6zm2.6464 3.6464L5 7.293 3.3536 5.6464l-.7072.7072L5 8.707l4.3536-4.3535-.7072-.7072z"
                                            fill="currentColor" fill-rule="evenodd" style=""></path>
                                    </svg>
                                </span>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-secondary fs-5">Degree Attestation </span>
                                    <span class="text-secondary"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="16" height="16" fill="currentColor"
                                            class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                        </svg> Initiated | December 14, 2023 </span>
                                        <span class="text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                width="16" height="16" fill="currentColor"
                                                class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                            </svg>  Completed | January 6, 2024
                                        </span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start border-0 text-start mb-1"
                            style="background: inherit;gap:10px">
                            <span style="width: 20px;height:20px" class="text-success mt-1">
                                <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"
                                    style="max-width:100%" height="100%">
                                    <path
                                        d="M6 0c3.3137 0 6 2.6863 6 6s-2.6863 6-6 6-6-2.6863-6-6 2.6863-6 6-6zm2.6464 3.6464L5 7.293 3.3536 5.6464l-.7072.7072L5 8.707l4.3536-4.3535-.7072-.7072z"
                                        fill="currentColor" fill-rule="evenodd" style=""></path>
                                </svg>
                            </span>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-secondary fs-5">Police Clearance </span>
                                <span class="text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor"
                                        class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                    </svg> Initiated | December 14, 2023 
                                </span>
                                <span class="text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor"
                                        class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                    </svg>  Completed | January 6, 2024
                                </span>
                            </div>
                        </li>
                            
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 border-1 p-4" style="background: rgba(111, 116, 144, 0.06)">
                    <h5 class="text-start head-color fw-bold pb-4 border-bottom">Onboarding Updates</h5>
                    <h6 class="text-start mt-2 mb-4 fw-bold text-secondary">January 28, 2024</h6>
                    <p class="text-start text-secondary">We are optimistic that the approval for his visa is on track
                        and foresee it being granted on or before February 15th. Our expectations are based on the
                        current processing timelines, and we are diligently monitoring the progress to ensure a timely
                        and successful outcome. Rest assured, we are committed to keeping you informed of any
                        developments regarding the visa approval process.</p>

                </div>
            </div>
        </div>
    </div>
</main>
@include('includes.footer')
