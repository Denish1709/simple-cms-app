<?php

  // make sure only admin can access
  if ( !Authentication::whoCanAccess('admin') ) {
    header('Location: /dashboard');
    exit;
  }

  // step 1: set CSRF token
  CSRF::generateToken( 'add_post_form' );

  // step 2: make sure post request
  if ( $_SERVER["REQUEST_METHOD"] === 'POST' ) {

    // step 3: do error check
     $rules = [
      'title' => 'required',
      'csrf_token' => 'add_edit_form_csrf_token',
      'password' => 'password_check',
      'confirm_password' => 'is_password_match'
    ];

    $error = FormValidation::validate(
      $_POST,
      $rules
    );

    // make sure there is no error
    if ( !$error ) {

      // step 4 = add new post
      Post::add(
        $_POST['title'],
        $_POST['status'],
      );

      // step 5: remove the CSRF token
      CSRF::removeToken( 'add_post_form' );

      // step 6: redirect to manage users page
      header("Location: /manage-posts");
      exit;

    } // end - $error


  } // end - $_SERVER["REQUEST_METHOD"]


require dirname(__DIR__) . '/parts/header.php';
?>
    <div class="container mx-auto my-5" style="max-width: 700px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1">Add New Post</h1>
      </div>
      <div class="card mb-2 p-4">
      <?php require dirname( __DIR__ ) . '/parts/error_box.php'; ?>
        <form
          method="POST"
          action="<?php echo $_SERVER["REQUEST_URI"]; ?>"
          >
          <div class="mb-3">
            <label for="post-title" class="form-label">Title</label>
            <input type="text" 
            class="form-control" 
            id="post-title"
            name="title"
            />
          </div>
          <div class="mb-3">
            <label for="post-content" class="form-label">Content</label>
            <textarea
              class="form-control"
              id="post-content"
              name="content"
              rows="10"
            ></textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Add</button>
          </div>
          <input
            type="hidden"
            name="csrf_token"
            value="<?php echo CSRF::getToken( 'add_user_form' ); ?>"
            />
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
