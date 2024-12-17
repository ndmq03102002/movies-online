<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use App\Models\Country;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Year;
use App\Models\Episode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovieAPIController extends Controller
{
    public function index()
    {
        return view('admin.movie.form');
    }
    public function index1()
    {
        return view('admin.movie.form2');
    }
    public function show()
    {
        return view('admin.movie.form1');
    }
    public function showkk($slug) // hiển thị thông tin phim qua api
    {
        $url = "https://phimapi.com/phim/{$slug}";

        $response = Http::get($url);

        if ($response->successful() && $response['status']) {
            $movie = $response['movie'];

            // Lấy thông tin cần thiết
            $data = [
                'name' => $movie['name'],
                'type' => $movie['type'],
                'content' => $movie['content'],
                'trailer_url' => $movie['trailer_url'],
                'time' => $movie['time'],
                'episode_total' => $movie['episode_total'],
                'quality' => $movie['quality'],
                'lang' => $movie['lang'],
                'year' => $movie['year'],
                'categories' => array_map(fn($category) => $category['name'], $movie['category']),
                'countries' => array_map(fn($country) => $country['name'], $movie['country']),
            ];
            $linkEmbed = isset($response['episodes'][0]['server_data'][0]['link_embed'])
                ? $response['episodes'][0]['server_data'][0]['link_embed']
                : null;

            $data['link_embed'] = $linkEmbed; // Thêm link_embed vào dữ li

            return view('admin.movie.show1', compact('data'));
        } else {
            // Xử lý lỗi nếu không tìm thấy phim
            return redirect()->back()->with('error', 'Phim không tồn tại.');
        }
    }


    public function store(Request $request)
    {

        // Validate URL input
        $request->validate([
            'api_url' => 'required|url',
        ], [
            'api_url.required' => 'Bạn phải nhập URL API.',
            'api_url.url' => 'URL không hợp lệ.',
        ]);

        // Lấy dữ liệu từ API
        $response = Http::timeout(240)->get($request->api_url);
        // Lấy tất cả các quốc gia từ cơ sở dữ liệu
        $countries = Country::pluck('id', 'slug')->toArray(); // ['Việt nam' => 4, 'Mỹ' => 5, ...]
        $years = Year::pluck('id', 'slug')->toArray(); // ['2021' => 1, '2020' => 2, ...]
        $genres = Genre::pluck('id', 'slug')->toArray(); // ['Hành động' => 1, 'Tình cảm' => 2, ...]

        // Kiểm tra API phản hồi thành công
        if ($response->successful()) {
            $data = $response->json();

            // Kiểm tra trạng thái phản hồi
            if ($data['status'] === true && isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    // Kiểm tra xem phim đã tồn tại chưa
                    $existingMovie = Movie::where('slug', $item['slug'])->first();
                    if (!$existingMovie) {
                        // Thêm phim mới
                        $movie = new Movie();
                        $movie->name = $item['name'];
                        $movie->slug = $item['slug'];
                        $movie->name_en = $item['origin_name'];
                        $movie->image = $data['pathImage'] . $item['thumb_url'];
                        // Kiểm tra xem poster_url có phải là một chuỗi hợp lệ (không phải base64 hoặc định dạng URL không hợp lệ)
                        if (isset($item['poster_url']) && !empty($item['poster_url']) && !preg_match('/^data:image\/[a-z]+;base64,/', $item['poster_url'])) {
                            // Nếu hợp lệ, gán giá trị vào $movie->poster
                            $movie->poster = $data['pathImage'] . $item['poster_url'];
                        } else {
                            // Nếu không hợp lệ, gán giá trị null
                            $movie->poster = null;
                        }

                        // Lấy thông tin chi tiết phim từ API dựa theo slug
                        $res = Http::timeout(240)->get("https://ophim1.com/phim/" . $item['slug']);
                        if ($res->successful()) {
                            $dt = $res->json();
                            if ($dt['status'] === true && isset($dt['movie'])) {

                                $mov = $dt['movie']; // Dữ liệu phim từ API
                                $movie->description = $mov['content'];
                                $parts = explode(' ', $mov['episode_total']);
                                $movie->sotap = $parts[0];
                                if ($mov['trailer_url']) {
                                    $url = $mov['trailer_url'];
                                    $start = strpos($url, "v=") + 2; // Tìm vị trí bắt đầu của mã video
                                    $videoId = substr($url, $start); // Lấy chuỗi từ vị trí bắt đầu
                                    $movie->trailer = $videoId;
                                } else {
                                    $movie->trailer = null;
                                }


                                $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
                                if ($mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1") {
                                    $movie->thuocphim = 1;
                                } else {
                                    $movie->thuocphim = 0;
                                }
                                $movie->thoiluong = $mov['time'];
                                $movie->phude = $mov['lang'] == "Vietsub" ? 0 : 1;
                                $movie->count_views = 0;
                                if ($mov['type'] ==   "single") {
                                    $movie->category_id = 4;
                                } else if ($mov['type'] == "series") {
                                    $movie->category_id = 3;
                                } else if ($mov['type'] == "hoathinh") {
                                    $movie->category_id = 5;
                                } else if ($mov['type'] == "tvshows") {
                                    $movie->category_id = 7;
                                } else if ($mov['chieurap'] == true) {
                                    $movie->category_id = 6;
                                } else {
                                    $movie->category_id = 3;
                                }


                                // Lấy country_id từ tên quốc gia trả về từ API
                                if (!empty($mov['country'])) {
                                    $countryName = $mov['country'][0]['slug']; // Lấy quốc gia đầu tiên
                                    $movie->country_id = $countries[$countryName] ?? 4;
                                } else {
                                    $movie->country_id = 4;
                                }

                                // Lấy year_id từ năm trả về từ API
                                if (!empty($mov['year'])) {
                                    $yearName = $mov['year']; // API trả về năm
                                    $movie->year_id = $years[$yearName] ?? 1;
                                }


                                $genreName = $mov['category'][0]['slug']; // Lấy thể loại đầu tiên
                                $movie->genre_id = $genres[$genreName] ?? 1;
                                $movie->source = "ophim";

                                // Lưu phim
                                $movie->save();

                                // Lấy thể loại đầu tiên và gán cho movie



                                $genreIds = [];
                                foreach ($mov['category'] as $gen) {
                                    $genreName = $gen['slug'];
                                    $genreId = $genres[$genreName] ?? 1;
                                    if ($genreId) {
                                        $genreIds[] = $genreId;
                                    }
                                }

                                // Đồng bộ các thể loại vào bảng movie_genre
                                $movie->genres()->sync($genreIds);
                            } else {
                                return redirect()->back()->with(['error' => 'API không trả về dữ liệu hợp lệ.']);
                            }
                        } else {
                            return redirect()->back()->with(['error' => 'Không thể kết nối đến API.']);
                        }
                        $movie->status = 1;
                        $movie->save();
                    }
                }

                return redirect()->back()->with('success', 'Thêm phim thành công!');
            } else {
                return redirect()->back()->withErrors(['api_url' => 'API không trả về dữ liệu hợp lệ.']);
            }
        } else {
            return redirect()->back()->withErrors(['api_url' => 'Không thể kết nối đến API.']);
        }
    }




    // public function store1(Request $request)// Thêm 1 loạt các page phim từ API ophim
    // {

    //     set_time_limit(1000000);
    //     // Khởi tạo mảng chứa các thông báo lỗi (nếu có)
    //     $errors = [];

    //     for ($page = $request->start; $page <= $request->end; $page++) {
    //         // Lấy dữ liệu từ API
    //         $response = Http::timeout(120)->get('https://ophim1.com/danh-sach/phim-moi-cap-nhat?page=' . $page);

    //         // Lấy tất cả các quốc gia từ cơ sở dữ liệu
    //         $countries = Country::pluck('id', 'slug')->toArray();
    //         $years = Year::pluck('id', 'slug')->toArray();
    //         $genres = Genre::pluck('id', 'slug')->toArray();

    //         // Kiểm tra API phản hồi thành công
    //         if ($response->successful()) {
    //             $data = $response->json();

    //             // Kiểm tra trạng thái phản hồi
    //             if ($data['status'] === true && isset($data['items'])) {
    //                 foreach ($data['items'] as $item) {
    //                     // Kiểm tra xem phim đã tồn tại chưa
    //                     $existingMovie = Movie::where('slug', $item['slug'])->first();
    //                     if (!$existingMovie) {
    //                         // Lấy thông tin chi tiết phim từ API dựa theo slug
    //                         $res = Http::timeout(120)->get("https://ophim1.com/phim/" . $item['slug']);
    //                         if ($res->successful()) {
    //                             $dt = $res->json();
    //                             if ($dt['status'] === true && isset($dt['movie'])) {
    //                                $mov = $dt['movie'];
    //                                 // Thêm phim mới
    //                                 $movie = new Movie();
    //                                 $movie->name = $mov['name'];
    //                                 $movie->slug = $mov['slug'];
    //                                 $movie->name_en = $mov['origin_name'];
    //                                 $movie->image =  $mov['thumb_url'];
    //                                 // Kiểm tra xem poster_url có phải là một chuỗi hợp lệ (không phải base64 hoặc định dạng URL không hợp lệ)
    //                                 if (isset($mov['poster_url']) && !empty($mov['poster_url']) && !preg_match('/^data:image\/[a-z]+;base64,/', $mov['poster_url'])) {
    //                                     // Nếu hợp lệ, gán giá trị vào $movie->poster
    //                                     $movie->poster = $mov['poster_url'];
    //                                 } else {
    //                                     // Nếu không hợp lệ, gán giá trị null
    //                                     $movie->poster = null;
    //                                 }


    //                                 // Cập nhật các trường thông tin cho movie
    //                                 $movie->description = $mov['content'];
    //                                 $parts = explode(' ', $mov['episode_total']);
    //                                 $movie->sotap = $parts[0];
    //                                 if ($mov['trailer_url']) {
    //                                     $url = $mov['trailer_url'];
    //                                     $start = strpos($url, "v=") + 2;
    //                                     $videoId = substr($url, $start);
    //                                     $movie->trailer = $videoId;
    //                                 } else {
    //                                     $movie->trailer = null;
    //                                 }

    //                                 $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
    //                                 $movie->thuocphim = ($mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1") ? 1 : 0;
    //                                 $movie->thoiluong = $mov['time'];
    //                                 $movie->phude = $mov['lang'] == "Vietsub" ? 0 : 1;
    //                                 $movie->count_views = 0;
    //                                 $movie->category_id = match ($mov['type']) {
    //                                     "single" => 4,
    //                                     "series" => 3,
    //                                     "hoathinh" => 5,
    //                                     "tvshows" => 7,
    //                                     default => 6,
    //                                 };

    //                                 // Xử lý country và year
    //                                 if (!empty($mov['country'])) {
    //                                     $countryName = $mov['country'][0]['slug'];
    //                                     $movie->country_id = $countries[$countryName] ?? 4;
    //                                 } else {
    //                                     $movie->country_id = 4;
    //                                 }

    //                                 if (!empty($mov['year'])) {
    //                                     $yearName = $mov['year'];
    //                                     $movie->year_id = $years[$yearName] ?? 1;
    //                                 }

    //                                 // Xử lý genre
    //                                 $genreIds = [];
    //                                 foreach ($mov['category'] as $gen) {
    //                                     $genreName = $gen['slug'];
    //                                     $genreId = $genres[$genreName] ?? 1;
    //                                     if ($genreId) {
    //                                         $genreIds[] = $genreId;
    //                                     }
    //                                 }

    //                                 // Lưu phim và đồng bộ thể loại
    //                                 $movie->source = "ophim";
    //                                 $movie->status = 1;
    //                                 $movie->save();
    //                                 $movie->genres()->sync($genreIds);
    //                             } else {
    //                                 $errors[] = "API không trả về dữ liệu hợp lệ cho slug: {$item['slug']}.";
    //                                 continue;
    //                             }
    //                         } else {
    //                             $errors[] = "Không thể kết nối đến API để lấy thông tin chi tiết phim slug: {$item['slug']}.";
    //                             continue;
    //                         }
    //                     }else{
    //                         $errors[] = "phim đã tồn tại: {$item['slug']}.";
    //                             continue;
    //                         }
    //                 }
    //             } else {
    //                 $errors[] = "API không trả về dữ liệu hợp lệ tại trang {$page}.";
    //                 continue;
    //             }
    //         } else {
    //             $errors[] = "Không thể kết nối đến API tại trang {$page}.";
    //             continue;
    //         }
    //     }

    //     // Kiểm tra nếu có lỗi
    //     if (!empty($errors)) {
    //         return redirect()->back()->withErrors($errors);
    //     }

    //     return redirect()->back()->with('success', 'Thêm phim thành công!');
    // }


    public function store1(Request $request) // Thêm 1 loạt các page phim từ API ophim
    {
        set_time_limit(1000000); // Tăng giới hạn thời gian thực thi nếu cần

        $errors = [];
        $startPage = $request->start;
        $endPage = $request->end;
        $chunkSize = 20; // Chia nhỏ thành các nhóm 10 trang một lần

        // Lấy tất cả các slug của phim đã tồn tại
        $existingSlugs = Movie::pluck('slug')->toArray();

        // Khởi tạo các mảng dữ liệu để ánh xạ
        $countries = Country::pluck('id', 'slug')->toArray();
        $years = Year::pluck('id', 'slug')->toArray();
        $genres = Genre::pluck('id', 'slug')->toArray();

        // Chia nhỏ các trang thành nhiều chunk để xử lý tuần tự
        $pageRange = range($startPage, $endPage);
        $chunks = array_chunk($pageRange, $chunkSize);

        // Xử lý từng chunk
        foreach ($chunks as $chunk) {
            // Sử dụng pool để gửi nhiều request đồng thời cho từng chunk
            $responses = Http::pool(function ($pool) use ($chunk) {
                foreach ($chunk as $page) {
                    $pool->as("page{$page}")->get("https://ophim1.com/danh-sach/phim-moi-cap-nhat?page={$page}");
                }
            });

            // Xử lý kết quả của từng trang dữ liệu
            foreach ($responses as $key => $response) {
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === true && isset($data['items'])) {
                        $items = $data['items'];

                        // Tìm các slug chưa tồn tại trong database
                        $newItems = array_filter($items, function ($item) use ($existingSlugs) {
                            return !in_array($item['slug'], $existingSlugs);
                        });

                        // Tiếp tục nếu có phim mới
                        if (!empty($newItems)) {
                            // Chia nhỏ các newItems để tránh quá tải khi gửi đồng thời nhiều request
                            $newItemChunks = array_chunk($newItems, $chunkSize);

                            foreach ($newItemChunks as $newItemChunk) {
                                // Sử dụng pool để lấy thông tin chi tiết từng phim mới trong từng chunk
                                $movieResponses = Http::pool(function ($pool) use ($newItemChunk) {
                                    foreach ($newItemChunk as $item) {
                                        $slug = $item['slug'];
                                        $pool->as($slug)->get("https://ophim1.com/phim/{$slug}");
                                    }
                                });

                                // Xử lý kết quả của từng phim trong chunk
                                foreach ($newItemChunk as $item) {
                                    $slug = $item['slug'];
                                    $response = $movieResponses[$slug];

                                    if ($response->successful()) {
                                        $dt = $response->json();
                                        if ($dt['status'] === true && isset($dt['movie'])) {
                                            $mov = $dt['movie'];

                                            // Thêm phim mới vào database
                                            $movie = new Movie();
                                            $movie->name = $mov['name'];
                                            $movie->slug = $mov['slug'];
                                            $movie->name_en = $mov['origin_name'];
                                            $movie->image = $mov['thumb_url'];

                                            // Kiểm tra và gán poster_url hợp lệ
                                            if (isset($mov['poster_url']) && !preg_match('/^data:image\/[a-z]+;base64,/', $mov['poster_url'])) {
                                                $movie->poster = $mov['poster_url'];
                                            } else {
                                                $movie->poster = null;
                                            }

                                            $movie->description = $mov['content'];
                                            $parts = explode(' ', $mov['episode_total']);
                                            $movie->sotap = $parts[0];

                                            // Xử lý trailer
                                            if (!empty($mov['trailer_url'])) {
                                                $url = $mov['trailer_url'];
                                                $start = strpos($url, "v=") + 2;
                                                $movie->trailer = substr($url, $start);
                                            } else {
                                                $movie->trailer = null;
                                            }

                                            // Cập nhật các trường khác
                                            $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
                                            $movie->thuocphim = ($mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1") ? 1 : 0;
                                            $movie->thoiluong = $mov['time'];
                                            $movie->phude = $mov['lang'] == "Vietsub" ? 0 : 1;
                                            $movie->count_views = 0;

                                            // Phân loại phim theo category
                                            if ($mov['type'] ==   "single") {
                                                $movie->category_id = 4;
                                            } else if ($mov['type'] == "series") {
                                                $movie->category_id = 3;
                                            } else if ($mov['type'] == "hoathinh") {
                                                $movie->category_id = 5;
                                            } else if ($mov['type'] == "tvshows") {
                                                $movie->category_id = 7;
                                            } else if ($mov['chieurap'] == true) {
                                                $movie->category_id = 6;
                                            } else {
                                                $movie->category_id = 3;
                                            }

                                            // Xử lý country và year
                                            $movie->country_id = $countries[$mov['country'][0]['slug']] ?? 4;
                                            $movie->year_id = $years[$mov['year']] ?? 1;

                                            // Xử lý genre
                                            $genreIds = [];
                                            foreach ($mov['category'] as $gen) {
                                                $genreName = $gen['slug'];
                                                $genreId = $genres[$genreName] ?? 1;
                                                if ($genreId) {
                                                    $genreIds[] = $genreId;
                                                }
                                            }
                                            $movie->genre_id = $genreIds[0] ?? 1;
                                            // Lưu phim và đồng bộ thể loại
                                            $movie->source = "ophim";
                                            $movie->status = 1;
                                            $movie->save();
                                            $movie->genres()->sync($genreIds);
                                        } else {
                                            $errors[] = "API không trả về dữ liệu hợp lệ cho slug: {$item['slug']}.";
                                        }
                                    } else {
                                        $errors[] = "Không thể lấy thông tin chi tiết phim slug: {$item['slug']}.";
                                    }
                                }
                            }
                        }
                    } else {
                        $errors[] = "API không trả về dữ liệu hợp lệ tại trang {$key}.";
                    }
                } else {
                    $errors[] = "Không thể kết nối đến API tại trang {$key}.";
                }
            }
        }

        // Kiểm tra nếu có lỗi
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Thêm phim thành công!');
    }


    public function storeSingleMovie(Request $request)
    {
        // Validate URL input


        // Lấy dữ liệu từ API
        $response = Http::timeout(120)->get("https://ophim1.com/phim/{$request->api_url}");

        // Kiểm tra API phản hồi thành công
        if ($response->successful()) {
            $data = $response->json();

            // Kiểm tra trạng thái phản hồi
            if ($data['status'] === true && isset($data['movie'])) {
                $mov = $data['movie'];

                // Kiểm tra xem phim đã tồn tại chưa
                $existingMovie = Movie::where('slug', $mov['slug'])->first();
                if (!$existingMovie) {
                    // Thêm phim mới
                    $movie = new Movie();
                    $movie->name = $mov['name'];
                    $movie->slug = $mov['slug'];
                    $movie->name_en = $mov['origin_name'];
                    $movie->image = $mov['thumb_url'];
                    $movie->poster = $mov['poster_url'];
                    $movie->description = $mov['content'];
                    $parts = explode(' ', $mov['episode_total']);
                    $movie->sotap = $parts[0];
                    $movie->trailer = $mov['trailer_url'] ? $this->extractVideoId($mov['trailer_url']) : null;
                    $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
                    $movie->thuocphim = $mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1" ? 1 : 0;
                    $movie->thoiluong = $mov['time'];
                    $movie->phude = $mov['lang'] == "Vietsub" ? 0 : 1;
                    $movie->count_views = 0;
                    $movie->status = 1;
                    $movie->source = "ophim";

                    if ($mov['type'] ==   "single") {
                        $movie->category_id = 4;
                    } else if ($mov['type'] == "series") {
                        $movie->category_id = 3;
                    } else if ($mov['type'] == "hoathinh") {
                        $movie->category_id = 5;
                    } else if ($mov['type'] == "tvshows") {
                        $movie->category_id = 7;
                    } else if ($mov['chieurap'] == true) {
                        $movie->category_id = 6;
                    } else {
                        $movie->category_id = 3;
                    }
                    // Lấy country_id từ tên quốc gia trả về từ API
                    if (!empty($mov['country'])) {
                        $countryName = $mov['country'][0]['slug']; // Lấy quốc gia đầu tiên
                        $movie->country_id = Country::where('slug', $countryName)->value('id') ?? 4;
                    } else {
                        $movie->country_id = 23;
                    }

                    // Lấy year_id từ năm trả về từ API
                    $yearName = $mov['year'] ?? null;
                    $movie->year_id = Year::where('slug', $yearName)->value('id') ?? 1;

                    // Lấy genre_ids và gán cho movie


                    // Lưu phim
                    $movie->save();
                    $genreIds = [];
                    foreach ($mov['category'] as $gen) {
                        $genreName = $gen['slug'];
                        $genreId = $genres[$genreName] ?? 1;
                        if ($genreId) {
                            $genreIds[] = $genreId;
                        }
                    }
                    $movie->genre_id = $genreIds[0] ?? 1;
                    // Đồng bộ các thể loại vào bảng movie_genre
                    $movie->genres()->sync($genreIds);


                    return redirect()->back()->with('success', 'Thêm phim thành công!');
                } else {
                    return redirect()->back()->with(['error' => 'Phim đã tồn tại.']);
                }
            } else {
                return redirect()->back()->withErrors(['api_url' => 'API không trả về dữ liệu hợp lệ.']);
            }
        } else {
            return redirect()->back()->withErrors(['api_url' => 'Không thể kết nối đến API.']);
        }
    }
    public function storeSingleMoviekk(Request $request) // thêm 1 phim nguồn kk
    {
        // Validate URL input


        // Lấy dữ liệu từ API
        $response = Http::timeout(120)->get("https://phimapi.com/phim/{$request->api_url}");

        // Kiểm tra API phản hồi thành công
        if ($response->successful()) {
            $data = $response->json();

            // Kiểm tra trạng thái phản hồi
            if ($data['status'] === true && isset($data['movie'])) {
                $mov = $data['movie'];

                // Kiểm tra xem phim đã tồn tại chưa
                $existingMovie = Movie::where('slug', $mov['slug'])->first();
                if (!$existingMovie) {
                    // Thêm phim mới
                    $movie = new Movie();
                    $movie->name = $mov['name'];
                    $movie->slug = $mov['slug'];
                    $movie->name_en = $mov['origin_name'];
                    $movie->poster = $mov['thumb_url'];
                    $movie->image = $mov['poster_url'];
                    $movie->description = $mov['content'];
                    $movie->sotap = (int)$mov['episode_total'];
                    $movie->trailer = $mov['trailer_url'] ? $this->extractVideoId($mov['trailer_url']) : null;
                    $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
                    $movie->thuocphim = $mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1" ? 1 : 0;
                    $movie->thoiluong = $mov['time'];
                    $movie->phude = $mov['lang'] == "Vietsub" ? 0 : 1;
                    $movie->count_views = 0;
                    $movie->status = 1;
                    $movie->source = "kkphim";

                    if ($mov['type'] ==   "single") {
                        $movie->category_id = 4;
                    } else if ($mov['type'] == "series") {
                        $movie->category_id = 3;
                    } else if ($mov['type'] == "hoathinh") {
                        $movie->category_id = 5;
                    } else if ($mov['type'] == "tvshows") {
                        $movie->category_id = 7;
                    } else if ($mov['chieurap'] == true) {
                        $movie->category_id = 6;
                    } else {
                        $movie->category_id = 3;
                    }
                    // Lấy country_id từ tên quốc gia trả về từ API
                    if (!empty($mov['country'])) {
                        $countryName = $mov['country'][0]['slug']; // Lấy quốc gia đầu tiên
                        $movie->country_id = Country::where('slug', $countryName)->value('id') ?? 4;
                    } else {
                        $movie->country_id = 23;
                    }

                    // Lấy year_id từ năm trả về từ API
                    $yearName = $mov['year'] ?? null;
                    $movie->year_id = Year::where('slug', $yearName)->value('id') ?? 1;

                    // Lấy genre_ids và gán cho movie


                    // Lưu phim
                    $movie->save();
                    $genreIds = [];
                    foreach ($mov['category'] as $gen) {
                        $genreName = $gen['slug'];
                        $genreId = $genres[$genreName] ?? 1;
                        if ($genreId) {
                            $genreIds[] = $genreId;
                        }
                    }
                    $movie->genre_id = $genreIds[0] ?? 1;
                    // Đồng bộ các thể loại vào bảng movie_genre
                    $movie->genres()->sync($genreIds);


                    return redirect()->back()->with('success', 'Thêm phim thành công!');
                } else {
                    return redirect()->back()->with(['error' => 'Phim đã tồn tại.']);
                }
            } else {
                return redirect()->back()->withErrors(['api_url' => 'API không trả về dữ liệu hợp lệ.']);
            }
        } else {
            return redirect()->back()->withErrors(['api_url' => 'Không thể kết nối đến API.']);
        }
    }

    private function extractVideoId($url)
    {
        $start = strpos($url, "v=") + 2; // Tìm vị trí bắt đầu của mã video
        return substr($url, $start); // Lấy chuỗi từ vị trí bắt đầu
    }


    public function storekk(Request $request)
    {
        // Validate URL input
        $request->validate([
            'api_url' => 'required|url',
        ], [
            'api_url.required' => 'Bạn phải nhập URL API.',
            'api_url.url' => 'URL không hợp lệ.',
        ]);

        // Lấy dữ liệu từ API
        try {
            $response = Http::timeout(60)->get($request->api_url);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['api_url' => 'Không thể kết nối đến API.']);
        }

        // Kiểm tra API phản hồi thành công
        if ($response->successful()) {
            $data = $response->json();
            // Kiểm tra trạng thái phản hồi
            if ($data['status'] === "success" && isset($data['data']['items'])) {
                $countries = Country::pluck('id', 'slug')->toArray();
                $years = Year::pluck('id', 'slug')->toArray();
                $genres = Genre::pluck('id', 'slug')->toArray();

                foreach ($data['data']['items'] as $item) {
                    // Kiểm tra xem phim đã tồn tại chưa
                    $existingMovie = Movie::where('slug', $item['slug'])->first();
                    if (!$existingMovie) {
                        $movie = new Movie();
                        $movie->name = $item['name'];
                        $movie->slug = $item['slug'];
                        $movie->name_en = $item['origin_name'];

                        $movie->thoiluong = $item['time'];
                        $movie->phude = $item['lang'] == "Vietsub" ? 0 : 1;

                        // Lấy thông tin chi tiết phim từ API dựa theo slug
                        try {
                            $res = Http::timeout(60)->get("https://phimapi.com/phim/" . $item['slug']);
                            if ($res->successful()) {
                                $dt = $res->json();
                                if ($dt['status'] === true && isset($dt['movie'])) {
                                    $mov = $dt['movie'];
                                    $movie->description = $mov['content'] ?? null;
                                    $movie->poster = $mov['thumb_url'];
                                    $movie->image = $mov['poster_url'];
                                    $parts = explode(' ', $mov['episode_total']);
                                    $movie->sotap = $parts[0];
                                    $movie->trailer = isset($mov['trailer_url']) ? substr($mov['trailer_url'], strpos($mov['trailer_url'], "v=") + 2) : null;
                                    $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
                                    $movie->thuocphim = ($mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1") ? 1 : 0;
                                    $movie->count_views = 0;
                                    if ($mov['type'] ==   "single") {
                                        $movie->category_id = 4;
                                    } else if ($mov['type'] == "series") {
                                        $movie->category_id = 3;
                                    } else if ($mov['type'] == "hoathinh") {
                                        $movie->category_id = 5;
                                    } else if ($mov['type'] == "tvshows") {
                                        $movie->category_id = 7;
                                    } else if ($mov['chieurap'] == true) {
                                        $movie->category_id = 6;
                                    } else {
                                        $movie->category_id = 3;
                                    }


                                    // Lấy country_id
                                    $countryName = $mov['country'][0]['slug'] ?? null;
                                    $movie->country_id = $countryName ? ($countries[$countryName] ?? 4) : 4;

                                    // Lấy year_id
                                    $yearName = $mov['year'] ?? null;
                                    $movie->year_id = $yearName ? ($years[$yearName] ?? 1) : 1;

                                    // Lấy genre_id
                                    $genreIds = [];
                                    foreach ($mov['category'] as $gen) {
                                        $genreName = $gen['slug'];
                                        $genreId = $genres[$genreName] ?? 1;
                                        if ($genreId) {
                                            $genreIds[] = $genreId;
                                        }
                                    }
                                    $movie->genre_id = $genreIds[0] ?? 1; // Thể loại đầu tiên

                                    // Lưu phim
                                    $movie->status = 1;
                                    $movie->source = "kkphim";
                                    $movie->save();

                                    // Đồng bộ các thể loại vào bảng movie_genre
                                    $movie->genres()->sync($genreIds);
                                } else {
                                    return redirect()->back()->with(['error' => 'API không trả về dữ liệu hợp lệ.']);
                                }
                            } else {
                                return redirect()->back()->with(['error' => 'Không thể kết nối đến API.']);
                            }
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['api_url' => 'Không thể kết nối đến API chi tiết.']);
                        }
                    }
                }

                return redirect()->back()->with('success', 'Thêm phim thành công!');
            } else {
                return redirect()->back()->withErrors(['api_url' => 'API không trả về dữ liệu hợp ']);
            }
        } else {
            return redirect()->back()->withErrors(['api_url' => 'Không thể kết nối đến API.']);
        }
    }

    // public function storekk1(Request $request)
    // {

    //     set_time_limit(10000);
    //     // Tạo mảng để lưu thông báo lỗi (nếu có)
    //     $errors = [];

    //     // Lặp qua từ trang 70 đến 75
    //     for ($page = $request->start; $page <= $request->end; $page++) {
    //         $apiUrl = 'https://phimapi.com/v1/api/danh-sach/'.$request->loaiphim.'?page=' . $page;

    //         // Lấy dữ liệu từ API
    //         try {
    //             $response = Http::timeout( 120)->get($apiUrl);
    //         } catch (\Exception $e) {
    //             $errors[] = "Không thể kết nối đến API trang $page.";
    //             continue; // Bỏ qua trang này và tiếp tục với trang tiếp theo
    //         }

    //         // Kiểm tra API phản hồi thành công
    //         if ($response->successful()) {
    //             $data = $response->json();

    //             // Kiểm tra trạng thái phản hồi
    //             if ($data['status'] === "success" && isset($data['data']['items'])) {
    //                 $countries = Country::pluck('id', 'slug')->toArray();
    //                 $years = Year::pluck('id', 'slug')->toArray();
    //                 $genres = Genre::pluck('id', 'slug')->toArray();

    //                 foreach ($data['data']['items'] as $item) {
    //                     // Kiểm tra xem phim đã tồn tại chưa
    //                     $existingMovie = Movie::where('slug', $item['slug'])->first();
    //                     if (!$existingMovie) {
    //                         $movie = new Movie();
    //                         $movie->name = $item['name'];
    //                         $movie->slug = $item['slug'];
    //                         $movie->name_en = $item['origin_name'];

    //                         $movie->thoiluong = $item['time'];
    //                         $movie->phude = $item['lang'] == "Vietsub" ? 0 : 1;

    //                         // Lấy thông tin chi tiết phim từ API dựa theo slug
    //                         try {
    //                             $res = Http::timeout(120)->get("https://phimapi.com/phim/" . $item['slug']);
    //                             if ($res->successful()) {
    //                                 $dt = $res->json();
    //                                 if ($dt['status'] === true && isset($dt['movie'])) {
    //                                     $mov = $dt['movie'];
    //                                     $movie->description = $mov['content'] ?? null;
    //                                     $movie->poster = $mov['thumb_url'];
    //                                     $movie->image = $mov['poster_url'];
    //                                     $parts = explode(' ', $mov['episode_total']);
    //                                     $movie->sotap = $parts[0];
    //                                     $movie->trailer = isset($mov['trailer_url']) ? substr($mov['trailer_url'], strpos($mov['trailer_url'], "v=") + 2) : null;
    //                                     $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
    //                                     $movie->thuocphim = ($mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1") ? 1 : 0;
    //                                     $movie->count_views = 0;
    //                                     $movie->category_id = match ($mov['type']) {
    //                                         "single" => 4,
    //                                         "series" => 3,
    //                                         "hoathinh" => 5,
    //                                         "tvshows" => 7,
    //                                         default => 6,
    //                                     };

    //                                     // Lấy country_id
    //                                     $countryName = $mov['country'][0]['slug'] ?? null;
    //                                     $movie->country_id = $countryName ? ($countries[$countryName] ?? 4) : 4;

    //                                     // Lấy year_id
    //                                     $yearName = $mov['year'] ?? null;
    //                                     $movie->year_id = $yearName ? ($years[$yearName] ?? 1) : 1;

    //                                     // Lấy genre_id
    //                                     $genreIds = [];
    //                                     foreach ($mov['category'] as $gen) {
    //                                         $genreName = $gen['slug'];
    //                                         $genreId = $genres[$genreName] ?? 1;
    //                                         if ($genreId) {
    //                                             $genreIds[] = $genreId;
    //                                         }
    //                                     }
    //                                     $movie->genre_id = $genreIds[0] ?? 1; // Thể loại đầu tiên

    //                                     // Lưu phim
    //                                     $movie->status = 1;
    //                                     $movie->source = "kkphim";
    //                                     $movie->save();

    //                                     // Đồng bộ các thể loại vào bảng movie_genre
    //                                     $movie->genres()->sync($genreIds);
    //                                 } else {
    //                                     $errors[] = "API không trả về dữ liệu hợp lệ ở trang $page.";
    //                                     continue;
    //                                 }
    //                             } else {
    //                                 $errors[] = "Không thể kết nối đến API chi tiết ở trang $page.";
    //                                 continue;
    //                             }
    //                         } catch (\Exception $e) {
    //                             $errors[] = "Không thể kết nối đến API chi tiết ở trang $page.";
    //                             continue;
    //                         }
    //                     }else{
    //                         $errors[] = "phim đã tồn tại: {$item['slug']}.";
    //                         continue;
    //                     }
    //                 }
    //             } else {
    //                 $errors[] = "API không trả về dữ liệu hợp lệ ở trang $page.";
    //                 continue;
    //             }
    //         } else {
    //             $errors[] = "Không thể kết nối đến API ở trang $page.";
    //             continue;
    //         }
    //     }

    //     // Kiểm tra nếu có lỗi xảy ra
    //     if (count($errors) > 0) {
    //         return redirect()->back()->withErrors($errors);
    //     }

    //     return redirect()->back()->with('success', 'Thêm phim thành công!');
    // }

    public function storekk1(Request $request)
    {
        // Tạo mảng để lưu thông báo lỗi (nếu có)
        $errors = [];
        $chunkSize = 50; // Kích thước chunk cho việc chia nhỏ các request

        // Lấy tất cả các slug của phim đã tồn tại
        $existingSlugs = Movie::pluck('slug')->toArray();

        // Lấy các dữ liệu ánh xạ từ database
        $countries = Country::pluck('id', 'slug')->toArray();
        $years = Year::pluck('id', 'slug')->toArray();
        $genres = Genre::pluck('id', 'slug')->toArray();

        // Lặp qua các trang theo yêu cầu
        $pageRange = range($request->start, $request->end);
        $chunks = array_chunk($pageRange, $chunkSize); // Chia nhỏ các trang thành từng chunk

        foreach ($chunks as $chunk) {
            // Sử dụng pool để lấy dữ liệu từ các trang trong từng chunk
            $responses = Http::pool(function ($pool) use ($chunk, $request) {
                foreach ($chunk as $page) {
                    $apiUrl = 'https://phimapi.com/v1/api/danh-sach/' . $request->loaiphim . '?limit=50' . '&page=' . $page;
                    $pool->as("page{$page}")->get($apiUrl);
                }
            });

            // Xử lý kết quả của từng trang
            foreach ($responses as $key => $response) {
                if ($response->successful()) {
                    $data = $response->json();

                    // Kiểm tra xem API trả về dữ liệu hợp lệ
                    if ($data['status'] === "success" && isset($data['data']['items'])) {
                        $items = $data['data']['items'];

                        // Lọc ra những phim chưa tồn tại trong database
                        $newItems = array_filter($items, function ($item) use ($existingSlugs) {
                            return !in_array($item['slug'], $existingSlugs);
                        });

                        if (!empty($newItems)) {
                            // Chia nhỏ các newItems thành các chunk nhỏ hơn để xử lý chi tiết phim
                            $newItemChunks = array_chunk($newItems, $chunkSize);

                            foreach ($newItemChunks as $newItemChunk) {
                                // Sử dụng pool để lấy chi tiết của từng phim trong chunk
                                $movieResponses = Http::pool(function ($pool) use ($newItemChunk) {
                                    foreach ($newItemChunk as $item) {
                                        $slug = $item['slug'];
                                        $pool->as($slug)->get("https://phimapi.com/phim/{$slug}");
                                    }
                                });

                                // Xử lý chi tiết của từng phim
                                foreach ($newItemChunk as $item) {
                                    $slug = $item['slug'];
                                    $response = $movieResponses[$slug];

                                    if ($response->successful()) {
                                        $dt = $response->json();

                                        if ($dt['status'] === true && isset($dt['movie'])) {
                                            $mov = $dt['movie'];
                                            $movie = new Movie();
                                            $movie->name = $mov['name'];
                                            $movie->slug = $mov['slug'];
                                            $movie->name_en = $mov['origin_name'];
                                            $movie->thoiluong = $mov['time'];
                                            $movie->phude = $mov['lang'] == "Vietsub" ? 0 : 1;
                                            $movie->description = $mov['content'] ?? null;
                                            $movie->poster = $mov['thumb_url'];
                                            $movie->image = $mov['poster_url'];
                                            $parts = explode(' ', $mov['episode_total']);
                                            $movie->sotap = $parts[0];
                                            $movie->trailer = isset($mov['trailer_url']) ? substr($mov['trailer_url'], strpos($mov['trailer_url'], "v=") + 2) : null;
                                            $movie->quality = $mov['quality'] == "HD" ? 0 : 1;
                                            $movie->thuocphim = ($mov['episode_total'] == "1 Tập" || $mov['episode_total'] == "1") ? 1 : 0;
                                            $movie->count_views = 0;
                                            if ($mov['type'] ==   "single") {
                                                $movie->category_id = 4;
                                            } else if ($mov['type'] == "series") {
                                                $movie->category_id = 3;
                                            } else if ($mov['type'] == "hoathinh") {
                                                $movie->category_id = 5;
                                            } else if ($mov['type'] == "tvshows") {
                                                $movie->category_id = 7;
                                            } else if ($mov['chieurap'] == true) {
                                                $movie->category_id = 6;
                                            } else {
                                                $movie->category_id = 3;
                                            }

                                            // Xử lý country
                                            $countryName = $mov['country'][0]['slug'] ?? null;
                                            $movie->country_id = $countryName ? ($countries[$countryName] ?? 4) : 4;

                                            // Xử lý year
                                            $yearName = $mov['year'] ?? null;
                                            $movie->year_id = $yearName ? ($years[$yearName] ?? 1) : 1;

                                            // Xử lý genres
                                            $genreIds = [];
                                            foreach ($mov['category'] as $gen) {
                                                $genreName = $gen['slug'];
                                                $genreId = $genres[$genreName] ?? 1;
                                                if ($genreId) {
                                                    $genreIds[] = $genreId;
                                                }
                                            }
                                            $movie->genre_id = $genreIds[0] ?? 1; // Thể loại đầu tiên

                                            // Lưu phim vào database
                                            $movie->status = 1;
                                            $movie->source = "kkphim";
                                            $movie->save();

                                            // Đồng bộ các thể loại vào bảng movie_genre
                                            $movie->genres()->sync($genreIds);
                                        } else {
                                            $errors[] = "API không trả về dữ liệu hợp lệ cho slug: {$slug}.";
                                        }
                                    } else {
                                        $errors[] = "Không thể lấy thông tin chi tiết phim cho slug: {$slug}.";
                                    }
                                }
                            }
                        }
                    } else {
                        $errors[] = "API không trả về dữ liệu hợp lệ ở trang $key.";
                    }
                } else {
                    $errors[] = "Không thể kết nối đến API ở trang $key.";
                }
            }
        }

        // Kiểm tra nếu có lỗi xảy ra
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Thêm phim thành công!');
    }
}
