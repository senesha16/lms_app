<?php
 
session_start();
 
require_once('classes/database.php');
$con = new database();
 
$sweetAlertConfig = ""; //Initialize SweetAlert script variable
 
if (isset($_POST['add'])) {
 
  $authorFN = $_POST['author_FN'];
  $authorLN = $_POST['author_LN'];
  $authorBD = $_POST['author_birthdate'];
  $authorNat = $_POST['author_nat'];
  $authorID = $con->addAuthor($authorFN, $authorLN, $authorBD, $authorNat);
 
 
  if ($authorID) {
 
    $sweetAlertConfig = "
    <script>
   
    Swal.fire({
        icon: 'success',
        title: 'Author added successfully!',
        text: 'The author has been successfully added to the system.',
        confirmationButtontext: 'OK'
     }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php'
        }
            });
 
    </script>";
 
  } else {
 
    $_SESSION['error'] = "Sorry, there was an error signing up.";
   
  }
 
}
 
?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link rel="stylesheet" href="./package/dist/sweetalert2.css">
  <title>Authors</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Library Management System (Admin)</a>
      <a class="btn btn-outline-light ms-auto active" href="add_authors.html">Add Authors</a>
      <a class="btn btn-outline-light ms-2" href="add_genres.html">Add Genres</a>
      <a class="btn btn-outline-light ms-2" href="add_books.html">Add Books</a>
      <div class="dropdown ms-2">
        <button class="btn btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle"></i> <!-- Bootstrap icon -->
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li>
              <a class="dropdown-item" href="profile.html">
                  <i class="bi bi-person-circle me-2"></i> See Profile Information
              </a>
            </li>
          <li>
            <button class="dropdown-item" onclick="updatePersonalInfo()">
              <i class="bi bi-pencil-square me-2"></i> Update Personal Information
            </button>
          </li>
          <li>
            <button class="dropdown-item" onclick="updatePassword()">
              <i class="bi bi-key me-2"></i> Update Password
            </button>
          </li>
          <li>
            <button class="dropdown-item text-danger" onclick="logout()">
              <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<div class="container my-5 border border-2 rounded-3 shadow p-4 bg-light">


  <h4 class="mt-5">Add New Author</h4>
  <form method="post" action="" novalidate>
    <div class="mb-3">
      <label for="authorFirstName" class="form-label">First Name</label>
      <input type="text" name="author_FN" class="form-control" id="authorFirstName" required>
    </div>
    <div class="mb-3">
      <label for="authorLastName" class="form-label">Last Name</label>
      <input type="text" name="author_LN"  class="form-control" id="authorLastName" required>
    </div>
    <div class="mb-3">
      <label for="authorBirthYear"  class="form-label">Birth Date</label>
      <input type="date" name="author_birthdate" class="form-control" id="authorBirthYear" max="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="mb-3">
      <label for="authorNationality"  class="form-label">Nationality</label>
      <select class="form-select" name="author_nat" id="authorNationality" required>
        <option value="" disabled selected>Select Nationality</option>
        <option value="American">Filipino</option>
        <option value="American">American</option>
        <option value="British">British</option>
        <option value="Canadian">Canadian</option>
        <option value="Chinese">Chinese</option>
        <option value="French">French</option>
        <option value="German">German</option>
        <option value="Indian">Indian</option>
        <option value="Japanese">Japanese</option>
        <option value="Mexican">Mexican</option>
        <option value="Russian">Russian</option>
        <option value="South African">South African</option>
        <option value="Spanish">Spanish</option>
        <option value="Other">Other</option>
      </select>
    </div>
    <button type="submit" name="add" class="btn btn-primary">Add Author</button>
  </form>
  <script src="./package/dist/sweetalert2.js"></script>
  <?php echo $sweetAlertConfig; ?>
</div>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>
