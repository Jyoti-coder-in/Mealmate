<?php
/*
 * MealMate image helpers
 * $prefix = path from the current page to the project root
 *           ''    -> pages in root   (index.php, login.php, register.php)
 *           '../' -> pages in student/ or admin/
 */

function mm_default_foods() {
    return [
        'fries'       => 'assets/images/foods/fries.jpg',
        'noodles'     => 'assets/images/foods/noodles.jpg',
        'paneer_roll' => 'assets/images/foods/paneer_roll.jpg',
        'pasta'       => 'assets/images/foods/pasta.jpg',
        'pizza'       => 'assets/images/foods/pizza.webp',
    ];
}

/* Image URL for a food row: admin upload -> name match -> repeat default images */
function food_image_url($food, $prefix = '') {
    $root = dirname(__DIR__) . '/';

    // 1) image uploaded by admin
    $img = trim($food['image'] ?? '');
    if ($img !== '') {
        foreach (['uploads/foods/', 'uploads/'] as $dir) {
            if (is_file($root . $dir . $img)) {
                return $prefix . $dir . rawurlencode($img);
            }
        }
    }

    // 2) match by food name / category
    $defaults = mm_default_foods();
    $text = strtolower(($food['food_name'] ?? '') . ' ' . ($food['category'] ?? ''));
    $map = [
        'pizza'       => ['pizza'],
        'pasta'       => ['pasta', 'penne', 'macaroni', 'spaghetti'],
        'noodles'     => ['noodle', 'chowmein', 'chow mein', 'hakka', 'maggi', 'manchurian'],
        'paneer_roll' => ['roll', 'wrap', 'kathi', 'paneer', 'frankie', 'tikka'],
        'fries'       => ['fries', 'french', 'chips', 'potato', 'loaded'],
    ];
    foreach ($map as $key => $words) {
        foreach ($words as $w) {
            if (strpos($text, $w) !== false) {
                return $prefix . $defaults[$key];
            }
        }
    }

    // 3) no match (burger, dosa, etc.) -> reuse the 5 images in rotation
    $keys = array_keys($defaults);
    $seed = isset($food['id']) ? (int)$food['id'] : crc32($text);
    return $prefix . $defaults[$keys[abs($seed) % count($keys)]];
}

/* Direct URL of one default food image: 'pizza', 'pasta', 'fries', 'noodles', 'paneer_roll' */
function mm_food_img($key, $prefix = '') {
    $d = mm_default_foods();
    return $prefix . ($d[$key] ?? $d['pizza']);
}

/* Navbar logo: pink rounded chip (logo has a pink background, navbar is dark) */
function mm_logo($prefix = '', $height = 46) {
    $root = dirname(__DIR__) . '/';
    if (is_file($root . 'assets/images/logo_nav.png')) {
        return '<img src="' . $prefix . 'assets/images/logo_nav.png" alt="MealMate" style="height:' . (int)$height . 'px;width:auto;vertical-align:middle;border-radius:12px;display:block;">';
    }
    return '<span style="font-weight:800;letter-spacing:.5px;">Meal<span style="color:#f062a6;">Mate</span></span>';
}

/* Big logo (with tagline) for login / register / footer */
function mm_logo_big($prefix = '', $width = 260) {
    $root = dirname(__DIR__) . '/';
    if (is_file($root . 'assets/images/logo.png')) {
        return '<img src="' . $prefix . 'assets/images/logo.png" alt="MealMate - For College Babies only" style="width:' . (int)$width . 'px;max-width:100%;height:auto;border-radius:18px;">';
    }
    return mm_logo($prefix);
}

/* Full-width banner: assets/images/banner.png if present, else pink fallback */
function mm_banner($prefix = '', $link = null) {
    $root = dirname(__DIR__) . '/';
    if (is_file($root . 'assets/images/banner.png')) {
        $img = '<img src="' . $prefix . 'assets/images/banner.png" alt="MealMate - Get Food on 50% OFF with College ID" style="width:100%;height:auto;display:block;border-radius:22px;box-shadow:0 8px 25px rgba(190,24,93,.2);">';
    } else {
        $img = '<div class="text-center p-4 p-md-5" style="background:linear-gradient(120deg,#be185d,#ec4899);color:white;border-radius:22px;"><h2 class="fw-bold mb-1">MealMate - Get Food on 50% OFF</h2><p class="mb-0">With College ID as proof</p></div>';
    }
    return $link ? '<a href="' . $link . '">' . $img . '</a>' : $img;
}

/* Description + steps shown below the banner */
function mm_offer_steps() {
    $steps = [
        ['🪪', 'Upload your College ID', 'Log in as a student and upload a clear photo of your valid College ID as proof.'],
        ['✅', 'Admin approves your request', 'Our admin checks your ID and accepts your request. You will be verified as a college student.'],
        ['🎟️', 'Apply the 50% OFF coupon', 'Once approved, apply coupon <strong>STUDENT50</strong> at checkout and get 50% OFF on every food item.'],
        ['🍽️', 'Order & enjoy your meal', 'Place your order, pay, and enjoy your food at half the price!'],
    ];
    $h  = '<div style="background:white;border:1px solid #f9a8d4;border-radius:22px;padding:26px;margin-top:18px;box-shadow:0 5px 18px rgba(190,24,93,.10);">';
    $h .= '<h4 style="color:#d63384;font-weight:800;margin-bottom:6px;">🎓 How to get 50% OFF on MealMate</h4>';
    $h .= '<p style="color:#4a1834;margin-bottom:18px;">MealMate is made for college students. Show your College ID as proof, get verified once, and enjoy <strong>50% OFF on all food items</strong>. The offer is not for one order only &mdash; you can use it on <strong>every order</strong> you place.</p>';
    $h .= '<div class="row g-3">';
    foreach ($steps as $i => $s) {
        $h .= '<div class="col-md-6 col-lg-3"><div style="height:100%;background:#fff0f6;border-radius:16px;padding:18px;text-align:center;">'
            . '<div style="width:34px;height:34px;line-height:34px;margin:0 auto 8px;border-radius:50%;background:#d63384;color:white;font-weight:bold;">' . ($i + 1) . '</div>'
            . '<div style="font-size:32px;">' . $s[0] . '</div>'
            . '<h6 style="color:#4a1834;font-weight:bold;margin-top:6px;">' . $s[1] . '</h6>'
            . '<p style="color:#6b2850;font-size:14px;margin-bottom:0;">' . $s[2] . '</p>'
            . '</div></div>';
    }
    $h .= '</div>';
    $h .= '<p style="color:#9d174d;font-size:13px;margin:14px 0 0;text-align:center;">* Valid College ID required. Offer is available only after admin approval.</p>';
    $h .= '</div>';
    return $h;
}
