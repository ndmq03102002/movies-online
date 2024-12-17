@extends('layout')
@section('content')

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="{{route('homepage')}}"><i class="fa fa-home"></i> Home</a>
                    <a href="{{route('homepage')}}">Categories</a>
                    <span>{{$cate_slug->name}}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.include.locphim')

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
<section class="product-page spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="product__page__content">
                    <div class="product__page__title">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-6">
                                <div class="section-title">
                                    <h4>{{$cate_slug->name}}</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <div class="product__page__filter">
                                    <p>Order by:</p>
                                    <select>
                                        <option value="">A-Z</option>
                                        <option value="">1-10</option>
                                        <option value="">10-50</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row movie-section">
                      @foreach($movies as $key => $mov)
                        <a href={{route('movie', $mov->slug)}}>
                          <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="product__item">
                              @if (filter_var($mov->image, FILTER_VALIDATE_URL)) 
                              <!-- Nếu là URL từ bên ngoài -->
                              <div
                                class="product__item__pic lazyload"
                                style="background-repeat: no-repeat; background-size: cover; background-position: top center;"
                                data-setbg="{{$mov->image}}" alt="{{ $mov->name }}" loading="lazy"
                              >
                              @else
                              <!-- Nếu là đường dẫn cục bộ (tải lên từ máy) -->
                              <div
                                class="product__item__pic lazyload"
                                style="background-repeat: no-repeat; background-size: cover; background-position: top center;"
                                {{-- data-setbg="{{ asset('uploads/movie/' . $movie->image) }}" alt="{{ $movie->name }}" --}} 
                                data-setbg="{{ asset('uploads/movie/' . $mov->image) }}" alt="{{ $mov->name }}" loading="lazy"
                              >
                            @endif
                                <div class="ep">{{$mov->episode->count()}} / {{$mov->sotap}}</div>
                                <div class="comment">
                                  <i class="fa fa-comments"></i>
                                  <!-- Tính tổng số lượng comment của tất cả các tập của phim này -->
                                  {{ $mov->episode->reduce(function($carry, $episode) {
                                      return $carry + $episode->comments->count();
                                  }, 0) }}
                                </div>
                                <div class="view"><i class="fa fa-eye"></i> 
                                  @php
                        
                                      $views = $mov->count_views;

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
                                  <a href="{{route('movie', $mov->slug)}}" class="movie-title">{{$mov->name}}</a>
                                </h5>
                              </div>
                            </div>
                          </div>
                        </a>
                      @endforeach
                    </div>
                </div>
                {{$movies->links('pagination::bootstrap-4')}}
               
            </div>
            @include('pages.include.topview')
        </div>
    </div>
</section>



@endsection
