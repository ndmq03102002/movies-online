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
                        <h2>Sign Up</h2>
                        <p>Welcome to the official Anime blog.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Signup Section Begin -->
    <section class="signup spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Sign Up</h3>
                        <form action="{{route('register')}}" method="POST">
                            @csrf
                            <div class="input__item">
                                <input type="text" name="username" placeholder="Username">
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="text" name="email" placeholder="Email address">
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Password">
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" class="site-btn">Sign Up</button>
                        </form>
                        <h5>Already have an account? <a href="{{route('auth.login')}}">Login!</a></h5>
                    </div>
                </div>
                @include('pages.include.social')
            </div>
        </div>
    </section>
    <!-- Signup Section End -->

    @include('pages.include.footer')

    @include('pages.include.search')

    <!-- Js Plugins -->
    @include('pages.include.script')

</body>

</html>


