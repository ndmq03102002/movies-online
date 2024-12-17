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
                        <h2>PROFILE</h2>
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
                        <h3 >Thông tin cá nhân</h3>
                        <form action="{{route('profile.update')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Họ và tên -->
                            <div class="input__item">
                                <input type="text" name="name" placeholder="Họ và tên" value="{{ old('name', $profile->name ?? '') }}">
                                <span class="icon_profile"></span>
                                @if($errors->has('name'))
                                    <p class="error-message">* {{ $errors->first('name') }}</p>
                                @endif
                            </div>
                    
                            <!-- Ngày sinh -->
                            <div class="input__item">
                                <input 
                                    type="date" 
                                    id="dateofbirth" 
                                    class="form-control" 
                                    name="dateofbirth" 
                                    value="{{ old('dateofbirth', $profile->dateofbirth ?? '') }}">
                                <span class="icon_calendar"></span>
                                @if($errors->has('dateofbirth'))
                                    <p class="error-message">* {{ $errors->first('dateofbirth') }}</p>
                                @endif
                            </div>
                    
                            <!-- Giới tính -->
                            <div class="input__item">
                                <input type="text" name="sex" placeholder="Giới tính" value="{{ old('sex', $profile->sex ?? '') }}">
                                <span class="icon_group"></span>
                                @if($errors->has('name'))
                                    <p class="error-message">* {{ $errors->first('name') }}</p>
                                @endif
                            </div>
                    
                            <!-- Địa chỉ -->
                            <div class="input__item">
                                <input type="text" name="address" placeholder="Địa chỉ" value="{{ old('address', $profile->address ?? '') }}">
                                <span class="icon_map"></span>
                                @if($errors->has('address'))
                                    <p class="error-message">* {{ $errors->first('address') }}</p>
                                @endif
                            </div>
                    
                            <!-- Avatar -->
                            <div class="input__item">
                                <input type="text" name="avatar" 
                                id="image_url" 
                                class="form-control" 
                                placeholder="Nhập URL ảnh"
                                value="{{ old('avatar', $profile->avatar ?? '') }}">
                                >

                                <span class="icon_camera_alt"></span>
                                @if($errors->has('avatar'))
                                    <p class="error-message">* {{ $errors->first('avatar') }}</p>
                                @endif
                               
                                    @if($profile && $profile->avatar)
                                        <img src="{{$profile->avatar}}" alt="Avatar" style="width: 100px; height: 100px; margin-top: 10px;">
                                    @endif
                            </div>
                    
                            <!-- Nút Submit -->
                            <button type="submit" class="site-btn">Cập nhật hồ sơ</button>
                        </form>
                    </div>
                    
                    
                </div>
                {{-- @include('pages.include.social') --}}
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


