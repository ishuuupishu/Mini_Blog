<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Blogify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="#">Blogify</a>
      <div class="ms-auto" id="auth-area"></div>
     
    </div>
  </nav>

  <main class="container my-4">
    <div class="row g-4">
     
      <div class="col-lg-5">
        <div id="auth-area" class="mb-3"></div>
        <div class="card shadow-sm" id="create-area" style="display:none;">
          <div class="card-header bg-white">
            <h5 class="mb-0">Create a Post</h5>
          </div>
          <div class="card-body">
            <form id="createForm" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Give your post a title" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="body" id="body" class="form-control" rows="5" placeholder="Write something..." required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Attachment (image/video/PDF • max 15MB)</label>
                <input type="file" name="file" id="file" class="form-control" accept="image/*,video/mp4,video/webm,application/pdf" />
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Publish</button>
              </div>
            </form>
          </div>
        </div>

 <div class="d-flex flex-column align-items-end mt-4"></div>
 <div class="card shadow-sm mt-4" style="background: linear-gradient(135deg, #f8f9fa, #e0f7fa); border-left: 6px solid #00acc1;">
    <div class="card-body">
        <h5 class="card-title text-primary mb-3">🌟 Welcome to Our Blog!</h5>
        <p class="card-text" style="color: #333; font-size: 1rem;">
            Hello, dear visitor! We're thrilled to have you here. This is your space to 
            <span style="color: #d32f2f; font-weight: bold;">share your thoughts</span> 
            and ideas with the world. Every story, tip, or experience you post adds value to our community. 
            So don't be shy – let your voice be heard! 💬
        </p>
        <p class="small text-secondary">Together, let's inspire, learn, and grow. 🌱</p>
    </div>
</div>

          <div class="card-body">
            <h6 class="text-secondary mb-2">Tips</h6>
            <ul class="small mb-0">
              <li>Only logged-in users can create, edit, or delete posts.</li>
              <li>Attachments support images, MP4/WebM videos, and PDF files.</li>
              <li>You can replace the attachment when editing a post.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h4 class="mb-0">Latest Posts</h4>
          <button class="btn btn-outline-secondary btn-sm" id="refreshBtn">Refresh</button>
        </div>
        <div id="posts" class="vstack gap-3"></div>
      </div>
    </div>
  </main>

  <footer class="py-4 border-top bg-white">
    <div class="container text-center small text-muted">
     Blogify · Share your thoughts with the world!
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
