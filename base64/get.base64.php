<?php

    $request = json_decode(file_get_contents("php://input"));
    if (!empty($request)){
        $_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
    }

    $fileName = ($_POST['fileName']) ? $_POST['fileName'] : null;
    $data['fileName'] = $fileName;

    $img = file_get_contents('https://system.spktcoop.com/assets/images/templete_img/receipt/'.$fileName.'_1.png');

    // $img = file_get_contents('https://system.spktcoop.com/assets/images/templete_img/receipt/10B61000751_1.png');

    $imgToBase64 = "data:image/png;base64,".base64_encode($img);
    $data['base64'] = $imgToBase64;
    
    //echo  $imgToBase64;

    echo json_encode($data); 
    exit();
?>
<!-- <br/>
<img src="<?php echo $imgToBase64; ?>" /> -->