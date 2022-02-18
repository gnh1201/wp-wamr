<?php
function hanoi_move($n,$from,$to,$via) {
    if ($n === 1) {
        //print("Move disk from pole $from to pole $to");
    } else {
        hanoi_move($n-1,$from,$via,$to);
        hanoi_move(1,$from,$to,$via);
        hanoi_move($n-1,$via,$to,$from);
    }
}
