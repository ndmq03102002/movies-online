@extends('layout')
@section('content')

<div class="breadcrumb-option">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="breadcrumb__links">
          <a href="{{route('homepage')}}"><i class="fa fa-home"></i> Home</a>
          <a href="{{route('category',$movie->category->slug)}}">{{$movie->category->name}}</a>
          <a href="{{route('genre',$movie->genres[0]->slug)}}">{{$movie->genres[0]->name}}</a>

          <span>{{$movie->name}}</span>
        </div>
      </div>
    </div>
  </div>
</div>
<section class="anime-details spad">
    <div class="container">
      <div class="anime__details__content">

        <div class="row">
          <div class="col-lg-3">
            @if (filter_var($movie->image, FILTER_VALIDATE_URL)) 
              <!-- Nếu là URL từ bên ngoài -->
              <div
                class="anime__details__pic set-bg"
                data-setbg="{{$movie->image}}" alt="{{ $movie->name }}"
              >
              @else
              <!-- Nếu là đường dẫn cục bộ (tải lên từ máy) -->
              <div
                class="anime__details__pic set-bg"
                {{-- data-setbg="{{ asset('uploads/movieie/' . $movieie->image) }}" alt="{{ $movieie->name }}" --}} 
                data-setbg="{{ asset('uploads/movie/' . $movie->image) }}" alt="{{ $movie->name }}" loading="lazy"
              >
            @endif
              <div class="comment"><i class="fa fa-comments"></i> {{ $movie->comments->count()}} </div>
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
          </div>
          <div class="col-lg-9">
            <div class="anime__details__text">
              <div class="anime__details__title">
                <h3>{{$movie->name}}</h3>
                <span>{{$movie->name_en}}</span>
              </div>
             
              <style>
                ul.list-inline {
                    padding: 0;
                    margin: 0;
                    list-style: none;
                }

                ul.list-inline .rating1 {
                    display: inline-block;
                    margin-right: 5px;
                    pointer-events: auto;
                }

              </style>
              <div class="anime__details__rating">
                <div class="rating" id="rating-stars">
                    <ul class = "list-inline" title = "Average Rating">
                      @for($count=1; $count <= 5; $count++)
                        @php
                          if($count <= $ratings) {
                            $color = 'color: #ffcc00;';
                          } else {
                            $color = 'color: #ccc;';
                          }
                        @endphp
                        <li class = "rating1" title = "start_rating" 
                        id="{{ $movie->id }}-{{ $count }}" 
                        data-index="{{ $count }}" 
                        data-movie_id="{{ $movie->id }}" 
                        data-rating="{{ $ratings }}"
                        style="cursor:pointer; {{$color}}; font-size: 30px;">&#9733;</li>
                        </li>
                      @endfor 
                    </ul>
                </div>
                <span>{{$totalVotes}} Votes</span>
            </div>
              <p>
                {{strip_tags($movie->description)}}
              </p>
              <div class="anime__details__widget">
                <div class="row">
                  <div class="col-lg-6 col-md-6">
                    <style>
                      /* làm trăng màu chữ của thẻ a trong thẻ ul */
                      .col-lg-6 ul li a {
                          color: white !important;
                      }
                    </style>
                    <ul>
                      <li><span>Type:</span> {{$movie->category->name}}</li>
                      <li><span>Episode:</span> {{$movie->episode->count()}}   / {{$movie->sotap}}</li>
                      <li><span>Country:</span> {{$movie->country->name}}</li>
                      <li><span>Year:</span> {{$movie->year->name}}</li>
                      <li>
                        <span>Genre:</span> 
                        @foreach($movie->movie_genre->take(3) as $gen)
                            <a href="{{ route('genre', $gen->slug) }}">{{ $gen->name }}  </a>
                            @if (!$loop->last), @endif
                        @endforeach
                      </li>
                    </ul>
                  </div>
                  <div class="col-lg-6 col-md-6">
                    <ul>
                      <li><span>Sub:</span> {{$movie->phude == 0 ? "Vietsub" : "Thuyết minh"}}</li>
                      <li><span>Status:</span> {{$movie->status == 1 ? "Đang khởi chiếu" : "Tạm ẩn"}}</li>
                      <li><span>Duration:</span> {{$movie->thoiluong}}</li>
                      <li><span>Quality:</span> {{$movie->quality == 0 ? "HD" : "FullHD"}}</li>
                      <li><span>Views:</span>
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
                        
                        </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="anime__details__btn">
                @php
                    // Lấy danh sách slug của các phim yêu thích
                    $favoriteSlugs = $favorites->pluck('movie_slug')->toArray();
                @endphp

                @if(in_array($movie->slug, $favoriteSlugs))
                    <form action="{{ route('favorites.destroy', $movie->slug) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE') <!-- Thêm phương thức DELETE -->
                        <button type="submit" class="follow-btn">
                            <i class="fa fa-heart"></i> Unfollow
                        </button>
                    </form>
                @else
                    <form action="{{ route('favorites.store', $movie->slug) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="follow-btn">
                            <i class="fa fa-heart-o"></i> Follow
                        </button>
                    </form>
                @endif

                @if(isset($tapdau))
                <a href="{{route('watch',['slug'=>$movie->slug, 'tap'=> $tapdau])}}" class="watch-btn" autoload
                  ><span>Watch Now</span> <i class="fa fa-angle-right"></i
                ></a>
                @else
                <a href="{{route('watch_trailer', $movie->slug)}}" class="watch-btn" autoload
                  ><span>Watch Trailer</span> <i class="fa fa-angle-right"></i
                ></a>
                @endif
               

              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="row">
        @include('pages.include.comment')
        <div class="col-lg-4 col-md-4">
          <div class="product__sidebar__comment">
            <div class="section-title">
              <h5>you might like...</h5>
            </div>
            @foreach($related as $mov)
              <a href={{route('movie', $mov->slug)}} autoload>
                <div
                  class="product__sidebar__view__item lazyload"
                  style="background-repeat: no-repeat; background-size: cover; background-position: top center;"
                  
                  data-setbg="{{$mov->poster}}" loading="lazy"
                >
                <div class="ep">{{$mov->episode->count()}} / {{$mov->sotap}}</div>
                <div class="view"><i class="fa fa-eye"></i> {{$mov->count_views}}</div>
                  <h5 style="color: white ; font-weight: bold;" >
                    {{$mov->name}}
                  </h5>
                </div>
                
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
  @endsection

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script>
    function remove_background(movie_id) {
        for (var count = 1; count <= 5; count++) {
            $('#' + movie_id + '-' + count).css('color', '#ccc');
        }
    }

    // hover chuột vào sao
    $(document).on('mouseover', '.rating1', function() {
        var index = $(this).data("index");
        var movie_id = $(this).data("movie_id");
        remove_background(movie_id);
        for (var count = 1; count <= index; count++) {
            $('#' + movie_id + '-' + count).css('color', '#ffcc00');
        }
    });

    // nhả chuột k đánh giá
    $(document).on('mouseleave', '.rating1', function() {
        var movie_id = $(this).data("movie_id");
        var rating = $(this).data("rating");
        remove_background(movie_id);
        for (var count = 1; count <= rating; count++) {
            $('#' + movie_id + '-' + count).css('color', '#ffcc00');
        }
    });

    // click vào sao
    $(document).on('click', '.rating1', function() {
        var index = $(this).data("index");
        var movie_id = $(this).data("movie_id");
        $.ajax({
          url: "{{ route('ratings.store') }}",
          method: "POST",
          data: {
              rating: index,
              movie_id: movie_id,
              _token: "{{ csrf_token() }}"
          },
          success: function(data) {
              if (data['success']) {
                  alert('Bạn đã đánh giá ' + index + ' sao');
                  remove_background(movie_id); // Xóa màu nền hiện tại
                for (var count = 1; count <= index; count++) {
                    $('#' + movie_id + '-' + count).css('color', '#ffcc00'); // Đặt màu vàng cho các sao đã đánh giá
                }
                $('.rating1[data-movie_id="' + movie_id + '"]').data('rating', index);
              } else {
                  alert('Bạn cần đăng nhập để đánh giá phim');
              }
          },
          error: function(jqXHR, textStatus, errorThrown) {
              console.error("Error: " + textStatus + ", " + errorThrown);
              alert('Có lỗi xảy ra: ' + textStatus);
          }
      });

    });
</script>

