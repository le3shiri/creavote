<?php
// Profile Edit Controller
session_start();
require_once '../../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $twitter = trim($_POST['twitter'] ?? '');
    $errors = [];

    // Basic validation
    if (!$firstname || !$lastname || !$email) {
        $errors[] = 'First name, last name, and email are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    // Max length checks
    if (strlen($firstname) > 100) $errors[] = 'First name too long.';
    if (strlen($lastname) > 100) $errors[] = 'Last name too long.';
    if (strlen($display_name) > 100) $errors[] = 'Display name too long.';
    if (strlen($email) > 50) $errors[] = 'Email too long.';
    if (strlen($phone) > 20) $errors[] = 'Phone number too long.';
    if (strlen($country) > 100) $errors[] = 'Country name too long.';
    if (strlen($location) > 100) $errors[] = 'Location too long.';
    if (strlen($description) > 500) $errors[] = 'Description too long (max 500 chars).';
    if (strlen($website) > 200) $errors[] = 'Website URL too long.';
    if (strlen($linkedin) > 200) $errors[] = 'LinkedIn URL too long.';
    if (strlen($instagram) > 200) $errors[] = 'Instagram URL too long.';
    if (strlen($twitter) > 200) $errors[] = 'Twitter URL too long.';
    // Phone validation
    if ($phone && (!preg_match('/^\d{8,20}$/', $phone))) {
        $errors[] = 'Phone number must be 8-20 digits.';
    }
    // URL validation
    foreach ([['website',$website],['linkedin',$linkedin],['instagram',$instagram],['twitter',$twitter]] as $field) {
        if ($field[1] && !filter_var($field[1], FILTER_VALIDATE_URL)) {
            $errors[] = ucfirst($field[0]) . ' must be a valid URL.';
        }
    }
    // Unique email check
    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ?');
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = 'This email address is already used by another account.';
    }

    // Profile picture upload
    $profile_picture_url = null;
    // Only process upload if not removing picture
    if (isset($_POST['remove_picture']) && $_POST['remove_picture'] == '1') {
        $profile_picture_url = null;
        file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Remove picture requested by user $user_id\n", FILE_APPEND);
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['profile_picture']['size'] <= 36*1024*1024) {
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $destination = __DIR__ . '/../../uploads/' . $filename;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                $profile_picture_url = '/Creavote/uploads/' . $filename;
                file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Uploaded new picture for user $user_id: $filename\n", FILE_APPEND);
                // Update user profile picture in DB immediately
                $stmt = $pdo->prepare('UPDATE users SET profile_picture = ? WHERE user_id = ?');
                $stmt->execute([$profile_picture_url, $user_id]);
                // Insert notification
                $notif_stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, message, is_read) VALUES (?, ?, ?, 0)');
                $notif_stmt->execute([$user_id, 'message', 'You updated your profile picture!']);
                // If AJAX, return JSON and exit
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode(['success'=>true, 'profile_picture'=>$profile_picture_url]);
                    exit;
                }
            } else {
                $errors[] = 'Failed to move uploaded file.';
                file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Failed to move uploaded file for user $user_id\n", FILE_APPEND);
            }
        } else {
            $errors[] = 'Invalid profile picture file. Only JPG, JPEG, PNG, GIF up to 35MB are allowed.';
            file_put_contents(__DIR__.'/edit_profile_debug.log', date('c') . " - Invalid file type or size for user $user_id\n", FILE_APPEND);
        }
    }

    if ($errors) {
        $_SESSION['profile_edit_errors'] = $errors;
        header('Location: ../../views/profile-edit.php');
        exit;
    }

    // Update user in DB
    $sql = 'UPDATE users SET firstname=?, lastname=?, email=?, phone=?, country=?';
    $params = [$firstname, $lastname, $email, $phone, $country];
    if ($profile_picture_url !== null) {
        $sql .= ', profile_picture=?';
        $params[] = $profile_picture_url;
    }
    $sql .= ' WHERE user_id=?';
    $params[] = $user_id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $_SESSION['profile_edit_success'] = 'Profile updated successfully!';
    header('Location: ../../views/profile-edit.php');
    exit;
} else {
    header('Location: ../../views/profile-edit.php');
    exit;
}
