<?php

// $test = '<bookstore>
// <owner>Test</owner>
// <attr name="attribute test"/>
// <book><title>Harry Potter and the Philosiphers Stone</title><author>J K Rowling</author><price currency="£">5.99</price></book>
// <book><title>Lord of the Rings: The Two Towers</title><author>Tolkein</author><price currency="$">10.99</price></book>
// <book><title>Call of Duty</title><author>Price</author><price currency="£">1.97</price></book>
// </bookstore>';

// $xmlobj = simplexml_load_string($test);

$xmlobj = simplexml_load_file('large_xml.xml');

$root = $xmlobj->getName();

$xpaths = [$root => '//' . $xmlobj->getName()];

process('//' . $xmlobj->getName(), $xmlobj, $xpaths);


foreach(array_unique($xpaths) as $item => $xpathString)
{
  foreach(getValues($xmlobj, $item, $xpathString) as $value) echo "'" . $xpathString . "': " . $value .PHP_EOL;
}

function getValues($xmlobj, $item, $xpathString)
{
  $query = $xmlobj->xpath($xpathString);
  foreach($query as $results)
  {
    //echo count($query) . PHP_EOL;
    // var_dump($query);
    switch(substr($item,0,1)){
      case '@':
        $dataItems[] =  (string) $results->attributes()[substr($item,1)];
        break;
      default:
        $dataItems[] = (string) $results;
        break;
    }
  }

  return $dataItems;
}

function process($parent, $xmlobj, &$xpaths)
{
  if(count($xmlobj->attributes()) > 0)
  {
    foreach($xmlobj->attributes() as $attr_key => $attr_val)
    {
      $xpaths['@'.$attr_key] = $parent . '[@' . $attr_key . ']';
    }
  }

  if(count($xmlobj) == 0) {
    return;
  }

  foreach($xmlobj as $child)
  {
    $childtag = $child->getName();
    $xpaths[$childtag] = $parent . '/' . $childtag;
    $parentstring = $parent . '/' .$child->getName();
    process($parentstring, $child, $xpaths);
  }
}