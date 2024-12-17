
<header class="header">
    <div class="container">
      <div class="row">
        <div class="col-lg-2">
          <div class="header__logo">
            <a href="{{route('homepage')}}">
              <img src="fontend/img/logo.png" alt=""  />
            </a>
          </div>
        </div>
        <div class="col-lg-10">
          <div class="header__nav">
            <nav class="header__menu mobile-menu">
              <ul> 
                @if(isset($config) && $config['method'] == 'homepage')
                    <li class="active"><a href="{{route('homepage')}}">Trang chủ</a></li>
                @else
                <li><a href="{{route('homepage')}}" autoload>Trang chủ</a></li>
                @endif                       

                {{-- <li>
                  <a>Danh mục phim <span class="arrow_carrot-down"></span></a>
                  <ul class="dropdown">
                    @foreach($category_home as $key => $cate)
                      <li><a href="{{route('category',$cate->slug)}}">{{$cate->name}}</a></li>
                    @endforeach
                  </ul>
                </li> --}}
                
                @foreach($category_home->take(4) as $key => $cate)
                {{-- Request::is('danh-muc/'.$cate->slug): Đây là hàm kiểm tra URL hiện tại có trùng với URL của category hay không. --}}
                <li class="{{ Request::is('danh-muc/'.$cate->slug) ? 'active' : '' }}"> 
                    <a href="{{route('category', $cate->slug)}}">{{$cate->name}}</a>
                </li>
                @endforeach
                
                <li>
                  <a>Thể loại <span class="arrow_carrot-down"></span></a>
                  <ul class="dropdown">
                    @foreach($genre_home as $key => $gen)
                    <li><a href="{{route('genre',$gen->slug)}}">{{$gen->name}}</a></li>
                    @endforeach
                  </ul>
                </li>
                {{-- <li>
                  <a>Quốc gia <span class="arrow_carrot-down"></span></a>
                  <ul class="dropdown">
                    @foreach($country_home as $key => $count)
                    <li><a href="{{route('country',$count->slug)}}">{{$count->name}}</a></li>
                    @endforeach
                  </ul>
                </li>
                <li>
                  <a>Năm <span class="arrow_carrot-down"></span></a>
                  <ul class="dropdown">
                    @foreach($year_home as $key => $y)
                    <li><a href="{{route('year',$y->slug)}}">{{$y->name}}</a></li>
                    @endforeach
                  </ul>
                </li> --}}
                
                <li>
                  <a href="javascript:void(0)" class="search-switch" onclick="toggleSearch()">
                      <span class="icon_search"></span>
                  </a>
              </li>              
                @if(Auth::check())
                <li>
                  <a><span class="icon_profile"></span></a>
                  <ul class="dropdown">
                    <li><a href="{{route('profile.edit')}}">Thông tin</a></li>
                    <li><a href="{{route('favorites.show')}}">Phim yêu thích</a></li>
                    <li><a href="{{route('change.password' )}}">Thay đổi mật khẩu</a></li>
                    <li><a href="{{route('auth.logout')}}">Đăng xuất</a></li>
                  </ul>
                </li>
                @else
                <li>
                  <a href="{{route('auth.login')}}"><span class="icon_profile"></span></a>
                </li>
                @endif
              </ul>
            </nav>
          </div>
        </div>
       
      </div>
      <div id="mobile-menu-wrap"></div>
    </div>
  </header>
