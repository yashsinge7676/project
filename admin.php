<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$server = "localhost";
$username = "root";
$password = "";
$database = "adminpanel";
$conn = new mysqli($server, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_photo']) && $_FILES['media_photo']['error'] === UPLOAD_ERR_OK) {
    $photoName = basename($_FILES['media_photo']['name']);
    $targetDir = __DIR__ . '/uploads/photos/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . $photoName;
    if (move_uploaded_file($_FILES['media_photo']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO media (type, filename) VALUES ('photo', ?)");
        $stmt->bind_param("s", $photoName);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php"); exit();
    }
}
// Handle add video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_video']) && $_FILES['media_video']['error'] === UPLOAD_ERR_OK) {
    $videoName = basename($_FILES['media_video']['name']);
    $targetDir = __DIR__ . '/uploads/videos/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . $videoName;
    if (move_uploaded_file($_FILES['media_video']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO media (type, filename) VALUES ('video', ?)");
        $stmt->bind_param("s", $videoName);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php"); exit();
    }
}
// Handle add text
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['media_text']) && trim($_POST['media_text']) !== '') {
    $text = trim($_POST['media_text']);
    $stmt = $conn->prepare("INSERT INTO media (type, text_content) VALUES ('text', ?)");
    $stmt->bind_param("s", $text);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php"); exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT * FROM media WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if ($row['type'] === 'photo') {
            $file = __DIR__ . '/uploads/photos/' . $row['filename'];
            if (file_exists($file)) unlink($file);
        } elseif ($row['type'] === 'video') {
            $file = __DIR__ . '/uploads/videos/' . $row['filename'];
            if (file_exists($file)) unlink($file);
        }
    }
    $conn->query("DELETE FROM media WHERE id=$id");
    header("Location: admin.php"); exit();
}

// Handle update (text only)
if (isset($_POST['update_id']) && isset($_POST['update_text'])) {
    $id = intval($_POST['update_id']);
    $text = trim($_POST['update_text']);
    $stmt = $conn->prepare("UPDATE media SET text_content=? WHERE id=?");
    $stmt->bind_param("si", $text, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php"); exit();
}

// Fetch media for grid
$photos = [];
$videos = [];
$texts = [];
$res = $conn->query("SELECT * FROM media WHERE type='photo' ORDER BY id DESC LIMIT 3");
while ($row = $res->fetch_assoc()) $photos[] = $row;
$res = $conn->query("SELECT * FROM media WHERE type='video' ORDER BY id DESC LIMIT 3");
while ($row = $res->fetch_assoc()) $videos[] = $row;
$res = $conn->query("SELECT * FROM media WHERE type='text' ORDER BY id DESC LIMIT 3");
while ($row = $res->fetch_assoc()) $texts[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
	.media-add-bar .media-btn {
	  background: #ff9800 !important;
	  color: #fff !important;
	  border: 2px solid #333 !important;
	  border-radius: 4px;
	  padding: 10px 20px;
	  font-size: 1.1em;
	  cursor: pointer;
	  z-index: 10;
	  position: relative;
	  display: inline-block;
	}
	</style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-left">
        <span class="brand">AdminPanel</span>
    </div>
    <div class="navbar-center">
        <form method="get" action="" class="search-container">
            <input type="text" name="search" class="search-input" placeholder="Search media...">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="navbar-right">
        <div class="admin-profile" title="Admin Profile" style="display:flex;align-items:center;gap:8px;">
            <img src="https://dummyimage.com/40x40/343a40/fff&text=A" alt="Admin" class="profile-logo">
            <span style="font-weight:500;">Admin</span>
        </div>
    </div>
</nav>
<main class="main-content">
    <div class="media-add-bar">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="media_photo" accept="image/*" id="mediaPhotoInput" style="display:none;" onchange="this.form.submit()">
            <button type="button" class="media-btn" onclick="document.getElementById('mediaPhotoInput').click()"><i class="fas fa-image"></i> Add Photo</button>
        </form>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="media_video" accept="video/*" id="mediaVideoInput" style="display:none;" onchange="this.form.submit()">
            <button type="button" class="media-btn" onclick="document.getElementById('mediaVideoInput').click()"><i class="fas fa-video"></i> Add Video</button>
        </form>
        <form method="post">
            <input type="text" name="media_text" placeholder="Add Text..." class="media-input">
            <button type="submit" class="media-btn"><i class="fas fa-font"></i> Add Text</button>
        </form>
    </div>
    <div class="media-grid">
        <!-- Row 1: Photos -->
        <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="media-card">
                <?php if (isset($photos[$i])): $m = $photos[$i]; ?>
                    <img src="uploads/photos/<?php echo htmlspecialchars($m['filename']); ?>" alt="Photo">
                    <div class="media-actions">
                        <a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('Delete this photo?');" class="media-btn delete"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                <?php else: ?>
                    <div style="height:120px;"></div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
        <!-- Row 2: Videos -->
        <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="media-card">
                <?php if (isset($videos[$i])): $m = $videos[$i]; ?>
                    <video src="uploads/videos/<?php echo htmlspecialchars($m['filename']); ?>" controls></video>
                    <div class="media-actions">
                        <a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('Delete this video?');" class="media-btn delete"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                <?php else: ?>
                    <div style="height:120px;"></div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
        <!-- Row 3: Texts -->
        <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="media-card">
                <?php if (isset($texts[$i])): $m = $texts[$i]; ?>
                    <div class="media-text"><i class="fas fa-font"></i> <?php echo htmlspecialchars($m['text_content']); ?></div>
                    <div class="media-actions">
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="update_id" value="<?php echo $m['id']; ?>">
                            <input type="text" name="update_text" value="<?php echo htmlspecialchars($m['text_content']); ?>" style="width:90px;padding:3px 6px;border-radius:3px;border:1px solid #ccc;">
                            <button type="submit" class="media-btn update"><i class="fas fa-edit"></i> Update</button>
                        </form>
                        <a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('Delete this text?');" class="media-btn delete"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                <?php else: ?>
                    <div style="height:120px;"></div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</main>
</body>
</html>
<!--
SQL code to create the tables:

CREATE TABLE photos (
	id INT AUTO_INCREMENT PRIMARY KEY,
	filename VARCHAR(255) NOT NULL,
	uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE videos (
	id INT AUTO_INCREMENT PRIMARY KEY,
	filename VARCHAR(255) NOT NULL,
	uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-->
</body>
</html>
