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
            <div class="row">
                <div class="col-lg-12">
                    <div class="anime__video__player">
                        {{-- <video id="player" playsinline controls data-poster="fontend/videos/anime-watch.jpg">
                            <source src="fontend/videos/1.mp4" type="video/mp4" />
                            <!-- Captions are optional -->
                            <track kind="captions" label="English captions" src="#" srclang="en" default />
                        </video> --}}
                        <style type="text/css">
                            .iframe_phim {
                                position: relative;
                                overflow: hidden;
                                padding-top: 56.25%; /* Tỷ lệ khung hình 16:9 */
                            }
                            
                            .iframe_phim iframe {
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                border: 0;
                            }
                        </style>
                        
                        <div class="iframe_phim">
                            {{-- Nhúng link video bằng iframe --}}
                            @if(isset($episode->linkphim))
                                <iframe src="{{$episode->linkphim}}" allowfullscreen></iframe>
                            @else
                                <iframe src="https://www.youtube.com/embed/{{$movie->trailer}}" allowfullscreen></iframe>
                            @endif
                        </div>
                        
                        
                    </div>
                    <div class="anime__details__title">
                        <h3>{{$movie->name}}</h3>
                        <br>
                    </div>
                    
                    <div class="anime__details__episodes">
                        <style>
                            .active-episode {
                                color: red !important; /* Màu đỏ để làm nổi bật */
                                font-weight: bold; /* Tô đậm */
                            }

                        </style>
                        @if($movie->thuocphim == 0)
                            <div class="section-title">
                                <h5>List Name</h5>
                            </div>

                            @if(isset($episode->linkphim))
                                @if($movie->source == "ophim" || $movie->source == NULL)
                                    @foreach($episodes as $ep)
                                        <a href="{{ route('watch', ['slug' => $movie->slug, 'tap' => $ep->episode]) }}" autoload
                                        class="{{ request()->tap == $ep->episode ? 'active-episode' : '' }}">
                                        
                                            Tập {{ $ep->episode }}
                                        </a>
                                    @endforeach
                                @else
                                    @foreach($movie->episode as $ep)
                                        <a href="{{ route('watch', ['slug' => $movie->slug, 'tap' => $ep->episode]) }}" autoload
                                        class="{{ request()->tap == $ep->episode ? 'active-episode' : '' }}">
                                        
                                            {{ $ep->episode }}
                                        </a>
                                    @endforeach
                                @endif
                            @else
                                <a>Xem trailer</a>
                            @endif

                        @endif

                    </div>
                </div>
            </div>
            <div class="row">
                @include('pages.include.comment')
                <div class="col-lg-4 col-md-4">
                    <div class="anime__details__sidebar">
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
