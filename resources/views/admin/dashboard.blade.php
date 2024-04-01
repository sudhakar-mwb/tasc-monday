
        @include('includes.header')

        <main class="px-3 pt-5">
            <p class="lead mt-3">
                <a href="#" class="btn btn-lg btn-light fs-4 text-dark fw-bold border-white bg-white">Welcome
                    to</a>
            </p>
            <h1 class="header-heading1 mt-2 mb-5 fw-bold " style="color:rgb(3,96,132)">{{ $heading }}</h1>
            <p class="lead">
            <div class="row">
            
                @foreach ($cards as $card)
                    <div class="col-lg-4 d-flex flex-column justify-content-between" style="min-height: 300px">
                        <div>
                          <div class="header-icons">
                                {!! $card['icon'] !!}
                            </div>
                            <h2 class="fw-bold mt-3 mb-3">{{ $card['title'] }}</h2>
                            <p>{{ $card['description'] }}</p>
                        </div>
                        <p>
                            <a class="btn btn-secondary btn-gradiant" href={{$card['link']}}>
                                <span>{{ $card['btn_text'] }}</span> &nbsp;
                                <span>»</span>
                            </a>
                        </p>
                    </div>
                @endforeach
                <!-- /.col-lg-4 --><!-- /.col-lg-4 -->
            </div>
            </p>

        </main>

        @include('includes.footer')

