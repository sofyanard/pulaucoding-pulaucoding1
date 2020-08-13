<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="<?= base_url(); ?>/css/bootstrap.min.css">

        <title><?=$title ?></title>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url(); ?>">PulauCoding</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="<?= base_url(); ?>">Home</a>
                    <a class="nav-item nav-link" href="<?= base_url('home/welcome'); ?>"  target="_blank">Pulau</a>
                    <a class="nav-item nav-link" href="<?= base_url('home/info'); ?>">Coding</a>
                </div>
                <div class="navbar-nav ml-auto">
                    <?php if (session()->get('loginUser') != NULL) { ?>
                        <a class="nav-item nav-link mx-1" href="<?= base_url('account/manage'); ?>">
                            <?= session()->get('loginUser')['FullName']; ?>
                        </a>
                        <a class="btn btn-outline-danger mx-1" href="<?= base_url('account/logout'); ?>">Logout</a>
                    <?php } else { ?>
                        <a class="btn btn-outline-info mx-1" href="<?= base_url('account/register'); ?>">Register</a>
                        <a class="btn btn-outline-success mx-1" href="<?= base_url('account'); ?>">Login</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </nav>

        <!-- Content -->
        <?= $this->renderSection('content'); ?>

        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="<?= base_url(); ?>/js/jquery-3.5.1.slim.min.js"></script>
        <script src="<?= base_url(); ?>/js/popper.min.js"></script>
        <script src="<?= base_url(); ?>/js/bootstrap.min.js"></script>

        <!-- Optional JavaScript -->
        <?= $this->renderSection('script'); ?>
  </body>
</html>