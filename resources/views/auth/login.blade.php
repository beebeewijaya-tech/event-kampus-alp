<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>Login</h2>

<?php if(session('error')): ?>
<p style="color:red"><?= session('error') ?></p>
<?php endif; ?>

<form method="POST" action="/login">
    <?php echo csrf_field(); ?>
    <p>Email: <input type="email" name="email" value="<?= old('email') ?>"></p>
    <p>Password: <input type="password" name="password"></p>
    <p><button type="submit">Login</button></p>
</form>

<p>Belum punya akun? <a href="/register">Daftar</a></p>
</body>
</html>
