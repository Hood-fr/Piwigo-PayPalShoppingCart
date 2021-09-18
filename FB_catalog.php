<?php
//defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');
//
//define('RVS_DIR' , basename(dirname(__FILE__)));
//define('RVS_PATH' , PHPWG_PLUGINS_PATH . RVS_DIR . '/');
//load_language('plugin.lang', RVS_PATH);

function sitemaps_get_config_file_name()
{
  global $conf;
  $dir = PHPWG_ROOT_PATH.$conf['data_location'].'plugins/';
  mkgetdir( $dir );
  return $dir.basename(dirname(__FILE__)).'.dat';
}

function start_xml($filename)
{
  global $file;
  $url=get_root_url().$filename;
  $file = fopen( $filename, 'w' );
//  out_xml('<?xml version="1.0" encoding="UTF-8"?'.'>
//<?xml-stylesheet type="text/xsl" href="'.get_root_url().'plugins/'.basename(dirname(__FILE__)).'/sitemap.xsl"?'.'>
//<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">', $gzip );
  out_xml(  '<?xml version="1.0" encoding="UTF-8"?'.'>
            <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"'.'>
            <channel'.'>
            <title>Catalogue Daedalum pour FB - France</title'.'>
            <link>'.$url.'</link'.'>
            <description>Catalogue Daedalum pour FB - France</description>'            
          );
}

function out_xml($xml)
{
  global $file;
  fwrite($file, $xml);
}

function end_xml()
{
  global $file;
  out_xml('</channel>'.'
          </rss>');           
  fclose( $file );
}

$item_count=0;

function add_item($row, $ref_cat, $conf, $link, $image_link)
{
    
  $xml='<item>';

  if ( isset($row['item_option']) and strlen($row['item_option'])>0 )
  {
    $xml.='<g:id>'.$row['item']."_".$row['item_option'].'</g:id>';
    $xml.='<g:item_group_id>'.$row['item'].'</g:item_group_id>';
    $xml.='<g:material>'.$row['item_option'].'</g:material>';
  }
  else
  {
    $xml.='<g:id>'.$row['item'].'</g:id>';
  }

  if ( isset($row['title']) and strlen($row['title'])>0 )
  {
      $xml.='<g:title>'.$row['title'].'</g:title>';
  }
  
  if ( $row['minSize'] == $row['maxSize'] )
  {
      $xml.='<g:description>'.'Dimension : '.$row['minSize'].' '.$row['units'].'</g:description>';
  }
  else
  {
      $xml.='<g:description>'.'Dimensions : '.$row['minSize'].' to '.$row['maxSize'].' '.$row['units'].'</g:description>';
  }
  
    $xml.='<g:availability>'.'in_stock'.'</g:availability>';
    $xml.='<g:condition>'.'new'.'</g:condition>';
    $xml.='<g:brand>'.$conf['PayPalShoppingCart']['Brand'].'</g:brand>';
    $xml.='<g:price>'.$row['price'].' '.$row['currency'].'</g:price>';
    $xml.='<g:link>'.$link.'</g:link>';

  if ( $ref_cat)
  {
    $xml.='<g:image_link>'.$image_link.'</g:image_link>';      
    $xml.='<g:google_product_category>'.$conf['PayPalShoppingCart']['GoogleId'].'</g:google_product_category>';
    $xml.='<g:fb_product_category>'.$conf['PayPalShoppingCart']['FBId'].'</g:fb_product_category>';
    $xml.='<g:shipping>'.$row['shipping'].'</g:shipping>';    
   }
  else
  {
    if ( isset($row['countryISOcode']) and strlen($row['countryISOcode'])==2 )
    {
       $xml.='<g:override>'.$row['countryISOcode'].'</g:override>';
       $xml.='<g:shipping>'.$row['countryISOcode'].'::GROUND:'.$row['shipping'].' '.$row['currency'].'</g:shipping>';      
    }
  }

  $xml.='</item>';
    

  global $item_count;
  $item_count++;
  out_xml($xml);
}


include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);



// BEGIN AS GUEST
//$save_user = $user;
//$user = build_user( $conf['guest_id'], true);


//}
//
//// END AS GUEST
////$user = $save_user;
//




//$template->set_filename('sitemap', dirname(__FILE__).'/sitemap.tpl');
//$template->assign_var_from_handle('ADMIN_CONTENT', 'sitemap');

?>