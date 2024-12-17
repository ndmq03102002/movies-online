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
                        <h2>Reset Password</h2>
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
                        <h3>Reset Password</h3>
                        <style>
                            .error-message {
                                color: red;
                                font-size: 20px;
                            }
                        </style>
                        <form action="{{ route('password.update', $token)}}" method="POST">
                            @csrf
                            <div class="input__item">
                                <input type="text" name = "email" value="{{$email}}" readonly>
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Password" requied>
                                <span class="icon_lock"></span>
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input__item">
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" requied>
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" class="site-btn">Reset Password</button>
                        </form>
                        
                    </div>
                </div>
                
            </div>
            
        </div>
    </section>
    <!-- Login Section End -->

    @include('pages.include.footer')
    @include('pages.include.search')
    <!-- Js Plugins -->
    @include('pages.include.script')

</body>

</html>