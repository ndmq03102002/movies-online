<!DOCTYPE html>
<html lang="zxx">
@include('pages.include.head')

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>
    @include('pages.include.nav')
    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg" data-setbg="fontend/img/normal-breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="normal__breadcrumb__text">
                        <h2>Fogot Your Password</h2>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Login Section Begin -->
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Fogot Your Password</h3>
                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <div class="input__item">
                                <input type="email" name = "email" placeholder="Email address" requied>
                                <span class="icon_mail"></span>
                                @error('email')
                                    <div>{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="site-btn">Send Now</button>
                        </form>
                        
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Dont’t Have An Account?</h3>
                        <a href="{{route('auth.register')}}" class="primary-btn">Register Now</a>
                    </div>
                </div>
            </div>
            @include('pages.include.social')
        </div>
    </section>
    <!-- Login Section End -->

    @include('pages.include.footer')
    @include('pages.include.search')
    <!-- Js Plugins -->
    @include('pages.include.script')

</body>

</html>