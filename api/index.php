<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earn Points - Telegram Mini App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"="/assets/css/style.css">
    <!-- Monetag SDK -->
    <script src='//libtl.com/sdk.js' data-zone='9662488' data-sdk='show_9662488'></script>
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <h1><i class="fas fa-coins"></i> Earn Points</h1>
            <div class="user-points">
                <i class="fas fa-star"></i> <span id="user-points">0</span> Points
            </div>
        </header>
        
        <main class="app-main">
            <!-- Dashboard Section -->
            <section class="dashboard-section">
                <div class="card">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value" id="checkin-streak">0</div>
                            <div class="stat-label">Check-in Streak</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="ads-watched">0</div>
                            <div class="stat-label">Ads Today</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="referrals-count">0</div>
                            <div class="stat-label">Referrals</div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Check-in Section -->
            <section class="checkin-section">
                <div class="card">
                    <h2><i class="fas fa-calendar-check"></i> Daily Check-in</h2>
                    <div class="checkin-bonus">
                        <div class="bonus-day day-1">
                            <div class="day-label">Day 1</div>
                            <div class="bonus-points">10 pts</div>
                        </div>
                        <div class="bonus-day day-2">
                            <div class="day-label">Day 2</div>
                            <div class="bonus-points">29 pts</div>
                        </div>
                        <div class="bonus-day day-3">
                            <div class="day-label">Day 3</div>
                            <div class="bonus-points">38 pts</div>
                        </div>
                        <div class="bonus-day day-4">
                            <div class="day-label">Day 4</div>
                            <div class="bonus-points">45 pts</div>
                        </div>
                        <div class="bonus-day day-5">
                            <div class="day-label">Day 5</div>
                            <div class="bonus-points">55 pts</div>
                        </div>
                    </div>
                    <button id="checkin-btn" class="btn btn-primary">
                        <i class="fas fa-gift"></i> Check In Now
                    </button>
                </div>
            </section>
            
            <!-- Watch Ads Section -->
            <section class="ads-section">
                <div class="card">
                    <h2><i class="fas fa-ad"></i> Watch & Earn</h2>
                    <p>Watch an ad and earn 20 points</p>
                    <div class="ads-progress">
                        <div class="progress-info">
                            <span id="ads-count">0/100</span> ads today
                        </div>
                        <div class="progress-bar">
                            <div id="ads-progress" class="progress-fill" style="width: 0%"></div>
                        </div>
                    </div>
                    <button id="watch-ad-btn" class="btn btn-primary">
                        <i class="fas fa-play"></i> Watch Ad Now
                    </button>
                </div>
            </section>
            
            <!-- Referral Section -->
            <section class="referral-section">
                <div class="card">
                    <h2><i class="fas fa-user-friends"></i> Referral Program</h2>
                    <p>Invite friends and earn bonus points</p>
                    <div class="referral-link-container">
                        <input type="text" id="referral-link" class="referral-link" readonly>
                        <button id="copy-referral-btn" class="btn btn-secondary">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </section>
            
            <!-- Sponsor Ad Section -->
            <section class="sponsor-ad-section">
                <div class="sponsor-ad">
                    <h3>Buy Flash USDT Here</h3>
                    <a href="https://www.hhminer.online" target="_blank">Visit Now</a>
                </div>
            </section>
            
            <!-- Withdraw Section -->
            <section class="withdraw-section">
                <div class="card">
                    <h2><i class="fas fa-money-bill-wave"></i> Withdraw Points</h2>
                    <p>Minimum withdrawal: 5000 points ($2.00 USD)</p>
                    <form id="withdraw-form">
                        <div class="form-group">
                            <label for="withdraw-method">Withdrawal Method</label>
                            <select id="withdraw-method" class="form-control">
                                <option value="paypal">PayPal</option>
                                <option value="usdt">USDT (TRC20)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="withdraw-address">Address</label>
                            <input type="text" id="withdraw-address" class="form-control" placeholder="Enter your PayPal email or USDT address">
                        </div>
                        <div class="form-group">
                            <label for="withdraw-amount">Points to Withdraw</label>
                            <input type="number" id="withdraw-amount" class="form-control" min="5000" step="100" placeholder="Minimum 5000 points">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Withdraw Now
                        </button>
                    </form>
                </div>
            </section>
        </main>
        
        <footer class="app-footer">
            <p>&copy; 2023 Earn Points Mini App. All rights reserved.</p>
        </footer>
    </div>
    
    <!-- Footer Navigation -->
    <nav class="footer-nav">
        <a href="#" class="nav-item active" data-section="dashboard">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="nav-item" data-section="withdraw">
            <i class="fas fa-money-bill-wave"></i>
            <span>Withdraw</span>
        </a>
        <a href="#" class="nav-item" data-section="referral">
            <i class="fas fa-user-friends"></i>
            <span>Ref</span>
        </a>
        <a href="#" class="nav-item" data-section="support">
            <i class="fas fa-headset"></i>
            <span>Support</span>
        </a>
    </nav>
    
    <!-- Notification Container -->
    <div id="notification" class="notification"></div>
    
    <!-- Ad Loading Modal -->
    <div id="ad-loading" class="ad-loading">
        <div class="ad-loading-content">
            <div class="spinner"></div>
            <h3>Loading Ad...</h3>
            <p>Please wait while we prepare an ad for you</p>
        </div>
    </div>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
