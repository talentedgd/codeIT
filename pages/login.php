<?php
# Проерить является ли посетитель гостем
if (!User::isGuest()) {
    header('Location: /' . Application::getDirUrl() . '/account');
}

# Авторизация
if (isset($_POST['log_in'])) {

    $identifier = $_POST['identification'];
    $pwd = $_POST['password'];

    # Проверить корректность данных пользователя
    $userId = User::checkDataLogin($identifier, $pwd);

    # Авторизируем пользователя
    if ($userId !== false && $userId > 0) {
        User::auth($userId);

        # Перенаправить на страницу профиля
        header('Location: ./account');
    }

}
?>

<?php include "./inc/header.php" ?>
<div id="page-login">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Вход
                    </div>
                    <div class="card-body">

                        <?php if (isset($userId) && $userId === false) { ?>
                            <div class="alert alert-danger"
                                 role="alert">Не верный логин/email или пароль
                            </div>
                        <?php } ?>

                        <form action="#" method="post">
                            <input type="text" name="identification" placeholder="Логин или email" class="form-control"
                                   value="<?php if (isset($identifier)) echo $identifier; ?>">
                            <input type="password" name="password" placeholder="Пароль" class="form-control">
                            <input type="submit" value="Войти" name="log_in" class="btn btn-success">
                        </form>
                        <br>
                        <a href="./registration">Создайте аккаунт</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "./inc/footer.php" ?>

