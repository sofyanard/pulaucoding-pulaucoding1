<!-- LAYOUT TEMPLATE -->
<?= $this->extend('template/layout'); ?>

<!-- CONTENT BODY -->
<?= $this->section('content'); ?>
<div class="container mt-5">
    <p>
        <h3>Account Registration</h3>
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
    <form action="<?= base_url('account/create'); ?>" method="POST">
        <?= csrf_field(); ?>
        <div class="form-group">
            <label for="fullname">Full Name:</label>
            <input type="text" class="form-control <?= ($validation->hasError('fullname')) ? 'is-invalid' : ''; ?>" placeholder="Enter full name" id="fullname" name="fullname" value="<?= old('fullname'); ?>">
            <div class="invalid-feedback"><?= $validation->getError('fullname'); ?></div>
        </div>
        <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : ''; ?>" placeholder="Enter email" id="email" name="email" value="<?= old('email'); ?>">
            <div class="invalid-feedback"><?= $validation->getError('email'); ?></div>
        </div>
        <div class="form-group">
            <label for="fullname">Phone Number:</label>
            <input type="number" class="form-control <?= ($validation->hasError('phone')) ? 'is-invalid' : ''; ?>" placeholder="Enter phone number" id="phone" name="phone" value="<?= old('phone'); ?>">
            <div class="invalid-feedback"><?= $validation->getError('phone'); ?></div>
        </div>
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
        <button type="submit" class="btn btn-success">Register</button>
    </form>
    <p>
        <div>Already have an account? <a href="<?= base_url('account'); ?>">Login</a></div>
    </p>
</div>
<?= $this->endSection(); ?>

<!-- OPTIONAL JAVASCRIPT -->
<?= $this->section('script'); ?>
<script type="text/javascript">

</script>
<?= $this->endSection(); ?>