<?php
/*
Plugin Name: PayPal Shopping Cart
Version: 12.a
Description: Append PayPal Shopping Cart on Piwigo to sell photos
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=499
Author: queguineur.fr
Author URI: http://www.queguineur.fr
Has Settings: webmaster
*/
/*
  Plugin Panier PayPal Pour Piwigo
  Copyright (C) 2011 www.queguineur.fr � Tous droits r�serv�s.
  
  Ce programme est un logiciel libre ; vous pouvez le redistribuer ou le
  modifier suivant les termes de la �GNU General Public License� telle que
  publi�e par la Free Software Foundation : soit la version 3 de cette
  licence, soit (� votre gr�) toute version ult�rieure.
  
  Ce programme est distribu� dans l�espoir qu�il vous sera utile, mais SANS
  AUCUNE GARANTIE : sans m�me la garantie implicite de COMMERCIALISABILIT�
  ni d�AD�QUATION � UN OBJECTIF PARTICULIER. Consultez la Licence G�n�rale
  Publique GNU pour plus de d�tails.
  
  Vous devriez avoir re�u une copie de la Licence G�n�rale Publique GNU avec
  ce programme ; si ce n�est pas le cas, consultez :
  <http://www.gnu.org/licenses/>.
*/
/*
Historique
1.0.0   10/02/2011
Version initiale
		
1.0.1   10/02/2011
Ajout du Plugin URI pour permettre les mises � jours
Traduction en Anglais du Plugin Name et du nom du r�pertoire
        
1.0.2   10/02/2011
Correction du probl�me de compatibilit� avec exif view (double affichage des boutons)
	
1.0.3   15/02/2011
Add lv_LV (Latvian) thanks to Aivars Baldone

1.0.4   17/02/2011
Add de_DE and it_IT (par Sugar888)

1.0.5   27/02/2011
Correction pb compatibilit� avec certains th�mes
D�placement des boutons PayPal en d�but de table info

1.0.6   05/03/2011
Add sk_SK (by dodo)

1.0.7   26/03/2011
Add hu_HU language (Hungarian) thanks to samli

*/
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

defined('PPPPP_ID') or define('PPPPP_ID', basename(dirname(__FILE__)));
define('PPPPP_PATH' , PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');
//define('PPPPP_SIZE_TABLE', $prefixeTable.'ppppp_size');
define('PPPPP_SIZES_TABLE', $prefixeTable.'ppppp_sizes');
define('PPPPP_SUPPORT_TABLE', $prefixeTable.'ppppp_support');
define('PPPPP_MATERIAL_TABLE', $prefixeTable.'ppppp_material');
define('PPPPP_OPTION_TABLE', $prefixeTable.'ppppp_support_options');
define('PPPPP_PROMOCODE_TABLE', $prefixeTable.'ppppp_promocode');
define('PPPPP_PRICE_TABLE', $prefixeTable.'ppppp_prices');
define('PPPPP_COUNTRY_TABLE', $prefixeTable.'ppppp_countries');
define('PPPPP_PROVIDER_TABLE', $prefixeTable.'ppppp_providers');
define('PPPPP_RATIO_TABLE', $prefixeTable.'ppppp_ratio');
define('PPPPP_VERSION', '2.7.c');


function ppppp_append_form($tpl_source)
{
  global $theme;
  
    
  $pattern = '#<.*\"infoTable\".*>#';
  $replacement = '
  {literal}
 <script type="text/javascript">
 function pppppValid(){
  var amount=pppppPriceCompute();
  var size=document.ppppp_size.size;
  var material_type=document.ppppp_material.material;
  var option1=document.ppppp_option1.option1;
  var option2=document.ppppp_option2.option2;
  var price=document.ppppp_price.price;
  var shipping=document.ppppp_shipping.shipping;
  var ppppp_price=price.value.slice(0,price.value.length-4);
  var ppppp_shipping=shipping.value.slice(0,price.value.length-4);
  var selectedSize=size[size.selectedIndex];
  var selectedMaterial=material_type[material_type.selectedIndex];
  var selectedOption1=option1[option1.selectedIndex];
  var selectedOption2=option2[option2.selectedIndex];
  //document.ppppp_add_to_cart.item_name.value="Photo \"{/literal}{$current.TITLE}\", File {$INFO_FILE}, Ref {$COMMENT_IMG}, {\'Size\'|@translate} : {literal} "+selectedMaterial.text+ " "+selectedSize.text;
  document.ppppp_add_to_cart.item_name.value="{/literal}Ref:{$COMMENT_IMG}, {literal} "+selectedSize.text+ " "+selectedMaterial.text+ " "+selectedOption1.text+ " "+selectedOption2.text+" Photo \"{/literal}{$current.TITLE}\", {$INFO_FILE}{literal} ";
  document.ppppp_add_to_cart.amount.value=ppppp_price;
  document.ppppp_add_to_cart.shipping.value=ppppp_shipping;
  }
  
  
function pppppPriceCompute(){
    pppppGetPromoCode();
    var price=document.ppppp_price.price;
    var option2=document.ppppp_option2.option2;
    var selectedOption2=option2[option2.selectedIndex];
    var currency = price.value.slice(-4,price.value.length);
    var pos = selectedOption2.value.indexOf("&");
    var raw_price = selectedOption2.value.slice(0,pos);
    var raw_shipping =  selectedOption2.value.slice(pos+1,selectedOption2.value.length);
    var final_price = Math.round(raw_price*(100-promo_rel)/100-promo_abs);
    var final_shipping = Math.round(raw_shipping-promo_ship);
    var final_total = final_price+final_shipping;
    document.ppppp_price.price.value=final_price + currency;
    document.ppppp_shipping.shipping.value=final_shipping + currency;
    document.ppppp_total_price.total.value=final_total + currency;      
}

function pppppUpdateOpt(){
    fillInPromoText()
    var code=document.ppppp_promocode_form.promocode.value;
    if(code=="{/literal}{\'Insert promo code\'|@translate}{literal}"){
        var promocode="";
    }
    else{
        var promocode="&PromoCode="+code;
    }
    var material_type=document.ppppp_material.material;
    var material_Id=material_type[material_type.selectedIndex];
    var size_type=document.ppppp_size.size;
    var size_Id=size_type[size_type.selectedIndex];
    var option1_type=document.ppppp_option1.option1;
    var option1_Id=option1_type[option1_type.selectedIndex];
    var current_page = location.href;
    var new_page = current_page;
    var pos = current_page.indexOf("&");
    if(pos>0)
        {
            new_page = current_page.slice(0,pos) + promocode + "&Material=" + material_Id.value + "&Size=" + size_Id.value + "&Option1=" + option1_Id.value;
        }
    else
        {
            new_page = current_page + promocode + "&Material=" + material_Id.value + "&Size=" + size_Id.value + "&Option1=" + option1_Id.value;
        }
self.location.href=new_page;
}

function pppppUpdateSize(){
    var code=document.ppppp_promocode_form.promocode.value;
    if(code=="{/literal}{\'Insert promo code\'|@translate}{literal}"){
        var promocode="";
    }
    else{
        var promocode="&PromoCode="+code;
    }
    var material_type=document.ppppp_material.material;
    var material_Id=material_type[material_type.selectedIndex];
    var size_type=document.ppppp_size.size;
    var size_Id=size_type[size_type.selectedIndex];
    var current_page = location.href;
    var new_page = current_page;
    var pos = current_page.indexOf("&");
    if(pos>0)
        {
            new_page = current_page.slice(0,pos) + promocode + "&Material=" + material_Id.value + "&Size=" + size_Id.value;
        }
    else
        {
            new_page = current_page + promocode + "&Material=" + material_Id.value + "&Size=" + size_Id.value;
        }
self.location.href=new_page;
}

function pppppUpdateMat(){
    var code=document.ppppp_promocode_form.promocode.value;
    if(code=="{/literal}{\'Insert promo code\'|@translate}{literal}"){
        var promocode="";
    }
    else{
        var promocode="&PromoCode="+code;
    }
    var material_type=document.ppppp_material.material;
    var material_Id=material_type[material_type.selectedIndex];
    var current_page = location.href;
    var new_page = current_page;
    var pos = current_page.indexOf("&");
    if(pos>0)
        {
            new_page = current_page.slice(0,pos) + promocode + "&Material=" + material_Id.value;
        }
    else
        {
            new_page = current_page + promocode + "&Material=" + material_Id.value;
        }
self.location.href=new_page;
}

function pppppGetPromoCode(){
    promo_rel=0;
    promo_abs=0;
    promo_ship=0;
    code_array={/literal}{$ppppp_array_promocode|@json_encode}{literal}
    code=document.ppppp_promocode_form.promocode.value;
    code_array.find(getPromo);
}

function getPromo(item,index){
    if (item.Code==code){
        promo_rel=item.Promo_rel;
        promo_abs=item.Promo_abs;
        promo_ship=item.Promo_ship;
    }
}

function fillInPromoText(){
    if (code===""){
        document.ppppp_promocode_form.promocode.value="{/literal}{\'Insert promo code\'|@translate}{literal}";
    }
}

function pppppCleanPromo(){
    if (code==="{/literal}{\'Insert promo code\'|@translate}{literal}"){
        document.ppppp_promocode_form.promocode.value="";
    }
}

 </script>
 {/literal}
 
 <tbody>
 <tr>
    <td class="label">{\'Select shipping country\'|@translate}</td>
    <td>
    <form name="ppppp_country" method="post" >
         <select name="currency" id="currency" onchange="return confirm(\'{\'Are you sure?\'|translate|@escape:javascript}\')">
	  {foreach from=$ppppp_array_countries item=ppppp_row_country}
          <option value="{$ppppp_row_country.Currency}&{$ppppp_row_country.CountryCode}"{if $ppppp_row_country.CountryCode==$ppppp_country_code} selected{/if}>{$ppppp_row_country.CountryName} ({$ppppp_row_country.Currency})</option>  
	  {/foreach}
      </select>
      {if isset($ppppp_country_code)}
      <input type="submit" value="{\'Change country\'|@translate}">
      {else}
      <input type="submit" value="{\'Select country\'|@translate}">
      {/if}      
    </form>
    </td>
 </tr>
 {if $ppppp_material_found==false}
 <tr>
    <td class="label" colspan=2>{\'No support found\'|@translate}</p>
 </tr>
 {else}
  <tr>
    <td class="label">{\'Select material\'|@translate}</td>
    <td>
    <form name="ppppp_material" method="get">
        <select name="material" onChange="pppppUpdateMat()"> 
         {foreach from=$ppppp_array_material item=ppppp_row_material}
         <option value="{$ppppp_row_material.Id}"{if $ppppp_row_material.Id==$ppppp_material_id} selected{/if}>{$ppppp_row_material.Material|@translate}</option>
	 {/foreach}
        </select>
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Select size\'|@translate}</td>
    <td>
    <form name="ppppp_size" method="get">
        <select name="size" onChange="pppppUpdateSize()"> 
	  {foreach from=$ppppp_array_sizes item=ppppp_row_sizes}	
          <option value="{$ppppp_row_sizes.Id}"{if $ppppp_row_sizes.Id==$ppppp_sizes_id} selected{/if}>{$ppppp_row_sizes.Size} ({$ppppp_row_sizes.AltSize})</option>
	  {/foreach}
        </select>
    </form>
    </td>
 </tr>
  <tr>
    <td class="label">{\'Select first option\'|@translate}</td>
    <td>
    <form name="ppppp_option1" method="get">
        <select name="option1" onChange="pppppUpdateOpt()"> 
	  {foreach from=$ppppp_array_option1 item=ppppp_row_option1}	
          <option value="{$ppppp_row_option1.Id}"{if $ppppp_row_option1.Id==$ppppp_option1_id} selected{/if}>{$ppppp_row_option1.SupportOption1|@translate}</option>
	  {/foreach}
        </select>
    </form>
    </td>
 </tr>
  <tr>
    <td class="label">{\'Select second option\'|@translate}</td>
    <td>
    <form name="ppppp_option2" method="post" onSubmit="javascript:pppppChangeOption2()">
        <select name="option2" onChange="pppppPriceCompute()"> 
	  {foreach from=$ppppp_array_option2 item=ppppp_row_option2}	
          <option value="{$ppppp_row_option2.Price}&{$ppppp_row_option2.Shipping}">{$ppppp_row_option2.SupportOption2|@translate}</option>
	  {/foreach}
        </select>
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Price\'|@translate}</td>
    <td>
    <form name="ppppp_price">
        <input type="text" readonly size=7 name="price" value="{$ppppp_price} {$ppppp_currency}">
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Shipping fees\'|@translate}</td>
    <td>
    <form name="ppppp_shipping">
        <input type="text" readonly size=7 name="shipping" value="{$ppppp_shipping} {$ppppp_currency}">
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Promo code\'|@translate}</td>
    <td>
    <form name="ppppp_promocode_form">
        <input type="text" size=20 name="promocode" value="{$ppppp_promocode|@translate}" oninput="pppppPriceCompute()" onblur="pppppUpdateOpt()" onfocus="pppppCleanPromo()"><br/>
    </form>
    </td>
 </tr>
  <tr>
    <td class="label">{\'Total price\'|@translate}</td>
    <td>
    <form name="ppppp_total_price">
        <input type="text" readonly size=7 name="total" value="{$ppppp_total} {$ppppp_currency}">
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Buy this picture\'|@translate}</td>
    <td>
    <form name="ppppp_add_to_cart" target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" onSubmit="javascript:pppppValid()">
    <!--//<form name="ppppp_add_to_cart" target="paypal" action="mailto:webmaster@daedalum.org" method="post" onSubmit="javascript:pppppValid()">-->
     <input type="hidden" name="add" value="1">
     <input type="hidden" name="cmd" value="_cart">
     <input type="hidden" name="charset" value="utf-8">
     <input type="hidden" name="business" value="{$ppppp_e_mail}">
     <input type="hidden" name="item_name">
     <input type="hidden" name="amount" value="{$ppppp_price}">
     <input type="hidden" name="no_shipping" value="2"><!-- shipping address mandatory -->
     <input type="hidden" name="handling_cart" value="0">
     <input type="hidden" name="shipping" value="{$ppppp_shipping}">
     <input type="hidden" name="currency_code" value="{$ppppp_currency}">
     <input type="submit" value="{\'Add to cart\'|@translate}">
     </form>

    <form name="ppppp_checkout_cart" target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <!--//<form name="ppppp_checkout_cart" target="paypal" action="mailto:webmaster@daedalum.org" method="post">-->
     <input type="hidden" name="cmd" value="_cart">
     <input type="hidden" name="business" value="{$ppppp_e_mail}">
     <input type="hidden" name="display" value="1">
     <input type="hidden" name="no_shipping" value="2">
     <input type=submit value="{\'View Shopping Cart\'|@translate}">
    </form>
   </td>
   
   <!-- API PayPal : https://developer.paypal.com/docs/paypal-payments-standard/integration-guide/Appx-websitestandard-htmlvariables/ -->
   
</tr>
{/if}
<tr>
   <td colspan=2 align=center>
        {\'Ask support\'|@translate} <a href="mailto:online.shop@daedalum.org" target="_blank">online.shop@daedalum.org</a></br>
   </td>
</tr>
<tr>
   <td colspan=2 align=center>
        <form>
        <a href="https://www.paypal.com/us/webapps/mpp/paypal-safety-and-security" target="_blank"><img src="{PPPPP_PATH}include/payment-logos.png" height=48 onLoad="pppppPriceCompute()"></a></br>
        <input type="hidden" onLoad="pppppPriceCompute()">
        </form>
    </td>
</tr>
<tr>
   <td colspan=2  align=center>
        <a href="https://photos.daedalum.org/index.php?/page/daedalum_online_shop_terms_conditions" target="_blank">{\'Please click here for terms & conditions\'|@translate}</a>
   </td>
</tr>
  
  <p id="demo"></p>

 </tbody>

 ';

  if (strpos($theme, 'stripped') === 0)
  {
    $pattern = '#</div>\s*<!--\s*theImage\s*-->#';
    $replacement = '{combine_css path="plugins/PayPalShoppingCart/stripped.css"}<table id="paypalCart">'.$replacement.'</table>';
    $replacement = $replacement.'$0';
  }
  else
  {
    if(!preg_match($pattern,$tpl_source))
    {
      $pattern='#{if isset\(\$COMMENT_IMG\)}#';
      $replacement='<table>'.$replacement.'</table>';
      $replacement=$replacement.'$0';
    }
    else
    {
      $replacement='$0'.$replacement;
    }
  }
  
  return preg_replace($pattern, $replacement, $tpl_source,1);
}

//function ppppp_picture_handler() // avec l'ancien handler

function ppppp_picture_handler($content,$current_picture)
{
  global $template, $conf, $page, $queryFilterSize, $IMG_name;

  if (!ppppp_is_paypal_active())
  {
    return;
  }
      
  $IMG_name=$current_picture['comment'];
  
  $min_res_tolerance=1.025;

  $src_size=$current_picture['src_image']->get_size();
  $IMG_Height=min(floatval($src_size[1]),floatval($src_size[0]));
  $IMG_Width=max(floatval($src_size[1]),floatval($src_size[0]));
  $IMG_ratio=round($IMG_Width/$IMG_Height,1);  
    
  $template->assign(
  array(
    'F_ACTION'=>PHPWG_ROOT_PATH.'main.inc.php')
  );  

  $template->set_prefilter('picture', 'ppppp_append_form');
  load_language('plugin.lang', PPPPP_PATH);
    
//  $query_promocode='SELECT * FROM '.PPPPP_PROMOCODE_TABLE.' '.@$conf['PayPalShoppingCart_promocode_order_by'].';';
//  $result_promocode = pwg_query($query_promocode);
//  while($row_promocode = pwg_db_fetch_assoc($result_promocode))
//  {
//    $template->append('ppppp_array_promocode',$row_promocode);
//  }
  
  
  $array_currency=array();
  $query_country='SELECT DISTINCT T5.*'.
        ' FROM '.PPPPP_PRICE_TABLE.' T1'.
        ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider'.
        @$conf['PayPalShoppingCart_country_order_by'].';';
  $result_country = pwg_query($query_country);
  $country_count=0;
  while($row_country = pwg_db_fetch_assoc($result_country))
  {
    $template->append('ppppp_array_countries',$row_country);
    $concat_code = $row_country['Currency'] . "&" . $row_country['CountryCode'];
    $array_currency[$concat_code]=$row_country['CountryCode'];
      if($country_count==0){
        $first_row_country_index=$row_country['CountryCode'];
    }
    $country_count=$country_count+1;  
  }
    
  $query_material='SELECT DISTINCT T3.Id as Id, T3.Material'.
        ' FROM '.PPPPP_PRICE_TABLE.' T1'.
        ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T2 ON T1.Support = T2.Id'.
        ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T3 ON T2.SupportMaterial = T3.Id'.
        ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider'.
        ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T6 ON T1.Size=T6.Id'.
        ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T7 ON T6.Ratio=T7.Id'.
        ' WHERE T5.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
        ' AND abs(T7.RatioValue-'.$IMG_ratio.') <=1e-2'.
        ' AND T6.Height_in<'.$IMG_Height.'*'.$min_res_tolerance.'/T6.MinRes'.
        ' ORDER BY T1.Price,T3.Id ;';
//  echo '<pre>'; print_r($query_material); echo '</pre>';
  $result_material = pwg_query($query_material);
  $material_count=0;
  while($row_material = pwg_db_fetch_assoc($result_material))
  {
    $template->append('ppppp_array_material',$row_material);
    if($material_count==0){
        $first_row_material_index=$row_material['Id'];
    }
    $material_count=$material_count+1;
  }
  
 if($material_count>0){
    $material_found=true;
    if(isset($_GET['Material']))
       {
         $material_Id=$_GET['Material'];
       } 
    else
       {
         $material_Id=$first_row_material_index;
       }
     $query_sizes='SELECT DISTINCT T2.Id AS Id, T2.SizeName AS Size, T2.AltSizeName AS AltSize'. //, T3.RatioValue, T2.Height_cm AS Height, T2.Width_cm AS Width'.
             ' FROM '.PPPPP_PRICE_TABLE.' T1'.
             ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size=T2.Id'.
             ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T6 ON T1.Support = T6.Id'.
             ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T7 ON T6.SupportMaterial = T7.Id'.
             ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T3 ON T2.Ratio=T3.Id'.
             ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T4 ON T1.Provider = T4.Provider'.
             ' WHERE abs(T3.RatioValue-'.$IMG_ratio.') <=1e-2'.
             ' AND T2.Height_in<'.$IMG_Height.'*'.$min_res_tolerance.'/T2.MinRes'.
             ' AND T4.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
             ' AND T7.Id="'.$material_Id."\"".
             ' ORDER BY T1.Price, T2.Height_cm;';
  //    echo '<pre>'; print_r($query_sizes); echo '</pre>';
      $result_sizes = pwg_query($query_sizes);
      $sizes_count=0;
     while($row_sizes = pwg_db_fetch_assoc($result_sizes ))
     {
       $template->append('ppppp_array_sizes',$row_sizes );
       if($sizes_count==0){
          $first_row_sizes_index=$row_sizes['Id'];
       }
       $sizes_count=$sizes_count+1;       
    }
}
 else{
    $material_found=false;
    $material_Id=0;
    $sizes_count=0;
}
//   echo '<pre>'; print_r($material_found); echo '</pre>';
 
 if($sizes_count>0){
    $sizes_found=true;
    if(isset($_GET['Size']))
       {
         $sizes_Id=$_GET['Size'];
       } 
    else
       {
         $sizes_Id=$first_row_sizes_index;
       }
     $query_option1='SELECT DISTINCT T3.Id AS Id, T3.OptionName AS SupportOption1'. //, T4.RatioValue, T2.Height_cm AS Height, T2.Width_cm AS Width, T1.Price, T1.Shipping'.
             ' FROM '.PPPPP_PRICE_TABLE.' T1'.
             ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size=T2.Id'.
             ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T6 ON T1.Support = T6.Id'.
             ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T7 ON T6.SupportMaterial = T7.Id'.
             ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T6.SupportOption1 = T3.Id'.
             ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T4 ON T2.Ratio=T4.Id'.
             ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider'.
             ' WHERE abs(T4.RatioValue-'.$IMG_ratio.') <=1e-2'.
             ' AND T2.Height_in<'.$IMG_Height.'*'.$min_res_tolerance.'/T2.MinRes'.
             ' AND T5.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
             ' AND T7.Id="'.$material_Id."\"".
             ' AND T2.Id="'.$sizes_Id."\"".
             ' ORDER BY T1.Price, T3.Id;';
 //     echo '<pre>'; print_r($query_option1); echo '</pre>';
      $result_option1 = pwg_query($query_option1);
      $option1_count=0;

     while($row_option1 = pwg_db_fetch_assoc($result_option1 ))
     {
       $template->append('ppppp_array_option1',$row_option1 );
       if($option1_count==0){
          $first_row_option1_index=$row_option1['Id'];
       }
       $option1_count=$option1_count+1;       
     }
 }
 else{
    $sizes_found=false;
    $sizes_Id=0;
    $option1_count=0;   
 }

  if($option1_count>0){
    $option1_found=true;
    if(isset($_GET['Option1']))
       {
         $option1_Id=$_GET['Option1'];
       } 
    else
       {
         $option1_Id=$first_row_option1_index;
        }
      $query_option2='SELECT DISTINCT T8.OptionName AS SupportOption2, T1.Price, T1.Shipping'. //, T4.RatioValue, T2.Height_cm AS Height, T2.Width_cm AS Width'.
             ' FROM '.PPPPP_PRICE_TABLE.' T1'.
             ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size=T2.Id'.
             ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T6 ON T1.Support = T6.Id'.
             ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T7 ON T6.SupportMaterial = T7.Id'.
             ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T6.SupportOption1 = T3.Id'.
             ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T8 ON T6.SupportOption2 = T8.Id'.
             ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T4 ON T2.Ratio=T4.Id'.
             ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider'.
             ' WHERE abs(T4.RatioValue-'.$IMG_ratio.') <=1e-2'.
             ' AND T2.Height_in<'.$IMG_Height.'*'.$min_res_tolerance.'/T2.MinRes'.
             ' AND T5.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
             ' AND T7.Id="'.$material_Id."\"".
             ' AND T2.Id="'.$sizes_Id."\"".
             ' AND T3.Id="'.$option1_Id."\"".
             ' ORDER BY T1.Price, T8.Id';
 //    echo '<pre>'; print_r($query_option2); echo '</pre>';
      $result_option2 = pwg_query($query_option2);

     while($row_option2 = pwg_db_fetch_assoc($result_option2 ))
     {
       $template->append('ppppp_array_option2',$row_option2 );
     }
 }
 else{
    $option1_found=false;
    $option1_Id=0;
 }
 
 if(isset($_GET['PromoCode']))
 {
     $ppppp_promocode=$_GET['PromoCode'];
     
    $query_promocode='SELECT * FROM '.PPPPP_PROMOCODE_TABLE.' WHERE code= "'.$_GET['PromoCode'].'";';
//     echo '<pre>'; print_r($query_promocode); echo '</pre>';
    $result_promocode = pwg_query($query_promocode);
    while($row_promocode = pwg_db_fetch_assoc($result_promocode))
    {
      $template->append('ppppp_array_promocode',$row_promocode);
    }
 }
 else{
     $ppppp_promocode='Insert promo code';
      $array_no_promo = array(
      'Id'=>'1',
      'Code'=>'Insert promo code',
      'Promo_abs'=>'0',
      'Promo_rel'=>'0',
      'Promo_ship'=>'0',
      );

      $template->assign(
      array(
        'ppppp_array_promocode' => [$array_no_promo],
        )
      );

 }

 $country_code=$first_row_country_index;
 
    if(isset($_POST['currency']) and isset($array_currency[ $_POST['currency'] ]))
    {
      $curr=substr($_POST['currency'],0,3);
      $country_code=substr($_POST['currency'],4,2);
      $conf['PayPalShoppingCart']['currency'] = $curr;
      $conf['PayPalShoppingCart']['country'] = $country_code;
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
  $template->assign(
    array(
//      'ppppp_support_found' => $support_found,
      'ppppp_material_found' => $material_found,
      'ppppp_sizes_found' => $sizes_found,
      'ppppp_option1_found' => $option1_found,
      'ppppp_promocode' => $ppppp_promocode,
//      'ppppp_support_id' => $support_Id,
      'ppppp_material_id' => $material_Id,
      'ppppp_sizes_id' => $sizes_Id,
      'ppppp_option1_id' => $option1_Id,
      'ppppp_country_code' => $conf['PayPalShoppingCart']['country'],
 //     'ppppp_fixed_shipping' => $conf['PayPalShoppingCart']['fixed_shipping'],
      'ppppp_currency' => $conf['PayPalShoppingCart']['currency'],
 //     'ppppp_e_mail' => get_webmaster_mail_address(),
      'ppppp_e_mail' => $conf['PayPalShoppingCart']['PayPalAccount'],
      'ppppp_price' => 0,
      'ppppp_shipping' => 0,
      'ppppp_total' => 0,
        )
    );
}


//add_event_handler('loc_begin_picture', 'ppppp_picture_handler'); //ancien handler ne permettait pas de recuperer le nom de l'image


add_event_handler('render_element_content', 'ppppp_picture_handler', EVENT_HANDLER_PRIORITY_NEUTRAL-10);


// Decommenter les lignes ci dessous pour faire apparaitre un lien "View shopping cart" dans le menu de gauche.
// D�sactiv� car ne marchait pas !

/*function ppppp_append_js($tpl_source){
 load_language('plugin.lang', PPPPP_PATH);
 if(strstr($tpl_source,"{'Menu'|@translate}")==false)
  return $tpl_source;
 $pattern = '#{/foreach}#';  
 $replacement = '{/foreach}
 <li><a href="" title="'.l10n('View my PayPal Shopping Cart').'" onclick="document.forms[\'ppppp_form_view_cart\'].submit()">'.l10n('View Shopping Cart').'</a></li>
<form name="ppppp_form_view_cart" target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
     <input type="hidden" name="cmd" value="_cart">
     <input type="hidden" name="business" value="{$ppppp_e_mail}">
     <input type="hidden" name="display" value="1">
     <input type="hidden" name="no_shipping" value="2">
  </form>
  ';
 return preg_replace($pattern, $replacement, $tpl_source); 
 }

function ppppp_index_handler(){
 global $template;
 $template->set_prefilter('menubar', 'ppppp_append_js');
 $template->assign('ppppp_e_mail',get_webmaster_mail_address()); 
 }

add_event_handler('loc_begin_index', 'ppppp_index_handler');*/

//fonction ci-dessous normalement supprimee suite au passage a Piwigo 12
function ppppp_admin_menu($menu){
 load_language('plugin.lang', PPPPP_PATH);
 array_push($menu, array(
  'NAME' => l10n('PayPal Shopping Cart'),
  'URL' => get_admin_plugin_menu_link(PPPPP_PATH . 'admin.php')));
 return $menu;
 }

add_event_handler('get_admin_plugin_menu_links', 'ppppp_admin_menu');

add_event_handler('init', 'ppppp_init');
/**
 * plugin initialization
 *   - unserialize configuration
 *   - load language
 */
function ppppp_init()
{
  global $conf;
  
  // load plugin language file
  load_language('plugin.lang', PPPPP_PATH);
  
  // prepare plugin configuration
  $conf['PayPalShoppingCart'] = isset($conf['PayPalShoppingCart']) ? safe_unserialize($conf['PayPalShoppingCart']);
}

// add_event_handler('loc_end_index_thumbnails', 'ppppp_loc_end_index_thumbnails');
add_event_handler('loc_begin_index', 'ppppp_lightbox_exception');
function ppppp_lightbox_exception()
{
  if (!ppppp_is_paypal_active())
  {
    return;
  }
  
  remove_event_handler('loc_end_index_thumbnails', 'lightbox_plugin', 40);
}

function ppppp_is_paypal_active()
{
  global $conf, $page;

  if ($conf['PayPalShoppingCart']['apply_to_albums'] == 'list')
  {
    if (!isset($page['category']))
    {
      return false;
    }

    $query = '
SELECT
    paypal_active
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$page['category']['id'].'
;';
    list($paypal_active) = pwg_db_fetch_row(pwg_query($query));

    if ('false' == $paypal_active)
    {
      return false ;
    }
  }

  return true;
}
?>
