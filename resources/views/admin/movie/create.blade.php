@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('movie.store');
        $title = 'Thêm mới phim';
        $movie = null; // Đảm bảo biến $genre không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('movie.update', $movie->id);
        $title = 'Cập nhật phim';
        $method = 'PUT';
    }
@endphp

@include('admin.dashboard.component.breadcrumb', ['title' => $title])
    <form action="{{$url}}" method="POST" enctype = "multipart/form-data" >
    @csrf
    @method($method)
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">Thông tin phim</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của phim mới</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Name <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        id="slug"
                                        value="{{ old('name', $movie->name ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                        onkeyup="ChangeToSlug()"
                                    >
                                    @if($errors->has('name'))
                                        <p class="error-message">* {{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Slug </label>
                                    <input 
                                        type="text"
                                        name="slug"
                                        id="convert_slug"
                                        value="{{ old('name', $movie->slug ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="description" class="control-label text-left">Description</label>
                                    <textarea 
                                        type="text"
                                        name="description"
                                        id="description"
                                        
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                        stlye = "resize: none;"
                                        rows = "6"
                                    >{{ old('description', $movie->description ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name_en" class="control-label text-left">English Name</label>
                                    <input 
                                        type="text"
                                        name="name_en"
                                        id="name_en"
                                        value="{{ old('name_en', $movie->name_en ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="trailer" class="control-label text-left">Trailer</label>
                                    <input 
                                        type="text"
                                        name="trailer"
                                        id="trailer"
                                        value="{{ old('trailer', $movie->trailer ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="thoiluong" class="control-label text-left">Thời lượng</label>
                                    <input 
                                        type="text"
                                        name="thoiluong"
                                        id="thoiluong"
                                        value="{{ old('thoiluong', $movie->thoiluong ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="tags" class="control-label text-left">Tags</label>
                                    <input 
                                        type="text"
                                        name="tags"
                                        id="tags"
                                        value="{{ old('tags', $movie->tags ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="sotap" class="control-label text-left">Số tập</label>
                                    <input 
                                        type="text"
                                        name="sotap"
                                        id="sotap"
                                        value="{{ old('sotap', $movie->sotap ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="count_views" class="control-label text-left">Số view</label>
                                    <input 
                                        type="text"
                                        name="count_views"
                                        id="count_views"
                                        value="{{ old('count_views', $movie->count_views ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>                
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="category_id" class="control-label text-left">Category</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        @foreach($category as $key => $value)
                                            <option value="{{ $key }}" {{ isset($movie) && $movie->category_id == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>                                  
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="genre_id" class="control-label text-left">Genre</label>
                                    <select name="genre_id[]" id="genre_id" class="form-control select2" multiple="multiple">
                                        @foreach($genre as $key => $value)
                                            <option value="{{ $key }}" 
                                                {{-- Nếu đang ở chế độ edit và genre_id của movie khớp với id thì chọn nó --}}
                                                {{ isset($movie) && $movie->genres->contains($key) ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="country_id" class="control-label text-left">Country</label>
                                    <select name="country_id" id="country_id" class="form-control">
                                        @foreach($country as $key => $value)
                                            <option value="{{ $key }}" {{ isset($movie) && $movie->country_id == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>                                  
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="year_id" class="control-label text-left">Year</label>
                                    <select name="year_id" id="year_id" class="form-control">
                                        @foreach($year as $key => $value)
                                            <option value="{{ $key }}" {{ isset($movie) && $movie->year_id == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>                                  
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="topview" class="control-label text-left">TopView</label>
                                    <select name="topview" class="form-control">
                                        <option value="0" {{ isset($movie) && $movie->topview == '0' ? 'selected' : '' }}>Không</option>
                                        <option value="1" {{ isset($movie) && $movie->topview == '1' ? 'selected' : '' }}>Có</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="new_comment" class="control-label text-left">NewComment</label>
                                    <select name="new_comment" class="form-control">
                                        <option value="0" {{ isset($movie) && $movie->new_comment == '0' ? 'selected' : '' }}>Không</option>
                                        <option value="1" {{ isset($movie) && $movie->new_comment == '1' ? 'selected' : '' }}>Có</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="phim_hot" class="control-label text-left">Phim hot</label>
                                    <select name="phim_hot" class="form-control">
                                        <option value="0" {{ isset($movie) && $movie->phim_hot == '0' ? 'selected' : '' }}>Không</option>
                                        <option value="1" {{ isset($movie) && $movie->phim_hot == '1' ? 'selected' : '' }}>Có</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="quality" class="control-label text-left">Quanlity</label>
                                    <select name="quality" class="form-control">
                                        <option value="0" {{ isset($movie) && $movie->quality == '0' ? 'selected' : '' }}>HD</option>
                                        <option value="1" {{ isset($movie) && $movie->quality == '1' ? 'selected' : '' }}>FullHD</option>
                                        <option value="2" {{ isset($movie) && $movie->quality == '2' ? 'selected' : '' }}>Trailer</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="phude" class="control-label text-left">Phụ đề</label>
                                    <select name="phude" class="form-control">
                                        <option value="0" {{ isset($movie) && $movie->phude == '0' ? 'selected' : '' }}>Vietsub</option>
                                        <option value="1" {{ isset($movie) && $movie->phude == '1' ? 'selected' : '' }}>Thuyết minh</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="thuocphim" class="control-label text-left">Thuộc loại phim</label>
                                    <select name="thuocphim" class="form-control">
                                        <option value="0" {{ isset($movie) && $movie->thuocphim == '0' ? 'selected' : '' }}>Phim bộ</option>
                                        <option value="1" {{ isset($movie) && $movie->thuocphim == '1' ? 'selected' : '' }}>Phim lẻ</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="season" class="control-label text-left">Season</label>
                                    <select name="season" id="season" class="form-control">
                                        @for($key = 0; $key <= 20; $key++)
                                            <option value="{{ $key }}" {{ isset($movie) && $movie->season == $key ? 'selected' : '' }}>
                                                {{ $key }}
                                            </option>
                                        @endfor

                                    </select>                                  
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="image_option" class="control-label">Chọn cách tải ảnh:</label>
                                    <div class="form-check">
                                        <input type="radio" name="image_option" id="url_option" value="url" class="form-check-input" checked>
                                        <label class="form-check-label" for="url_option">Nhập URL ảnh</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="image_option" id="upload_option" value="upload" class="form-check-input" >
                                        <label class="form-check-label" for="upload_option">Tải ảnh từ máy</label>
                                    </div>
                                    
                                </div>
                                <!-- Input nhập URL ảnh -->
                                <div class="form-row mt-3" id="url_image" >
                                    <label for="image_url" class="control-label text-left">URL ảnh đại diện <span class="text-danger">(*)</span></label>
                                    <input type="text" name="image_url" id="image_url" class="form-control" placeholder="Nhập URL ảnh">
                                    @if(isset($movie))
                                        @if (filter_var($movie->image, FILTER_VALIDATE_URL)) 
                                            <!-- Nếu là URL từ bên ngoài -->
                                            <img src="{{ $movie->image }}" alt="{{ $movie->name }}" width="100" />
                                        @else
                                            <!-- Nếu là đường dẫn cục bộ (tải lên từ máy) -->
                                            <img src="{{ asset('uploads/movie/' . $movie->image) }}" alt="{{ $movie->name }}" width="100" />
                                        @endif
                                    @endif
                                </div>
                                <!-- Input tải ảnh từ máy -->
                                <div class="form-row mt-3" id="upload_image" style="display: none;">
                                    <label for="image" class="control-label text-left">Ảnh đại diện <span class="text-danger">(*)</span></label>
                                    <input type="file" name="image" id="image" class="form-control">
                                    @if(isset($movie))
                                        @if (filter_var($movie->image, FILTER_VALIDATE_URL)) 
                                            <!-- Nếu là URL từ bên ngoài -->
                                            <img src="{{ $movie->image }}" alt="{{ $movie->name }}" width="100" />
                                        @else
                                            <!-- Nếu là đường dẫn cục bộ (tải lên từ máy) -->
                                            <img src="{{ asset('uploads/movie/' . $movie->image) }}" alt="{{ $movie->name }}" width="100" />
                                        @endif
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        
                        <script>
                            document.getElementById('url_option').addEventListener('click', function() {
                                document.getElementById('url_image').style.display = 'block';
                                document.getElementById('upload_image').style.display = 'none';
                            });
                            document.getElementById('upload_option').addEventListener('click', function() {
                                document.getElementById('url_image').style.display = 'none';
                                document.getElementById('upload_image').style.display = 'block';
                            });
                        </script>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <!-- Input nhập URL ảnh -->
                                <div class="form-row mt-3" id="url_image" >
                                    <label for="poster" class="control-label text-left">URL ảnh poster </label>
                                    <input type="text" 
                                    name="poster" 
                                    id="poster" 
                                    class="form-control" 
                                    placeholder="Nhập URL ảnh"
                                    value="{{ old('poster', $movie->poster ?? '') }}"
                                    >
                                    @if(isset($movie))
                                        <img src="{{ $movie->poster }}" alt="{{ $movie->name }}" width="100" />
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="status" class="control-label text-left">Active</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ isset($movie) && $movie->status == '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ isset($movie) && $movie->status == '0' ? 'selected' : '' }}>Không</option>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection