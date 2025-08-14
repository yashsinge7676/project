<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <?php
    session_start();
    // If you want to show the username as logo, get it from session or set a default
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
    ?>
    <nav style="display: flex; align-items: center; background: #333; color: #fff; padding: 10px 20px;">
        <div id="profileBtn" onclick="toggleSidebar()" style="font-weight: bold; font-size: 1.5em; margin-right: 30px; cursor:pointer; display:flex; align-items:center;">
            <span style="background: #fff; color: #333; border-radius: 50%; padding: 8px 16px; margin-right: 10px;">👤</span>
            <?php echo htmlspecialchars($username); ?>
        </div>
        <a href="home.php" style="color: #fff; text-decoration: none; margin-right: 30px; font-size: 1.1em;">Home</a>
        <form method="get" action="#" style="margin: 0; display: flex; align-items: center;">
            <input type="text" name="search" placeholder="Search..." style="padding: 6px 10px; border-radius: 4px 0 0 4px; border: none; outline: none;">
            <button type="submit" style="padding: 6px 16px; border: none; background: #ff9800; color: #fff; border-radius: 0 4px 4px 0; cursor: pointer;">Search</button>
        </form>
    </nav>

    <div id="sidebarOverlay" onclick="closeSidebar()" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:999;"></div>
    <!-- Sidebar will be injected dynamically -->

    <script>
    function toggleSidebar() {
        // If sidebar already exists, just show it
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (!sidebar) {
            // Create sidebar
            var sidebarDiv = document.createElement('div');
            sidebarDiv.id = 'sidebar';
            sidebarDiv.style.position = 'fixed';
            sidebarDiv.style.top = '0';
            sidebarDiv.style.right = '0';
            sidebarDiv.style.width = '350px';
            sidebarDiv.style.height = '100%';
            sidebarDiv.style.background = '#222';
            sidebarDiv.style.color = '#fff';
            sidebarDiv.style.boxShadow = '-2px 0 8px rgba(0,0,0,0.2)';
            sidebarDiv.style.zIndex = '1000';
            sidebarDiv.style.padding = '30px 20px';
            sidebarDiv.innerHTML = `
                <span onclick="closeSidebar()" style="position:absolute; top:10px; right:20px; font-size:2em; cursor:pointer;">&times;</span>
                <h2>User Profile</h2>
                <p>Hi, <strong><?php echo htmlspecialchars($username); ?></strong>!</p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($username); ?></p>
            `;
            document.body.appendChild(sidebarDiv);
        }
        overlay.style.display = 'block';
    }
    function closeSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (sidebar) {
            sidebar.parentNode.removeChild(sidebar);
        }
        overlay.style.display = 'none';
    }
    </script>
    <!-- Page content here -->
    <div class="main-content">
    <?php
    // Database connection (same as adminpanel)
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "adminpanel";
    $conn = new mysqli($server, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch media by category (3x3 grid: photos, videos, texts)
    $photos = [];
    $videos = [];
    $texts = [];
    $res = $conn->query("SELECT * FROM media WHERE type='photo' ORDER BY id DESC LIMIT 3");
    while ($row = $res->fetch_assoc()) $photos[] = $row;
    $res = $conn->query("SELECT * FROM media WHERE type='video' ORDER BY id DESC LIMIT 3");
    while ($row = $res->fetch_assoc()) $videos[] = $row;
    $res = $conn->query("SELECT * FROM media WHERE type='text' ORDER BY id DESC LIMIT 3");
    while ($row = $res->fetch_assoc()) $texts[] = $row;

    echo '<div class="media-grid">';
    // Row 1: Photos
    for ($i = 0; $i < 3; $i++) {
        echo '<div class="media-card">';
        if (isset($photos[$i])) {
            $m = $photos[$i];
            $src = 'adminpanel/uploads/photos/' . htmlspecialchars($m['filename']);
            echo "<img src='$src' alt='Photo'>";
        } else {
            echo '<div style="height:120px;"></div>';
        }
        echo '</div>';
    }
    // Row 2: Videos
    for ($i = 0; $i < 3; $i++) {
        echo '<div class="media-card">';
        if (isset($videos[$i])) {
            $m = $videos[$i];
            $src = 'adminpanel/uploads/videos/' . htmlspecialchars($m['filename']);
            echo "<video src='$src' controls></video>";
        } else {
            echo '<div style="height:120px;"></div>';
        }
        echo '</div>';
    }
    // Row 3: Texts
    for ($i = 0; $i < 3; $i++) {
        echo '<div class="media-card">';
        if (isset($texts[$i])) {
            $m = $texts[$i];
            echo "<div class='media-text'><i class='fas fa-font'></i> " . htmlspecialchars($m['text_content']) . "</div>";
        } else {
            echo '<div style="height:120px;"></div>';
        }
        echo '</div>';
    }
    echo '</div>';
    $conn->close();
    ?>
    </div>
</body>
</html>