<?php
# Проерить является ли посетитель гостем
if (!User::isGuest()) {
    header('Location: /' . Application::getDirUrl() . '/account');
}

if (isset($_POST['send'])) {
    $email = $_POST['email'];
    $login = $_POST['login'];
    $real_name = $_POST['real_name'];
    $password = $_POST['password'];
    $date = $_POST['date'];
    $countryId = $_POST['countries'];
    $agreement = $_POST['agreement'];

    # Проверить валидность данных
    $result = User::checkDataRegistr($email, $login, $real_name, $password, $date, $countryId, $agreement);

    # Регистрируем пользователя
    if ($result === true) {
        $userId = User::registration($email, $login, $real_name, $password, $date, $countryId, $agreement);

        # Авторизируем пользователя
        User::auth($userId);

        # Перенаправить на страницу профиля
        header('Location: ./account');
    }
}

// Получить список всех стран
$countries_list = Country::getCountries();

include "./inc/header.php" ?>
<div id="page-login">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Регистрация
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($result) && $result !== true) { ?>
                            <div class="alert alert-danger"
                                 role="alert"><?php echo $result ?></div>
                        <?php } ?>

                        <?php
                        if (isset($result) && $result === true) { ?>
                            <div class="alert alert-success"
                                 role="alert">success
                            </div>
                        <?php } ?>

                        <form action="#" method="post">
                            <input type="email" name="email" placeholder="Email" class="form-control"
                                   value="<?php if (isset($email)) echo $email ?>">

                            <input type="text" name="login" placeholder="Логин" class="form-control"
                                   value="<?php if (isset($login)) echo $login ?>">

                            <input type="text" name="real_name" placeholder="Настоящее имя" class="form-control"
                                   value="<?php if (isset($real_name)) echo $real_name ?>">

                            <input type="password" name="password" placeholder="Пароль" class="form-control">

                            <input type="date" name="date" class="form-control"
                                   value="<?php if (isset($date)) echo $date ?>">

                            <select class="form-control" name="countries">
                                <?php
                                foreach ($countries_list as $country) {
                                    ?>
                                    <option value="<?php echo $country['id'] ?>"
                                    <?php if(isset($countryId)) if($countryId === $country['id'] ) echo 'selected'; ?>
                                    >
                                        <?php echo $country['name'] ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <div class="form-check">
                                <input type="checkbox" name="agreement" class="form-check-input"
                                <?php if(isset($agreement)) if($agreement === 'on') echo 'checked=""'; ?>
                                >
                                <label class="form-check-label" for="exampleCheck1">Я принимаю <a>условия
                                        сайта</a></label>
                            </div><br>
                            <input type="submit" value="Войти" name="send" class="btn btn-success">

                            <p style="margin: 0">Есть аккаунт? <a href="./login">Войдите</a>.</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "./inc/footer.php" ?>

