<?php
/*
Plugin Name: PayPal Shopping Cart
Version: 2.7.c
Description: Append PayPal Shopping Cart on Piwigo to sell photos
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=499
Author: queguineur.fr
Author URI: http://www.queguineur.fr
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
define('PPPPP_SIZE_TABLE', $prefixeTable.'ppppp_size');
define('PPPPP_SIZES_TABLE', $prefixeTable.'ppppp_sizes');
define('PPPPP_SUPPORT_TABLE', $prefixeTable.'ppppp_support');
define('PPPPP_OPTION_TABLE', $prefixeTable.'ppppp_support_options');
define('PPPPP_PROMOCODE_TABLE', $prefixeTable.'ppppp_promocode');
define('PPPPP_PRICE_TABLE', $prefixeTable.'ppppp_prices');
define('PPPPP_COUNTRY_TABLE', $prefixeTable.'ppppp_countries');
define('PPPPP_PROVIDER_TABLE', $prefixeTable.'ppppp_providers');
define('PPPPP_RATIO_TABLE', $prefixeTable.'ppppp_ratio');
define('PPPPP_VERSION', '2.7.c');


function ppppp_append_form($tpl_source, &$smarty)
{
  global $theme;
  
    
  $pattern = '#<.*\"infoTable\".*>#';
  $replacement = '
  {literal}
 <script type="text/javascript">
 function pppppValid(){
  var amount=pppppPriceCompute();
  var size=document.ppppp_size.size;
  var support_type=document.ppppp_support.support;
  var price=document.ppppp_price.price;
  var shipping=document.ppppp_shipping.shipping;
  var ppppp_price=price.value.slice(0,price.value.length-4);
  var ppppp_shipping=shipping.value.slice(0,price.value.length-4);
  var selectedSize=size[size.selectedIndex];
  var selectedSupport=support_type[support_type.selectedIndex];
  //document.ppppp_add_to_cart.item_name.value="Photo \"{/literal}{$current.TITLE}\", File {$INFO_FILE}, Ref {$COMMENT_IMG}, {\'Size\'|@translate} : {literal} "+selectedSupport.text+ " "+selectedSize.text;
  document.ppppp_add_to_cart.item_name.value="{/literal}Ref {$COMMENT_IMG}, {\'Size\'|@translate} : {literal} "+selectedSize.text+ " "+selectedSupport.text+" Photo \"{/literal}{$current.TITLE}\", {$INFO_FILE}{literal} ";
  document.ppppp_add_to_cart.amount.value=ppppp_price;
  document.ppppp_add_to_cart.handling_cart.value=ppppp_shipping;
  }
  
  
function pppppPriceCompute(){
    pppppGetPromoCode();
    var price=document.ppppp_price.price;
    var size=document.ppppp_size.size;
    var support_type=document.ppppp_support.support;
    var selectedSize=size[size.selectedIndex];
    var selectedSupport=support_type[support_type.selectedIndex];
    var currency = price.value.slice(-4,price.value.length);
    var pos = selectedSize.value.indexOf("&");
    var raw_price = selectedSize.value.slice(0,pos);
    var raw_shipping =  selectedSize.value.slice(pos+1,selectedSize.value.length);
    var final_price = Math.round(raw_price*(100-reduc_rel)/100-reduc_abs);
    var final_shipping = Math.round(raw_shipping-reduc_ship);
    var final_total = final_price+final_shipping;
    document.ppppp_price.price.value=final_price + currency;
    document.ppppp_shipping.shipping.value=final_shipping + currency;
    document.ppppp_total_price.total.value=final_total + currency;      
}

function pppppChangeCountry(){
    
}

function pppppChangeSupport(){
    var code=document.ppppp_promocode_form.promocode.value;
    if(code=="Insert promo code"){
        var promocode="";
    }
    else{
        var promocode="&PromoCode="+code;
    }
    var support_type=document.ppppp_support.support;
    var support_Id=support_type[support_type.selectedIndex];
    var current_page = location.href;
    var new_page = current_page;
    var m = current_page.indexOf("&PromoCode");    
    var n = current_page.indexOf("&Support");
    if(n>0 && m>0)
        {
        new_page = current_page.slice(0,m) + promocode + "&Support=" + support_Id.value;
        }
    else if(n>0 && m<0)
        {
        new_page = current_page.slice(0,n) + promocode + "&Support=" + support_Id.value;
        }
    else if(n<0 && m>0)
        {
        new_page = current_page.slice(0,m) + promocode + "&Support=" + support_Id.value;
        }
    else if(n<0 && m<0)
        {
        new_page = current_page + promocode + "&Support=" + support_Id.value;
        }
    self.location.href=new_page;
}

function pppppGetPromoCode(){
    reduc_rel=0;
    reduc_abs=0;
    reduc_ship=0;
    code_array={/literal}{$ppppp_array_promocode|@json_encode}{literal}
    code=document.ppppp_promocode_form.promocode.value;
    code_array.find(getReduc);
}

function getReduc(item,index){
    if (item.code==code){
        reduc_rel=item.reduc_rel;
        reduc_abs=item.reduc_abs;
        reduc_ship=item.reduc_ship;
    }
}

function fillInPromoText(){
    if (code===""){
        document.ppppp_promocode_form.promocode.value="{/literal}{\'Insert promo code\'|@translate}{literal}";
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
      <input type="submit" value="{\'Select country\'|@translate}">
    </form>
    </td>
 </tr>
 {if $ppppp_support_found==false}
 <tr>
    <td class="label" colspan=2>Sorry the resolution of the digital file does not allow for reasonable printing size.</p>
 </tr>
 {else}
 <tr>
    <td class="label">{\'Select support\'|@translate}</td>
    <td>
    <form name="ppppp_support" action="{$ppppp_support_action}" method="get">
        <select name="support" onChange="pppppChangeSupport()"> 
         {foreach from=$ppppp_array_support item=ppppp_row_support}
         <option value="{$ppppp_row_support.Id}"{if $ppppp_row_support.Id==$ppppp_support_id} selected{/if}>{$ppppp_row_support.SupportName|@translate}{if $ppppp_row_support.SupportOption1!="None"} {$ppppp_row_support.SupportOption1|@translate}{/if}{if $ppppp_row_support.SupportOption2!="None"} {$ppppp_row_support.SupportOption2|@translate}{/if}</option>
	 {/foreach}
        </select>
        <!--//<input type="submit" value="{\'Select support\'|@translate}">-->
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Select size\'|@translate}</td>
    <td>
    <form name="ppppp_size" method="post" onSubmit="javascript:pppppChangeSize()">
        <select name="size" onChange="pppppPriceCompute()"> //onChange="this.form.submit()">
	  {foreach from=$ppppp_array_sizes item=ppppp_row_sizes}	
          <option value="{$ppppp_row_sizes.Price}&{$ppppp_row_sizes.Shipping}">{$ppppp_row_sizes.Size}</option>
	  {/foreach}
        </select>
        <!--//<input type="submit" value="{\'Select size\'|@translate}">-->
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Price\'|@translate}</td>
    <td>
    <form name="ppppp_price">
        <input type="text" size=7 name="price" value="{$ppppp_price} {$ppppp_currency}">
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Shipping fees\'|@translate}</td>
    <td>
    <form name="ppppp_shipping">
        <input type="text" size=7 name="shipping" value="{$ppppp_shipping} {$ppppp_currency}">
    </form>
    </td>
 </tr>
 <tr>
    <td class="label">{\'Promo Code\'|@translate}</td>
    <td>
    <form name="ppppp_promocode_form" onsubmit="pppppPriceCompute()">
        <input type="text" size=20 name="promocode" value="{$ppppp_promocode|@translate}" oninput="pppppPriceCompute()" onchange="fillInPromoText()"><br/>
    </form>
    </td>
 </tr>
  <tr>
    <td class="label">{\'Total price\'|@translate}</td>
    <td>
    <form name="ppppp_total_price">
        <input type="text" size=7 name="total" value="{$ppppp_total} {$ppppp_currency}">
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
     <input type="hidden" name="handling_cart"><!--  value="{$ppppp_fixed_shipping}">--> 
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
</tr>
{/if}
<tr>
   <td colspan=2>
        <form>
        <a href="https://www.paypal.com/us/webapps/mpp/paypal-safety-and-security" target="_blank"><img src="{PPPPP_PATH}include/payment-logos.png" height=48 onLoad="pppppPriceCompute()"></a></br>
        <input type="hidden" onLoad="pppppPriceCompute()">
        </form>
    </td>
</tr>
<tr>
   <td colspan=2>
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
  
  $min_res_tolerance=1.05;
  $IMG_ratio=round(floatval($current_picture['width'])/floatval($current_picture['height']),1);
  $src_size=$current_picture['src_image']->get_size();
  $IMG_Height=floatval($src_size[1]);
  $IMG_Length=floatval($src_size[0]);
  
    
  $template->assign(
  array(
    'F_ACTION'=>PHPWG_ROOT_PATH.'main.inc.php')
  );  

  $template->set_prefilter('picture', 'ppppp_append_form');
  load_language('plugin.lang', PPPPP_PATH);


    
  $queryFilterSize='WHERE 1';
        
  $query='SELECT * FROM '.PPPPP_SIZE_TABLE.' '.$queryFilterSize.' '.@$conf['PayPalShoppingCart_sizes_order_by'].';';
        
  $result = pwg_query($query);
    
  while($row = pwg_db_fetch_assoc($result))
  {
    $template->append('ppppp_array_size',$row);
  }

    
  $query_promocode='SELECT * FROM '.PPPPP_PROMOCODE_TABLE.' '.@$conf['PayPalShoppingCart_promocode_order_by'].';';
  $result_promocode = pwg_query($query_promocode);
  while($row_promocode = pwg_db_fetch_assoc($result_promocode))
  {
    $template->append('ppppp_array_promocode',$row_promocode);
  }
  
  
  $array_currency=array();
  $query_country='SELECT * FROM '.PPPPP_COUNTRY_TABLE.' '.@$conf['PayPalShoppingCart_country_order_by'].';';
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
  
    
  $query_support='SELECT DISTINCT T2.Id AS Id, T2.SupportName, T3.OptionName AS SupportOption1, T4.OptionName AS SupportOption2'.
        ' FROM '.PPPPP_PRICE_TABLE.' T1'.
        ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T2 ON T1.Support = T2.Id'.
        ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T2.SupportOption1 = T3.Id'.
        ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T4 ON T2.SupportOption2 = T4.Id'.
        ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider'.
        ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T6 ON T1.Size=T6.Id'.
        ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T7 ON T6.Ratio=T7.Id'.
        ' WHERE T5.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
        ' AND T7.RatioValue='.$IMG_ratio.
        ' AND T6.Height<'.$IMG_Height.'*'.$min_res_tolerance.'/T1.MinRes*IF(T6.Units="cm", 2.54, IF(T6.Units="ft", 1/12, 1))'.
        ' ORDER BY T2.SupportName, SupportOption1, SupportOption2 ;';
  $result_support = pwg_query($query_support);
  $support_count=0;
  while($row_support = pwg_db_fetch_assoc($result_support))
  {
    $template->append('ppppp_array_support',$row_support);
    if($support_count==0){
        $first_row_support_index=$row_support['Id'];
    }
    $support_count=$support_count+1;
  }

 if($support_count>0){
    $support_found=true;
    if(isset($_GET['Support']))
       {
         $support_Id=$_GET['Support'];
         $query_sizes='SELECT DISTINCT T2.SizeName AS Size, T3.RatioValue, T2.Height AS Height, T2.Length AS Length, T2.Units, T1.Price, T1.Shipping'.
                 ' FROM '.PPPPP_PRICE_TABLE.' T1'.
                 ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size=T2.Id'.
                 ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T3 ON T2.Ratio=T3.Id'.
                 ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T4 ON T1.Provider = T4.Provider'.
                 ' WHERE T3.RatioValue='.$IMG_ratio.
                 ' AND Height<'.$IMG_Height.'*'.$min_res_tolerance.'/T1.MinRes*IF(T2.Units="cm", 2.54, IF(T2.Units="ft", 1/12, 1))'.
                 ' AND T4.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
                 ' AND T1.Support="'.$support_Id."\"".
                 ' ORDER BY Size, Height;';
       } 
    else
       {
         $support_Id=$first_row_support_index;
         $query_sizes='SELECT DISTINCT T2.SizeName AS Size, T3.RatioValue, T2.Height AS Height, T2.Length, T2.Units, T1.Price, T1.Shipping'.
                 ' FROM '.PPPPP_PRICE_TABLE.' T1'.
                 ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size=T2.Id'.
                 ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T3 ON T2.Ratio=T3.Id'.
                 ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T4 ON T1.Provider = T4.Provider'.
                 ' WHERE T3.RatioValue='.$IMG_ratio.
                 ' AND Height<'.$IMG_Height.'*'.$min_res_tolerance.'/T1.MinRes*IF(T2.Units="cm", 2.54, IF(T2.Units="ft", 1/12, 1))'.
                 ' AND T4.Currency= "'.$conf['PayPalShoppingCart']['currency']."\"".
                 ' AND T1.Support="'.$support_Id."\"".
                 ' ORDER BY Size, Height;';    
       }
     $result_sizes = pwg_query($query_sizes);

     while($row_sizes = pwg_db_fetch_assoc($result_sizes ))
     {
       $template->append('ppppp_array_sizes',$row_sizes );
     }
 }
 else{
    $support_found=false;
    $support_Id=0;
 }

 
 if(isset($_GET['PromoCode']))
 {
     $ppppp_promocode=$_GET['PromoCode'];
 }
 else{
     $ppppp_promocode='Insert promo code';
 }

 $country_code=$first_row_country_index;
 
    if(isset($_POST['currency']) and isset($array_currency[ $_POST['currency'] ]))
    {
      $curr=substr($_POST['currency'],0,3);
      $country_code=substr($_POST['currency'],4,3);
      $conf['PayPalShoppingCart']['currency'] = $curr;
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
  $template->assign(
    array(
      'ppppp_support_found' => $support_found,
      'ppppp_promocode' => $ppppp_promocode,
      'ppppp_support_id' => $support_Id,
      'ppppp_country_code' => $country_code,
      'ppppp_fixed_shipping' => $conf['PayPalShoppingCart']['fixed_shipping'],
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

/*function ppppp_append_js($tpl_source, &$smarty){
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
  $conf['PayPalShoppingCart'] = safe_unserialize($conf['PayPalShoppingCart']);
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
