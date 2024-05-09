<footer class="mt-auto text-white-50">
    <div class="w-100 mt-5 mb-5 align-center text-secondary fs-6">
        <small>Powered by TASC Outsourcing®</small>
    </div>
    <div id="full-loader" style="display: none">
        <div class="full-loader d-flex align-items-center justify-content-center">
            <img height="90" class="rotating-img" src="{{ asset('asset/loader.png') }}" alt="">
        </div>
    </div>
    </div>
    <style>
        .rotating-img {
            animation: rotate 2s linear infinite;
            /* Adjust duration as needed */
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .full-loader {
            z-index: 200000;
            position: fixed;
            background-color: rgba(255, 255, 255, 0.281);
            backdrop-filter: blur(1px);
            height: 100vh;
            width: 100vw;
            top: 0;
            left: 0;
            /* border: 2px solid rgb(0, 94, 255); */
        }
    </style>
</footer>
</div>
@include('footerscript')
</body>
<script>
    // Function to check if an element is visible in the viewport
    // Function to check if an element is visible in the viewport
    function isElementVisible(element) {
        const rect = element.getBoundingClientRect();
        return (rect.top >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) || rect
            .top <= (window.innerHeight || document.documentElement.clientHeight));
    }

    // Function to trigger animations for all visible elements
    function triggerAnimations() {
        const animationContainers = document.querySelectorAll('.animation-container');
        animationContainers.forEach(function(container) {
            const content = container.querySelector('.animation-content');
            if (content && isElementVisible(content)) {
                content.classList.add('show');
            }
        });
    }

    // Function to add scroll event listener
    function addScrollListener() {
        window.addEventListener('scroll', function() {
            triggerAnimations();
        });
    }

    // Trigger animations initially when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        triggerAnimations();
        addScrollListener();
    });


    function showLoader() {
        $("#full-loader").show();
    }

    function hideLoader() {
        $("#full-loader").hide();
    }
    $(document).ready(function() {
        window.addEventListener('beforeunload', function() {
            $("#full-loader").show();
            setTimeout(() => {
                $("#full-loader").hide();
            }, 6000);
        });
    })
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

</html>
