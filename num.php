<form method="get" action="?">
    <div style="width: auto">
        <input name="num" type="number" value="<?=$_GET['num']?>" placeholder="input ex. 2, 8, 14">
        <button type="submit">enter</button>
    </div>
</form>
<?php
if(isset($_GET['num']) && !empty($_GET['num'])){

    $n = $_GET['num'];

    $x =   ($n / 2) + 1 ;

    $arr_square = array();
    for($i=0; $i < $x; $i++){
        for($j=$i; $j < $x+$i ; $j++){
            $arr_square[$i][] = $j;
        }
    }

    echo "<div> OUTPUT <hr>";
    echo "<table style='border-collapse: collapse'>";
    foreach ($arr_square as $key => $val){
        echo "<tr>";
        foreach ( $val as $index => $item){
            echo "<td style='border: 1px solid #000; text-align: center; vertical-align: middle; width: 20px; height: 20px'>".$item."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

}
?>