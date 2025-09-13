<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Member Home</title>
    <link rel="stylesheet" href="memberhome.css" />
</head>
<body>
    <header class="header">
        <div class="logo">AFA Internet Cafe</div>
        <nav class="nav-menu">
            <ul>
                <li><a href="anno.php">ANNOUNCEMENT</a></li>
                <li><a href="memberhome.php" class="active">GAME</a></li>
                <li><a href="food.php">FOOD</a></li>
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
        <section class="game-list-section">
            <h2>DAFTAR GAME</h2>
            <div class="game-grid">
                <div class="game-card">
                    <img src="GAMES/csgo.png" alt="CS : GO" />
                    <div class="game-name">CS : GO</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/Genshin.png" alt="Genshin Impact" />
                    <div class="game-name">Genshin Impact</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/RainbowSix.png" alt="Rainbow Six Siege" />
                    <div class="game-name">Rainbow Six Siege</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/VALORANT.png" alt="VALORANT" />
                    <div class="game-name">VALORANT</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/PUBG.png" alt="PUBG" />
                    <div class="game-name">PUBG</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/WUWA.png" alt="Wuthering Waves" />
                    <div class="game-name">Wuthering Waves</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/MINE.png" alt="Minecraft" />
                    <div class="game-name">Minecraft</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/PB.png" alt="Point Blank" />
                    <div class="game-name">Point Blank</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/ROBLOX.png" alt="ROBLOX" />
                    <div class="game-name">ROBLOX</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/DOOM.png" alt="DOOM" />
                    <div class="game-name">DOOM</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/DF.png" alt="DOOM" />
                    <div class="game-name">Delta Force</div>
                </div>
                <div class="game-card">
                    <img src="GAMES/CODW.png" alt="DOOM" />
                    <div class="game-name">COD Warzone</div>
                </div>
            </div>
        </section>
    </main>
    <script src="memberhome.js"></script>
</body>
</html>
