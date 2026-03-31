<?php
// member/dashboard.php
// ----------------------------------------------------------
//  Example post-login page. The two lines below are all you
//  need to protect any page. Copy them to every private page.
// ----------------------------------------------------------

require_once __DIR__ . '/../config/auth_check.php';
require_login('/~ulkere/M-TSA/index.php');   // redirects if not logged in

// Uncomment to restrict this page to executives and above:
// require_role('executive');

// Uncomment to restrict to admins only:
// require_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard — MTS</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'DM Sans', sans-serif; padding: 2rem; background: #FAFAF7; }
    .welcome { font-size: 1.5rem; font-weight: 500; color: #5A1230; }
    .meta    { color: #6B6B70; margin-top: .5rem; }
    .badge   { display: inline-block; padding: .25rem .75rem; border-radius: 20px;
               background: #F7EEF2; color: #7B1C3E; font-size: .8rem; font-weight: 500; margin-left: .5rem; }
    .logout  { display: inline-block; margin-top: 2rem; color: #7B1C3E;
               text-decoration: none; font-size: .875rem; }
  </style>
</head>
<body>
  <p class="welcome">
    Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!
    <span class="badge"><?= htmlspecialchars($_SESSION['user_role']) ?></span>
  </p>
  <p class="meta">Logged in as <?= htmlspecialchars($_SESSION['user_email']) ?></p>
  <a class="logout" href="/~ulkere/M-TSA/api/logout.php">Log out</a>
</body>
</html>
