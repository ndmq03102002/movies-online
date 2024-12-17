@php
function format_decimal($number) {
    $number = floor($number * 10) / 10; // Giữ lại 1 chữ số sau dấu thập phân mà không làm tròn
    return number_format($number, 1, '.', '');
}
 $views = 19899999;

 if ($views < 1000) {
     echo $views;
 } elseif ($views < 1000000) {
     if ($views % 1000 == 0) {
         echo ($views / 1000) . 'k';
     } else {
       echo format_decimal($views / 1000) . 'k';
     }
 } else {
     if ($views % 1000000 == 0) {
         echo ($views / 1000000) . 'tr';
     } else {
         echo format_decimal($views / 1000000) . 'tr';
     }
 }
@endphp