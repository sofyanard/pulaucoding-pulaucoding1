<!-- LAYOUT TEMPLATE -->
<?= $this->extend('template/layout'); ?>

<!-- CONTENT BODY -->
<?= $this->section('content'); ?>
<div class="container mt-5">
    <p>
        <h3>Account Reset Password</h3>
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
    <form action="<?= base_url('account/resetpassword'); ?>" method="POST">
        <?= csrf_field(); ?>
        <input type="hidden" id="token" name="token" value="<?= $_GET['token']; ?>">
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>" placeholder="Enter password" id="password" name="password">
            <div class="invalid-feedback"><?= $validation->getError('password'); ?></div>
        </div>
        <div class="form-group">
            <label for="confirmpassword">Confirm Password:</label>
            <input type="password" class="form-control <?= ($validation->hasError('confirmpassword')) ? 'is-invalid' : ''; ?>" placeholder="Confirm password" id="confirmpassword" name="confirmpassword">
            <div class="invalid-feedback"><?= $validation->getError('confirmpassword'); ?></div>
        </div>
        <p></p>
        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>
<?= $this->endSection(); ?>

<!-- OPTIONAL JAVASCRIPT -->
<?= $this->section('script'); ?>
<script type="text/javascript">

</script>
<?= $this->endSection(); ?>