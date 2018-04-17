<?php
# Получить Id пользовтеля
$userId = User::getId();

# Получить данные пользователя
$user = User::getUserById($userId);
?>



<?php include "./inc/header.php" ?>

<div id="page-account">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Ваш профиль
                    </div>
                    <div class="card-body">

                        <p>Имя: <?php echo $user['real_name'] ?></p>
                        <p>Email: <?php echo $user['email'] ?></p>

                        <a href="./account/logout"<button class="btn btn-danger">Выход</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "./inc/footer.php" ?>

