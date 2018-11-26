//<?php
/**
 * makePDF
 * 
 * Геренация PDF средствами библиотеки mpdf
 *
 * @author	    webber (web-ber12@yandex.ru)
 * @category 	snippet
 * @version 	0.1
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal	@modx_category Content
 * @internal    @installset base, sample
 */
 
//примеры использования
//для вывода pdf на страницу (страница должна иметь тип application/pdf и не выводить ничего кроме данного сниппета)
//
// [[makePDF? &html=`<html><body>hello world!</body></html>` &flag=`S`]]
// [[makePDF? &page=`25`]]
// [[makePDF? &page=`test/test.html?print`]] - лучше, конечно, выводить версию для печати, иначе непредсказуемый вывод вполне предсказуем :)
//
//использование в сниппете prepareProcess FormLister для формирования аттача к письму
//
/*
$attachFiles = $modx->runSnippet("makePDF", array('action' => 'FormLister', 'data' => $pfd_data, 'tpl' => 'zajavkaReportTpl', 'folder_name' => 'zajavka'));
if (is_array($attachFiles)) {
    $FormLister->config->setConfig(array('attachFiles' => $attachFiles));
}
*/
//
//
//параметры вызова
// $page - id html-страницы сайта либо ее адрес (имеет приоритет)
// $html - html-код для формирования pdf (имеет приоритет), если не задан $html используется массив $data, если и он не задан, используется связка параметров $table-$idField-$id
// $data - массив данных для парсинга
// $table - таблица (без префикса) для поиска строки для парсинга (default - site_content)
// $idField - имя уникального поля в таблице (default - id)
// $id - значение уникального поля в таблице для поиска строки (default - false)
// $tpl - имя чанка для парсинга
/*** что будем делать с файлом **/
// $action - что делать с файлом. FormLister - возврат массива для attachFile, по умолчанию - simple - возвращает путь к файлу либо сам файл (используется совместно с $flag)
// $flag флаг для mPDF, F - генерация в файл, I - отправка в поток, D- отправить и начать сохранять, S- отправить как строку

/*** параметры для случая сохранения файла в папку ***/
// $folder_name - имя папки для сохранения файла. Общий путь будет 'assets/files/' . $folder_name . '/' . date("Y") . '/' . date("m") . '/'
// $filename - имя сохраняемого файла, вместе с расширением (если не задано, сформируется автоматически из id (при наличии idField) и даты-времени создания
// $custom_path - путь для сохранения файла (например  &custom_path=`assets/files/myfolder/`). Приоритет перед $folder_name

return require_once MODX_BASE_PATH . 'assets/snippets/makePDF/snippet.makePDF.php';

