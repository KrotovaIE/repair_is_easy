<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();   
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth   = true;
    // $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

    // Настройки вашей почты
    $mail->Host       = 'smtp.example.com'; // SMTP сервера вашей почты
    $mail->Username   = 'user'; // Логин на почте
    $mail->Password   = 'secret'; // Пароль для внешнего приложения
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->setFrom('from@example.com', 'Mailer'); // Адрес самой почты и имя отправителя

    // Получатель письма
    $mail->addAddress('joe@example.net', 'Joe User');  

    //Тело письма
    $body = '<html><h1>Добрый день. </h1><br><h2>Новая заявка.</h2>';

    if(trim(!empty($_POST['name']))){
        $body.='<p><strong>Имя клиента:</strong> '.$_POST['name'].'</p>';
    }
    if(trim(!empty($_POST['email']))){
        $body.='<p><strong>Почта клиента:</strong> '.$_POST['email'].'</p>';
    }
    if(trim(!empty($_POST['phone']))){
        $body.='<p><strong>Номер телефона:</strong> '.$_POST['phone'].'</p>';
    }
    if(trim(!empty($_POST['message']))){
        $body.='<p><strong>Описание заявки:</strong> '.$_POST['message'].'</p><br><p>С уважением, Ваш новый клиент!</p></html>';
    }

        //Прикрепление файла
    if (!empty($_FILES['image']['name'])) {
        $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['image']['name']));
        $filename = $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
            $body.='<br><p>P.S. фото объекта во вложении.</p>';
            $mail->addAttachment($uploadfile, $filename);
        }
    }

    $mail->IsHTML(true);
    $mail->Subject = 'Заявка на ремонтные работы';
    $mail->Body = $body;

    //Отправляем
    if (!$mail->send()) {
        $message = 'Ошибка';
    } else {
        $message = 'Данные отправлены!';
    }

    $response = ['message' => $message];

    header('Content-type: application/json');
    echo json_encode($response);

    ?>