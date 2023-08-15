<?php

// В качестве СУБД используется Mysql
$servername = "localhost"; // Имя сервера MySQL
$username = "mysql"; //  Имя пользователя MySQL
$password = "mysql"; //  Пароль MySQL
$dbname = "alifCobinetTask"; // Имя базы данных

$currentCustomerId = 1; // id Текущего пользователя
$roomId = 2; // id Нашего выбранного кобинета
$reserveFrom = "2023-08-15 15:42:25"; // Желаемая дата начало брони
$reserveTo = "2023-08-15 17:30:11"; // Желаемая дата окончания брони




function reserveProcess()
{
    // Проверка на наличие брони в нашем промежутке времени $reserveFrom $reserveTo
    // Eсли true то текущий кобинет по $roomId забронирован иначе бронируем
    if (freeRooms()->num_rows) {
        $reserved = freeRooms()->fetch_assoc();
        print($reserved["firstname"] . " " . $reserved["lastname"] . " - " . $reserved["reserved_to"]);
    } else {
        reserveRoom();
    }
}






function freeRooms()
{
    // Делаем переменные доступными
    global $servername;
    global $username;
    global $password;
    global $dbname;

    global $roomId;
    global $reserveFrom;
    global $reserveTo;

    // Создаем соединение
    $db = new mysqli($servername, $username, $password, $dbname);

    // Запрос проверяет есть ли бронь в нашем промежутке времени $reserveTo $reserveTo
    $sql = "SELECT 
        reserved_rooms.reserved_to,
        customers.firstname,
        customers.lastname
        FROM reserved_rooms 
        INNER JOIN customers ON reserved_rooms.customer_id = customers.id 
        WHERE room_id = $roomId 
        AND  reserved_from <= '$reserveTo'
        AND  reserved_to >= '$reserveFrom'";
    $result = $db->query($sql);

    // Закрываем соединение
    $db->close();

    return $result;
}







function reserveRoom()
{
    // Делаем переменные доступными
    global $servername;
    global $username;
    global $password;
    global $dbname;


    global $currentCustomerId;
    global $roomId;
    global $reserveFrom;
    global $reserveTo;

    // Создаем соединение
    $db = new mysqli($servername, $username, $password, $dbname);

    // Добавляем бронь
    $sql = "INSERT INTO reserved_rooms (customer_id, room_id, reserved_from, reserved_to) 
    VALUES ('$currentCustomerId','$roomId', '$reserveFrom', '$reserveTo')";
    $db->query($sql);

    // Берем наш кобинет по $roomId и почту с номером телефона по $currentCustomerId
    $room = $db->query("SELECT number from rooms WHERE id=$roomId")->fetch_assoc()["number"];
    $customer = $db->query("SELECT gmail,phone from customers WHERE id=$currentCustomerId")->fetch_assoc();

    // Имитация отправки данных на почту и номер телефона
    sendNotification($customer["gmail"], $customer["phone"], $room, $reserveFrom, $reserveTo);

    // Закрываем соединение
    $db->close();
}





function sendNotification($gmail, $phone, $room, $reserveFrom, $reserveTo)
{
    $email = "example123@gmail.com"; // Электронный адрес получателя

    // Данные для отправки
    $subject = "Room Reservation Details";
    $message = "Room Number: $room\nReserved From: $reserveFrom\nReserved To: $reserveTo";
    $headers = "From: sender123@gmail.com";

    // Имитация отправки на почту
    mail($email, $subject, $message, $headers);

    // Имитация отправки на номер телефона 
    echo ("Phone Number:$phone;  $message");
}




reserveProcess();
