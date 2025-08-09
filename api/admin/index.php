<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /api/admin/auth.php');
    exit;
}

require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Earn Points Mini App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"="/assets/css/style.css">
    <style>
        .admin-nav {
            background-color: var(--dark-color);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-nav h1 {
            font-size: 1.5rem;
        }
        
        .admin-content {
            padding: 20px;
        }
        
        .admin-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .admin-card h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            padding: 15px;
            text-align: center;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-card .label {
            color: var(--gray-color);
            margin-top: 5px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .admin-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-right: 5px;
            cursor: pointer;
            border: none;
        }
        
        .btn-approve {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-reject {
            background-color: var(--danger-color);
            color: white;
        }
        
        .admin-menu {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .admin-menu-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .admin-menu-item.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="admin-nav">
            <h1><i class="fas fa-cogs"></i> Admin Panel</h1>
            <div>
                <span>Welcome, Admin</span>
                <a href="/api/admin/auth.php?logout=1" class="btn btn-secondary" style="margin-left: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="value"><?php echo getTotalUsers(); ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="value"><?php echo getTotalAdsWatched(); ?></div>
                    <div class="label">Ads Watched</div>
                </div>
                <div class="stat-card">
                    <div class="value"><?php echo getTotalWithdrawals(); ?></div>
                    <div class="label">Withdrawals</div>
                </div>
                <div class="stat-card">
                    <div class="value">$<?php echo getTotalRevenue(); ?></div>
                    <div class="label">Total Revenue</div>
                </div>
            </div>
            
            <div class="admin-menu">
                <div class="admin-menu-item active" data-tab="users">Users</div>
                <div class="admin-menu-item" data-tab="withdrawals">Withdrawals</div>
                <div class="admin-menu-item" data-tab="settings">Settings</div>
            </div>
            
            <div id="users-tab" class="admin-tab-content">
                <div class="admin-card">
                    <h2><i class="fas fa-users"></i> Manage Users</h2>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Telegram ID</th>
                                    <th>Username</th>
                                    <th>Points</th>
                                    <th>Referrals</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo getUsersTable(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div id="withdrawals-tab" class="admin-tab-content" style="display: none;">
                <div class="admin-card">
                    <h2><i class="fas fa-money-bill-wave"></i> Manage Withdrawals</h2>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Address</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo getWithdrawalsTable(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div id="settings-tab" class="admin-tab-content" style="display: none;">
                <div class="admin-card">
                    <h2><i class="fas fa-sliders-h"></i> App Settings</h2>
                    <form id="settings-form">
                        <div class="form-group">
                            <label for="points-per-ad">Points Per Ad</label>
                            <input type="number" id="points-per-ad" class="form-control" value="<?php echo getSetting('points_per_ad'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="daily-ad-limit">Daily Ad Limit</label>
                            <input type="number" id="daily-ad-limit" class="form-control" value="<?php echo getSetting('daily_ad_limit'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="min-withdraw-points">Minimum Withdraw Points</label>
                            <input type="number" id="min-withdraw-points" class="form-control" value="<?php echo getSetting('min_withdraw_points'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="usd-per-point">USD Per Point</label>
                            <input type="text" id="usd-per-point" class="form-control" value="<?php echo getSetting('usd_per_point'); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const menuItems = document.querySelectorAll('.admin-menu-item');
            const tabContents = document.querySelectorAll('.admin-tab-content');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Update active menu item
                    menuItems.forEach(mi => mi.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show corresponding tab
                    tabContents.forEach(tc => tc.style.display = 'none');
                    document.getElementById(`${tabId}-tab`).style.display = 'block';
                });
            });
            
            // Handle withdrawal actions
            document.querySelectorAll('.btn-approve, .btn-reject').forEach(btn => {
                btn.addEventListener('click', function() {
                    const withdrawalId = this.getAttribute('data-id');
                    const action = this.classList.contains('btn-approve') ? 'approve' : 'reject';
                    
                    if (confirm(`Are you sure you want to ${action} this withdrawal?`)) {
                        // Send AJAX request to update withdrawal status
                        fetch(`/api/admin/withdrawals.php?action=${action}&id=${withdrawalId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('Error: ' + data.error);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                    }
                });
            });
            
            // Handle settings form
            document.getElementById('settings-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('/api/admin/settings.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Settings saved successfully!');
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    </script>
</body>
</html>

<?php
function getTotalUsers() {
    global $supabase;
    $result = $supabase->select('users');
    return count($result);
}

function getTotalAdsWatched() {
    global $supabase;
    $result = $supabase->select('ads_watched');
    return count($result);
}

function getTotalWithdrawals() {
    global $supabase;
    $result = $supabase->select('withdrawals');
    return count($result);
}

function getTotalRevenue() {
    global $supabase;
    $completed_withdrawals = $supabase->select('withdrawals', 'status=eq.completed');
    $total = 0;
    
    foreach ($completed_withdrawals as $withdrawal) {
        $total += $withdrawal['amount'];
    }
    
    return number_format($total, 2);
}

function getUsersTable() {
    global $supabase;
    $users = $supabase->select('users', '', 'created_at.desc', '20');
    
    $html = '';
    foreach ($users as $user) {
        // Get referrals count
        $referrals = $supabase->select('users', 'referred_by=eq.' . $user['id']);
        $referrals_count = count($referrals);
        
        $html .= '<tr>';
        $html .= '<td>' . $user['id'] . '</td>';
        $html .= '<td>' . $user['telegram_id'] . '</td>';
        $html .= '<td>' . ($user['username'] ?: 'N/A') . '</td>';
        $html .= '<td>' . $user['points'] . '</td>';
        $html .= '<td>' . $referrals_count . '</td>';
        $html .= '<td>' . date('M j, Y', strtotime($user['created_at'])) . '</td>';
        $html .= '<td>';
        $html .= '<button class="btn-action btn-edit" data-id="' . $user['id'] . '"><i class="fas fa-edit"></i></button> ';
        $html .= '<button class="btn-action btn-delete" data-id="' . $user['id'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    return $html;
}

function getWithdrawalsTable() {
    global $supabase;
    $withdrawals = $supabase->select('withdrawals', '', 'created_at.desc', '20');
    
    $html = '';
    foreach ($withdrawals as $withdrawal) {
        // Get user info
        $user = $supabase->select('users', 'id=eq.' . $withdrawal['user_id']);
        $username = !empty($user) ? ($user[0]['username'] ?: 'ID: ' . $user[0]['telegram_id']) : 'Unknown';
        
        $statusClass = 'status-' . $withdrawal['status'];
        $html .= '<tr>';
        $html .= '<td>' . $withdrawal['id'] . '</td>';
        $html .= '<td>' . $username . '</td>';
        $html .= '<td>$' . number_format($withdrawal['amount'], 2) . '</td>';
        $html .= '<td>' . ucfirst($withdrawal['method']) . '</td>';
        $html .= '<td>' . substr($withdrawal['address'], 0, 15) . '...</td>';
        $html .= '<td>' . date('M j, Y', strtotime($withdrawal['created_at'])) . '</td>';
        $html .= '<td><span class="status-badge ' . $statusClass . '">' . ucfirst($withdrawal['status']) . '</span></td>';
        $html .= '<td>';
        if ($withdrawal['status'] === 'pending') {
            $html .= '<button class="btn-action btn-approve" data-id="' . $withdrawal['id'] . '"><i class="fas fa-check"></i></button> ';
            $html .= '<button class="btn-action btn-reject" data-id="' . $withdrawal['id'] . '"><i class="fas fa-times"></i></button>';
        }
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    return $html;
}
?>
