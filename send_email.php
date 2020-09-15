<?php

if (!$_POST) die();
if ($_POST['glut'] != "") die();

$method = $_SERVER['REQUEST_METHOD'];

//Script Foreach
$c = true;
if ( $method === 'POST' ) {

	$admin_email  = 'hinrko@gmail.com';			// Email Admin
	$project_name = 'PROBIOCOMPLEX';				// Название сайта/проекта
	$url_name = trim($_POST["project_name"]);				// Url сайта/проекта
	$form_subject = trim($_POST["form_subject"]);		// Тема / Заголовок формы

	foreach ( $_POST as $key => $value ) {

		switch ($key) {
		case "name":
				$keys = "Имя";
				break;
		case "custom_tel":
				$keys = "Телефон";
				break;
		case "email":
				$keys = "E-mail";
				break;
		case "custom_comment":
				$keys = "Вопрос";
				break;
		case "kolichestvo":
				$keys = "Количество	";
				break;
		case "id_form":
				$keys = "ID Формы";
				break;
		default:
				$keys = $key;
		}

		if ( $value != "" && $keys != "project_name" && $keys != "admin_email" && $keys != "form_subject" && $keys != "url_name" && $keys != "start_day" && $keys != "campaign_token" ) {
			$message .= "
			" . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$keys</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
		</tr>
		";
		}
	}
}

$message = "<h3>$form_subject</h3><table style='width: 100%;'>$message</table>";

function adopt($text) {
	return '=?UTF-8?B?'.base64_encode($text).'?=';
}

$headers = "MIME-Version: 1.0" . PHP_EOL .
"Content-Type: text/html; charset=utf-8" . PHP_EOL .
'From: '.adopt($project_name).' <'.$admin_email.'>' . PHP_EOL .
'Reply-To: '.$admin_email.'' . PHP_EOL;

mail($admin_email, adopt($form_subject), $message, $headers );



/* =============================================================================
   Send to Google Doc
   ========================================================================== */

$field1 = isset($_POST['name']) ? $_POST['name'] : false;
$field2 = isset($_POST['custom_tel']) ? $_POST['custom_tel'] : "";
$field3 = isset($_POST['kolichestvo']) ? $_POST['kolichestvo'] : "";
// $field3 = isset($_POST['email']) ? $_POST['email'] : "";

$field4 = isset($_POST['form_subject']) ? $_POST['form_subject'] : false;
$field5 = isset($_POST['custom_comment']) ? $_POST['custom_comment'] : "";
$field6 = isset($_POST['id_form']) ? $_POST['id_form'] : false;

// подготовим данные для отправки в гугл форму
$url = 'https://docs.google.com/forms/d/e/1FAIpQLSfzdzSfO0SMKcLlc_g_Ztzlcfr_l009eYUCrSgMtik45fQHaA/formResponse'; // атрибут action у гугл формы
$data = array();
$data['entry.650452848'] = $field1;
$data['entry.63920205'] = $field2;
$data['entry.785673778'] = $field3;
// $data['entry.1434515776'] = $field4;
// $data['entry.270608553'] = $field5;
// $data['entry.1982367137'] = $field6;

$data = http_build_query($data); // сериализуем массив данных в строку для отправки

$options = array( // задаем параметры запроса
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => $data,
    ),
);
$context  = stream_context_create($options); // создаем контекст отправки
$result = file_get_contents($url, false, $context); // отправляем

if (!$result) { // если что-то не так
    $response['ok'] = 0;
    $response['message'] = '<p class="error">Что-то пошло не так, попробуйте отправить позже.</p>'; // пишем ответ
    die(json_encode($response));
}

$response['ok'] = 1; // все ок
$response['message'] = '<p class="">Все ок, отправилось.</p>'; // пишем ответ
die(json_encode($response));

?>
