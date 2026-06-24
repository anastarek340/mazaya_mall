<?php

function category_icon($slug){
    $icons = [
        'household' => 'fa-solid fa-broom',
        'plastics'  => 'fa-solid fa-cubes-stacked',
        'furnishings' => 'fa-solid fa-couch',
    ];
    return $icons[$slug] ?? 'fa-solid fa-tag';
}


function format_price($price){
    return number_format($price, 0, '', ',') . ' ج.م';
}


function star_rating($rating){
    $rating = round($rating);
    $html = '';
    for($i = 1; $i <= 5; $i++){
        $html .= $i <= $rating ? '★' : '☆';
    }
    return $html;
}


function get_product_image($image){
    if (!$image) {
        return 'assets/images/placeholder.png';
    }


    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }

    return 'uploads/' . $image;
}



function is_admin(){
    return isset($_SESSION['admin_id']);
}


function redirect($url){
    header('Location: ' . $url);
    exit;
}

function clean_input($data){
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function is_valid_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


function is_valid_phone($phone){
    // رقم هاتف مصري أساسي
    $phone = preg_replace('/[^\d]/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 20;
}

?>
