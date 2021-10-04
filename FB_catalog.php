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

function start_xml($filename, $XMLlang)
{
  global $file;
  $url=get_root_url().$filename;
  $file = fopen( $filename, 'w' );
//  out_xml('<?xml version="1.0" encoding="UTF-8"?'.'>
//<?xml-stylesheet type="text/xsl" href="'.get_root_url().'plugins/'.basename(dirname(__FILE__)).'/sitemap.xsl"?'.'>
//<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">', $gzip );
  out_xml(  '<?xml version="1.0" encoding="UTF-8"?>'."\r"."\n".
            '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">'."\r"."\n".
            '<channel>'."\r"."\n".
            '<title>'.$XMLlang['title'].'</title>'."\r"."\n".
            '<link>'.$url.'</link>'."\r"."\n".
            '<description>'.$XMLlang['description'].'</description>'."\r"."\n"            
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
  out_xml('</channel>'."\r"."\n".
          '</rss>'."\r"."\n");           
  fclose( $file );
}

$item_count=0;

function add_item($row, $ref_cat, $conf, $links, $XMLlang)
{
    
  $xml='<item>'."\r"."\n";

  if ( isset($row['item_option']) and strlen($row['item_option'])>0 )
  {
    $xml.='<g:id>'.$row['item']."_".$row['item_option'].'</g:id>'."\r"."\n";
    $xml.='<g:item_group_id>'.substr($row['item'],0,8).'</g:item_group_id>'."\r"."\n";
    switch($row['item_option']){
        case('Poster'):
            $mat_desc=$XMLlang['Poster'];
            $print_desc=$XMLlang['support_poster'];
            if(isset($XMLlang['Poster_options'])) $options=$XMLlang['Poster_options'];
            if(isset($XMLlang['Poster_images'])) $option_images=$XMLlang['Poster_images'];
            break;
        case('Canvas'):
            $mat_desc=$XMLlang['Canvas'];
            $print_desc=$XMLlang['support_canvas'];
            if(isset($XMLlang['Canvas_options'])) $options=$XMLlang['Canvas_options'];
            if(isset($XMLlang['Canvas_images'])) $option_images=$XMLlang['Canvas_images'];
        break;
        case('Dibond®'):
            $mat_desc=$XMLlang['Dibond'];
            $print_desc=$XMLlang['support_dibond'];
            if(isset($XMLlang['Dibond_options'])) $options=$XMLlang['Dibond_options'];
            if(isset($XMLlang['Dibond images'])) $option_images=$XMLlang['Dibond_images'];
        break;
    }
    $xml.='<g:material>'.$mat_desc.'</g:material>'."\r"."\n";
  }
  else
  {
    $xml.='<g:id>'.$row['item'].'</g:id>'."\r"."\n";
  }

  if ( isset($row['title']) and strlen($row['title'])>0 )
  {
    // Limite la longueur du titre à 65 caracteres, en pratique au dernier mot avant le 65 chara  
    $lastPos = 0;
    $MaxPos = strlen($row['title']);

    if ($MaxPos>65)
    {
        while (($lastPos = strpos($row['title'], ' ', $lastPos))!== false) {
            if($lastPos<65)
            {
                $MaxPos=$lastPos;
            }
            $lastPos = $lastPos + 1; 
        }
    }
    
    $croppedTitle =substr($row['title'],0,$MaxPos);
    if (strcmp(substr($croppedTitle, -2),' -')==0)
    {
       $croppedTitle=substr($croppedTitle,0,$MaxPos-2); 
    }

    
     $xml.='<g:title>'.htmlspecialchars($croppedTitle).'</g:title>'."\r"."\n";
  }
    
  switch($XMLlang['units']){
      case('cm'):
          $MinSize=$row['minSize_cm'].'x'.round($row['minSize_cm']/$row['Ratio'],0).'cm';
          $MaxSize=$row['maxSize_cm'].'x'.round($row['maxSize_cm']/$row['Ratio'],0).'cm';
      break;
      case('in'):
          $MinSize=$row['minSize_in'].'x'.round($row['minSize_in']/$row['Ratio'],0).'in';
          $MaxSize=$row['maxSize_in'].'x'.round($row['maxSize_in']/$row['Ratio'],0).'in';
      break;
  }
  
  if ( $MinSize == $MaxSize )
  {
      $xml.='<g:description>'.$print_desc.$XMLlang['size0'].$MinSize.'</g:description>'."\r"."\n";
  }
  else
  {
      $xml.='<g:description>'.$print_desc.$XMLlang['size1'].$MinSize.$XMLlang['size2'].$MaxSize.$XMLlang['size3'].'</g:description>'."\r"."\n";
  }

    if(sizeof($options)>0){
        $xml.='<additional_variant_attribute>'."\r"."\n";        
        foreach ($options as $optionname => $optionarray){
            $xml.='<label>'.$optionname.'</label>'."\r"."\n";
            foreach ($optionarray as $optionvalue){
                $xml.='<value>'.$optionvalue.'</value>'."\r"."\n";            
            }       
        }
        $xml.='</additional_variant_attribute>'."\r"."\n"; 
    }
  
    $xml.='<g:availability>'.'in_stock'.'</g:availability>'."\r"."\n";
    $xml.='<g:condition>'.'new'.'</g:condition>'."\r"."\n";
    $xml.='<g:brand>'.$conf['PayPalShoppingCart']['Brand'].'</g:brand>'."\r"."\n";
    $xml.='<g:price>'.$row['price'].' '.$row['currency'].'</g:price>'."\r"."\n";
    $xml.='<g:link>'.$links['item_url'].'</g:link>'."\r"."\n";
    $xml.='<g:custom_label_0>'.$row['categoryName'].'</g:custom_label_0>'."\r"."\n";
    $xml.='<g:custom_label_1>'.$row['categoryPL'].'</g:custom_label_1>'."\r"."\n";
   
  if ( $ref_cat)
  {
    $xml.='<g:image_link>'.$links['image_link1'].'</g:image_link>'."\r"."\n";      
    $xml.='<additionnal_image_link>'.$links['image_link2'].'</additionnal_image_link>'."\r"."\n";
    if(sizeof($option_images)>0){
        foreach ($option_images as $imageURL){
            $xml.='<additionnal_image_link>'.get_root_url().$imageURL.'</additionnal_image_link>'."\r"."\n";        
        }
    }
    $xml.='<g:google_product_category>'.$conf['PayPalShoppingCart']['GoogleId'].'</g:google_product_category>'."\r"."\n";
    $xml.='<product_type>Home &amp; Garden &gt; Decor &gt; Artwork &gt; Posters, Prints, &amp; Visual Artwork</product_type>'."\r"."\n"; 
    $xml.='<g:fb_product_category>'.$conf['PayPalShoppingCart']['FBId'].'</g:fb_product_category>'."\r"."\n";
   }
  else
  {
    if ( isset($row['countryISOcode']) and strlen($row['countryISOcode'])==2 )
    {
       $xml.='<g:override>'.$row['countryISOcode'].'</g:override>'."\r"."\n";
    }
  }

  $xml.='<g:shipping>'."\r"."\n";
    $xml.='<g:country>'.$row['countryISOcode'].'</g:country>'."\r"."\n";
    $xml.='<g:service>'.'Tracked delivery'.'</g:service>'."\r"."\n";
    $xml.='<g:price>'.$row['shipping'].' '.$row['currency'].'</g:price>'."\r"."\n";
    $xml.='<g:min_handling_time>1</g:min_handling_time>'."\r"."\n";
    $xml.='<g:max_handling_time>3</g:max_handling_time>'."\r"."\n";
    $xml.='<g:min_transit_time>7</g:min_transit_time>'."\r"."\n";
    $xml.='<g:max_transit_time>14</g:max_transit_time>'."\r"."\n";
  $xml.='</g:shipping>'."\r"."\n";  

  $xml.='</item>'."\r"."\n";
    

  global $item_count;
  $item_count++;
  out_xml($xml);
}

function add_item_lang($row, $XMLlang)
{
    
  $xml='<item>'."\r"."\n";

  if ( isset($row['item_option']) and strlen($row['item_option'])>0 )
  {
    $xml.='<g:id>'.$row['item']."_".$row['item_option'].'</g:id>'."\r"."\n";
    $xml.='<g:item_group_id>'.substr($row['item'],0,8).'</g:item_group_id>'."\r"."\n";
    switch($row['item_option']){
        case('Poster'):
            $mat_desc=$XMLlang['Poster'];
            $print_desc=$XMLlang['support_poster'];
            break;
        case('Canvas'):
            $mat_desc=$XMLlang['Canvas'];
            $print_desc=$XMLlang['support_canvas'];
        break;
        case('Dibond®'):
            $mat_desc=$XMLlang['Dibond'];
            $print_desc=$XMLlang['support_dibond'];
        break;
    }
    $xml.='<g:material>'.$mat_desc.'</g:material>'."\r"."\n";
  }
  else
  {
    $xml.='<g:id>'.$row['item'].'</g:id>'."\r"."\n";
  }

  if ( isset($row['title']) and strlen($row['title'])>0 )
  {
    // Limite la longueur du titre à 65 caracteres, en pratique au dernier mot avant le 65 chara  
    $lastPos = 0;
    $MaxPos = strlen($row['title']);

    if ($MaxPos>65)
    {
        while (($lastPos = strpos($row['title'], ' ', $lastPos))!== false) {
            if($lastPos<65)
            {
                $MaxPos=$lastPos;
            }
            $lastPos = $lastPos + 1; 
        }
    }
    
    $croppedTitle =substr($row['title'],0,$MaxPos);
    if (strcmp(substr($croppedTitle, -2),' -')==0)
    {
       $croppedTitle=substr($croppedTitle,0,$MaxPos-2); 
    }

    
     $xml.='<g:title>'.htmlspecialchars($croppedTitle).'</g:title>'."\r"."\n";
  }
    
  switch($XMLlang['units']){
      case('cm'):
          $MinSize=$row['minSize_cm'].'x'.round($row['minSize_cm']/$row['Ratio'],0).'cm';
          $MaxSize=$row['maxSize_cm'].'x'.round($row['maxSize_cm']/$row['Ratio'],0).'cm';
      break;
      case('in'):
          $MinSize=$row['minSize_in'].'x'.round($row['minSize_in']/$row['Ratio'],0).'in';
          $MaxSize=$row['maxSize_in'].'x'.round($row['maxSize_in']/$row['Ratio'],0).'in';
      break;
  }
  
  if ( $MinSize == $MaxSize )
  {
      $xml.='<g:description>'.$print_desc.$XMLlang['size0'].$MinSize.'</g:description>'."\r"."\n";
  }
  else
  {
      $xml.='<g:description>'.$print_desc.$XMLlang['size1'].$MinSize.$XMLlang['size2'].$MaxSize.$XMLlang['size3'].'</g:description>'."\r"."\n";
  }
  
    $xml.='<g:availability>'.'in_stock'.'</g:availability>'."\r"."\n";
    $xml.='<g:condition>'.'new'.'</g:condition>'."\r"."\n";

    if ( isset($row['langISOcode']) and strlen($row['langISOcode'])==5 )
    {
       $xml.='<g:override>'.$row['langISOcode'].'</g:override>'."\r"."\n";
    }
  

  $xml.='</item>'."\r"."\n";
    

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