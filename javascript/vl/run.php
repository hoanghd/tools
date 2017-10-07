<?php
    $numbers = file('./numbers.txt', FILE_IGNORE_NEW_LINES);
    $loop = [];
    $cal = [];
    
    foreach($numbers as $i=>$line){
        $numbers[$i] = str_split($line);
    }
    
    foreach($numbers as $i=>$row){
        if(isset($numbers[$i+1])){
            foreach($row as $j=>$num){
                $cal[$i][$j] = ($num - $numbers[$i+1][$j]);
            }
        }
    }
    
    $max = 0;
    for($i=0; $i<12; $i++){
        $col = array_column($cal, $i);
        $first = $col[0];
        $loop[$i] = [];
        
        foreach($col as $j=>$val){
            if($j>0 && $val == $first){
                $loop[$i][] = $col[$j-1];
            }
        }
        
        $loop[$i] = array_count_values($loop[$i]);
        arsort($loop[$i]);
        
        $loop[$i] = array_keys($loop[$i]);
        $max = max($max, count($loop[$i]));
    }
    
    for($j=($max-1);$j>=0;$j--){
        echo "\n";
        for($i=0; $i<12; $i++){
            $val = "  ";
            if(count($loop[$i])>$j){
                $val = $loop[$i][$j];
                if($val>=0){
                    $val = " {$val}";
                }
            }
            
            echo $val ."\t";
        }
    }
    
	echo "\n---------------------------------------------------------------------------------------------\n";
    echo "\n---------------------------------------------------------------------------------------------\n";
    foreach ($cal[0] as $val){
        echo (($val>=0) ? " " : '') . $val . "\t";
    }
    echo "\n---------------------------------------------------------------------------------------------\n";
    
    for($j=0; $j<$max; $j++){
        for($i=0; $i<12; $i++){
            $val = "  ";
            if(count($loop[$i])>$j){
                $val = $loop[$i][$j] + $cal[0][$i];
                if($val>=0){
                    $val = " {$val}";
                }
            }
            
            echo $val ."\t";
        }
        echo "\n";
    }
    
    echo "\n";
	
	$parts = [
		[], [], [], [], [], [], [], [], [], [], [], []
	];
	$first = $cal[0];
	
	for($i=(count($cal)-1); $i>=0; $i--){
		$cur = $cal[$i];
		for($j=0;$j<12;$j++){
			if($first[$j] == $cur[$j]){
				$parts[$j][] = getPart4ByIndex($cal, $i, $j);
			}
		}
	}
	
	echo "\n";
	foreach($parts as $row){
		echo "\n\n";
		printRow($row);
	}
	
	function getPart4ByIndex($list, $index, $col){
		$start = max(0, ($index - 1));
		$end = min((count($list)-1), $index + 2);
		
		$row = [];
		for($i=$start; $i<=$end; $i++){
			$row[] = $list[$i][$col];
		}
		
		return $row;
	}
	
	function printRow($row){
		for($i=0;$i<4;$i++){
			for($j=0;$j<count($row);$j++){
				echo isset($row[$j][$i]) ? $row[$j][$i] . "\t" : " \t";
			}
			echo "\n";
		}
	}
?>
