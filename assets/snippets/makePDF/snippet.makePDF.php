<?php
//примеры использования
//для вывода pdf на страницу (страница должна иметь тип application/pdf и не выводить ничего кроме данного сниппета)
// [[makePDF? &text=`<html><body>hello world!</body></html>` &flag=`S`]]
//использование в сниппете prepareProcess FormLister для формирования аттача к письму
/****
$attachFiles = $modx->runSnippet("makePDF", array('action' => 'FormLister', 'data' => $pfd_data, 'tpl' => 'zajavkaReportTpl', 'folder_name' => 'zajavka'));
if (is_array($attachFiles)) {
    $FormLister->config->setConfig(array('attachFiles' => $attachFiles));
}
***/
//
//параметры вызова
//$html - html-код для формирования pdf (имеет приоритет), если не задан $html используется массив $data, если и он не задан, используется связка параметров $table-$idField-$id
//$data - массив данных для парсинга
//$table - таблица (без префикса) для поиска строки для парсинга (default - site_content)
//$idField - имя уникального поля в таблице (default - id)
//$id - значение уникального поля в таблице для поиска строки (default - false)
//$tpl - имя чанка для парсинга
/*** что будем делать с файлом **/
//$action - что делать с файлом. FormLister - возврат массива для attachFile, по умолчанию - simple - возвращает путь к файлу либо сам файл (используется совместно с $flag)
//$flag флаг для mPDF, F - генерация в файл, I - отправка в поток, D- отправить и начать сохранять, S- отправить как строку
	
/*** параметры для случая сохранения файла в папку ***/
//$folder_name - имя папки для сохранения файла. Общий путь будет 'assets/files/' . $folder_name . '/' . date("Y") . '/' . date("m") . '/'
//$filename - имя сохраняемого файла, вместе с расширением


$action = isset($action) ? $action : 'simple';
$folder_name = isset($folder_name) ? $folder_name : 'pdf';
$filename = isset($filename) ? $filename : '[%uid%]' . date("Y") . '-' . date("m") . '-' . date("d") . '_' . date("H") . '-' . date("i") . '-' . date("s") . '.pdf';
//флаг для TCPDF, F - генерация в файл, I - отправка в поток, D- отправить и начать сохранять, S- отправить как строку
$flag = isset($flag) ? trim($flag) : 'S';
//$fontfamily = isset($fontfamily) ? $fontfamily : 'dejavusans';
$tpl = isset($tpl) ? $tpl : false;
$id = isset($id) ? trim($id) : false;
$table = isset($table) ? trim($table) : 'site_content';
$idField = isset($idField) ? trim($idField) : 'id';
$html = isset($html) ? $html : false;

if ($flag == 'F' || $action == 'FormLister') {//если планируем сохранять файл, сразу подготовим все исходные данные
	$path = 'assets/files/' . $folder_name . '/' . date("Y") . '/' . date("m") . '/';
	$folder = MODX_BASE_PATH . $path;
	
}

//go
$out = '';
//сначала парсим текст
//получаем итоговый html для pdf в переменной $doc
$doc = false;
$uid = '';
if ($html) {//сначала парсим из параметра $html
	$doc = $html;
} else {//если html напрямую не передан, пытаемся его найти сначала в массиве $data 
	$plh = array();
	if ($tpl) {
		$doc = $modx->getChunk($tpl);
		if (isset($data) && is_array($data)) {
			$plh = $data;
			$uid = isset($plh[$idField]) ? $plh[$idField] . '_' : '';
		} else if ($table && $idField && $id) {//если массива $data нет, то ищем в таблице
			$q = $modx->db->query("SELECT * FROM " . $modx->getFullTableName($table) . " WHERE `" . $idField . "`='" . $id . "' LIMIT 0,1");
			if ($modx->db->getRecordCount($q) == 1) {
				$plh = $modx->db->getRow($q);
				$uid = isset($plh[$idField]) ? $plh[$idField] . '_' : '';
			}
		} else {}
		if (!empty($plh)) {
			$doc = $modx->parseChunk($tpl, $plh, '[+', '+]');
		}
	}
}
$filename = str_replace('[%uid%]', $uid, $filename);

if ($doc) {//есть текст для отправки в pdf
	if (isset($_SESSION['perevod']) && is_array($_SESSION['perevod'])) {//hello evoBabel
    	$doc = $modx->parseText($doc, $_SESSION['perevod'], '[%', '%]');
	}
	include_once (MODX_BASE_PATH . 'assets/lib/Helpers/FS.php');
	$FS = \Helpers\FS::getInstance();
	$dir = false;
	if ($flag == 'F') {
		$dir = $FS->makeDir($folder);
		if (!$dir) {
			$modx->logEvent(1, 1, 'Snippet makePDF: Не удалось создать директорию ' . $folder, 'Snippet makePDF: makeDir error');
			return;
		}
	}
	
	require_once __DIR__ . '/mpdf/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf();
	$mpdf->WriteHTML($doc);
	
	switch ($action) {
		case 'FormLister':
			$mpdf->Output($folder . $filename, 'F');
			if (is_file($folder . $filename) && is_readable($folder . $filename)) {
				$out[] = array('filepath' => $path . $filename, 'filename' => $filename);
			} else {
				$modx->logEvent(1, 1, 'Snippet makePDF: Не удалось прочитать файл ' . $folder . $filename, 'Snippet makePDF: FormLister error');
			}
			break;
		case 'simple':
			switch ($flag) {
				case 'F':
					//F: сохранить файл на сервере
					$mpdf->Output($folder . $filename, 'F');
					$out = $path . $filename;
					break;
				case 'D':
					//D: Отправит в браузер, и начнет загружать на компьютер
					$out = $mpdf->Output($filename, 'D');
					break;
				case 'I':
					//I: отправить файл в браузер
					$out = $mpdf->Output($filename, 'I');
					break;
				case 'S':
					//S: вернуть документ как string
					$out = $mpdf->Output($filename, 'S');
					break;
				default:
					break;
			}
			break;
		default:
			break;
	}
}
return $out;
