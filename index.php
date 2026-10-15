<?php
require_once 'config/db.php';

// Student profile (starter table)
$profile = null;
$sql = "SELECT * FROM profile LIMIT 1";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $profile = mysqli_fetch_assoc($result);
}

// Education (starter table)
$educations = [];
$sql = "SELECT * FROM education ORDER BY start_year DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $educations[] = $row;
    }
}

// Lab answer key: retrieve skills and training records from MySQL.
$skills = [];
$stmt = mysqli_prepare($conn, 'SELECT skill_name, skill_level FROM skill WHERE profile_id = ? ORDER BY id');
if ($stmt) {
    $profileId = (int)($profile['id'] ?? 0);
    mysqli_stmt_bind_param($stmt, 'i', $profileId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) { $skills[] = $row; }
    mysqli_stmt_close($stmt);
}
$trainings = [];
$stmt = mysqli_prepare($conn, 'SELECT course_name, organization, training_year FROM training WHERE profile_id = ? ORDER BY training_year DESC, id DESC');
if ($stmt) {
    $profileId = (int)($profile['id'] ?? 0);
    mysqli_stmt_bind_param($stmt, 'i', $profileId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) { $trainings[] = $row; }
    mysqli_stmt_close($stmt);
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $profile ? htmlspecialchars($profile['full_name']) : 'My Resume'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <p><a href="skills.php" style="display:inline-block;padding:12px 18px;background:#135c9a;color:white;border-radius:10px;text-decoration:none">Manage Skills (CRUD Lab) →</a></p>
    <header class="hero card">
        <img class="avatar" src="assets/images/<?php echo $profile ? htmlspecialchars($profile['photo']) : 'student.jpg'; ?>" alt="Profile Photo">
        <div>
            <h1><?php echo $profile ? htmlspecialchars($profile['full_name']) : 'Your Name'; ?></h1>
            <p class="headline"><?php echo $profile ? htmlspecialchars($profile['headline']) : 'Student Resume Website'; ?></p>
            <?php if ($profile): ?>
                <p>📧 <?php echo htmlspecialchars($profile['email']); ?></p>
                <p>📱 <?php echo htmlspecialchars($profile['phone']); ?></p>
                <p>📍 <?php echo htmlspecialchars($profile['address']); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <section class="card">
        <h2>About Me</h2>
        <p><?php echo $profile ? nl2br(htmlspecialchars($profile['about_me'])) : 'Add your profile data in phpMyAdmin.'; ?></p>
    </section>

    <section class="card">
        <h2>Education</h2>
        <?php if (count($educations) > 0): ?>
            <?php foreach ($educations as $edu): ?>
                <div class="item">
                    <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>
                    <p><?php echo htmlspecialchars($edu['institution']); ?></p>
                    <p><?php echo htmlspecialchars($edu['start_year']); ?> - <?php echo htmlspecialchars($edu['end_year']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No education data found.</p>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Skills</h2>
        <div class="skills">
            <?php foreach ($skills as $skill): ?>
                <div class="skill-badge">
                    <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>
                    <span><?php echo htmlspecialchars($skill['skill_level']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
    </section>

    <section class="card">
        <h2>Training / Certificates</h2>
        <?php foreach ($trainings as $training): ?>
            <div class="item">
                <h3><?php echo htmlspecialchars($training['course_name']); ?></h3>
                <p><?php echo htmlspecialchars($training['organization']); ?>, <?php echo htmlspecialchars($training['training_year']); ?></p>
            </div>
        <?php endforeach; ?>
        
    </section>

    <footer>
        Database Lab: Personal Resume Website with PHP + MySQL
    </footer>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
