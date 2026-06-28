<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<h2>Daftar Akun</h2>

<form method="POST" action="/register">
    <?php echo csrf_field(); ?>
    <p>Nama: <input type="text" name="name" value="<?= old('name') ?>"></p>
    <p>Email: <input type="email" name="email" value="<?= old('email') ?>"></p>
    <p>No. HP: <input type="text" name="phone" value="<?= old('phone') ?>"></p>
    <p>Password: <input type="password" name="password"></p>
    <p>Konfirmasi Password: <input type="password" name="password_confirmation"></p>
    <p><button type="submit">Daftar</button></p>
</form>

<?php $errors = $errors ?? null; ?>
<?php if($errors && $errors->any()): ?>
<ul style="color:red">
    <?php foreach($errors->all() as $e): ?>
    <li><?= $e ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<p>Sudah punya akun? <a href="/login">Login</a></p>
</body>
</html>
