# makePDF
Позволяет генерировать пдф из переданного html, массива $data либо строки из базы данных. При этом для шаблона используется заданный чанк.
Работает на базе mpdf - https://github.com/mpdf/mpdf (уже установлена в пакете, так что для тех, кто не дружит с composer проблем не будет).

# Немного примеров вызова
```[[makePDF? &html=`<html><body>hello world!</body></html>`]]```<br>
```[[makePDF? &page=`25`]]```<br>
```[[makePDF? &page=`test/test.html?print` &flag=`F` &custom_path=`assets/files/myfolder/`]]```<br>
```[[makePDF? &id=`5` &tpl=`chunkName`]]```<br>
```[[makePDF? &id=`10` &table=`mytable` &idField=`pid` &tpl=`chunkName`]]```<br><br>
```
$attachFiles = $modx->runSnippet("makePDF", array('action' => 'FormLister', 'data' => $pfd_data, 'tpl' => 'zajavkaReportTpl', 'folder_name' => 'zajavka'));
if (is_array($attachFiles)) {
    $FormLister->config->setConfig(array('attachFiles' => $attachFiles));
}
```

### author webber (web-ber12@yandex.ru)

### DONATE
---------
если считаете данный продукт полезным и хотите отблагодарить автора материально,
либо просто пожертвовать немного средств на развитие проекта - 
можете сделать это на любой удобный Вам электронный кошелек<br><br>
<strong>Яндекс.Деньги</strong> 410011544038803<br>
<strong>Webmoney WMR:</strong> R133161482227<br>
<strong>Webmoney WMZ:</strong> Z202420836069<br><br>
с необязательной пометкой от кого и за что именно

