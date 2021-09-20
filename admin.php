<?php
/*
  Plugin Panier PayPal Pour Piwigo
  Copyright (C) 2011 www.queguineur.fr — Tous droits réservés.
  
  Ce programme est un logiciel libre ; vous pouvez le redistribuer ou le
  modifier suivant les termes de la “GNU General Public License” telle que
  publiée par la Free Software Foundation : soit la version 3 de cette
  licence, soit (à votre gré) toute version ultérieure.
  
  Ce programme est distribué dans l'espoir qu'il vous sera utile, mais SANS
  AUCUNE GARANTIE : sans même la garantie implicite de COMMERCIALISABILITÉ
  ni d'ADÉQUATION À UN OBJECTIF PARTICULIER. Consultez la Licence Générale
  Publique GNU pour plus de détails.
  
  Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec
  ce programme ; si ce n'est pas le cas, consultez :
  <http://www.gnu.org/licenses/>.
*/
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
global $template;
include_once(PHPWG_ROOT_PATH .'admin/include/tabsheet.class.php');
include_once('FB_catalog.php');
load_language('plugin.lang', PPPPP_PATH);
$my_base_url = get_admin_plugin_menu_link(__FILE__);


//Fonction pour génération catalogue Facebook
$min_res_tolerance=1.025;

// onglets
if (!isset($_GET['tab']))
    $page['tab'] = 'currency';
else
    $page['tab'] = $_GET['tab'];

$tabsheet = new tabsheet();
$tabsheet->add('currency',
               l10n('Currency'),
               $my_base_url.'&amp;tab=currency');
$tabsheet->add('settings',
               l10n('Settings'),
               $my_base_url.'&amp;tab=settings');
$tabsheet->add('albums', l10n('Albums'), $my_base_url.'&amp;tab=albums');
$tabsheet->add('provider',
               l10n('Providers'),
               $my_base_url.'&amp;tab=provider');
$tabsheet->add('country',
               l10n('Countries'),
               $my_base_url.'&amp;tab=country');
$tabsheet->add('material',
               l10n('Materials'),
               $my_base_url.'&amp;tab=material');
$tabsheet->add('support_option',
               l10n('Support Options'),
               $my_base_url.'&amp;tab=support_option');
$tabsheet->add('support',
               l10n('Supports'),
               $my_base_url.'&amp;tab=support');
$tabsheet->add('ratio',
               l10n('Ratios'),
               $my_base_url.'&amp;tab=ratio');
$tabsheet->add('size',
               l10n('Sizes'),
               $my_base_url.'&amp;tab=size');
$tabsheet->add('price',
               l10n('Prices'),
               $my_base_url.'&amp;tab=price');
$tabsheet->add('code',
               l10n('Promo codes'),
               $my_base_url.'&amp;tab=code');			   
$tabsheet->add('FB_catalog',
               l10n('Facebook catalog'),
               $my_base_url.'&amp;tab=FB_catalog');	
$tabsheet->select($page['tab']);
$tabsheet->assign();

switch($page['tab'])
{

    case 'settings':
    
    if (isset($_POST['PayPalAccountEmail'])and filter_var($_POST['PayPalAccountEmail'], FILTER_VALIDATE_EMAIL))
    {
      $conf['PayPalShoppingCart']['PayPalAccount'] = $_POST['PayPalAccountEmail'];
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);
      
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    elseif (isset($_POST['PayPalAccountEmail'])and !filter_var($_POST['PayPalAccountEmail'], FILTER_VALIDATE_EMAIL))
    {  
      $page['infos'][] = l10n('Invalid account. Please make sure you have entered a valid email address.');
    }
    
    $template->assign('ppppp_account', $conf['PayPalShoppingCart']['PayPalAccount']);
    break;
    
    case 'currency':
    
    $array_currency = array(
      'AUD'=>'Australian Dollar',
      'BRL'=>'Brazilian Real',
      'CAD'=>'Canadian Dollar',
      'CZK'=>'Czech Koruna',
      'DKK'=>'Danish Krone',
      'EUR'=>'Euro',
      'HKD'=>'Hong Kong Dollar',
      'HUF'=>'Hungarian Forint',
      'ILS'=>'Israeli New Sheqel',
      'JPY'=>'Japanese Yen',
      'MYR'=>'Malaysian Ringgit',
      'MXN'=>'Mexican Peso',
      'NOK'=>'Norwegian Krone',
      'NZD'=>'New Zealand Dollar',
      'PHP'=>'Philippine Peso',
      'PLN'=>'Polish Zloty',
      'GBP'=>'Pound Sterling',
      'SGD'=>'Singapore Dollar',
      'SEK'=>'Swedish Krona',
      'CHF'=>'Swiss Franc',
      'TWD'=>'Taiwan New Dollar',
      'THB'=>'Thai Baht',
      'USD'=>'U.S. Dollar'
      );
  
    if(isset($_POST['currency']) and isset($array_currency[ $_POST['currency'] ]))
    {
      $conf['PayPalShoppingCart']['currency'] = $_POST['currency'];
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);
      
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
 
    $template->assign(
      array(
        'ppppp_currency' => $conf['PayPalShoppingCart']['currency'],
        'ppppp_array_currency' => $array_currency,
        )
      );
    
    break;
    
   case 'albums' :

     if (isset($_POST['apply_to_albums']) and in_array($_POST['apply_to_albums'], array('all', 'list')))
     {
       $conf['PayPalShoppingCart']['apply_to_albums'] = $_POST['apply_to_albums'];
       conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);

       if ($_POST['apply_to_albums'] == 'list')
       {
         check_input_parameter('albums', $_POST, true, PATTERN_ID);

         if (empty($_POST['albums']))
         {
           $_POST['albums'][] = -1;
         }
       
         $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET paypal_active = \'false\'
  WHERE id NOT IN ('.implode(',', $_POST['albums']).')
;';
         pwg_query($query);

         $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET paypal_active = \'true\'
  WHERE id IN ('.implode(',', $_POST['albums']).')
;';
         pwg_query($query);
       }

       $page['infos'][] = l10n('Your configuration settings are saved');
     }
   
     // associate to albums
     $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE paypal_active = \'true\'
;';
     $paypal_albums = array_from_query($query, 'id');

     $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
     display_select_cat_wrapper($query, $paypal_albums, 'album_options');

     $template->assign('apply_to_albums', $conf['PayPalShoppingCart']['apply_to_albums']);

     break;
  
 
  case 'country':
    
      $array_currency = array(
      'AUD'=>'Australian Dollar',
      'BRL'=>'Brazilian Real',
      'CAD'=>'Canadian Dollar',
      'CZK'=>'Czech Koruna',
      'DKK'=>'Danish Krone',
      'EUR'=>'Euro',
      'HKD'=>'Hong Kong Dollar',
      'HUF'=>'Hungarian Forint',
      'ILS'=>'Israeli New Sheqel',
      'JPY'=>'Japanese Yen',
      'MYR'=>'Malaysian Ringgit',
      'MXN'=>'Mexican Peso',
      'NOK'=>'Norwegian Krone',
      'NZD'=>'New Zealand Dollar',
      'PHP'=>'Philippine Peso',
      'PLN'=>'Polish Zloty',
      'GBP'=>'Pound Sterling',
      'SGD'=>'Singapore Dollar',
      'SEK'=>'Swedish Krona',
      'CHF'=>'Swiss Franc',
      'TWD'=>'Taiwan New Dollar',
      'THB'=>'Thai Baht',
      'USD'=>'U.S. Dollar'
      );

      $template->assign(
      array(
        'ppppp_array_currency' => $array_currency,
        )
      );

      if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_COUNTRY_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['CountryName']) and isset($_POST['CountryCode']) and isset($_POST['Currency']) and isset($_POST['Provider']))
    {
      single_insert(
        PPPPP_COUNTRY_TABLE,
        array(
          'CountryName' => pwg_db_real_escape_string($_POST['CountryName']),
          'CountryCode' => pwg_db_real_escape_string($_POST['CountryCode']),
          'Currency' => pwg_db_real_escape_string($_POST['Currency']),
          'Provider' => pwg_db_real_escape_string($_POST['Provider']),
          )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT T1.Id, T1.CountryName, T1.CountryCode, T1.Currency, T2.Name as Name FROM '.PPPPP_COUNTRY_TABLE.' T1 LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T2 ON T1.Provider=T2.Id ORDER BY T1.Provider, T1.CountryName;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_country',$row);
    }
    

    $query='SELECT * FROM '.PPPPP_PROVIDER_TABLE.' ORDER BY Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider',$row);
    }        

    break;     

    case 'provider':
    
      $array_currency = array(
      'AUD'=>'Australian Dollar',
      'BRL'=>'Brazilian Real',
      'CAD'=>'Canadian Dollar',
      'CZK'=>'Czech Koruna',
      'DKK'=>'Danish Krone',
      'EUR'=>'Euro',
      'HKD'=>'Hong Kong Dollar',
      'HUF'=>'Hungarian Forint',
      'ILS'=>'Israeli New Sheqel',
      'JPY'=>'Japanese Yen',
      'MYR'=>'Malaysian Ringgit',
      'MXN'=>'Mexican Peso',
      'NOK'=>'Norwegian Krone',
      'NZD'=>'New Zealand Dollar',
      'PHP'=>'Philippine Peso',
      'PLN'=>'Polish Zloty',
      'GBP'=>'Pound Sterling',
      'SGD'=>'Singapore Dollar',
      'SEK'=>'Swedish Krona',
      'CHF'=>'Swiss Franc',
      'TWD'=>'Taiwan New Dollar',
      'THB'=>'Thai Baht',
      'USD'=>'U.S. Dollar'
      );

      $template->assign(
      array(
        'ppppp_array_currency' => $array_currency,
        )
      );

      if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_PROVIDER_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['ProviderName']) and isset($_POST['ProviderUrl']) and isset($_POST['Currency']))
    {
      single_insert(
        PPPPP_PROVIDER_TABLE,
        array(
          'Name' => pwg_db_real_escape_string($_POST['ProviderName']),
          'URL' => pwg_db_real_escape_string($_POST['ProviderUrl']),
          'Currency' => pwg_db_real_escape_string($_POST['Currency']),
          )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
 
    $query='SELECT * FROM '.PPPPP_PROVIDER_TABLE.' ORDER BY Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider',$row);
    }
    
    break;     

    case 'support':
    
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_SUPPORT_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['support']))
    {
      single_insert(
        PPPPP_SUPPORT_TABLE,
        array(
          'SupportMaterial' => pwg_db_real_escape_string($_POST['support']),
          'SupportOption1' => pwg_db_real_escape_string($_POST['option1']),
          'SupportOption2' => pwg_db_real_escape_string($_POST['option2']),
          )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_OPTION_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_support_options',$row);
    }

    $query='SELECT * FROM '.PPPPP_MATERIAL_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_materials',$row);
    }

    
    $query='SELECT DISTINCT T1.Id AS Id, T4.Material AS SupportMaterial, T2.OptionName AS SupportOption1, T3.OptionName AS SupportOption2'.
            ' FROM '.PPPPP_SUPPORT_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T4 ON T1.SupportMaterial = T4.Id '.
            ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T2 ON T1.SupportOption1 = T2.Id '.
            ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T1.SupportOption2 = T3.Id '.
            ' ORDER BY T1.SupportMaterial, SupportOption1, SupportOption2 ;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_support',$row);
    }
    
    break;

     case 'support_option':
    
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_OPTION_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['OptionName']))
    {
      single_insert(
        PPPPP_OPTION_TABLE,
        array(
          'OptionName' => pwg_db_real_escape_string($_POST['OptionName']),
            )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_OPTION_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_support_options',$row);
    }
    
    break;
  
     case 'material':
    
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_MATERIAL_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['Material']))
    {
      single_insert(
        PPPPP_MATERIAL_TABLE,
        array(
          'Material' => pwg_db_real_escape_string($_POST['Material']),
            )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_MATERIAL_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_materials',$row);
    }
    
    break;
  
    case 'size':
    
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_SIZES_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['SizeName']) and isset($_POST['AltSizeName']) and isset($_POST['Ratio']) and isset($_POST['Height_cm']) and isset($_POST['Width_cm']) and isset($_POST['Height_in']) and isset($_POST['Width_in']) and isset($_POST['MinRes']))
    {      
      single_insert(
        PPPPP_SIZES_TABLE,
        array(
          'SizeName' => pwg_db_real_escape_string($_POST['SizeName']),
          'AltSizeName' => pwg_db_real_escape_string($_POST['AltSizeName']),
          'Ratio' => pwg_db_real_escape_string($_POST['Ratio']),
          'Height_cm' => pwg_db_real_escape_string($_POST['Height_cm']),
          'Width_cm' => pwg_db_real_escape_string($_POST['Width_cm']),
          'Height_in' => pwg_db_real_escape_string($_POST['Height_in']),
          'Width_in' => pwg_db_real_escape_string($_POST['Width_in']),
          'MinRes' => pwg_db_real_escape_string($_POST['MinRes']),
            )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT DISTINCT T1.Id AS Id, SizeName, AltSizeName, T2.RatioName AS Ratio, Width_cm, Height_cm,  Width_in, Height_in, MinRes'.
            ' FROM '.PPPPP_SIZES_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T2'.
            ' ON T1.Ratio=T2.Id'.
            ' ORDER BY Ratio, Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_sizes',$row);
    }
    
    $query='SELECT * FROM '.PPPPP_RATIO_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_ratio',$row);
    }
 
    break;
  
    case 'ratio':
    
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_RATIO_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['RatioName']) and isset($_POST['RatioValue']))
    {
      single_insert(
        PPPPP_RATIO_TABLE,
        array(
          'RatioName' => pwg_db_real_escape_string($_POST['RatioName']),
          'RatioValue' => pwg_db_real_escape_string($_POST['RatioValue']),
            )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_RATIO_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_ratio',$row);
    }
    
    break;
        
  case 'code':
    
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_PROMOCODE_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['code']) and isset($_POST['reduc_rel']))
    {
      single_insert(
        PPPPP_PROMOCODE_TABLE,
        array(
          'code' => pwg_db_real_escape_string($_POST['code']),
          'reduc_rel' => pwg_db_real_escape_string($_POST['reduc_rel']),
          'reduc_abs' => pwg_db_real_escape_string($_POST['reduc_abs']),
          'reduc_ship' => pwg_db_real_escape_string($_POST['reduc_ship']),
      )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_PROMOCODE_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_promocode',$row);
    }
    
    break;

      case 'price':
    
         
    $where_clause =' WHERE 1';      
          
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_PRICE_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['price']) and isset($_POST['shipping']) and isset($_POST['support']) and isset($_POST['provider']) and isset($_POST['size']))
    {
      single_insert(
        PPPPP_PRICE_TABLE,
        array(
          'Provider' => pwg_db_real_escape_string($_POST['provider']),
          'Size' => pwg_db_real_escape_string($_POST['size']),
          'Support' => pwg_db_real_escape_string($_POST['support']),
          'Price' => pwg_db_real_escape_string($_POST['price']),
          'Shipping' => pwg_db_real_escape_string($_POST['shipping']),
            )
        );

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['filter']))
    {
      $template->assign('ppppp_material_filt',$_POST['filtMaterial']);
      $template->assign('ppppp_option1_filt',$_POST['filtOption1']);
      $template->assign('ppppp_option2_filt',$_POST['filtOption2']);
      $template->assign('ppppp_ratio_filt',$_POST['filtRatio']);
      $template->assign('ppppp_size_filt',$_POST['filtSize']);
      $template->assign('ppppp_altsize_filt',$_POST['filtAltSize']);
      $template->assign('ppppp_height_filt',$_POST['filtHeight']);
      $template->assign('ppppp_width_filt',$_POST['filtWidth']);
      $template->assign('ppppp_provider_filt',$_POST['filtProvider']);
      
     $clause_count=false; 
     $where_clause =' WHERE ';      
     if($_POST['filtMaterial']!='*'){ $where_clause.='T9.Id = '.$_POST['filtMaterial']; $clause_count=true;}
     if($_POST['filtOption1']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T3.Id = '.$_POST['filtOption1']; $clause_count=true;}
     if($_POST['filtOption2']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T4.Id = '.$_POST['filtOption2']; $clause_count=true;}     
     if($_POST['filtSize']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.SizeName = "'.$_POST['filtSize'].'"'; $clause_count=true;}     
     if($_POST['filtAltSize']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.AltSizeName = "'.$_POST['filtAltSize'].'"'; $clause_count=true;}     
     if($_POST['filtRatio']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T6.Id = '.$_POST['filtRatio']; $clause_count=true;}     
     if($_POST['filtHeight']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.Height_cm = '.$_POST['filtHeight']; $clause_count=true;}     
     if($_POST['filtWidth']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.Width_cm = '.$_POST['filtWidth']; $clause_count=true;}     
     if($_POST['filtProvider']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T8.Id = '.$_POST['filtProvider']; $clause_count=true;}     
     if(!$clause_count){$where_clause.='1';}
      
    }
    else if (isset($_POST['reset']))
    {
      $template->assign('ppppp_material_filt','*');
      $template->assign('ppppp_option1_filt','*');
      $template->assign('ppppp_option2_filt','*');
      $template->assign('ppppp_ratio_filt','*');
      $template->assign('ppppp_size_filt','*');
      $template->assign('ppppp_altsize_filt','*');
      $template->assign('ppppp_height_filt','*');
      $template->assign('ppppp_width_filt','*');
      $template->assign('ppppp_provider_filt','*');    

      $where_clause =' WHERE 1';        
    }    
//    echo('<pre>'.$where_clause.'</pre>' );
    
    $query='SELECT DISTINCT T1.Id AS Id, T2.SupportMaterial AS Support, T3.OptionName AS SupportOption1, T4.OptionName AS SupportOption2,'.
            ' T5.SizeName AS Size, T5.AltSizeName AS AltSize, T6.RatioName AS Ratio, T5.Height_cm AS Height, T5.Width_cm AS Width, '.
            ' T8.Name AS Provider, T1.Price, T1.Shipping, T7.Currency AS Currency, T9.Material as SupportMaterial'.
            ' FROM '.PPPPP_PRICE_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T2 ON T1.Support = T2.Id'.
            ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T9 ON T2.SupportMaterial = T9.Id'.
            ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T2.SupportOption1 = T3.Id'.
            ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T4 ON T2.SupportOption2 = T4.Id'.
            ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T5 ON T1.Size = T5.Id'.
            ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T6 ON T5.Ratio = T6.Id'.
            ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T7 ON T1.Provider = T7.Provider'.
            ' LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T8 ON T1.Provider = T8.Id'.
            $where_clause.
            ' ORDER BY Provider, Support, Size;';
//    echo('<pre>'.$query.'</pre>' );
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_price',$row);
    }
    
    $query='SELECT DISTINCT T1.Id AS Id, SizeName, AltSizeName, T2.RatioName AS Ratio, Width_cm, Height_cm'.
            ' FROM '.PPPPP_SIZES_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T2'.
            ' ON T1.Ratio=T2.Id'.
            ' ORDER BY Ratio, Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_sizes',$row);
    }

    $query='SELECT DISTINCT T1.Id AS Id, T4.Material AS SupportMaterial, T2.OptionName AS SupportOption1, T3.OptionName AS SupportOption2'.
            ' FROM '.PPPPP_SUPPORT_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T4 ON T1.SupportMaterial = T4.Id '.
            ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T2 ON T1.SupportOption1 = T2.Id '.
            ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T1.SupportOption2 = T3.Id '.
            ' ORDER BY T1.SupportMaterial, SupportOption1, SupportOption2 ;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_support',$row);
    }
    
    $query='SELECT DISTINCT T2.Name AS ProviderName FROM '.PPPPP_COUNTRY_TABLE.' T1 LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T2 ON T1.Provider = T2.Id ORDER BY ProviderName;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_country',$row);
    }
    
    $query='SELECT * FROM '.PPPPP_PROVIDER_TABLE.' ORDER BY Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider',$row);
    }

// QUERIES pour alimenter les selecteurs de filtres

    $query='SELECT DISTINCT Id, Material FROM '.PPPPP_MATERIAL_TABLE.' ORDER BY Material;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_material_filt',$row);
    }

    $query='SELECT DISTINCT T2.Id, T2.OptionName FROM '.PPPPP_SUPPORT_TABLE.' T1 LEFT JOIN '.PPPPP_OPTION_TABLE.' T2 ON T1.SupportOption1 = T2.Id ORDER BY T2.Id;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_option1_filt',$row);
    }

    $query='SELECT DISTINCT T2.Id, T2.OptionName FROM '.PPPPP_SUPPORT_TABLE.' T1 LEFT JOIN '.PPPPP_OPTION_TABLE.' T2 ON T1.SupportOption2 = T2.Id ORDER BY T2.Id;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_option2_filt',$row);
    }

    
    $query='SELECT DISTINCT Id, RatioName FROM '.PPPPP_RATIO_TABLE.' ORDER BY RatioValue;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_ratio_filt',$row);
    }

    
    $query='SELECT DISTINCT SizeName FROM '.PPPPP_SIZES_TABLE.' ORDER BY Width_cm, Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_size_filt',$row);
    }
    
    $query='SELECT DISTINCT AltSizeName FROM '.PPPPP_SIZES_TABLE.' ORDER BY Width_cm, Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_altsize_filt',$row);
    }
 
    $query='SELECT DISTINCT Height_cm FROM '.PPPPP_SIZES_TABLE.' ORDER BY Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_height_filt',$row);
    }
 
    $query='SELECT DISTINCT Width_cm FROM '.PPPPP_SIZES_TABLE.' ORDER BY Width_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_width_filt',$row);
    }    
    
    $query='SELECT DISTINCT Id, Name FROM '.PPPPP_PROVIDER_TABLE.' ORDER BY Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider_filt',$row);
    }
    
    
    
    break;

  case 'FB_catalog':
    
    if (isset($_POST['Brand']) and isset($_POST['GoogleId']) and is_numeric($_POST['GoogleId']) and isset($_POST['FBId']) and is_numeric($_POST['FBId']))
    {
      $conf['PayPalShoppingCart']['Brand'] = $_POST['Brand'];
      $conf['PayPalShoppingCart']['GoogleId'] = $_POST['GoogleId'];
      $conf['PayPalShoppingCart']['FBId'] = $_POST['FBId'];
      $conf['PayPalShoppingCart']['Ref_country'] = $_POST['Ref_country'];
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);
      
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    if (isset($conf['PayPalShoppingCart']['Brand']) and isset($conf['PayPalShoppingCart']['GoogleId']) and isset($conf['PayPalShoppingCart']['FBId'])  and isset($conf['PayPalShoppingCart']['Ref_country']))
    {
        $template->assign('ppppp_fb_brand', $conf['PayPalShoppingCart']['Brand']);
        $template->assign('ppppp_fb_googleId', $conf['PayPalShoppingCart']['GoogleId']);
        $template->assign('ppppp_fb_fbId', $conf['PayPalShoppingCart']['FBId']);
        $template->assign('ppppp_fb_ref_country', $conf['PayPalShoppingCart']['Ref_country']);
    }
    
    $query='SELECT T1.Id AS Id, T1.CountryName, T1.CountryCode, T1.Currency, T2.Name AS SupplierName FROM '.PPPPP_COUNTRY_TABLE.' T1 LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T2 ON T1.Provider=T2.Id ORDER BY T1.CountryName;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_country',$row);
    }
    
    if ( isset($_POST['submit']) )
    {
     //echo('<pre>'.var_export($_POST,true).'</pre>' );

    if ( $_POST['filename'] != '' )
      $filenameBasis = $_POST['filename'];
      $filename = $_POST['filename'].'_'.$_POST['catalog_country'].'.xml';
      
    set_make_full_url();

      if (isset($_POST['catalog_country']))
      {
          $ref_cat=($_POST['catalog_country']==$conf['PayPalShoppingCart']['Ref_country']);
          $countryCode = $_POST['catalog_country'];
      }
      else
      {
          $ref_cat=true;
          $countryCode = $conf['PayPalShoppingCart']['Ref_country'];
      }
      
      switch($countryCode){
          case 'FR':
              $XMLlang = array(
                    'title' => 'Catalogue en-ligne de la boutique Daedalum Photos - France',
                    'description' => 'Catalogue en-ligne de la boutique Daedalum Photos - France',
                    'support_poster' => 'Tirage poster sur papier photo mat ou brillant. ',
                    'support_canvas' => 'Tirage sur toile tendue sur cadre bois. ',
                    'support_dibond' => 'Tirage sur support aluminium Dibond®, avec option d\'impression directe, d\'impression sur papier photo ou rendu alu brossé. ',
                    'size0' => 'Dimensions : ',
                    'size1' => 'Dimensions disponibles de ',
                    'size2' => ' à ',
                    'size3' => ' de largeur.',
                    'units' => 'cm',
                  );
              break;
          case 'US':
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop - United States',
                    'description' => 'Online catalog for Daedalum Photos online shop - United States',
                    'support_poster' => 'Poster print on Fuji photo paper with a matte, glossy or silky lamination. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate, with optional direct print, print on Fuji photo paper or brushed aluminium finish. ',
                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'in',
                  );
              break;
          case 'UK':
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop - United Kingdom',
                    'description' => 'Online catalog for Daedalum Photos online shop - United Kingdom',
                    'support_poster' => 'Poster print on Fuji photo paper with a matte, glossy or silky lamination. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate, with optional direct print, print on Fuji photo paper or brushed aluminium finish. ',
                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
                  );
              break;
          case 'CA':
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop - Canada',
                    'description' => 'Online catalog for Daedalum Photos online shop - Canada',
                    'support_poster' => 'Poster print on Fuji photo paper with a matte, glossy or silky lamination. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate, with optional direct print, print on Fuji photo paper or brushed aluminium finish. ',
                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
                  );
              break;
          default:
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop',
                    'description' => 'Online catalog for Daedalum Photos online shop',
                    'support_poster' => 'Poster print on Fuji photo paper. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond&® aluminum plate. ',
                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
                  );
              break;
          }
      
 
    $query ='SELECT T1.Price AS price, T1.Shipping AS shipping, T5.Currency AS currency, T10.name AS title, T10.comment AS item, T10.file AS file, MIN(T2.Width_cm) AS minSize_cm, MAX(T2.Width_cm) AS maxSize_cm,'.
            ' MIN(T2.Width_in) AS minSize_in, MAX(T2.Width_in) AS maxSize_in, T10.path, T7.Material AS item_option, T10.Id AS imageId, T5.CountryCode AS countryISOcode, T12.Id AS categoryId, T9.RatioValue as Ratio, '.
            ' T12.name AS categoryName, T12.permalink as categoryPL '.
           'FROM '.PPPPP_PRICE_TABLE.' T1 '.
           'CROSS JOIN '.IMAGES_TABLE.' T10 '.
           'LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id '.
           'LEFT JOIN '.PPPPP_RATIO_TABLE.' T9 ON T2.Ratio = T9.Id '.
           'LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T6 ON T1.Support = T6.Id '.
           'LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T7 ON T6.SupportMaterial = T7.Id '.
           'LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T6.SupportOption1 = T3.Id '.
           'LEFT JOIN '.PPPPP_OPTION_TABLE.' T8 ON T6.SupportOption2 = T8.Id '.
           'LEFT JOIN '.PPPPP_RATIO_TABLE.' T4 ON T2.Ratio = T4.Id '.
           'LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider '.
           'LEFT JOIN '.IMAGE_CATEGORY_TABLE.' T11 ON T10.Id = T11.image_Id '.
           'LEFT JOIN '.CATEGORIES_TABLE.' T12 ON T11.category_id = T12.Id '.
           'WHERE T4.RatioValue= IF(T10.width>T10.height, ROUND(T10.width/T10.height, 1), ROUND(T10.height/T10.width, 1)) '.
           'AND T2.Height_in<T10.height*'.$min_res_tolerance.'/T2.MinRes '.
           'AND T5.CountryCode = "'.$countryCode.'" '.
           'AND T12.status = "public" '.
           'AND T12.visible = "true" '.
           'AND T12.paypal_active = TRUE '.
           'AND ISNULL(T10.comment) = 0 '.
           'GROUP BY Item, T7.Material '.
           'ORDER BY Item, T1.Price, T7.Id, T3.Id, T8.Id';
  //echo '<pre>'; print_r($query); echo '</pre>';
      $result = pwg_query($query);
      
      
    //ECRITURE DU FICHIER XML (fonctions définies dans FB_catalog.php)  
    start_xml($filename, $XMLlang);

    
    while ($row = pwg_db_fetch_assoc($result))
      {
         $subquery = 'SELECT * FROM '.IMAGES_TABLE.' T1 WHERE T1.Id = '.$row['imageId'].' LIMIT 1'; 
         $imgInfos = pwg_db_fetch_assoc(pwg_query($subquery));
         $links=array(
         'item_url' =>   make_picture_url( array(
                    'image_id' => $row['imageId'],
                    'image_file' => $row['file'],
                    'category' => array
                        (
                          'id' => $row['categoryId'],
                          'name' => $row['categoryName'],
                          'permalink' => $row['categoryPL']
                        ),
                    ) ),
          'image_link1' => DerivativeImage::url(IMG_SMALL, $imgInfos),
          'image_link2' => DerivativeImage::url(IMG_MEDIUM, $imgInfos),
           );
         add_item($row, $ref_cat, $conf, $links, $XMLlang);
      }

    unset_make_full_url();
    end_xml();

    $page['infos'][] = 'Catalog generated. '.$item_count.' items listed in catalog';

      }
      else
      {
        $filenameBasis = 'FB_catalog';
        $filename = 'FB_catalog.xml';
      }

    // END AS GUEST
    //$user = $save_user;

    if (isset($_POST['catalog_country']))
    {
        $template->assign('ppppp_catalog_country', $countryCode);
    }
    $template->assign( array(
      'FILENAME' => $filename,
      'FILENAMEBASIS' => $filenameBasis,
      'U_FILENAME' => get_root_url().$filename,
        )
      );
 
    break;
}

$template->set_filenames(array('plugin_admin_content' => dirname(__FILE__) . '/admin.tpl')); 
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>
