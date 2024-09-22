<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Update Profile
if (isset($_POST['ChangeProfile'])) {
  $admin_id = $_SESSION['admin_id'];
  $admin_name = $_POST['admin_name'];
  $admin_email = $_POST['admin_email'];

  // Update admin information in the database
  $query = "UPDATE rpos_admin SET admin_name = ?, admin_email = ? WHERE admin_id = ?";
  $stmt = $mysqli->prepare($query);
  if ($stmt) {
    $stmt->bind_param('ssi', $admin_name, $admin_email, $admin_id);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
      $success = "Account Updated";
      header("refresh:1; url=dashboard.php");
    } else {
      $err = "Failed to update account";
    }
  } else {
    $err = "Database error: " . $mysqli->error;
  }
}

// Change Password
//print_r($_POST['changePassword']);
if (isset($_POST['changePassword'])) {
  $admin_id = $_SESSION['admin_id'];
  $old_password = $_POST['old_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  // Debugging: Check form data values
  //var_dump($admin_id, $old_password, $new_password, $confirm_password);

  // Retrieve the current password from the database
  $query = "SELECT admin_password FROM rpos_admin WHERE admin_id = ?";
  $stmt = $mysqli->prepare($query);
  if ($stmt) {
    $stmt->bind_param('s', $admin_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();
    // Verify old password
    if (password_verify($old_password, $hashed_password)) {
      // Check if new password and confirm password match
      if ($new_password === $confirm_password) {
        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $query = "UPDATE rpos_admin SET admin_password = ? WHERE admin_id = ?";
        $stmt = $mysqli->prepare($query);

        if ($stmt) {
          $stmt->bind_param('ss', $new_password_hash, $admin_id);
          $stmt->execute();
          // Check if the update was successful
          if ($stmt->affected_rows > 0) {
            $success = "Password Changed" && header("refresh:1; url=dashboard.php");
          } else {
            $err = "Failed to update password";
          }
          $stmt->close();
        } else {
          $err = "Database error 2: " . $mysqli->error;
        }
      } else {
        $err = "Confirmation Password Does Not Match";
      }
    } else {
      $err = "Please Enter Correct Old Password";
    }
  } else {
    $err = "Database error 1: " . $mysqli->error;
  }
  // $error = 0;
  // if (isset($_POST['old_password']) && !empty($_POST['old_password'])) {
  //   $old_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['old_password']))));
  // } else {
  //   $error = 1;
  //   $err = "Old Password Cannot Be Empty";
  // }
  // if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
  //   $new_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['new_password']))));
  // } else {
  //   $error = 1;
  //   $err = "New Password Cannot Be Empty";
  // }
  // if (isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
  //   $confirm_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['confirm_password']))));
  // } else {
  //   $error = 1;
  //   $err = "Confirmation Password Cannot Be Empty";
  // }

  // if (!$error) {
  //   $admin_id = $_SESSION['admin_id'];
  //   $sql = "SELECT * FROM rpos_admin   WHERE admin_id = '$admin_id'";
  //   $res = mysqli_query($mysqli, $sql);
  //   if (mysqli_num_rows($res) > 0) {
  //     $row = mysqli_fetch_assoc($res);
  //     if ($old_password != $row['admin_password']) {
  //       $err =  "Please Enter Correct Old Password";
  //     } elseif ($new_password != $confirm_password) {
  //       $err = "Confirmation Password Does Not Match";
  //     } else {

  //       $new_password  = sha1(md5($_POST['new_password']));
  //       //Insert Captured information to a database table
  //       $query = "UPDATE rpos_admin SET  admin_password =? WHERE admin_id =?";
  //       $stmt = $mysqli->prepare($query);
  //       //bind paramaters
  //       $rc = $stmt->bind_param('ss', $new_password, $admin_id);
  //       $stmt->execute();

  //       //declare a varible which will be passed to alert function
  //       if ($stmt) {
  //         $success = "Password Changed" && header("refresh:1; url=dashboard.php");
  //       } else {
  //         $err = "Please Try Again Or Try Later";
  //       }
  //     }
  //   }
  // }
}
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    $admin_id = $_SESSION['admin_id'];
    //$login_id = $_SESSION['login_id'];
    $ret = "SELECT * FROM  rpos_admin  WHERE admin_id = '$admin_id'";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($admin = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center" style="min-height: 600px; background-image: url(assets/img/theme/resto-bg.jpg); background-size: cover; background-position: center top;">
        <!-- Mask -->
        <span class="mask bg-gradient-default opacity-8"></span>
        <!-- Header container -->
        <div class="container-fluid d-flex align-items-center">
          <div class="row">
            <div class="col-lg-7 col-md-10">
              <h1 class="display-2 text-white">Hello <?php echo $admin->admin_name; ?></h1>
              <p class="text-white mt-0 mb-5">This is your profile page. You can customize your profile as you want And also change password too</p>
            </div>
          </div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <div class="row">
          <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
            <div class="card card-profile shadow">
              <div class="row justify-content-center">
                <div class="col-lg-3 order-lg-2">
                  <div class="card-profile-image">
                    <label for="profile-image-upload">
                      <img id="profile-image-preview" src="assets/img/theme/user-a-min.png" class="rounded-circle" style="cursor: pointer;">
                    </label>
                    <input type="file" id="profile-image-upload" style="display: none;">
                    <!--
                    <a href="#">
                      <img src="assets/img/theme/user-a-min.png" class="rounded-circle">
                    </a>
                    -->
                    <script>
                      document.getElementById('profile-image-upload').addEventListener('change', function(event) {
                        var file = event.target.files[0];
                        var reader = new FileReader();
                        reader.onload = function(e) {
                          document.getElementById('profile-image-preview').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                      });
                    </script>

                  </div>
                </div>
              </div>
              <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                <div class="d-flex justify-content-between">
                </div>
              </div>
              <div class="card-body pt-0 pt-md-4">
                <div class="row">
                  <div class="col">
                    <div class="card-profile-stats d-flex justify-content-center mt-md-5">
                      <div>
                      </div>
                      <div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <h3>
                    <?php echo $admin->admin_name; ?></span>
                  </h3>
                  <div class="h5 font-weight-300">
                    <i class="ni location_pin mr-2"></i><?php echo $admin->admin_email; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-8 order-xl-1">
            <div class="card bg-secondary shadow">
              <div class="card-header bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">My account</h3>
                  </div>
                  <div class="col-4 text-right">
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form method="post">
                  <h6 class="heading-small text-muted mb-4">User information</h6>
                  <div class="pl-lg-4">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label" for="input-username">User Name</label>
                          <input type="text" name="admin_name" value="<?php echo $admin->admin_name; ?>" id="input-username" class="form-control form-control-alternative">
                        </div>
                      </div>
                      <div class=" col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label" for="input-email">Email address</label>
                          <input type="email" id="input-email" value="<?php echo $admin->admin_email; ?>" name="admin_email" class="form-control form-control-alternative">
                        </div>
                      </div>

                      <div class="col-lg-12">
                        <div class="form-group">
                          <input type="submit" id="input-email" name="ChangeProfile" class="btn btn-success form-control-alternative" value="Submit">
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                <hr>
                <form method="post" role="form" onsubmit="return validateForm()">
                  <h6 class="heading-small text-muted mb-4">Change Password</h6>
                  <div class="pl-lg-4">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label class="form-control-label" for="input-username">Old Password</label>
                          <input type="password" name="old_password" id="old_password" class="form-control form-control-alternative">
                        </div>
                      </div>

                      <div class="col-lg-12">
                        <div class="form-group">
                          <label class="form-control-label" for="input-email">New Password</label>
                          <input type="password" name="new_password" id="new_password" class="form-control form-control-alternative">
                        </div>
                      </div>

                      <div class="col-lg-12">
                        <div class="form-group">
                          <label class="form-control-label" for="input-email">Confirm New Password</label>
                          <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-alternative">
                        </div>
                      </div>

                      <div class="col-lg-12">
                        <div class="form-group">
                          <input type="submit" id="input-email" name="changePassword" class="btn btn-success form-control-alternative" value="Change Password">
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              </form>
              <script>
                function validateForm() {
                  var oldPassword = document.getElementById("old_password").value;
                  var newPassword = document.getElementById("new_password").value;
                  var confirmPassword = document.getElementById("confirm_password").value;

                  if (oldPassword.trim() == "") {
                    alert("Please enter your old password.");
                    return false;
                  }

                  if (newPassword.trim() == "") {
                    alert("Please enter your new password.");
                    return false;
                  }

                  if (confirmPassword.trim() == "") {
                    alert("Please confirm your new password.");
                    return false;
                  }

                  return true;
                }
              </script>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
    <?php
      require_once('partials/_footer.php');
    }
    ?>
  </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
</body>

</html>