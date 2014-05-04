<?php

//header('Content-type: image/jpg');
//echo file_get_contents('http://ersatzteile.callparts.de/TeileService/ITKImageHandler.ashx?pic=938843'); exit;

$list = array();

$xmlReader = new XMLReader;
$xmlReader->open('bmecat.xml');

$list = getModelList($xmlReader);
sort($list);

echo '<pre>'; print_r($list); echo '</pre>';
echo 'END';


function getAttributeList(&$xmlReader) {
    $attributeList = array();

    $isGoodNode = false;
    while ($xmlReader->read()) {
        if ($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'FNAME') {
            $isGoodNode = true;
        }
        if ($isGoodNode && $xmlReader->nodeType == XMLReader::TEXT) {
            if (!in_array($xmlReader->value, $attributeList)) {
                $attributeList[] = $xmlReader->value;
            }
        }
        if ($xmlReader->nodeType == XMLReader::END_ELEMENT) {
            $isGoodNode = false;
        }
    }

    return $attributeList;
}

function getModelList(&$xmlReader) {
    $modelList = array();

    $isGoodFNameNode = false;
    $isGoodFValueNode = false;
    while ($xmlReader->read()) {
        if ($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'FNAME') {
            $isGoodFNameNode = true;
        }
        if ($isGoodFNameNode && $xmlReader->nodeType == XMLReader::TEXT && $xmlReader->value == 'MODEL') {
            $isGoodFValueNode = true;
        } elseif ($isGoodFValueNode && $xmlReader->nodeType == XMLReader::TEXT) {
            if (!in_array($xmlReader->value, $modelList)) {
                $modelList[] = $xmlReader->value;
            }
            $isGoodFNameNode = false;
            $isGoodFValueNode = false;
        }
    }

    return $modelList;
}


function getDescriptionAttributeList(&$xmlReader) {
    $attributeList = array();

    $isGoodNode = false;
    while ($xmlReader->read()) {
        if ($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'DESCRIPTION_LONG') {
            $isGoodNode = true;
        }
        if ($isGoodNode && $xmlReader->nodeType == XMLReader::CDATA) {
            $list = getDescriptionAttributeItemList($xmlReader->value);
            $attributeList = array_merge($attributeList, $list);
        }
        if ($xmlReader->nodeType == XMLReader::END_ELEMENT) {
            $isGoodNode = false;
        }
    }

    return $attributeList;
}


function getDescriptionAttributeItemList($text) {
    $attributeItemList = array();

    $list = explode("\r\n", $text);
    foreach ($list as $item) {
        $item = strtolower(trim($item));
        if ($item) {
            list($attr) = explode(':', $item);
            $attr = strtolower(trim($attr));
            if ($attr != $item) {
                $attributeItemList[$attr] = $attr;
            }
        }

    }
    return $attributeItemList;
}
