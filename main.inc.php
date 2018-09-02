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
  Copyright (C) 2011 www.queguineur.fr — Tous droits réservés.
  
  Ce programme est un logiciel libre ; vous pouvez le redistribuer ou le
  modifier suivant les termes de la “GNU General Public License” telle que
  publiée par la Free Software Foundation : soit la version 3 de cette
  licence, soit (à votre gré) toute version ultérieure.
  
  Ce programme est distribué dans l’espoir qu’il vous sera utile, mais SANS
  AUCUNE GARANTIE : sans même la garantie implicite de COMMERCIALISABILITÉ
  ni d’ADÉQUATION À UN OBJECTIF PARTICULIER. Consultez la Licence Générale
  Publique GNU pour plus de détails.
  
  Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec
  ce programme ; si ce n’est pas le cas, consultez :
  <http://www.gnu.org/licenses/>.
*/
/*
Historique
1.0.0   10/02/2011
Version initiale
		
1.0.1   10/02/2011
Ajout du Plugin URI pour permettre les mises à jours
Traduction en Anglais du Plugin Name et du nom du répertoire
        
1.0.2   10/02/2011
Correction du problème de compatibilité avec exif view (double affichage des boutons)
	
1.0.3   15/02/2011
Add lv_LV (Latvian) thanks to Aivars Baldone

1.0.4   17/02/2011
Add de_DE and it_IT (par Sugar888)

1.0.5   27/02/2011
Correction pb compatibilité avec certains thèmes
Déplacement des boutons PayPal en début de table info

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
define('PPPPP_SUPPORT_TABLE', $prefixeTable.'ppppp_support');
define('PPPPP_PROMOCODE_TABLE', $prefixeTable.'ppppp_promocode');
define('PPPPP_VERSION', '2.7.c');


function ppppp_append_form($tpl_source, &$smarty)
{
  global $theme;
    

  $pattern = '#<.*\"infoTable\".*>#';
  $replacement = '
  <tr>
   <td class="label">{\'Buy this picture\'|@translate}</td>
   <td>
    <form name="ppppp_form" target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" onSubmit="javascript:pppppValid()">
     <!--//<form name="ppppp_form" target="paypal" action="mailto:webmaster@daedalum.org" method="post" onSubmit="javascript:pppppValid()">-->
     <input type="hidden" name="add" value="1">
     <input type="hidden" name="cmd" value="_cart">
     <input type="hidden" name="business" value="{$ppppp_e_mail}">
     <input type="hidden" name="item_name">
     <input type="hidden" name="amount">
     <input type="hidden" name="no_shipping" value="2"><!-- shipping address mandatory -->
     <input type="hidden" name="handling_cart"><!--  value="{$ppppp_fixed_shipping}">--> 
     <input type="hidden" name="currency_code" value="{$ppppp_currency}">
     <select name="support_factor" onChange="pppppPriceCompute()">
	  {foreach from=$ppppp_array_support item=ppppp_row_support}
      <option value="{$ppppp_row_support.factor}">{$ppppp_row_support.support|@translate}</option>
	  {/foreach}
      </select>
      <select name="size" onChange="pppppPriceCompute()">
	  {foreach from=$ppppp_array_size item=ppppp_row_size}	
      <option value="{$ppppp_row_size.price}">{$ppppp_row_size.size}</option>
	  {/foreach}
      </select>
      <input type="text" size=7 name="price" value="{$ppppp_price} {$ppppp_currency}">
     <input type="submit" value="{\'Add to cart\'|@translate}">
    </form>
    </td>
   <td>
    <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <!--//<form target="paypal" action="mailto:webmaster@daedalum.org" method="post">-->
     <input type="hidden" name="cmd" value="_cart">
     <input type="hidden" name="business" value="{$ppppp_e_mail}">
     <input type="hidden" name="display" value="1">
     <input type="hidden" name="no_shipping" value="2">
     <input type=submit value="{\'View Shopping Cart\'|@translate}">
    </form>
   </td>
  </tr>
  <tr>
    <td></td>
    <td>
    <form name="ppppp_promocode_form">
        <input type="text" size=20 name="promocode" value="{\'Insert promo code\'|@translate}" oninput="pppppPriceCompute()" onchange="fillInPromoText()">

    </form>
    </td>
  </tr>
  
  <p id="demo"></p>
 
 {literal}
 <script type="text/javascript">
 function pppppValid(){
  var amount=pppppPriceCompute();
  var size=document.ppppp_form.size;
  var price=document.ppppp_form.price;
  var ppppp_price=price.value.slice(0,price.value.length-4);
  var support_type=document.ppppp_form.support_factor;
  var selectedSize=size[size.selectedIndex];
  var selectedSupport=support_type[support_type.selectedIndex];
  document.ppppp_form.amount.value=ppppp_price;
  //document.ppppp_form.item_name.value="Photo \"{/literal}{$current.TITLE}\", File {$INFO_FILE}, Ref {$COMMENT_IMG}, {\'Size\'|@translate} : {literal} "+selectedSupport.text+ " "+selectedSize.text;
  document.ppppp_form.item_name.value="{/literal}Ref {$COMMENT_IMG}, {\'Size\'|@translate} : {literal} "+selectedSize.text+ " "+selectedSupport.text+" Photo \"{/literal}{$current.TITLE}\", {$INFO_FILE}{literal} ";
  document.ppppp_form.amount.value=ppppp_price;
  document.ppppp_form.handling_cart.value=ppppp_shipping;
  }
  
  function pppppPriceCompute(){
    pppppGetPromoCode();
    var price=document.ppppp_form.price;
    var size=document.ppppp_form.size;
    var support_type=document.ppppp_form.support_factor;
    var selectedSize=size[size.selectedIndex];
    var selectedSupport=support_type[support_type.selectedIndex];
    var currency = price.value.slice(-4,price.value.length);
    var ppppp_price=price.value.slice(0,price.value.length-4);
	var ppppp_raw_price= selectedSize.value * selectedSupport.value;
    ppppp_shipping={/literal}{$ppppp_fixed_shipping}{literal};
	var ppppp_price_class=ppppp_raw_price % 10;
	if (ppppp_price_class<5)
	{
		ppppp_price=Math.round(ppppp_raw_price+5-ppppp_price_class);
	}
	else
	{
        ppppp_price=Math.round(ppppp_raw_price+9-ppppp_price_class);
	}
    ppppp_price=Math.round(ppppp_price*(100-reduc_rel)/100-reduc_abs);
    ppppp_shipping=Math.round(ppppp_shipping-reduc_ship);
    document.ppppp_form.price.value=ppppp_price + currency;
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
    
  $template->set_prefilter('picture', 'ppppp_append_form');
  load_language('plugin.lang', PPPPP_PATH);

    
 //   echo 'GF SQ 52 31 41 ', strpos($IMG_name, '_GF'), ' ', strpos($IMG_name, '_SQ'), ' ',strpos($IMG_name, '_52'), ' ', strpos($IMG_name, '_31'),' ', strpos($IMG_name, '_41'), '</br>';
    
  $queryFilterSize='WHERE 1';

    if (strpos($IMG_name, '_SQ')!==false)
    {
       if (strpos($IMG_name, '_GF')!==false)
        {
            $queryFilterSize="WHERE `SQ`=1";
        }
        else
        {
            $queryFilterSize="WHERE (`SQ`=1 AND `GF`=0)";
        }
    }
    elseif (strpos($IMG_name, '_52')!==false)
    {
        if (strpos($IMG_name, '_GF')!==false)
        {
            $queryFilterSize="WHERE `Pano52`=1";
        }
        else
        {
            $queryFilterSize="WHERE (`Pano52`=1 AND `GF`=0)";
        }
    }
    elseif (strpos($IMG_name, '_31')!==false)
    {
        if (strpos($IMG_name, '_GF')!==false)
        {
            $queryFilterSize="WHERE `Pano31`=1";
        }
        else
        {
            $queryFilterSize="WHERE (`Pano31`=1 AND `GF`=0)";
        }
    }
    elseif (strpos($IMG_name, '_41')!==false)
    {
        if (strpos($IMG_name, '_GF')!==false)
        {
            $queryFilterSize="WHERE `Pano41`=1";
        }
        else
        {
            $queryFilterSize="WHERE (`Pano41`=1 AND `GF`=0)";
        }
    }
    else
    {
        if (strpos($IMG_name, '_GF')!==false)
        {
            $queryFilterSize="WHERE (`SQ`=0 AND `Pano52`=0 AND `Pano31`=0 AND `Pano41`=0)";
        }
        else
        {
            $queryFilterSize="WHERE (`SQ`=0 AND `Pano52`=0 AND `Pano31`=0  AND `Pano41`=0 AND `GF`=0)";
        }
    }
        
  $query='SELECT * FROM '.PPPPP_SIZE_TABLE.' '.$queryFilterSize.' '.@$conf['PayPalShoppingCart_sizes_order_by'].';';
        
  $result = pwg_query($query);
    
  while($row = pwg_db_fetch_assoc($result))
  {
    $template->append('ppppp_array_size',$row);
  }

  $query_support='SELECT * FROM '.PPPPP_SUPPORT_TABLE.' '.@$conf['PayPalShoppingCart_supports_order_by'].';';
  $result_support = pwg_query($query_support);
  while($row_support = pwg_db_fetch_assoc($result_support))
  {
    $template->append('ppppp_array_support',$row_support);
  }
    
  $query_promocode='SELECT * FROM '.PPPPP_PROMOCODE_TABLE.' '.@$conf['PayPalShoppingCart_supports_order_by'].';';
  $result_promocode = pwg_query($query_promocode);
  while($row_promocode = pwg_db_fetch_assoc($result_promocode))
  {
    $template->append('ppppp_array_promocode',$row_promocode);
  }

    
  $template->assign(
    array(
      'ppppp_fixed_shipping' => $conf['PayPalShoppingCart']['fixed_shipping'],
      'ppppp_currency' => $conf['PayPalShoppingCart']['currency'],
 //     'ppppp_e_mail' => get_webmaster_mail_address(),
     'ppppp_e_mail' => 'online.shop@daedalum.org',
      'ppppp_price' => 15,
     )
    );
}


//add_event_handler('loc_begin_picture', 'ppppp_picture_handler'); //ancien handler ne permettait pas de recuperer le nom de l'image


add_event_handler('render_element_content', 'ppppp_picture_handler', EVENT_HANDLER_PRIORITY_NEUTRAL-10);


// Decommenter les lignes ci dessous pour faire apparaitre un lien "View shopping cart" dans le menu de gauche.
// Désactivé car ne marchait pas !

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
