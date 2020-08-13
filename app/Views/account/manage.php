<!-- LAYOUT TEMPLATE -->
<?= $this->extend('template/layout'); ?>

<!-- CONTENT BODY -->
<?= $this->section('content'); ?>
<div class="container mt-5">
    <p>
        <h3>Account Manage</h3>
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
    
    <div class="row">
        <div class="col-md-4">
            <form action="<?= base_url('account/upload'); ?>" method="POST" enctype="multipart/form-data">
                <!--
                <div class="custom-file">
                    <input type="file" class="custom-file-input <?= ($validation->hasError('avatar')) ? 'is-invalid' : ''; ?>" id="avatar" name="avatar">
                    <div class="invalid-feedback"><?= $validation->getError('avatar'); ?></div>
                    <label class="custom-file-label" for="avatar">Choose image</label>
                </div>
                -->
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" class="form-control-file" id="avatar" name="avatar">
                </div>
                <div><input type="hidden" id="hidden" name="hidden" value="hidden"></div>
                <div><?= $validation->getError('avatar'); ?></div>
                <p></p>
                <button type="submit" class="btn btn-success">Upload</button>
            </form>
        </div>
        <div class="col-md-8">
            <form action="<?= base_url('account/update'); ?>" method="POST">
                <?= csrf_field(); ?>
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $user['Email']; ?>" readonly>
                    <div class="invalid-feedback"><?= $validation->getError('email'); ?></div>
                </div>
                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input type="text" class="form-control <?= ($validation->hasError('fullname')) ? 'is-invalid' : ''; ?>" id="fullname" name="fullname" value="<?= $user['FullName'] ?>">
                    <div class="invalid-feedback"><?= $validation->getError('fullname'); ?></div>
                </div>
                <div class="form-group">
                    <label for="fullname">Phone Number:</label>
                    <input type="number" class="form-control <?= ($validation->hasError('phone')) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?= $user['Phone'] ?>">
                    <div class="invalid-feedback"><?= $validation->getError('phone'); ?></div>
                </div>
                <p></p>
                <button type="submit" class="btn btn-success">Update</button>
            </form>
            <br/>
            <p>
                <a href="<?= base_url('account/passwordchange'); ?>" class="btn btn-primary">Change Password</a>
            </p>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<!-- OPTIONAL JAVASCRIPT -->
<?= $this->section('script'); ?>
<script type="text/javascript">

</script>
<?= $this->endSection(); ?>