<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Movie;
use App\Models\Episode;
use App\Models\Year;
use App\Models\Movie_Genre;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả các tập phim, sắp xếp theo movie_id và episode theo thứ tự từ nhỏ đến lớn
        $episodes = Episode::orderBy('movie_id', 'desc')
                            ->orderBy('episode', 'asc')
                            ->paginate(perPage: 100);
        
        return view('admin.episode.index', compact('episodes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $config['method'] = 'create';
        $movies = Movie::find($id);
        return view('admin.episode.create', compact('movies','config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'episode' => [
                'required',
                'integer',
                'min:1',
                // Kiểm tra xem tập phim đã tồn tại chưa
                function ($attribute, $value, $fail) use ($request) {
                    $movieId = $request->input('movie_id');
                    $exists = Episode::where('movie_id', $movieId)
                                      ->where('episode', $value)
                                      ->exists();
    
                    if ($exists) {
                        $fail('Tập phim ' . $value . ' đã tồn tại cho bộ phim này.');
                    }
                },
            ],
        ], [
            'episode.required' => 'Số tập là bắt buộc.',
            'episode.integer' => 'Số tập phải là số nguyên.',
            'episode.min' => 'Số tập phải lớn hơn hoặc bằng 1.',
        ]);
        $data = $request->all();
        $episode = new Episode();
        $episode->movie_id = $data['movie_id'];
        $episode->episode = $data['episode'];
        $episode->linkphim = $data['link'];
        $episode->status = $data['status'];
        $episode->save();
        $m = Movie::find($data['movie_id']);
        if($m->thuocphim = 1){
            return redirect()->route('movie.index')->with('success', 'Thêm tập phim thành công');
        }
        return redirect()->back()->with('success', 'Thêm tập phim thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $config['method'] = 'edit';
        $episode = Episode::find($id);
        return view('admin.episode.create', compact('episode','config'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'episode' => [
                'required',
                'integer',
                'min:1',
                // Kiểm tra xem tập phim đã tồn tại chưa
                function ($attribute, $value, $fail) use ($request, $id) {
                    $movieId = $request->input('movie_id');
                    $exists = Episode::where('movie_id', $movieId)
                                      ->where('episode', $value)
                                      ->where('id', '!=', $id)
                                      ->exists();
    
                    if ($exists) {
                        $fail('Tập phim ' . $value . ' đã tồn tại cho bộ phim này.');
                    }
                },
            ],
        ], [
            'episode.required' => 'Số tập là bắt buộc.',
            'episode.integer' => 'Số tập phải là số nguyên.',
            'episode.min' => 'Số tập phải lớn hơn hoặc bằng 1.',
        ]);
        $data = $request->all();
        $episode = Episode::find($id);
        
        $episode->episode = $data['episode'];
        $episode->linkphim = $data['link'];
        $episode->status = $data['status'];
        $episode->save();
        return redirect()->route('episode.index')->with('success', 'Cập nhật tập phim thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Episode::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa tập phim thành công');
    }

   
    public function createEpisodeApi($slug) // Thêm các tập phim từ API cho 1 phim nguồn ophim
    {
        $apiUrl = "https://ophim1.com/phim/{$slug}";
        try {
            // Gọi API
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['movie']) && isset($data['episodes'])) {
                    $movie = Movie::where('slug', $slug)->first();

                    if (!$movie) {
                        return redirect()->back()->withErrors(['error' => 'Không tìm thấy phim với slug này trong cơ sở dữ liệu.']);
                    }

                    // Lưu các tập phim
                    foreach ($data['episodes'][0]['server_data'] as $episodeData) {
                        $episodeData['name'] === "Full" ? $ep = 1 : $ep = $episodeData['name'];
                        $exists = Episode::where('movie_id', $movie->id)
                                         ->where('episode', $ep)
                                         ->exists();

                        if (!$exists && $episodeData['name'] !== "") {
                            $episode = new Episode();
                            $episode->movie_id = $movie->id;
                            $episode->episode = $ep;
                            $episode->linkphim = $episodeData['link_embed'];
                            $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                           
                            $episode->save();
                        }
                    }

                    return redirect()->back()->with('success', 'Thêm các tập phim thành công!');
                } else {
                    return redirect()->back()->withErrors(['error' => 'Dữ liệu API không hợp lệ.']);
                }
            } else {
                return redirect()->back()->withErrors(['error' => 'Không thể kết nối tới API.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    public function createEpisodeApikk($slug) // Thêm các tập phim từ API cho 1 phim nguồn kkphim
    {
        $apiUrl = "https://phimapi.com/phim/{$slug}";
        try {
            // Gọi API
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['movie']) && isset($data['episodes'])) {
                    $movie = Movie::where('slug', $slug)->first();

                    if (!$movie) {
                        return redirect()->back()->withErrors(['error' => 'Không tìm thấy phim với slug này trong cơ sở dữ liệu.']);
                    }

                    // Lưu các tập phim
                    foreach ($data['episodes'][0]['server_data'] as $episodeData) {
                        
                        $ep = $episodeData['name'] === "Full" ? 1 : $episodeData['name'];
                        $exists = Episode::where('movie_id', $movie->id)
                                         ->where('episode', $ep)
                                         ->exists();

                        if (!$exists && $episodeData['name'] !== "") {
                            $episode = new Episode();
                            $episode->movie_id = $movie->id;
                            $episode->episode = $ep;
                            $episode->linkphim = $episodeData['link_embed'];
                            $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                            
                            $episode->save();
                        }
                    }

                    return redirect()->back()->with('success', 'Thêm các tập phim thành công!');
                } else {
                    return redirect()->back()->withErrors(['error' => 'Dữ liệu API không hợp lệ.']);
                }
            } else {
                return redirect()->back()->withErrors(['error' => 'Không thể kết nối tới API.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }



// public function syncAllEpisodes()// nguồn ophim
// {
//     set_time_limit(1000000);

//     // $movies = Movie::where(function ($query) {
//     //     $query->where('sotap', '>', Episode::selectRaw('COUNT(*)')
//     //         ->whereColumn('movie_id', 'movies.id'))
//     //         ->orWhere('sotap', '?');
//     // });

//     // Lấy tất cả các phim có tổng số tập < sotap hoặc sotap là dấu chấm hỏi
//     $page = 1; // Lấy trang thứ 2

//     $movies = Movie::where(function ($query) {
//             $query->where('sotap', '>', Episode::selectRaw('COUNT(*)')
//                 ->whereColumn('movie_id', 'movies.id'))
//                 ->orWhere('sotap', '?');
//         })->where('source','ophim')->orderBy('id','desc')->paginate(200, ['*'], 'page', $page);

//     // $movies = Movie::orderBy('id','desc')->paginate(10);


//     // Duyệt qua từng phim
//     foreach ($movies as $movie) {
//         $slug = $movie->slug;
//         $apiUrl = "https://ophim1.com/phim/{$slug}";

//         try {
//             $response = Http::timeout(240)->get($apiUrl);
            
//             if ($response->successful()) {
//                 $data = $response->json();

//                 // Kiểm tra xem dữ liệu có chứa tập không
//                 if (!empty($data['episodes']) ) {
//                     $firstEpisode = $data['episodes'][0]; // Lấy episode đầu tiên
                    
//                     // Kiểm tra server_data có tồn tại
//                     if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
//                         foreach ($firstEpisode['server_data'] as $server) {
//                             // Xác định số tập
//                             $ep = $server['name'] === "Full" ? 1 : $server['name'];
//                             $exists = Episode::where('movie_id', $movie->id)
//                                              ->where('episode', $ep)
//                                              ->exists();
                            
//                             // Lưu thông tin vào bảng Episode
//                             if (!$exists && $server['name']!=="") {
//                                 $episode = new Episode();
//                                 $episode->movie_id = $movie->id;
//                                 $episode->episode = $ep;
//                                 $episode->linkphim = $server['link_embed'];
//                                 $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                                
//                                 $episode->save();
//                             }
//                         }
//                     } else {
//                        
//                         continue;
//                     }
//                 } else {
//                    
//                     continue;
//                 }
//             } else {
//                     // $this->createEpisodeApikk($slug);
//                     continue;
//             }
//         } catch (\Exception $e) {
//             Log::error("Error fetching movie data for {$movie->id}: " . $e->getMessage());
//         }
//     }

//     return redirect()->back()->with('success', 'Đồng bộ tất cả các tập phim thành công!');
// }


public function syncAllEpisodes() // nguồn ophim
{
    set_time_limit(1000000);

    $page = 1;

    // Lấy danh sách các phim cần cập nhật
    $movies = Movie::where(function ($query) {
            $query->where('sotap', '>', Episode::selectRaw('COUNT(*)')
                ->whereColumn('movie_id', 'movies.id'))
                ->orWhere('sotap', '?');
        })->where('source','ophim')
        ->orderBy('id','desc')
        ->paginate(500, ['*'], 'page', $page);

    // Chia nhỏ các phim thành batch, ví dụ mỗi batch 20 phim
    $moviesChunk = $movies->chunk(100); // Chia thành từng nhóm 20 phim

    // Lặp qua từng batch
    foreach ($moviesChunk as $moviesBatch) {
        $responses = Http::pool(function ($pool) use ($moviesBatch) {
            foreach ($moviesBatch as $movie) {
                $pool->as($movie->slug)->timeout(240)->get("https://ophim1.com/phim/{$movie->slug}");
            }
        });

        // Xử lý kết quả của mỗi batch
        foreach ($moviesBatch as $movie) {
            $slug = $movie->slug;

            if (isset($responses[$slug]) && $responses[$slug]->successful()) {
                $data = $responses[$slug]->json();

                // Kiểm tra xem dữ liệu có chứa tập không
                if (!empty($data['episodes'])) {
                    $firstEpisode = $data['episodes'][0]; // Lấy episode đầu tiên
                    
                    // Kiểm tra server_data có tồn tại
                    if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
                        foreach ($firstEpisode['server_data'] as $server) {
                            // Xác định số tập
                            $ep = $server['name'] === "Full" ? 1 : $server['name'];
                            $exists = Episode::where('movie_id', $movie->id)
                                             ->where('episode', $ep)
                                             ->exists();
                            
                            // Lưu thông tin vào bảng Episode nếu chưa tồn tại
                            if (!$exists && $server['name'] !== "") {
                                $episode = new Episode();
                                $episode->movie_id = $movie->id;
                                $episode->episode = $ep;
                                $episode->linkphim = $server['link_embed'];
                                $episode->status = 1; // hoặc tùy chỉnh theo yêu cầu
                                $episode->save();
                            }
                        }
                    } else {
                        // Trường hợp không có server_data
                        Log::warning("No server_data for movie: {$movie->id}");
                        continue;
                    }
                } else {
                    // Trường hợp không có episodes
                    Log::warning("No episodes for movie: {$movie->id}");
                    continue;
                }
            } else {
                // Trường hợp không lấy được dữ liệu phim
                Log::error("Failed to fetch data for movie slug: {$slug}");
            }
        }
    }

    return redirect()->back()->with('success', 'Đồng bộ tất cả các tập phim thành công!');
}



// public function syncAllEpisodeskk() // nguồn kkphim
// {
//     $page = 1; // Lấy trang thứ 2

//     $movies = Movie::where(function ($query) {
//             $query->where('sotap', '>', Episode::selectRaw('COUNT(*)')
//                 ->whereColumn('movie_id', 'movies.id'))
//                 ->orWhere('sotap', '?');
//         })->where('source','kkphim')->orderBy('id','desc')->paginate(20, ['*'], 'page', $page);

//         set_time_limit(1000000);

//     foreach ($movies as $movie) {
//         $slug = $movie->slug;
//         $apiUrl = "https://phimapi.com/phim/{$slug}";

//         try {
//             $response = Http::timeout(240)->get($apiUrl);

//             if ($response->successful()) {
//                 $data = $response->json();

//                 if (!empty($data['episodes'])) {
//                     $firstEpisode = $data['episodes'][0];

//                     if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
//                         // Sử dụng transaction để đảm bảo lưu từng phim riêng lẻ
//                         DB::beginTransaction();

//                         foreach ($firstEpisode['server_data'] as $server) {
//                             $ep = $server['name'] === "Full" ? 1 : $server['name'];
//                             $exists = Episode::where('movie_id', $movie->id)
//                                              ->where('episode', $ep)
//                                              ->exists();
//                             if (!$exists && $server['name']!=="") {
//                                 $episode = new Episode();
//                                 $episode->movie_id = $movie->id;
//                                 $episode->episode = $ep;
//                                 $episode->linkphim = $server['link_embed'];
//                                 $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                                
//                                 $episode->save();
//                             }
                            
//                         }

//                         DB::commit(); // Commit transaction sau khi lưu thành công
//                     } else {
//                         $this->createEpisodeApi($slug);
//                         // return redirect()->back()->with('error', 'Không có server_data!');

//                     }
//                 } else {
//                   $this->createEpisodeApi($slug);
//                     // return redirect()->back()->with('error', 'Không có tập! ' . $movie->name);
//                 }
//             } else {
//                     $this->createEpisodeApi($slug);
//             }
//         } catch (\Exception $e) {
//             // Rollback trong trường hợp có lỗi
//             DB::rollBack();
//             Log::error("Error fetching movie data for {$movie->id}: " . $e->getMessage());
//         }
//     }

//     return redirect()->back()->with('success', 'Đồng bộ tất cả các tập phim thành công!');
// }

// public function test(){
//     $movies = Movie::where('source', 'kkphim')->orderBy('id', 'DESC')->paginate(10);
//     foreach ($movies as $movie) {
//         $slug = $movie->slug;
//         $apiUrl = "https://phimapi.com/phim/{$slug}";
//         $mov = Movie::find($movie->id);
//         try {
//             // Gọi API
//             $response = Http::timeout(120)->get($apiUrl);

//             if ($response->successful()) {
//                 $data = $response->json();

//                 // Kiểm tra status và tồn tại items
//                 if ($data['status'] === true && isset($data['movie'])) {
//                     // Kiểm tra nếu poster tồn tại
//                     $item = $data['movie'];
//                         if (isset($item['thumb_url'])) {
//                             $mov->poster = $item['thumb_url'];
//                             $mov->save();
//                         } else {
//                             return redirect()->back()->with('error', 'Không tìm thấy link poster trong dữ liệu API.'. $movie->name);
//                         }
                    
//                 } else {
//                     return redirect()->back()->with('error', 'API trả về dữ liệu không hợp lệ.'. $movie->name);
//                 }
//             } else {
//                 return redirect()->back()->with('error', 'Lỗi khi kết nối với API.'. $movie->name);
//             }

//         } catch (\Exception $e) {
//             // Xử lý lỗi trong quá trình gọi API
//             return redirect()->back()->with('error', 'Lỗi khi cập nhật poster: ' . $e->getMessage());
//         }
//     }

//     return redirect()->back()->with('success', 'Cập nhật poster thành công!');
// }


public function syncAllEpisodeskk() // nguồn kkphim
{
    set_time_limit(1000000); // Tăng giới hạn thời gian thực thi nếu cần thiết

    $page = 1; // Trang muốn lấy
    $batchSize = 10; // Số lượng phim trong mỗi batch

    // Lấy danh sách các phim cần cập nhật
    $movies = Movie::where(function ($query) {
            $query->where('sotap', '>', Episode::selectRaw('COUNT(*)')
                ->whereColumn('movie_id', 'movies.id'))
                ->orWhere('sotap', '?');
        })->where('source','kkphim')
        ->orderBy('id','desc')
        ->paginate(10, ['*'], 'page', $page);

    // Chia nhỏ danh sách các phim thành từng nhóm (batch) với kích thước $batchSize
    $moviesChunk = $movies->chunk($batchSize);

    foreach ($moviesChunk as $moviesBatch) {
        // Sử dụng pool để gửi các yêu cầu song song
        $responses = Http::pool(function ($pool) use ($moviesBatch) {
            foreach ($moviesBatch as $movie) {
                $slug = $movie->slug;
                $pool->as($slug)->timeout(240)->get("https://phimapi.com/phim/{$slug}");
            }
        });

        // Xử lý kết quả của mỗi batch
        foreach ($moviesBatch as $movie) {
            $slug = $movie->slug;

            if (isset($responses[$slug]) && $responses[$slug]->successful()) {
                $data = $responses[$slug]->json();

                if (!empty($data['episodes'])) {
                    $firstEpisode = $data['episodes'][0];

                    if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
                        // Sử dụng transaction để đảm bảo lưu từng phim riêng lẻ
                        DB::beginTransaction();

                        foreach ($firstEpisode['server_data'] as $server) {
                            $ep = $server['name'] === "Full" ? 1 : $server['name'];
                            $exists = Episode::where('movie_id', $movie->id)
                                             ->where('episode', $ep)
                                             ->exists();
                            if (!$exists && $server['name'] !== "") {
                                $episode = new Episode();
                                $episode->movie_id = $movie->id;
                                $episode->episode = $ep;
                                $episode->linkphim = $server['link_embed'];
                                $episode->status = 1; // hoặc tùy chỉnh theo yêu cầu
                                
                                $episode->save();
                            }
                        }

                        DB::commit(); // Commit transaction sau khi lưu thành công
                    } else {
                        Log::warning("No server_data for movie: {$movie->id}");
                        continue;
                    }
                } else {
                    Log::warning("No episodes for movie: {$movie->id}");
                    continue;
                }
            } else {
                Log::error("Failed to fetch data for movie slug: {$slug}");
                continue;
            }
        }
    }

    return redirect()->back()->with('success', 'Đồng bộ tất cả các tập phim thành công!');
}

public function index1(){
    return view('admin.episode.update');
}

// public function updateEpisode(Request $request){ // ophim
//     $errors = [];

//     for ($page = $request->start; $page <= $request->end; $page++) {
//         // Lấy dữ liệu từ API
//         $response = Http::timeout(120)->get('https://ophim1.com/danh-sach/phim-moi-cap-nhat?page=' . $page);

//         // Kiểm tra API phản hồi thành công
//         if ($response->successful()) {
//             $data = $response->json();

//             // Kiểm tra trạng thái phản hồi
//             if ($data['status'] === true && isset($data['items'])) {
//                 foreach ($data['items'] as $item) {
//                     $movie = Movie::where('source','ophim')->where('slug', $item['slug'])->first();
//                     if($movie){
//                         $apiUrl = "https://ophim1.com/phim/{$item['slug']}";
//                         try {
//                             $response = Http::timeout(240)->get($apiUrl);
                            
//                             if ($response->successful()) {
//                                 $dt = $response->json();
                
//                                 // Kiểm tra xem dữ liệu có chứa tập không
//                                 if (!empty($dt['episodes']) ) {
//                                     $firstEpisode = $dt['episodes'][0]; // Lấy episode đầu tiên
                                    
//                                     // Kiểm tra server_data có tồn tại
//                                     if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
//                                         foreach ($firstEpisode['server_data'] as $server) {
//                                             // Xác định số tập
//                                             $ep = $server['name'] === "Full" ? 1 : $server['name'];
//                                             $exists = Episode::where('movie_id', $movie->id)
//                                                             ->where('episode', $ep)
//                                                             ->exists();
                                            
//                                             // Lưu thông tin vào bảng Episode
//                                             if (!$exists && $server['name']!=="") {
//                                                 $episode = new Episode();
//                                                 $episode->movie_id = $movie->id;
//                                                 $episode->episode = $ep;
//                                                 $episode->linkphim = $server['link_embed'];
//                                                 $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                                                
//                                                 $episode->save();
//                                             }
//                                         }
//                                     } else {
                                        
//                                         continue;                                    
//                                     }
//                                 } else {
                            
//                                     continue; 
                                    
//                                 }
//                             } else {
//                                 continue; 
//                             }
//                         } catch (\Exception $e) {
//                             Log::error("Error fetching movie data for {$movie->id}: " . $e->getMessage());
//                         }
//                     }else{
//                         continue;
//                     }
//                 }
//             } 
//         }

        
//     }
//     return redirect()->back()->with('success', 'Thêm phim thành công!');
// }


public function updateEpisode(Request $request)
{
    set_time_limit(1000000);
    // Lấy tất cả các slug phim từ DB có source là 'ophim' để so sánh
    $movies = Movie::where('source', 'ophim')->pluck('slug', 'id')->toArray();
    $errors = [];

    for ($page = $request->start; $page <= $request->end; $page++) {
        // Lấy dữ liệu từ API phim mới cập nhật
        $response = Http::timeout(120)->get('https://ophim1.com/danh-sach/phim-moi-cap-nhat?page=' . $page);

        // Kiểm tra API phản hồi thành công
        if ($response->successful()) {
            $data = $response->json();

            // Kiểm tra trạng thái phản hồi
            if ($data['status'] === true && isset($data['items'])) {
                // Lấy danh sách các slug phim mới cập nhật từ API
                $items = $data['items'];
                
                // Sử dụng Http::pool() để lấy thông tin chi tiết của tất cả các phim cùng lúc
                $responses = Http::pool(function ($pool) use ($items) {
                    foreach ($items as $item) {
                        $pool->as($item['slug'])->timeout(240)->get("https://ophim1.com/phim/{$item['slug']}");
                    }
                });

                // Xử lý kết quả trả về
                foreach ($items as $item) {
                    $movieId = array_search($item['slug'], $movies);
                    if ($movieId && isset($responses[$item['slug']]) && $responses[$item['slug']]->successful()) {
                        $dt = $responses[$item['slug']]->json();

                        // Kiểm tra xem dữ liệu có chứa tập không
                        if (!empty($dt['episodes'])) {
                            $firstEpisode = $dt['episodes'][0]; // Lấy episode đầu tiên

                            // Kiểm tra server_data có tồn tại
                            if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
                                foreach ($firstEpisode['server_data'] as $server) {
                                    // Xác định số tập
                                    $ep = $server['name'] === "Full" ? 1 : $server['name'];
                                    $exists = Episode::where('movie_id', $movieId)
                                        ->where('episode', $ep)
                                        ->exists();

                                    // Lưu thông tin vào bảng Episode
                                    if (!$exists && $server['name'] !== "") {
                                        $episode = new Episode();
                                        $episode->movie_id = $movieId;
                                        $episode->episode = $ep;
                                        $episode->linkphim = $server['link_embed'];
                                        $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                                        $episode->save();
                                    }
                                }
                            } else {
                                Log::warning("No server data for movie: {$movieId}");
                            }
                        } else {
                            Log::warning("No episodes for movie: {$movieId}");
                        }
                    } else {
                        Log::error("Failed to fetch detailed data for movie slug: {$item['slug']}");
                    }
                }
            }
        } else {
            Log::error("Failed to fetch movies list for page: {$page}");
        }
    }

    return redirect()->back()->with('success', 'Thêm phim thành công!');
}


// public function updateEpisodekk(Request $request){ // kkphim
//     $errors = [];
//     set_time_limit(1000000);
//     for ($page = $request->start; $page <= $request->end; $page++) {
//         $apiUrl = 'https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=' . $page;
//         // Lấy dữ liệu từ API
//         $response = Http::timeout(120)->get($apiUrl);

//         // Kiểm tra API phản hồi thành công
//         if ($response->successful()) {
//             $data = $response->json();

//             // Kiểm tra trạng thái phản hồi
//             if ($data['status'] === true && isset($data['items'])) {
//                 foreach ($data['items'] as $item) {
//                     $movie = Movie::where('slug', $item['slug'])->first();
//                     if($movie){
//                         try {
//                             $response = Http::timeout(240)->get("https://phimapi.com/phim/" . $item['slug']);
                            
//                             if ($response->successful()) {
//                                 $dt = $response->json();
                
//                                 // Kiểm tra xem dữ liệu có chứa tập không
//                                 if (!empty($dt['episodes']) ) {
//                                     $firstEpisode = $dt['episodes'][0]; // Lấy episode đầu tiên
                                    
//                                     // Kiểm tra server_data có tồn tại
//                                     if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
//                                         foreach ($firstEpisode['server_data'] as $server) {
//                                             // Xác định số tập
//                                             $ep = $server['name'] === "Full" ? 1 : $server['name'];
//                                             $exists = Episode::where('movie_id', $movie->id)
//                                                             ->where('episode', $ep)
//                                                             ->exists();
                                            
//                                             // Lưu thông tin vào bảng Episode
//                                             if (!$exists && $server['name']!=="") {
//                                                 $episode = new Episode();
//                                                 $episode->movie_id = $movie->id;
//                                                 $episode->episode = $ep;
//                                                 $episode->linkphim = $server['link_embed'];
//                                                 $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                                                
//                                                 $episode->save();
//                                             }
//                                         }
//                                     } else {
                                        
//                                         continue;                                    
//                                     }
//                                 } else {
                            
//                                     continue; 
                                    
//                                 }
//                             } else {
//                                 continue; 
//                             }
//                         } catch (\Exception $e) {
//                             Log::error("Error fetching movie data for {$movie->id}: " . $e->getMessage());
//                         }
//                     }else{
//                         continue;
//                     }
//                 }
//             } 
//         }

        
//     }
//     return redirect()->back()->with('success', 'Thêm phim thành công!');
// }

public function updateEpisodekk(Request $request)
{
    $errors = [];
    set_time_limit(1000000);

    // Lấy danh sách tất cả các phim từ database với 'slug' và 'id'
    $movies = Movie::where('source', 'kkphim')->pluck('slug', 'id')->toArray();

    // Lặp qua từng trang được yêu cầu
    for ($page = $request->start; $page <= $request->end; $page++) {
        $apiUrl = 'https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=' . $page;

        // Lấy dữ liệu từ API phim mới cập nhật
        $response = Http::timeout(120)->get($apiUrl);

        // Kiểm tra API phản hồi thành công
        if ($response->successful()) {
            $data = $response->json();

            // Kiểm tra trạng thái phản hồi
            if ($data['status'] === true && isset($data['items'])) {
                // Gửi các yêu cầu song song để lấy chi tiết các phim
                $responses = Http::pool(function ($pool) use ($data) {
                    foreach ($data['items'] as $item) {
                        $pool->as($item['slug'])->timeout(240)->get("https://phimapi.com/phim/{$item['slug']}");
                    }
                });

                // Xử lý kết quả từ API chi tiết phim
                foreach ($data['items'] as $item) {
                     // Kiểm tra slug có tồn tại trong danh sách phim không
                        $movieId = array_search($item['slug'], $movies); // Lấy ID của phim
                        
                        if ($movieId && isset($responses[$item['slug']]) && $responses[$item['slug']]->successful()) {
                            $dt = $responses[$item['slug']]->json();

                            // Kiểm tra xem dữ liệu có chứa tập không
                            if (!empty($dt['episodes'])) {
                                $firstEpisode = $dt['episodes'][0]; // Lấy episode đầu tiên

                                // Kiểm tra server_data có tồn tại
                                if (!empty($firstEpisode['server_data']) && is_array($firstEpisode['server_data'])) {
                                    foreach ($firstEpisode['server_data'] as $server) {
                                        // Xác định số tập
                                        $ep = $server['name'] === "Full" ? 1 : $server['name'];
                                        $exists = Episode::where('movie_id', $movieId)
                                            ->where('episode', $ep)
                                            ->exists();

                                        // Lưu thông tin vào bảng Episode
                                        if (!$exists && $server['name'] !== "") {
                                            $episode = new Episode();
                                            $episode->movie_id = $movieId;
                                            $episode->episode = $ep;
                                            $episode->linkphim = $server['link_embed'];
                                            $episode->status = 1; // hoặc tuỳ chỉnh theo yêu cầu
                                            $episode->save();
                                        }
                                    }
                                } else {
                                    Log::warning("No server data for movie: {$movieId}");
                                }
                            } else {
                                Log::warning("No episodes for movie: {$movieId}");
                            }
                        } else {
                            Log::error("Failed to fetch detailed data for movie slug: {$item['slug']}");
                        }
                    
                }
            }
        } else {
            Log::error("Failed to fetch movies list for page: {$page}");
        }
    }

    return redirect()->back()->with('success', 'Thêm phim thành công!');
}



}


  