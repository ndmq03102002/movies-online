
<!-- Search model Begin -->
<div class="search-model">
  <div class="h-100 d-flex align-items-center justify-content-center">
      <div class="search-close-switch"><i class="icon_close"></i></div>
      <form class="search-model-form" action="{{ route('search') }}" method="GET">
          <input type="text" id="search-input" name="query" placeholder="Search here....." autofocus />
          <button type="submit" style="display: none;">Search</button> <!-- Nút tìm kiếm ẩn -->
      </form>
  </div>
</div>
<!-- Search model end -->
