@extends('layout')
@section('home')


<section class="hero">
  <div class="container">
    <div class="hero__slider owl-carousel">
      @foreach($phim_hot as $key => $mov)
      
      <div class="hero__items  lazyload" data-setbg="{{$mov->poster}}" loading="lazy" style="background-repeat: no-repeat; background-size: cover; background-position: top center; object-fit: cover; height:auto ; width:auto">
       
        <div class="row">
          <div class="col-lg-6">
            <div class="hero__text">
              <div class="label">Adventure</div>
              <h2>{{$mov->name}}</h2>
              <p>{{ Str::limit(strip_tags($mov->description), 100, '...') }}</p>

              <a href="{{route('movie', $mov->slug)}}"
                ><span>Watch Now</span> <i class="fa fa-angle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- <section class="product spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        @foreach($category->take(2) as $key => $cate)
        <div class="trending__product">
          <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8">
              <div class="section-title">
                <h4>{{$cate->name}}</h4>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="btn__all">
                <a href="{{route('category', $cate->slug)}}" class="primary-btn"
                  >View All <span class="arrow_right"></span
                ></a>
              </div>
            </div>
          </div>
          <div class="row">
            @foreach($cate->movie->take(6) as $key => $movie)
            
            <a href="{{route('movie', $movie->slug)}}">
              <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="product__item">
                  @if (filter_var($movie->image, FILTER_VALIDATE_URL)) 
                  
                  <div
                    class="product__item__pic lazyload "
                    style="background-repeat: no-repeat; background-size: cover; background-position: top center;"
                    data-setbg="{{$movie->image}}"
                    loading="lazy"
                    alt="{{ $movie->name }}"
                  >
                  @else
                 
                  <div
                    class="product__item__pic lazyload"
                    style="background-repeat: no-repeat; background-size: cover; background-position: top center;"
                    data-setbg="{{ asset('uploads/movie/' . $movie->image) }}" loading="lazy" alt="{{ $movie->name }}"
                  >
                @endif
                    <div class="ep">{{$movie->episode->count()}} / {{$movie->sotap}}</div>
                    <div class="comment"><i class="fa fa-comments"></i> {{ $movie->comments->count()}}</div>
                    <div class="view"><i class="fa fa-eye"></i> {{$movie->count_views}}</div>
                  </div>
                  <div class="product__item__text">
                    <ul>
                      <li>Active</li>
                      <li>Movie</li>
                    </ul>
                    <h5>
                      <a href="{{route('movie', $movie->slug)}}">{{$movie->name}}</a>
                    </h5>
                  </div>
                </div>
              </div>
            </a>
            @endforeach
          </div>
        </div>
        @endforeach
        
      </div>
      @include('pages.include.topview')
    </div>
  </div>
</section> --}}

<style>
.movie-section .col-lg-4, 
.movie-section .col-md-6, 
.movie-section .col-sm-6 {
  width: 33.33%; /* Chia 3 cột cho các thiết bị */
  flex: 0 0 33.33%;
}

.movie-section .product__item__pic {
  background-repeat: no-repeat; 
  background-size: cover; 
  background-position: top center;
  padding-top: 150%; 
  position: relative; 
  width: 100%; 
  height: 100%; 
  object-fit: cover;
}

.movie-section .movie-title {
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

</style>

<section class="product spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        @foreach($category->take(4) as $key => $cate)
        <div class="trending__product">
          <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8">
              <div class="section-title">
                <h4>{{$cate->name}}</h4>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="btn__all">
                <a href="{{route('category', $cate->slug)}}" class="primary-btn"
                  >View All <span class="arrow_right"></span
                ></a>
              </div>
            </div>
          </div>
          <div class="row movie-section"> <!-- Thêm class movie-section -->
            @foreach($cate->movie->take(6) as $key => $movie)
            
            <a href="{{route('movie', $movie->slug)}}">
              <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="product__item">
                  @if (filter_var($movie->image, FILTER_VALIDATE_URL)) 
                  <!-- Nếu là URL từ bên ngoài -->
                  <div
                    class="product__item__pic lazyload"
                    data-setbg="{{$movie->image}}"
                    loading="lazy"
                    alt="{{ $movie->name }}"
                  >
                  @else
                  <!-- Nếu là đường dẫn cục bộ (tải lên từ máy) -->
                  <div
                    class="product__item__pic lazyload"
                    data-setbg="{{ asset('uploads/movie/' . $movie->image) }}" 
                    loading="lazy" 
                    alt="{{ $movie->name }}"
                  >
                  @endif
                    <div class="ep">{{$movie->episode->count()}} / {{$movie->sotap}}</div>
                    <div class="comment"><i class="fa fa-comments"></i> {{ $movie->comments->count()}}</div>
                    <div class="view"><i class="fa fa-eye"></i> 
                      @php
                        
                        $views = $movie->count_views;

                        if ($views < 1000) {
                            echo $views;
                        } elseif ($views < 1000000) {
                            if ($views % 1000 == 0) {
                                echo ($views / 1000) . 'k';
                            } else {
                              echo number_format(floor(($views / 1000) * 10) / 10, 1, '.', '') . 'k';
                            }
                        } elseif($views < 1000000000) {
                            if ($views % 1000000 == 0) {
                                echo ($views / 1000000) . 'tr';
                            } else {
                              echo number_format(floor(($views / 1000000) * 10) / 10, 1, '.', '') . 'tr';
                            }
                          } else {
                              if ($views % 1000000000 == 0) {
                                  echo ($views / 1000000000) . 'tỉ';
                              } else {
                                echo number_format(floor(($views / 1000000000) * 10) / 10, 1, '.', '') . 'tỉ';
                              }
                        }
                      @endphp
                    </div>
                  </div>
                  <div class="product__item__text">
                    <ul>
                      <li>Active</li>
                      <li>Movie</li>
                    </ul>
                    <h5>
                      <a href="{{ route('movie', $movie->slug) }}" class="movie-title">
                        {{ $movie->name }}
                      </a>
                    </h5>                      
                  </div>
                </div>
              </div>
            </a>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      <!-- Phần include không bị ảnh hưởng bởi CSS bên trên -->
      @include('pages.include.topview')
    </div>
  </div>
</section>


<script>
  document.addEventListener("DOMContentLoaded", function() {
      const lazyElements = document.querySelectorAll('.lazyload');
      const observer = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
              if (entry.isIntersecting) {
                  const img = entry.target;
                  const bgImage = img.getAttribute('data-setbg');
                  img.style.backgroundImage = `url(${bgImage})`;
                  img.classList.remove('lazyload');
                  observer.unobserve(img);
              }
          });
      });
  
      lazyElements.forEach(element => {
          observer.observe(element);
      });
  });
  
  </script>

@endsection



