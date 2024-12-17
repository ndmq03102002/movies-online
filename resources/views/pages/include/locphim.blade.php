<div class="breadcrumb-option">
  <div class="container">
      <form action="{{ route('locphim') }}" method="GET">
          <div class="row g-3">
              <div class="col-lg-2">
                  <select name="orderBy">
                      <option value="UpdateOn" {{ request('orderBy') == 'UpdateOn' ? 'selected' : '' }}>Mới cập nhật</option>
                      <option value="ViewNumber" {{ request('orderBy') == 'ViewNumber' ? 'selected' : '' }}>Lượt xem</option>
                      <option value="rating" {{ request('orderBy') == 'rating' ? 'selected' : '' }}>Đánh giá cao</option>
                  </select>
              </div>
              <div class="col-lg-2">
                  <select name="category">
                    <option value="">Danh mục</option>
                    @php
                        // Lấy slug từ URL hiện tại
                        $currentSlug = Request::segment(2); // Lấy segment thứ 2 của URL (sau '/danh-muc/')
                        $selectedCategory = $category_home->firstWhere('slug', $currentSlug); // Tìm category theo slug
                    @endphp
                    @foreach($category_home as $key => $cate)
                        @if(isset($selectedCategory))
                        <option value="{{ $cate->id }}" 
                                {{  $selectedCategory->id == $cate->id ? 'selected' : '' }}>
                            {{ $cate->name }}
                        </option>
                        @else
                            <option value="{{ $cate->id }}" {{ request('category') == $cate->id ? 'selected' : '' }}>{{ $cate->name }}</option>
                        @endif
                    @endforeach
                  </select>
              </div>
              <div class="col-lg-2">
                  <select name="genre">
                    @php
                        // Lấy slug từ URL hiện tại
                        $currentSlugGenre = Request::segment(2); // Lấy segment thứ 2 của URL (sau '/the-loai/')
                        $selectedGenre = $genre_home->firstWhere('slug', $currentSlugGenre); // Tìm category theo slug
                    @endphp
                      <option value="">Thể loại</option>
                      @foreach($genre_home as $key => $gen)
                      @if(isset($selectedGenre))
                      <option value="{{ $gen->id }}" 
                              {{  $selectedGenre->id == $gen->id ? 'selected' : '' }}>
                          {{ $gen->name }}
                      </option>
                      @else
                          <option value="{{ $gen->id }}" {{ request('genre') == $gen->id ? 'selected' : '' }}>{{ $gen->name }}</option>
                      @endif
                      @endforeach
                  </select>
              </div>
              <div class="col-lg-2">
                  <select name="country">
                      <option value="">Quốc gia</option>
                      @foreach($country_home as $key => $country)
                          <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-lg-2">
                  <select name="year">
                      <option value="">Năm</option>
                      @foreach($year_home as $key => $year)
                          <option value="{{ $year->id }}" {{ request('year') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-lg-2">
                  <button type="submit" class="btn btn-primary">Lọc phim</button>
              </div>
          </div>
      </form>
  </div>
</div>
