<?php

  // make sure only admin can access
  if ( !Authentication::whoCanAccess('admin') ) {
    header('Location: /dashboard');
    exit;
  }

  // Step 1: generate CSRF token
  CSRF::generateToken( 'delete_edit_form' );

  // Step 2: make sure it's POST request
  if ( $_SERVER["REQUEST_METHOD"] === 'POST' ) {

    // step 3: do error check
    $error = FormValidation::validate(
      $_POST,
      [
        'post_id' => 'required',
        'csrf_token' => 'delete_user_form_csrf_token'
      ]
    );

    // make sure there is no error
    if ( !$error ) {
      // step 4: delete user
      User::delete( $_POST['post_id'] );

      // step 5: remove CSRF token
      CSRF::removeToken( 'delete_post_form' );

      // step 6: redirect back to the same page
      header("Location: /manage-posts");
      exit;

    } // end - $error

  } // end - $_SERVER["REQUEST_METHOD"]

require dirname(__DIR__) . '/parts/header.php';
?>
    <div class="container mx-auto my-5" style="max-width: 700px;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h1">Manage Posts</h1>
        <div class="text-end">
          <a href="/manage-posts-add" class="btn btn-primary btn-sm"
            >Add New Post</a
          >
        </div>
      </div>
      <div class="card mb-2 p-4">
      <?php require dirname( __DIR__ ) . '/parts/error_box.php'; ?>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col" style="width: 40%;">Title</th>
              <th scope="col">Status</th>
              <th scope="col" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php 
            foreach( Post::getAllPosts() as $index => $post ) : 
            ?>
            <tr>
            <td><?php echo $index+1; ?></td>
              <th scope="row"><?php echo $post['id']; ?></th>
              <td><?php echo $post['title']; ?></td>
              <td><span class="badge bg-warning"><?php echo $post['status']; ?></span></td>
              <td class="text-end">
                <div class="buttons">
                  <a
                    href="/post"
                    target="_blank"
                    class="btn btn-primary btn-sm me-2 disabled"
                    ><i class="bi bi-eye"></i
                  ></a>
                  <a
                    href="/manage-posts-edit"
                    class="btn btn-secondary btn-sm me-2"
                    ><i class="bi bi-pencil"></i
                  ></a>
                  <?php if ( $_SESSION['post']['id'] !== $post['id'] ) : ?>
                    <button 
                      type="button" 
                      class="btn btn-danger btn-sm" 
                      data-bs-toggle="modal" 
                      data-bs-target="#post-<?php echo $post['id']; ?>">
                      <i class="bi bi-trash"></i>
                    </button>
                    <div class="modal fade" id="user-<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete User</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-start">
                            Are you sure you want to delete this post (<?php echo $post['title']; ?>)
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <form
                              method="POST"
                              action="<?php echo $_SERVER["REQUEST_URI"]; ?>"
                              >
                              <input 
                                type="hidden" 
                                name="post_id" 
                                value="<?php echo $post['id']; ?>" 
                                />
                              <input 
                                type="hidden" 
                                name="csrf_token" 
                                value="<?php echo CSRF::getToken( 'delete_post_form' ); ?>"
                                />
                              <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="text-center">
        <a href="/dashboard" class="btn btn-link btn-sm"
          ><i class="bi bi-arrow-left"></i> Back to Dashboard</a
        >
      </div>
    </div>
    <?php

require dirname(__DIR__) . '/parts/footer.php';
