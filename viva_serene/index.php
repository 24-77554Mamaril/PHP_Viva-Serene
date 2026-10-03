<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viva Serene Resort | Luxury Escape</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <a href="index.php" class="logo">
        <img src="assets/img/vivaserene_logo.png" alt="Viva Serene Logo" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        VIVA <span style="color: var(--gold);">SERENE</span>
    </a>
    <div class="nav-links">
        <a href="index.php#home">Home</a>
        <a href="index.php#rooms">Rooms</a>
        <a href="index.php#features">Amenities</a>
        <a href="admin.php">Staff Login</a>
    </div>
</nav>

<section class="hero" id="home" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('assets/img/vivaserene_bg.png');">
    <h1>Escape to Paradise</h1>
    <p>Where luxury meets serenity. Discover a sanctuary of peace, breathtaking views, and world-class service.</p>
    <a href="#rooms" class="btn-main">Book Your Stay</a>
</section>

<section class="features" id="features">
    <div class="feature-card">
        <i>🏖️</i>
        <h3>Private Beach</h3>
        <p>Pristine white sands and turquoise waters just steps from your suite.</p>
    </div>
    <div class="feature-card">
        <i>💆</i>
        <h3>Royal Spa</h3>
        <p>Holistic treatments designed to rejuvenate your spirit and body.</p>
    </div>
    <div class="feature-card">
        <i>🍽️</i>
        <h3>Fine Dining</h3>
        <p>Gourmet experiences crafted by Michelin-star chefs.</p>
    </div>
    <div class="feature-card">
        <i>🏊</i>
        <h3>Infinity Pool</h3>
        <p>A seamless blend of blue water and blue sky.</p>
    </div>
</section>

<div class="container" id="rooms">

    <div class="about-section">
        <div class="about-image">
            <img src="assets/img/vivaserene_poster.png" alt="Viva Serene Resort Poster">
        </div>
        <div class="about-text">
            <h2>Welcome to Viva Serene</h2>
            <p>Nestled in the heart of an untouched tropical paradise, Viva Serene Resort is more than just a destination—it is an experience. From the moment you arrive, you are enveloped in a world of absolute tranquility and unparalleled luxury.</p>
            <p>Our resort combines modern architectural elegance with the raw beauty of nature, offering guests a sanctuary where they can disconnect from the noise of the world and reconnect with themselves. Whether it's a romantic honeymoon, a family retreat, or a solo journey of wellness, Viva Serene provides the perfect backdrop for memories that last a lifetime.</p>
            <p>Experience our world-class amenities, from our crystal-clear infinity pools to our serene private beaches, all while enjoying the warmest hospitality in the region.</p>
        </div>
    </div>

    <div class="section-title">
        <h2>Our Accommodations</h2>
        <div class="underline"></div>
    </div>

    <div class="room-grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM rooms");
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                // Logic for better imagery based on room types
                $type = strtolower($row['room_type']);
                if(strpos($type, 'royal villa') !== false) {
                    $img_url = "assets/img/Royal%20Villa.png";
                } elseif(strpos($type, 'luxury villa') !== false) {
                    $img_url = "assets/img/Luxury%20Villa.png";
                } elseif(strpos($type, 'deluxe suite') !== false) {
                    $img_url = "assets/img/Deluxe%20Suite.png";
                } elseif(strpos($type, 'deluxe room') !== false) {
                    $img_url = "assets/img/Deluxe%20Room.png";
                } else {
                    $img_url = "assets/img/Deluxe%20Room.png";
                }

                echo "
                <div class='room-card'>
                    <div class='room-img-container'>
                        <img src=\"$img_url\" class='room-img' alt=\"{$row['room_type']}\">
                        <div class='room-overlay'>
                            <span class='status status-{$row['status']}'>{$row['status']}</span>
                        </div>
                    </div>
                    <div class='room-content'>
                        <h3>{$row['room_type']}</h3>
                        <p><strong>Room #{$row['room_number']}</strong><br>{$row['description']}</p>
                        <div class='room-footer'>
                            <span class='price'>₱{$row['price']} <small>/ night</small></span>
                        </div>
                        <a href='#' class='btn-book' onclick='bookRoom({$row['id']})'>Book Now</a>
                    </div>
                </div>";
            }
        } else {
            echo "<p style='text-align:center; grid-column: 1/-1;'>Our rooms are currently being refreshed. Please check back soon!</p>";
        }
        ?>
    </div>
</div>

<footer>
    <p>&copy; 2026 Viva Serene Resort & Spa. All rights reserved.</p>
    <p style='font-size: 0.8rem; opacity: 0.6;'>The Ultimate Destination for Tranquility</p>
</footer>

<script src="assets/js/main.js"></script>

</body>
</html>
