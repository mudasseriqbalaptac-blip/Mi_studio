<?php
$projectsFile = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'projects.json';

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

$projects = array_reverse(loadProjects($projectsFile));
$categories = [];
foreach ($projects as $project) {
    $category = isset($project['category']) ? trim((string) $project['category']) : '';
    if ($category === '') {
        $category = 'General';
    }
    $categories[] = $category;
}
$categories = array_values(array_unique($categories));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Projects | Mi Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <nav class="page-nav">
        <a href="home.html" class="nav-back">← Back to Home</a>
        <a href="project_upload.php" class="nav-pill">Upload a project</a>
    </nav>

    <main class="project-page">
        <section class="hero-card all-projects-hero">
            <div class="hero-copy">
                <p class="eyebrow">Community showcase</p>
                <h1>Discover projects from everyone on Mi Studio.</h1>
                <p>Browse inspiring work from creators, students, and builders who have shared their projects here.</p>
                <div class="hero-badges">
                    <span><?php echo count($projects); ?> shared projects</span>
                    <span><?php echo count($categories); ?> categories</span>
                    <span>✨ Beautiful community gallery</span>
                </div>
            </div>
            <div class="hero-panel hero-summary">
                <div class="summary-stat">
                    <strong><?php echo count($projects); ?></strong>
                    <span>Projects shared</span>
                </div>
                <div class="summary-stat">
                    <strong><?php echo count($categories); ?></strong>
                    <span>Topics covered</span>
                </div>
                <div class="summary-stat">
                    <strong>100%</strong>
                    <span>Community-driven</span>
                </div>
            </div>
        </section>

        <section class="gallery-section">
            <div class="section-title-row">
                <div>
                    <p class="eyebrow">All projects</p>
                    <h2>Everyone’s work in one place</h2>
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
                                    <span><?php echo htmlspecialchars(($project['category'] ?? 'General') ?: 'General', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span><?php echo htmlspecialchars($project['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <p class="project-uploader">By <?php echo htmlspecialchars($project['uploaded_by'] ?? $project['user_name'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8'); ?></p>
                                <h3><?php echo htmlspecialchars($project['title'] ?? 'Untitled project', ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p><?php echo htmlspecialchars($project['description'] ?? 'No description provided yet.', ENT_QUOTES, 'UTF-8'); ?></p>
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
                    <p>There are no shared projects right now. Be the first to upload one.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
