<!DOCTYPE html>
<html lang="zxx">

 @include('pages.include.head')
 
  <body>
    
    <!-- Page Preloder -->
    <div id="preloder">
      <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    @include('pages.include.nav')
    <!-- Header End -->

    <!-- Hero Section Begin -->
    @yield('home')
    <!-- Hero Section End -->

    <!-- Product Section Begin -->
   
    @yield('content')
    <!-- Product Section End -->

    @include('pages.include.footer')

    @include('pages.include.search')

    <!-- Js Plugins -->
    @include('pages.include.script')
  </body>
</html>
