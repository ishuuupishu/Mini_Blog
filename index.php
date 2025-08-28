<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mini Blog (PHP + AJAX)</title>

<link rel="stylesheet" href="assets/css/bootstrap.min.css">






  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: url('https://picsum.photos/1600/900?blur=3') no-repeat center center/cover;
      color: #333;
      min-height: 100vh;
    }

    .overlay {
      background: rgba(0, 0, 0, 0.6);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header h1 {
      text-align: center;
      margin: 30px 0;
      font-weight: 700;
      color: #fff;
      font-size: 2.5rem;
    }

    .auth-card {
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
      padding: 2rem;
    }

    .auth-card h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 20px;
      text-align: center;
    }

    .btn-custom {
      border-radius: 50px;
      font-weight: 600;
      padding: 0.7rem;
    }

    main {
      padding: 60px 0;
      flex: 1;
    }

    .card-post {
      border-radius: 1rem;
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
      padding: 1.5rem;
      background: #fff;
    }

    footer {
      text-align: center;
      padding: 20px;
      color: #fff;
      margin-top: auto;
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 2rem;
      }
      .auth-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="overlay">

    <!-- Header -->
    <header>
      <h1>Mini Blog</h1>
      <div id="auth-area">
        <div class="row justify-content-center">
          <div class="col-md-5 col-sm-10">
            <div class="auth-card">
              <h2>Welcome Back</h2>
              <!-- Login Form -->
              <form class="d-flex flex-column gap-3 mb-3">
                <input type="text" class="form-control" placeholder="Username">
                <input type="password" class="form-control" placeholder="Password">
                <button class="btn btn-primary w-100 btn-custom">Login</button>
              </form>
              <hr>
              <!-- Register Form -->
              <form class="d-flex flex-column gap-3">
                <input type="text" class="form-control" placeholder="New Username">
                <input type="password" class="form-control" placeholder="New Password">
                <button class="btn btn-success w-100 btn-custom">Register</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="container">
      <!-- Create Post -->
      <section class="create card-post mb-4" id="create-area" style="display:none;">
        <h2>Create Post</h2>
        <form id="createForm" class="d-flex flex-column gap-3">
          <input type="text" name="title" id="title" class="form-control" placeholder="Title" required />
          <textarea name="body" id="body" class="form-control" rows="5" placeholder="Write something..." required></textarea>
          <button type="submit" class="btn btn-primary btn-custom">Publish</button>
        </form>
      </section>

      <!-- Feed -->
      <section class="feed">
        <h2 class="text-white mb-3">Latest Posts</h2>
        <div id="posts" class="d-flex flex-column gap-3">
          <!-- Example Post -->
          <div class="card-post">
            <h5>Sample Blog Post</h5>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel eros nec ipsum placerat aliquet.</p>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer>
      <small>Built with PHP + MySQL + JS (fetch)</small>
    </footer>
  </div>



<script src="assets/js/bootstrap.bundle.min.js"></script>

  
</body>
</html>
