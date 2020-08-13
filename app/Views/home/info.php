<!-- LAYOUT TEMPLATE -->
<?= $this->extend('template/layout'); ?>

<!-- CONTENT BODY -->
<?= $this->section('content'); ?>
<div class="container mt-5">

    <h1>List of Controllers / Methods</h1>
    <hr/>
    
    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) { / home / index }
            </h5>
            <div class="card-body">
                <p class="card-text">Home page</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / home / info 
            </h5>
            <div class="card-body">
                <p class="card-text">List of Controllers and Methods (this page)</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / home / welcome 
            </h5>
            <div class="card-body">
                <p class="card-text">CodeIgniter welcome page</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / account { / index }
            </h5>
            <div class="card-body">
                <p class="card-text">Login form</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / account / register
            </h5>
            <div class="card-body">
                <h5 class="card-title">Registration form</h5>
                <p class="card-text">Setelah registrasi sukses, email berisi link untuk mengaktifkan account akan dikirimkan</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / account / requestactivate
            </h5>
            <div class="card-body">
                <p class="card-text">Form permintaan ulang untuk mengaktifkan account, jika user tidak menerima email sebelumnya</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / account / requestforgot
            </h5>
            <div class="card-body">
                <p class="card-text">Form permintaan untuk reset password, email berisi link untuk membuat password akan dikirimkan</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / account / passwordchange
            </h5>
            <div class="card-body">
                <p class="card-text">Form untuk mengubah password. Untuk user yang sudah login</p>
            </div>
        </div>
    </p>

    <p>
        <div class="card">
            <h5 class="card-header">
                (base-url) / account / manage
            </h5>
            <div class="card-body">
                <p class="card-text">Form untuk mengedit user profile. Untuk user yang sudah login</p>
            </div>
        </div>
    </p>



    <hr/>
    <p>
        ( ... ) --> parameter
        <br/>
        { ... } --> optional
    </p>

</div>
<?= $this->endSection(); ?>

<!-- OPTIONAL JAVASCRIPT -->
<?= $this->section('script'); ?>
<script type="text/javascript">

</script>
<?= $this->endSection(); ?>