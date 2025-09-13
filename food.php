<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Member Home</title>
    <link rel="stylesheet" href="food.css" />
</head>
<body>
    <header class="header">
        <div class="logo">AFA Internet Cafe</div>
        <nav class="nav-menu">
            <ul>
                <li><a href="anno.php">ANNOUNCEMENT</a></li>
                <li><a href="memberhome.php">GAME</a></li>
                <li><a href="food.php" class="active">FOOD</a></li>
                <li><a href="billing.php">BILLING</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="timer">90:00</div>
            <div class="icon cart-icon" title="Cart"><img src="FOTO/Keranj.png" alt="Shopping.png"></div>
            <div class="icon user-icon" title="User Profile"><img src="FOTO/Screenshot 2025-09-07 151420.png" alt="Profile Screenshot" style="width:60px; height:60px;"></div>
        </div>
    </header>
    <main>
        <section class="slideshow-section">
            <!-- Slideshow container -->
            <div class="slideshow-container">
                <div class="mySlides fade">
                    <img src="FOTO/1.png">
                </div>
                <div class="mySlides fade">
                    <img src="FOTO/2.png">
                </div>
            </div>
            <br>
            <!-- The dots/circles -->
            <div style="text-align:center">
                <span class="dot"></span> 
                <span class="dot"></span> 
            </div>
        </section>
        <section class="food-list-section">
            <h2>DAFTAR MAKANAN</h2>
            <div class="food-grid">
                <div class="food-card">
                    <img src="FOODS/Indomie Goreng.jpg" alt="Indomie Goreng" />
                    <div class="food-name">Indomie Goreng</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Indomie Kuah.jpg" alt="Indomie Kuah" />
                    <div class="food-name">Indomie Kuah</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Kentang Goreng.jpg" alt="Kentang Goreng" />
                    <div class="food-name">Kentang Goreng</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Keripik Pedas.jpg" alt="Keripik Pedas" />
                    <div class="food-name">Keripik Pedas</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Mie Ayam.jpg" alt="Mie Ayam" />
                    <div class="food-name">Mie Ayam</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Nasi Goreng.jpg" alt="Nasi Goreng" />
                    <div class="food-name">Nasi Goreng</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Nugget.jpg" alt="Nugget" />
                    <div class="food-name">Nugget</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Pisang Goreng Cokelat.jpg" alt="Pisang Goreng Cokelat" />
                    <div class="food-name">Pisang Goreng Cokelat</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Pop Mie.jpg" alt="Pop Mie" />
                    <div class="food-name">Pop Mie</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Popcorn.jpg" alt="Popcorn" />
                    <div class="food-name">Popcorn</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Roti Bakar Cokelat.jpg" alt="Roti Bakar Cokelat" />
                    <div class="food-name">Roti Bakar Cokelat</div>
                </div>
                <div class="food-card">
                    <img src="FOODS/Sosis tusukan.jpg" alt="Sosis tusukan" />
                    <div class="food-name">Sosis tusukan</div>
                </div>
            </div>
        </section>
    </main>
    <script src="food.js"></script>
</body>
</html>