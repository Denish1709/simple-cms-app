<?php

  // make sure only admin can access
  if ( !Authentication::whoCanAccess('admin') ) {
    header('Location: /dashboard');
    exit;
  }

  // load user data
  $user = User::getUserByID( $_GET['id'] );

  // step 1: set CSRF token
  CSRF::generateToken( 'edit_post_form' );


  // step 2: make sure post request
  if ( $_SERVER["REQUEST_METHOD"] === 'POST' ) {

    // step 3: do error check

      // Do a check to find out should we do password update or not
      $is_password_changed = ( 
        ( isset( $_POST['password'] ) && !empty( $_POST['password'] ) ) || 
        ( isset( $_POST['confirm_password'] ) && !empty( $_POST['confirm_password'] ) ) 
        ? true : false
      );

      // if both password & confirm_password fields are empty, 
      // skip error checking for both fields.
      $rules = [
        'name' => 'required',
        'email' => 'email_check',
        'role' => 'required',
        'csrf_token' => 'edit_user_form_csrf_token'
      ];

      // if password is updated
      if ( $is_password_changed ) {
        $rules['password'] = 'password_check'; // make sure the length >= 8
        $rules['confirm_password'] = 'is_password_match'; // make sure both fields are match
      }

      // if eiter password & confirm_password fields are not empty, 
      // do error check for both fields
      $error = FormValidation::validate(
        $_POST,
        $rules
      );

      // if email changed, make sure it cannot belongs to another user
      // we compare email from database and form for email changes
      if ( $user['email'] !== $_POST['email'] ) {
        // do database check to make sure new email wasn't already in use
        $error .= FormValidation::checkEmailUniqueness( $_POST['email'] );
      }

      // make sure there is no error
      if ( !$error ) {
        // step 4: update user
        User::update(
          $user['id'], // id
          $_POST['name'], // name
          $_POST['email'],// email
          $_POST['role'], // role
          ( $is_password_changed ? $_POST['password'] : null ) // password update if available
        );

        // step 5: remove the CSRF token
        CSRF::removeToken( 'edit_post_form');

        // Step 6: redirect to manage users page
        header("Location: /manage-posts");
        exit;

      }
  }

  require dirname(__DIR__) . '/parts/header.php';
?>
    <div class="container mx-auto my-5" style="max-width: 700px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1">Edit Post</h1>
      </div>
      <div class="card mb-2 p-4">
      <?php require dirname( __DIR__ ) . '/parts/error_box.php'; ?>
        <form
          method="POST"
          action="<?php echo $_SERVER["REQUEST_URI"]; ?>"
          >
          <div class="mb-3">
            <label for="post-title" class="form-label">Title</label>
            <input
              type="text"
              class="form-control"
              id="post-title"
              name="title"
              value="<?php echo $post['title']; ?>"
            />
          </div>
          <div class="mb-3">
            <label for="post-content" class="form-label">Content</label>
            <textarea class="form-control" id="post-content" rows="10">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris purus risus, euismod ac tristique in, suscipit quis quam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Vestibulum eget dapibus nibh. Pellentesque nec maximus odio. In pretium diam metus, sed suscipit neque porttitor vitae. Vestibulum a mattis eros. Integer fermentum arcu dolor, nec interdum sem tincidunt in. Cras malesuada a neque ut sodales. Nulla facilisi.

Phasellus sodales arcu quis felis sollicitudin vehicula. Aliquam viverra sem ac bibendum tincidunt. Donec pulvinar id purus sagittis laoreet. Sed aliquet ac nisi vehicula rutrum. Proin non risus et erat rhoncus aliquet. Nam sollicitudin facilisis elit, a consequat arcu placerat eu. Pellentesque euismod et est quis faucibus.

Curabitur sit amet nisl feugiat, efficitur nibh et, efficitur ex. Morbi nec fringilla nisl. Praesent blandit pellentesque urna, a tristique nunc lacinia quis. Integer semper cursus lectus, ac hendrerit mi volutpat sit amet. Etiam iaculis arcu eget augue sollicitudin, vel luctus lorem vulputate. Donec euismod eu dolor interdum efficitur. Vestibulum finibus, lectus sed condimentum ornare, velit nisi malesuada ligula, eget posuere augue metus et dolor. Nunc purus eros, ultricies in sapien quis, sagittis posuere risus.
                        </textarea
            >
          </div>
          <div class="mb-3">
            <label for="post-content" class="form-label">Status</label>
            <select class="form-control" id="post-status" name="status">
              <option value="review">Pending for Review</option>
              <option value="publish">Publish</option>
            </select>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
      <div class="text-center">
        <a href="/manage-post" class="btn btn-link btn-sm"
          ><i class="bi bi-arrow-left"></i> Back to Posts</a
        >
      </div>
    </div>
    <?php

require dirname(__DIR__) . '/parts/footer.php';
