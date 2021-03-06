<?php
$str = '
02 05 12 21 22 40
16 17 30 32 34 38
02 13 15 17 18 41
05 21 31 33 38 42
03 07 09 18 28 39
02 08 15 16 19 25
02 15 20 32 33 44
24 25 27 34 44 45
16 23 27 34 35 38
06 20 30 38 40 45
03 06 17 21 32 40
01 20 28 32 42 44
06 07 21 32 42 43
05 12 15 30 37 41
07 10 17 20 25 32
04 05 06 23 31 34
02 11 14 23 25 32
06 09 10 18 20 31
07 13 16 18 31 32
06 08 12 13 15 19
11 15 20 24 27 45
09 13 24 35 36 41
08 20 21 31 34 39
04 06 10 15 16 44
10 36 39 43 44 45
08 17 21 23 36 40
11 12 17 33 34 36
02 04 09 19 21 26
13 16 17 29 31 37
28 31 34 37 38 41
06 15 16 19 27 30
03 05 17 22 30 31
03 09 13 18 24 31
02 06 19 21 35 39
03 08 13 20 30 36
14 17 21 25 31 37
01 10 16 18 23 38
03 04 14 20 25 35
02 17 33 37 38 45';
/*
02 05 17 33 34 35
06 17 20 28 25 41
03 06 31 12 41 24
*/
function getMaxCount( $str = '' ){
    $count = [];
    $max = [];

    foreach( preg_split("/[\n\r]+/", $str) as $line ) {
        $line = str_replace(' ', '', $line);
        
        for( $i = 0; $i<strlen( $line ); $i++) {
           isset( $count[ $i ][ $line{$i} ] ) ? $count[ $i ][ $line{$i} ]++ : ($count[ $i ][ $line{$i} ] = 1);
        }
    }

    return $count;
}

print_r( getMaxCount($str) );

function analytic( $data = array() ){

}

/*
 - Qua từng đợt các số được đưa vào như thế nào
 - So sánh với số trước đó & và các số trong dãy số
 */ 
?>
