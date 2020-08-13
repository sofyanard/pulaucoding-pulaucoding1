<!-- LAYOUT TEMPLATE -->
<?= $this->extend('template/layout'); ?>

<!-- CONTENT BODY -->
<?= $this->section('content'); ?>
<div class="container mt-5">
    <p>
        <h3>Account Login</h3>
    </p>
    <hr>
    <p>
        <!-- Flash Message Alert -->
        <?php if (session()->getFlashdata('message') != null) { ?>
        <div class="<?= session()->getFlashdata('msgclass'); ?>" role="alert">
            <?= session()->getFlashdata('message'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php } ?>
    </p>
    <form action="<?= base_url('account/login'); ?>" method="POST">
        <?= csrf_field(); ?>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : ''; ?>" placeholder="Enter email" id="email" name="email" value="<?= old('email'); ?>">
            <div class="invalid-feedback"><?= $validation->getError('email'); ?></div>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>" placeholder="Enter password" id="password" name="password">
            <div class="invalid-feedback"><?= $validation->getError('password'); ?></div>
        </div>
        <p></p>
        <button type="submit" class="btn btn-success">Login</button>
    </form>
    <p>
        <div>Don't have an account? <a href="<?= base_url('account/register'); ?>">Register</a></div>
        <div>Account not yet activated? Check your email! Or <a href="<?= base_url('account/requestactivate'); ?>">request again</a></div>
        <div>Forgot your password? <a href="<?= base_url('account/requestforgot'); ?>">Reset</a></div>
    </p>
    <br/>
    <p>
        <a href="<?= base_url('account/logingoogle'); ?>" class="btn btn-danger">Login with <b>Google</b></a>
    </p>
    <p>
        <a href="<?= base_url('account/loginfacebook'); ?>" class="btn btn-primary">Login with <b>facebook</b></a>
    </p>
</div>
<?= $this->endSection(); ?>

<!-- OPTIONAL JAVASCRIPT -->
<?= $this->section('script'); ?>
<script type="text/javascript">

</script>
<?= $this->endSection(); ?>