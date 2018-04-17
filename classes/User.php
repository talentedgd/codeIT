<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.04.2018
 * Time: 22:23
 */

class User
{
    /**
     * Проверка email на корректность
     *
     * @param $email
     * @return bool
     */
    public static function checkEmailValid($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * Проверяет лоин на валидность
     *
     * @param $login
     * @return bool
     */
    public static function checkLoginValid($login)
    {
        $maxLenght = 15;
        $minLenght = 5;
        if (strlen($login) >= $minLenght && strlen($login) <= $maxLenght) {
            if (preg_match('/[a-z]+/', $login)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверяет имя на корректность
     *
     * @param $real_name
     * @return bool
     */
    public static function checkRealNameValid($real_name)
    {
        $maxLenght = 50;
        $minLenght = 2;
        if (strlen($real_name) >= $minLenght && strlen($real_name) <= $maxLenght) {
            if (preg_match('~^[a-zа-яёії]+([ ]([a-zа-яёії])+)?([ ]([a-zа-яёії])+)?$~ui',
                $real_name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверить имя на корректность
     *
     * @param $password
     * @return bool
     */
    public static function checkPasswordValid($password)
    {
        $maxLenght = 15;
        $minLenght = 5;
        if (strlen($password) >= $minLenght && strlen($password) <= $maxLenght) {
            return true;
        }

        return false;
    }

    /**
     * Проверить дату рождения на корректность
     *
     * @param $dateBirth
     * @return bool
     */
    public static function checkDateValid($dateBirth)
    {
        if (preg_match('~^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$~', $dateBirth)) {
            $dateBirthArr = explode('-', $dateBirth);
            $dateToday = date('d-m-Y', time());

            if (checkdate($dateBirthArr[1], $dateBirthArr[2],
                $dateBirthArr[0])
            ) {
                if (strtotime($dateToday) > strtotime($dateBirth)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Проверить принял ли пользователь правила при регистрации
     *
     * @param $agreement
     * @return bool
     */
    public static function checkAgreement($agreement)
    {
        if ($agreement == "on") return true;
        return false;
    }

    /**
     * Проверить данных при регистрации на валдиность
     *
     * @param $email
     * @param $login
     * @param $real_name
     * @param $password
     * @param $dateBirth
     * @param $country_id
     * @param $agreement
     * @return bool|mixed
     */
    public static function checkDataRegistr($email, $login, $real_name, $password, $dateBirth, $country_id, $agreement)
    {
        $errors = [];

        if (!self::checkUniqueAttr('email', $email)) $errors[] = 'Email не уникален!';
        if (!self::checkUniqueAttr('login', $login)) $errors[] = 'Логин не уникален!';
        if (!self::checkEmailValid($email)) $errors[] = 'Неправильный email!';
        if (!self::checkLoginValid($login)) $errors[] = 'Неправильный логин!';
        if (!self::checkRealNameValid($real_name)) $errors[] = 'Некорректное имя!';
        if (!self::checkPasswordValid($password)) $errors[] = 'Пароль должен содержать от 5 до 15 символов!';
        if (!self::checkDateValid($dateBirth)) $errors[] = 'Некорректная дата рождения!';
        if (!Country::existCountryById($country_id)) $errors[] = 'несуществующая страна!';
        if (!self::checkAgreement($agreement)) $errors[] = 'Примите условия сайта!';

        if (empty($errors)) return true;
        return $errors[0];
    }

    /**
     * Регистрирует пользователя
     *
     * @param $email
     * @param $login
     * @param $real_name
     * @param $password
     * @param $date
     * @param $countryId
     * @return bool|int
     */
    public static function registration($email, $login, $real_name, $password, $date, $countryId)
    {
        // Разбить дату на массив
        $dateBirth = explode('-', $date);

        // Зашифровать пароль
        $password = password_hash($password, PASSWORD_DEFAULT);
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO user 
                (email, login, real_name, password, date_y, date_m, date_d, 
                country_id, reg_time) 
                VALUES 
                (:email, :login, :real_name, :password, :date_y, :date_m,
                :date_d, :country_id, :reg_time)');
        $result = $query->execute([
            'email' => $email,
            'login' => $login,
            'real_name' => $real_name,
            'password' => $password,
            'date_y' => $dateBirth[0],
            'date_m' => $dateBirth[1],
            'date_d' => $dateBirth[2],
            'country_id' => $countryId,
            'reg_time' => time()
        ]);

        # Получаем Id записи
        if ($result) {
            return $db->lastInsertId();
        }

        return false;
    }


    /**
     * Проверяет уникальность заданного атрибута из таблицы user
     *
     * @param $attr
     * @param $value
     *
     * @return bool
     */
    private static function checkUniqueAttr($attr, $value)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT COUNT(*) AS count FROM user WHERE '
            . $attr . ' = ?');
        $query->execute([$value]);
        $count = $query->fetch();
        $count = $count['count'];
        if ($count > 0) {
            return false;
        }
        return true;
    }


    /**
     * Проверяет существует ли пользователь с заданными данными. В случае удачи
     * вернет Id пользователя.
     *
     * $identifier может содержать email или login
     *
     * @param $identifier
     * @param $password
     *
     * @return int|false
     */
    public static function checkDataLogin($identifier, $password)
    {
        # Получаем тип идентификатора
        $identifierType = 'login';
        if (self::checkEmailValid($identifier)) {
            $identifierType = 'email';
        }
        $db = Db::getConnection();
        $query
            = $db->prepare('SELECT COUNT(*) as count, id, password FROM user WHERE '
            . $identifierType . ' = ?');
        $query->execute([$identifier]);
        $result = $query->fetch();
        $count = $result['count'];
        if ($count > 0) {
            if (password_verify($password, $result['password'])) {
                return $result['id'];
            }
        }
        return false;
    }

    /**
     * Авторизирует пользователя с заданым Id
     *
     * @param $id
     */
    public static function auth($id)
    {
        $_SESSION['user']['id'] = $id;
    }


    /**
     * Возвращает массив с данными пользователя
     *
     * @param $id
     * @return array
     */
    public static function getUserById($id)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT email, real_name FROM user WHERE id = ?');
        $query->execute([$id]);

        $row = $query->fetch();

        return $row;
    }


    /**
     * Возвращает Id пользователя или перенаправляет на страницу авторизации (если пользователь не залогинен)
     *
     * @return int
     */
    public static function getId()
    {
        if (isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }

        header('Location: /' . Application::getDirUrl() . '/login');
        die();
    }

    /**
     * Проверяет является ли пользователь гостем
     *
     * @return bool
     */
    public static function isGuest()
    {
        if (empty($_SESSION['user']['id'])) {
            return true;
        }

        return false;
    }

    /**
     * Уничтожение сессии пользоателя
     */
    public static function userLogOut()
    {
        if (isset($_SESSION['user']['id'])) {
            unset($_SESSION['user']['id']);
        }
    }
}