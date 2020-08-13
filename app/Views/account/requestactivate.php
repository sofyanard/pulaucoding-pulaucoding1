<!-- LAYOUT TEMPLATE -->
<?= $this->extend('template/layout'); ?>

<!-- CONTENT BODY -->
<?= $this->section('content'); ?>
<div class="container mt-5">
    <p>
        <h3>Account Request Activation</h3>
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
    <form action="<?= base_url('account/processactivate'); ?>" method="POST">
        <?= csrf_field(); ?>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : ''; ?>" placeholder="Enter email" id="email" name="email" value="<?= old('email'); ?>">
            <div class="invalid-feedback"><?= $validation->getError('email'); ?></div>
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