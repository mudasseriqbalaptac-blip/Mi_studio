<?php
$projectsFile = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'projects.json';
$uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'projects';

if (!is_dir(dirname($projectsFile))) {
    mkdir(dirname($projectsFile), 0777, true);
}

if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

function sanitizeText($value) {
    return trim(strip_tags((string) $value));
}

function loadProjects($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }

    $raw = file_get_contents($filePath);
    if ($raw === false) {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function saveProjects($filePath, $projects) {
    file_put_contents($filePath, json_encode(array_values($projects), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeText($_POST['project_title'] ?? '');
    $description = sanitizeText($_POST['project_description'] ?? '');
    $link = sanitizeText($_POST['project_link'] ?? '');
    $category = sanitizeText($_POST['project_category'] ?? 'General');
    $uploadedBy = sanitizeText($_POST['uploaded_by'] ?? '');
    if ($uploadedBy === '') {
        $uploadedBy = 'Anonymous';
    }

    $imagePath = '';
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['project_image']['tmp_name']);
        finfo_close($finfo);

        if (in_array($mimeType, $allowedTypes, true) && $_FILES['project_image']['size'] <= 2 * 1024 * 1024) {
            $extension = pathinfo($_FILES['project_image']['name'], PATHINFO_EXTENSION);
            $fileName = 'project_' . uniqid() . '.' . strtolower($extension);
            $targetPath = $uploadsDir . DIRECTORY_SEPARATOR . $fileName;
            if (move_uploaded_file($_FILES['project_image']['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/projects/' . $fileName;
            }
        }
    }

    $projects = loadProjects($projectsFile);
    $projects[] = [
        'id' => uniqid(),
        'title' => $title,
        'description' => $description,
        'link' => $link,
        'category' => $category,
        'uploaded_by' => $uploadedBy,
        'image' => $imagePath,
        'created_at' => date('Y-m-d H:i:s')
    ];

    saveProjects($projectsFile, $projects);

    header('Location: project_upload.php?success=1');
    exit;
}

$projects = array_reverse(loadProjects($projectsFile));
$success = isset($_GET['success']) && $_GET['success'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Your Project | Mi Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <nav class="page-nav">
        <a href="home.html" class="nav-back">← Back to Home</a>
        <a href="home.html#projects" class="nav-pill">View Showcase</a>
    </nav>

    <main class="project-page">
        <section class="hero-card">
            <div class="hero-copy">
                <p class="eyebrow">Project Showcase</p>
                <div class="hero-badges">
                    <span>✨ Modern showcase</span>
                    <span>⚡ Lightning fast upload</span>
                    <span>🎨 Beautiful presentation</span>
                </div>
                <h1>Show your work to the world.</h1>
                <p>Upload a project, add a short description, and let people discover what you built.</p>
                <?php if ($success): ?>
                    <div class="success-box">Your project was uploaded successfully.</div>
                <?php endif; ?>
            </div>
            <div class="hero-panel">
                <form class="project-form" action="project_upload.php" method="post" enctype="multipart/form-data">
                    <div class="form-heading">
                        <h3>Upload your project</h3>
                        <p>Share screenshots, details, and a link in one polished submission.</p>
                    </div>
                    <label>
                        Project title
                        <input type="text" name="project_title" placeholder="My awesome project" required>
                    </label>
                    <label>
                        Your name
                        <input type="text" name="uploaded_by" placeholder="Your name" required>
                    </label>
                    <label>
                        Category
                        <input type="text" name="project_category" placeholder="Web Design, App, AI, etc.">
                    </label>
                    <label>
                        Description
                        <textarea name="project_description" rows="4" placeholder="Tell people what this project does and why it matters." required></textarea>
                    </label>
                    <label>
                        Project link
                        <input type="url" name="project_link" placeholder="https://example.com">
                    </label>
                    <div class="upload-field">
                        <label class="file-label" for="project_image">Project image</label>
                        <div class="file-input-shell">
                            <input id="project_image" type="file" name="project_image" accept="image/*" onchange="previewSelectedImage(this)">
                            <span class="file-hint">PNG, JPG, WEBP · up to 2MB</span>
                        </div>
                        <div id="imagePreview" class="image-preview">
                            <span>Preview will appear here</span>
                        </div>
                    </div>
                    <button type="submit">Upload project</button>
                </form>
            </div>
        </section>

        <section class="gallery-section">
            <div class="section-title-row">
                <div>
                    <p class="eyebrow">Recent uploads</p>
                    <h2>Featured projects</h2>
                </div>
                <span class="count-pill"><?php echo count($projects); ?> items</span>
            </div>

            <?php if (!empty($projects)): ?>
                <div class="project-grid">
                    <?php foreach ($projects as $project): ?>
                        <article class="project-card">
                            <?php if (!empty($project['image'])): ?>
                                <img src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <div class="project-placeholder">✦</div>
                            <?php endif; ?>
                            <div class="project-card-body">
                                <div class="project-meta">
                                    <span><?php echo htmlspecialchars($project['category'] ?: 'General', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span><?php echo htmlspecialchars($project['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <p class="project-uploader">By <?php echo htmlspecialchars($project['uploaded_by'] ?? $project['user_name'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8'); ?></p>
                                <h3><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php if (!empty($project['link'])): ?>
                                    <a href="<?php echo htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Visit project →</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No projects yet</h3>
                    <p>Be the first person to upload a project and start the showcase.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<script src="script.js"></script>
</body>
</html>
