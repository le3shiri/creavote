<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Fetch user info
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
function render_main() {
    global $user;
?>
<div class="flex-1 flex flex-col px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <a href="profile.php" class="flex items-center mb-6 text-gray-600 hover:text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Edit Profile
        </a>
        <?php
        if (!empty($_SESSION['profile_edit_errors'])) {
            echo '<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
            foreach ($_SESSION['profile_edit_errors'] as $error) {
                echo '<div>'.htmlspecialchars($error).'</div>';
            }
            echo '</div>';
            unset($_SESSION['profile_edit_errors']);
        }
        if (!empty($_SESSION['profile_edit_success'])) {
            echo '<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">'.htmlspecialchars($_SESSION['profile_edit_success']).'</div>';
            unset($_SESSION['profile_edit_success']);
        }
        ?>
        <form action="../controllers/profile/edit-profile.php" method="POST" enctype="multipart/form-data">
            <!-- Profile Picture Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6 flex items-center gap-8">
                <div class="flex flex-col items-center">
                    <img id="profile-pic-preview" src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'https://randomuser.me/api/portraits/lego/1.jpg'); ?>" class="w-20 h-20 rounded-full border-4 border-white shadow mb-2" alt="Profile Picture">
                    <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*">
                    <div class="flex gap-2 mt-2">
                        <label for="profile_picture" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded cursor-pointer font-semibold">Upload</label>
                        <button type="submit" name="remove_picture" value="1" class="bg-blue-100 hover:bg-blue-200 text-blue-600 px-6 py-2 rounded font-semibold">Remove</button>
                    </div>
                    <div id="profile-pic-success" class="text-green-600 text-sm mt-2" style="display:none;"></div>
                </div>
                <div class="flex-1">
                    <div class="text-lg font-semibold mb-2">Upload a new profile picture</div>
                </div>
            </div>
            <!-- Personal Information Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-bold mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-1">First Name</label>
                        <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Display Name</label>
                    <input type="text" name="display_name" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <span class="text-xs text-gray-400">This is how your name will appear publicly</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4 flex gap-2 items-center">
                    <label for="phone_prefix" class="sr-only">Country Code</label>
                    <?php include __DIR__ . '/countries.php'; ?>
                    <select name="phone_prefix" id="phone_prefix" class="px-2 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['prefix']); ?>" <?php if (($user['country'] ?? '') == strtolower($c['code'])) echo 'selected'; ?>><?php echo htmlspecialchars($c['prefix'] . ' (' . $c['name'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Phone Number (without country code)" maxlength="20">
                </div>
            </div>
            <!-- Location, Description, Social Links -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <label for="country" class="sr-only">Country</label>
                    <select name="country" id="country" class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo strtolower(htmlspecialchars($c['code'])); ?>" <?php if (($user['country'] ?? '') == strtolower($c['code'])) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" maxlength="500" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" oninput="document.getElementById('desc-count').textContent = this.value.length + '/500';"><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                    <div class="text-xs text-gray-400 text-right"><span id="desc-count">0/500</span></div>
                </div>
            </div>
            <!-- Social Links -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-bold mb-4">Social Links</h2>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1">Personal Website</label>
                    <input type="url" name="website" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1">LinkedIn</label>
                    <input type="url" name="linkedin" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1">Instagram</label>
                    <input type="url" name="instagram" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1">Twitter</label>
                    <input type="url" name="twitter" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            <div class="flex gap-4 mt-4">
                <a href="profile.php" class="flex-1 text-center bg-blue-100 hover:bg-blue-200 text-blue-600 px-6 py-3 rounded font-semibold">Cancel</a>
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded font-semibold">Save Changes</button>
            </div>
        <script>
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('profile_picture');
  const previewImg = document.getElementById('profile-pic-preview');
  const successMsg = document.getElementById('profile-pic-success');
  fileInput.addEventListener('change', function() {
    if (fileInput.files && fileInput.files[0]) {
      const formData = new FormData();
      formData.append('profile_picture', fileInput.files[0]);
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '../controllers/profile/edit-profile.php', true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.onload = function() {
        if (xhr.status === 200) {
          try {
            const res = JSON.parse(xhr.responseText);
            if (res.success && res.profile_picture) {
              previewImg.src = res.profile_picture;
              successMsg.textContent = 'Profile picture updated!';
              successMsg.style.display = 'block';
              setTimeout(() => { successMsg.style.display = 'none'; }, 2000);
            } else {
              successMsg.textContent = 'Failed to update profile picture.';
              successMsg.style.display = 'block';
            }
          } catch (e) {
            successMsg.textContent = 'Upload error.';
            successMsg.style.display = 'block';
          }
        } else {
          successMsg.textContent = 'Upload failed.';
          successMsg.style.display = 'block';
        }
      };
      xhr.send(formData);
    }
  });
});
</script>
</form>
    </div>
</div>
<?php
}
include 'base.php';
