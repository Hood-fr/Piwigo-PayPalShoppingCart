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
//$tabsheet->add('currency',
//               l10n('Currency'),
//               $my_base_url.'&amp;tab=currency');
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
$tabsheet->add('Catalog',
               l10n('Catalog'),
               $my_base_url.'&amp;tab=Catalog');	
$tabsheet->select($page['tab']);
$tabsheet->assign();

switch($page['tab'])
{

// CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY - CURRENCY //
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
  
    if(isset($_POST['currency']) && isset($array_currency[ $_POST['currency'] ]))
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
    
// SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS //
    case 'settings':
    
    if (isset($_POST['PayPalAccountEmail']) && filter_var($_POST['PayPalAccountEmail'], FILTER_VALIDATE_EMAIL))
    {
      $conf['PayPalShoppingCart']['PayPalAccount'] = $_POST['PayPalAccountEmail'];
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);
      
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    elseif (isset($_POST['PayPalAccountEmail']) && !filter_var($_POST['PayPalAccountEmail'], FILTER_VALIDATE_EMAIL))
    {  
      $page['infos'][] = l10n('Invalid account. Please make sure you have entered a valid email address.');
    }
    
    $template->assign('ppppp_account', $conf['PayPalShoppingCart']['PayPalAccount']);
    break;
    
// ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS //
    case 'albums' :

     if (isset($_POST['apply_to_albums']) && in_array($_POST['apply_to_albums'], array('all', 'list')))
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
  
// PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER //
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

    if(isset($_POST['IdToEdit']))
       {
            $template->assign('ProviderId',$_POST['IdToEdit']);
            $template->assign('ProviderName',$_POST['NameToEdit']);
            $template->assign('ProviderURL',$_POST['URLToEdit']);
            $template->assign('ProviderCurrency',$_POST['CurrencyToEdit']);
       }
    else
    {
            $template->assign('ProviderId','0');
            $template->assign('ProviderName','');
            $template->assign('ProviderURL','');
            $template->assign('ProviderCurrency','');        
    }

      if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_PROVIDER_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['ProviderId']) && isset($_POST['ProviderName']) && isset($_POST['ProviderUrl']) && isset($_POST['Currency']))
    {
        if (intval($_POST['ProviderId'])>0)
        {
            single_update(
              PPPPP_PROVIDER_TABLE,
              array(
                'Name' => pwg_db_real_escape_string($_POST['ProviderName']),
                'URL' => pwg_db_real_escape_string($_POST['ProviderUrl']),
                'Currency' => pwg_db_real_escape_string($_POST['Currency']),
                ),
              array('Id' => $_POST['ProviderId'])
              );
        }
        else
        {
            single_insert(
              PPPPP_PROVIDER_TABLE,
              array(
                'Name' => pwg_db_real_escape_string($_POST['ProviderName']),
                'URL' => pwg_db_real_escape_string($_POST['ProviderUrl']),
                'Currency' => pwg_db_real_escape_string($_POST['Currency']),
                )
              );
        }
        
        $page['infos'][] = l10n('Your configuration settings are saved');
            
    }
 
    $query='SELECT * FROM '.PPPPP_PROVIDER_TABLE.' ORDER BY Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider',$row);
    }
    
    break;     

// COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY // 
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

      if(isset($_POST['IdToEdit']))
        {
            $template->assign('CountryId',$_POST['IdToEdit']);
            $template->assign('CountryName',$_POST['CountryNameToEdit']);
            $template->assign('CountryCode',$_POST['CountryCodeToEdit']);
            $template->assign('CountryLang',$_POST['CountryLangToEdit']);
            $template->assign('CountryCurrency',$_POST['CurrencyToEdit']);
            $template->assign('ProviderId',$_POST['ProviderIdToEdit']);
        }
      else
        {
            $template->assign('CountryId','0');
            $template->assign('CountryName','');
            $template->assign('CountryCode','');
            $template->assign('CountryLang','');
            $template->assign('CountryCurrency','');        
            $template->assign('ProviderId','0');
        }

      if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_COUNTRY_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['CountryId']) && isset($_POST['CountryName']) && isset($_POST['CountryCode']) && isset($_POST['Currency']) && isset($_POST['Provider']))
    {
        if (isset($_POST['CountryLang']))
        {
            if (intval($_POST['CountryId'])>0)
            {
                single_update(
                   PPPPP_COUNTRY_TABLE,
                  array(
                    'CountryName' => pwg_db_real_escape_string($_POST['CountryName']),
                    'CountryCode' => pwg_db_real_escape_string($_POST['CountryCode']),
                    'CountryLang' => pwg_db_real_escape_string($_POST['CountryLang']),
                    'Currency' => pwg_db_real_escape_string($_POST['Currency']),
                    'Provider' => pwg_db_real_escape_string($_POST['Provider']),
                    ),
                  array('Id' => $_POST['CountryId'])
                  );
            }
            else
            {
                single_insert(
                  PPPPP_COUNTRY_TABLE,
                  array(
                    'CountryName' => pwg_db_real_escape_string($_POST['CountryName']),
                    'CountryCode' => pwg_db_real_escape_string($_POST['CountryCode']),
                    'CountryLang' => pwg_db_real_escape_string($_POST['CountryLang']),
                    'Currency' => pwg_db_real_escape_string($_POST['Currency']),
                    'Provider' => pwg_db_real_escape_string($_POST['Provider']),
                    )
            );
            }
        }
        else
        {
            if (intval($_POST['CountryId'])>0)
            {
                single_update(
                   PPPPP_COUNTRY_TABLE,
                  array(
                    'CountryName' => pwg_db_real_escape_string($_POST['CountryName']),
                    'CountryCode' => pwg_db_real_escape_string($_POST['CountryCode']),
                    'Currency' => pwg_db_real_escape_string($_POST['Currency']),
                    'Provider' => pwg_db_real_escape_string($_POST['Provider']),
                    ),
                  array('Id' => $_POST['CountryId'])
                  );
            }
            else
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
            }
        }
        $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT T1.Id, T1.CountryName, T1.CountryCode, T1.CountryLang, T1.Currency, T2.Id as ProviderId , T2.Name as ProviderName FROM '.PPPPP_COUNTRY_TABLE.' T1 LEFT JOIN '.
            PPPPP_PROVIDER_TABLE.' T2 ON T1.Provider=T2.Id ORDER BY T1.Provider, T1.CountryName;';
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

// MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL //
    case 'material':
    
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('MaterialId',$_POST['IdToEdit']);
            $template->assign('MaterialName',$_POST['MaterialNameToEdit']);
       }
    else
    {
            $template->assign('MaterialId','0');
            $template->assign('MaterialName','');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_MATERIAL_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['Material']))
    {
         if (intval($_POST['MaterialId'])>0)
        {
            single_update(
               PPPPP_MATERIAL_TABLE,
               array(
                 'Material' => pwg_db_real_escape_string($_POST['Material']),
                   ),
              array('Id' => $_POST['MaterialId'])
              );
        }
        else
        {
            single_insert(
               PPPPP_MATERIAL_TABLE,
               array(
                 'Material' => pwg_db_real_escape_string($_POST['Material']),
                   )
               );
        }
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_MATERIAL_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_materials',$row);
    }
    
    break;
  
// SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION //
    case 'support_option':
    
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('OptionId',$_POST['IdToEdit']);
            $template->assign('OptionName',$_POST['OptionNameToEdit']);
       }
    else
    {
            $template->assign('OptionId','0');
            $template->assign('OptionName','');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_OPTION_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['OptionId']) && isset($_POST['OptionName']))
    {
        if (intval($_POST['OptionId'])>0)
        {
            single_update(
              PPPPP_OPTION_TABLE,
              array(
                'OptionName' => pwg_db_real_escape_string($_POST['OptionName']),
                  ),
              array('Id' => $_POST['OptionId'])
              );
        }
        else
        {
            single_insert(
              PPPPP_OPTION_TABLE,
              array(
                'OptionName' => pwg_db_real_escape_string($_POST['OptionName']),
                  )
              );
        }
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_OPTION_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_support_options',$row);
    }
    
    break;
  
// SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT //
    case 'support':
    
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('SupportId',$_POST['IdToEdit']);
            $template->assign('MaterialId',$_POST['MaterialIdToEdit']);
            $template->assign('Option1Id',$_POST['Option1IdToEdit']);
            $template->assign('Option2Id',$_POST['Option2IdToEdit']);
       }
    else
    {
            $template->assign('SupportId','0');
            $template->assign('MaterialId','0');
            $template->assign('Option1Id','0');
            $template->assign('Option2Id','0');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_SUPPORT_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['SupportId']) && isset($_POST['Material']) && isset($_POST['Option1']) && isset($_POST['Option2']))
    {
         if (intval($_POST['SupportId'])>0)
        {
            single_update(
               PPPPP_SUPPORT_TABLE,
               array(
                 'SupportMaterial' => pwg_db_real_escape_string($_POST['Material']),
                 'SupportOption1' => pwg_db_real_escape_string($_POST['Option1']),
                 'SupportOption2' => pwg_db_real_escape_string($_POST['Option2']),
                 ),
              array('Id' => $_POST['SupportId'])
              );
        }
        else
        {
            single_insert(
               PPPPP_SUPPORT_TABLE,
               array(
                 'SupportMaterial' => pwg_db_real_escape_string($_POST['Material']),
                 'SupportOption1' => pwg_db_real_escape_string($_POST['Option1']),
                 'SupportOption2' => pwg_db_real_escape_string($_POST['Option2']),
                 )
               );
        }
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

    
    $query='SELECT DISTINCT T1.Id AS Id, T4.Material AS SupportMaterial, T2.OptionName AS SupportOption1, T3.OptionName AS SupportOption2, T4.Id AS MaterialId, T2.Id AS Option1Id, T3.Id AS Option2Id'.
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

// RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO //
    case 'ratio':
    
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('RatioId',$_POST['IdToEdit']);
            $template->assign('RatioName',$_POST['RatioNameToEdit']);
            $template->assign('RatioValue',$_POST['RatioValueToEdit']);
       }
    else
    {
            $template->assign('RatioId','0');
            $template->assign('RatioName','');
            $template->assign('RatioValue','');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_RATIO_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['RatioId']) && isset($_POST['RatioName']) && isset($_POST['RatioValue']))
    {
        if (intval($_POST['RatioId'])>0)
        {
            single_update(
              PPPPP_RATIO_TABLE,
              array(
                'RatioName' => pwg_db_real_escape_string($_POST['RatioName']),
                'RatioValue' => pwg_db_real_escape_string($_POST['RatioValue']),
                  ),
              array('Id' => $_POST['RatioId'])
              );
        }
        else
        {
            single_insert(
              PPPPP_RATIO_TABLE,
              array(
                'RatioName' => pwg_db_real_escape_string($_POST['RatioName']),
                'RatioValue' => pwg_db_real_escape_string($_POST['RatioValue']),
                  )
              );
        }
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_RATIO_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_ratio',$row);
    }
    
    break;
        
// SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE //
    case 'size':
    
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('SizeId',$_POST['IdToEdit']);
            $template->assign('SizeName',$_POST['SizeNameToEdit']);
            $template->assign('AltSizeName',$_POST['AltSizeNameToEdit']);
            $template->assign('RatioId',$_POST['RatioIdToEdit']);
            $template->assign('Height_cm',$_POST['HeightcmToEdit']);
            $template->assign('Width_cm',$_POST['WidthcmToEdit']);
            $template->assign('Height_in',$_POST['HeightinToEdit']);
            $template->assign('Width_in',$_POST['WidthinToEdit']);
            $template->assign('MinRes',$_POST['MinResToEdit']);
       }
    else
    {
            $template->assign('SizeId','0');
            $template->assign('SizeName','');
            $template->assign('AltSizeName','');
            $template->assign('RatioId','0');
            $template->assign('Height_cm','');
            $template->assign('Width_cm','');
            $template->assign('Height_in','');
            $template->assign('Width_in','');
            $template->assign('MinRes','');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_SIZES_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['SizeId']) && isset($_POST['SizeName']) && isset($_POST['AltSizeName']) && isset($_POST['RatioId']) && isset($_POST['Height_cm']) && isset($_POST['Width_cm']) && isset($_POST['Height_in']) && isset($_POST['Width_in']) && isset($_POST['MinRes']))
    {      
        if (intval($_POST['SizeId'])>0)
        {
            single_update(
             PPPPP_SIZES_TABLE,
             array(
               'SizeName' => pwg_db_real_escape_string($_POST['SizeName']),
               'AltSizeName' => pwg_db_real_escape_string($_POST['AltSizeName']),
               'Ratio' => pwg_db_real_escape_string($_POST['RatioId']),
               'Height_cm' => pwg_db_real_escape_string($_POST['Height_cm']),
               'Width_cm' => pwg_db_real_escape_string($_POST['Width_cm']),
               'Height_in' => pwg_db_real_escape_string($_POST['Height_in']),
               'Width_in' => pwg_db_real_escape_string($_POST['Width_in']),
               'MinRes' => pwg_db_real_escape_string($_POST['MinRes']),
                ),
              array('Id' => $_POST['SizeId'])
              );
        }
        else
        {
            single_insert(
             PPPPP_SIZES_TABLE,
             array(
               'SizeName' => pwg_db_real_escape_string($_POST['SizeName']),
               'AltSizeName' => pwg_db_real_escape_string($_POST['AltSizeName']),
               'Ratio' => pwg_db_real_escape_string($_POST['RatioId']),
               'Height_cm' => pwg_db_real_escape_string($_POST['Height_cm']),
               'Width_cm' => pwg_db_real_escape_string($_POST['Width_cm']),
               'Height_in' => pwg_db_real_escape_string($_POST['Height_in']),
               'Width_in' => pwg_db_real_escape_string($_POST['Width_in']),
               'MinRes' => pwg_db_real_escape_string($_POST['MinRes']),
                )
            );
        }
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT DISTINCT T1.Id AS Id, SizeName, AltSizeName, T2.RatioName AS Ratio, Width_cm, Height_cm,  Width_in, Height_in, MinRes, T2.Id AS RatioId'.
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
  
// PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE //
    case 'price':
         
    $where_clause =' WHERE 1';      
          
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('PriceId',$_POST['IdToEdit']);
            $template->assign('SupportId',$_POST['SupportIdToEdit']);
            $template->assign('SizeId',$_POST['SizeIdToEdit']);
            $template->assign('ProviderId',$_POST['ProviderIdToEdit']);
            $template->assign('Price',$_POST['PriceToEdit']);
            $template->assign('Shipping',$_POST['ShippingToEdit']);
       }
    else
    {
            $template->assign('PriceId','0');
            $template->assign('SupportId','0');
            $template->assign('SizeId','0');
            $template->assign('ProviderId','0');
            $template->assign('Price','');
            $template->assign('Shipping','');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_PRICE_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['PriceId']) && isset($_POST['Price']) && isset($_POST['Shipping']) && isset($_POST['SupportId']) && isset($_POST['ProviderId']) && isset($_POST['SizeId']))
        {
        if (intval($_POST['PriceId'])>0)
            {
            single_update(
              PPPPP_PRICE_TABLE,
              array(
                'Provider' => pwg_db_real_escape_string($_POST['ProviderId']),
                'Size' => pwg_db_real_escape_string($_POST['SizeId']),
                'Support' => pwg_db_real_escape_string($_POST['SupportId']),
                'Price' => pwg_db_real_escape_string($_POST['Price']),
                'Shipping' => pwg_db_real_escape_string($_POST['Shipping']),
                  ),
              array('Id' => $_POST['PriceId'])
              );
        }
        else
        {
            single_insert(
              PPPPP_PRICE_TABLE,
              array(
                'Provider' => pwg_db_real_escape_string($_POST['ProviderId']),
                'Size' => pwg_db_real_escape_string($_POST['SizeId']),
                'Support' => pwg_db_real_escape_string($_POST['SupportId']),
                'Price' => pwg_db_real_escape_string($_POST['Price']),
                'Shipping' => pwg_db_real_escape_string($_POST['Shipping']),
                  )
              );
        }
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    if (isset($_POST['filter']))
    {

     $price_filter_array=array(
             'Mat' => $_POST['filtMaterial'],
             'Op1' => $_POST['filtOption1'],
             'Op2' => $_POST['filtOption2'],
             'Siz' => $_POST['filtSize'],
             'Alt' => $_POST['filtAltSize'],
             'Rat' => $_POST['filtRatio'],
             'Hei' => $_POST['filtHeight'],
             'Wid' => $_POST['filtWidth'],
             'Prv' => $_POST['filtProvider'],
             'ClC' => !($_POST['filtMaterial']=='*' && $_POST['filtOption1']=='*' && $_POST['filtOption2']=='*' && $_POST['filtSize']=='*' && $_POST['filtAltSize']=='*'
                      && $_POST['filtRatio']=='*' && $_POST['filtHeight']=='*' && $_POST['filtWidth']=='*' && $_POST['filtProvider']=='*'),
     );
     
    $conf['PayPalShoppingCart']['price_filter'] = $price_filter_array;
    conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);

//    $page['infos'][] = l10n('Your configuration settings are saved');     
     
    }
    else if (isset($_POST['reset']))
    {

        $price_filter_array=array(
             'Mat' => '*',
             'Op1' => '*',
             'Op2' => '*',
             'Siz' => '*',
             'Alt' => '*',
             'Rat' => '*',
             'Hei' => '*',
             'Wid' => '*',
             'Prv' => '*',
             'ClC' => false
        );
            
        $conf['PayPalShoppingCart']['price_filter'] = $price_filter_array;
        conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);

//        $page['infos'][] = l10n('Your configuration settings are saved');
     }
 
    $price_filter_array=$conf['PayPalShoppingCart']['price_filter'];

    $template->assign('ppppp_material_filt',$price_filter_array['Mat']);
    $template->assign('ppppp_option1_filt',$price_filter_array['Op1']);
    $template->assign('ppppp_option2_filt',$price_filter_array['Op2']);
    $template->assign('ppppp_size_filt',$price_filter_array['Siz']);
    $template->assign('ppppp_altsize_filt',$price_filter_array['Alt']);
    $template->assign('ppppp_ratio_filt',$price_filter_array['Rat']);
    $template->assign('ppppp_height_filt',$price_filter_array['Hei']);
    $template->assign('ppppp_width_filt',$price_filter_array['Wid']);
    $template->assign('ppppp_provider_filt',$price_filter_array['Prv']);
    $template->assign('ppppp_filter_active',$price_filter_array['ClC']);
    
    if($price_filter_array['ClC'])
    {

        $clause_count=false; 

        $where_clause =' WHERE ';      
        if($price_filter_array['Mat']!='*'){ $where_clause.='T9.Id = '.$price_filter_array['Mat']; $clause_count=true;}
        if($price_filter_array['Op1']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T3.Id = '.$price_filter_array['Op1']; $clause_count=true;}
        if($price_filter_array['Op2']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T4.Id = '.$price_filter_array['Op2']; $clause_count=true;}     
        if($price_filter_array['Siz']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.SizeName = "'.$price_filter_array['Siz'].'"'; $clause_count=true;}     
        if($price_filter_array['Alt']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.AltSizeName = "'.$price_filter_array['Alt'].'"'; $clause_count=true;}     
        if($price_filter_array['Rat']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T6.Id = '.$price_filter_array['Rat']; $clause_count=true;}     
        if($price_filter_array['Hei']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.Height_cm LIKE "'.$price_filter_array['Hei'].'"'; $clause_count=true;}     
        if($price_filter_array['Wid']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T5.Width_cm LIKE "'.$price_filter_array['Wid'].'"'; $clause_count=true;}     
        if($price_filter_array['Prv']!='*'){if($clause_count){$where_clause.=' AND ';} $where_clause.='T8.Id = '.$price_filter_array['Prv']; $clause_count=true;}     
        if(!$clause_count){$where_clause.='1';}
    }
    else
    {
        $where_clause =' WHERE 1';
    }  
     
        
//    echo('<pre>'.$where_clause.'</pre>' );
    
    $query='SELECT DISTINCT T1.Id AS Id, T2.SupportMaterial AS Support, T3.OptionName AS SupportOption1, T4.OptionName AS SupportOption2,'.
            ' T5.SizeName AS Size, T5.AltSizeName AS AltSize, T6.RatioName AS Ratio, T5.Height_cm AS Height, T5.Width_cm AS Width, '.
            ' T8.Name AS Provider, T1.Price, T1.Shipping, T7.Currency AS Currency, T9.Material as SupportMaterial,'.
            ' T2.Id AS SupportId, T5.Id AS SizeId, T8.Id AS ProviderId'.
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

    $query='SELECT DISTINCT T3.Id, T3.Material FROM '.PPPPP_PRICE_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T2 ON T1.Support = T2.Id'.
            ' LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T3 ON T2.SupportMaterial = T3.Id'.
            ' ORDER BY T3.Material;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_material_filt',$row);
    }

    $query='SELECT DISTINCT T2.Id, T2.OptionName FROM '.PPPPP_PRICE_TABLE.' T3'.
           ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T1 ON T3.Support = T1.Id'.
           ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T2 ON T1.SupportOption1 = T2.Id'.
           ' ORDER BY T2.Id;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_option1_filt',$row);
    }

    $query='SELECT DISTINCT T2.Id, T2.OptionName FROM '.PPPPP_PRICE_TABLE.' T3'.
           ' LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T1 ON T3.Support = T1.Id'.
           ' LEFT JOIN '.PPPPP_OPTION_TABLE.' T2 ON T1.SupportOption2 = T2.Id'.
           ' ORDER BY T2.Id;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_option2_filt',$row);
    }

    
    $query='SELECT DISTINCT T3.Id, T3.RatioName FROM '.PPPPP_PRICE_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id'.
            ' LEFT JOIN '.PPPPP_RATIO_TABLE.' T3 ON T2.Ratio = T3.Id'.            
            ' ORDER BY T3.RatioValue;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_ratio_filt',$row);
    }

    
    $query='SELECT DISTINCT T2.SizeName FROM '.PPPPP_PRICE_TABLE.' T1 LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id ORDER BY T2.Width_cm, T2.Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_size_filt',$row);
    }
    
    $query='SELECT DISTINCT T2.AltSizeName FROM '.PPPPP_PRICE_TABLE.' T1 LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id ORDER BY T2.Width_cm, T2.Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_altsize_filt',$row);
    }
 
    $query='SELECT DISTINCT T2.Height_cm FROM '.PPPPP_PRICE_TABLE.' T1 LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id ORDER BY T2.Height_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_height_filt',$row);
    }
 
    $query='SELECT DISTINCT T2.Width_cm FROM '.PPPPP_PRICE_TABLE.' T1 LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id ORDER BY T2.Width_cm;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_width_filt',$row);
    }    
    
    $query='SELECT DISTINCT T2.Id, T2.Name FROM '.PPPPP_PRICE_TABLE.' T1 LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T2 ON T1.Provider = T2.Id ORDER BY T2.Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider_filt',$row);
    }
    
    
    
    break;

// PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE //
    case 'code':
    
    if(isset($_POST['IdToEdit']))
       {
            $template->assign('CodeId',$_POST['IdToEdit']);
            $template->assign('CodeValue',$_POST['CodeValueToEdit']);
            $template->assign('Relative',$_POST['RelToEdit']);
            $template->assign('Absolute',$_POST['AbsToEdit']);
            $template->assign('Shipping',$_POST['ShipToEdit']);
       }
    else
    {
            $template->assign('CodeId','0');
            $template->assign('CodeValue','');
            $template->assign('Relative','');
            $template->assign('Absolute','');
            $template->assign('Shipping','');
    }
        
    if (isset($_POST['delete']))
    {
      check_input_parameter('delete', $_POST, false, PATTERN_ID);
      
      pwg_query('DELETE FROM '.PPPPP_PROMOCODE_TABLE.' WHERE id = '.$_POST['delete'].';');

      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    else if (isset($_POST['CodeId']) && isset($_POST['Code']) && isset($_POST['Promo_rel']) && isset($_POST['Promo_abs']) && isset($_POST['Promo_ship']))
    {
        if (intval($_POST['CodeId'])>0)
            {
            single_update(
                PPPPP_PROMOCODE_TABLE,
                array(
                  'Code' => pwg_db_real_escape_string($_POST['Code']),
                  'Promo_rel' => pwg_db_real_escape_string($_POST['Promo_rel']),
                  'Promo_abs' => pwg_db_real_escape_string($_POST['Promo_abs']),
                  'Promo_ship' => pwg_db_real_escape_string($_POST['Promo_ship']),
                    ),
                array('Id' => $_POST['CodeId'])
                );
        }
        else
        {
        single_insert(
           PPPPP_PROMOCODE_TABLE,
           array(
             'Code' => pwg_db_real_escape_string($_POST['Code']),
             'Promo_rel' => pwg_db_real_escape_string($_POST['Promo_rel']),
             'Promo_abs' => pwg_db_real_escape_string($_POST['Promo_abs']),
             'Promo_ship' => pwg_db_real_escape_string($_POST['Promo_ship']),
                )
            );
        }
        
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    $query='SELECT * FROM '.PPPPP_PROMOCODE_TABLE.';';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_promocode',$row);
    }
    
    break;

// CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG //
    case 'Catalog':
    
    if (isset($_POST['Brand']) && isset($_POST['GoogleId']) && is_numeric($_POST['GoogleId']) && isset($_POST['FBId']) && is_numeric($_POST['FBId']) && isset($_POST['filename']))
    {
      $conf['PayPalShoppingCart']['Brand'] = $_POST['Brand'];
      $conf['PayPalShoppingCart']['GoogleId'] = $_POST['GoogleId'];
      $conf['PayPalShoppingCart']['FBId'] = $_POST['FBId'];
      $conf['PayPalShoppingCart']['Ref_country'] = $_POST['Ref_country'];
      $conf['PayPalShoppingCart']['CatalogFileName'] = $_POST['filename'];
      conf_update_param('PayPalShoppingCart', $conf['PayPalShoppingCart']);
      
      $page['infos'][] = l10n('Your configuration settings are saved');
    }
    
    if (isset($conf['PayPalShoppingCart']['Brand']) and isset($conf['PayPalShoppingCart']['GoogleId']) && isset($conf['PayPalShoppingCart']['FBId'])  && isset($conf['PayPalShoppingCart']['Ref_country']))
    {
        $template->assign('ppppp_cat_brand', $conf['PayPalShoppingCart']['Brand']);
        $template->assign('ppppp_cat_googleId', $conf['PayPalShoppingCart']['GoogleId']);
        $template->assign('ppppp_cat_fbId', $conf['PayPalShoppingCart']['FBId']);
        $template->assign('ppppp_cat_ref_country', $conf['PayPalShoppingCart']['Ref_country']);
        $template->assign('ppppp_cat_filenamebasis', $conf['PayPalShoppingCart']['CatalogFileName']);
    }
    
    $query='SELECT DISTINCT T3.Name, T2.CountryCode, T2.CountryName, T3.Currency, T3.Name AS SupplierName FROM '.PPPPP_PRICE_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T3 ON T1.Provider=T3.Id'.
            ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T2 ON T1.Provider=T2.Provider'.
            ' WHERE T2.CountryLang IS NULL'.
            ' ORDER BY T3.Name;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_provider',$row);
    }

    $query='SELECT DISTINCT T2.Id AS Id, T2.CountryName, T2.CountryCode, T2.CountryLang, T2.Currency, T3.Name AS SupplierName FROM '.PPPPP_PRICE_TABLE.' T1'.
            ' LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T2 ON T1.Provider=T2.Provider'.
            ' LEFT JOIN '.PPPPP_PROVIDER_TABLE.' T3 ON T1.Provider=T3.Id'.
            ' WHERE T2.CountryLang IS NOT NULL'.
            ' ORDER BY T2.CountryName;';
    $result = pwg_query($query);
    while($row = pwg_db_fetch_assoc($result))
    {
      $template->append('ppppp_array_country',$row);
    }
    
    if (isset($_POST['catalog_provider']))
    {
     //echo('<pre>'.var_export($_POST,true).'</pre>' );

    $filenameBasis = $conf['PayPalShoppingCart']['CatalogFileName'];
    $filename = $filenameBasis.'_'.$_POST['catalog_provider'].'.xml';
      
    set_make_full_url();

        $countryCode = $_POST['catalog_provider'];
        $ref_cat=($countryCode==$conf['PayPalShoppingCart']['Ref_country']);
        $template->assign('ppppp_catalog_provider', $countryCode);
        $template->assign( array(
          'CATALOG' => true,
          'TRANSLATION' => false,
          'FILENAME' => $filename,
          'U_FILENAME' => get_root_url().$filename,
            )
          );

      switch($countryCode){
          case 'FR':
              $XMLlang = array(
                    'title' => 'Catalogue en-ligne de la boutique Daedalum Photos - France',
                    'description' => 'Catalogue en-ligne de la boutique Daedalum Photos - France',
                    'support_poster' => 'Tirage poster sur papier photo mat ou brillant. ',
                    'support_canvas' => 'Tirage sur toile tendue sur cadre bois. ',
                    'support_dibond' => 'Tirage sur support aluminium Dibond®, avec option d\'impression directe, d\'impression sur papier photo ou rendu alu brossé. ',
                    'Poster' => 'Tirage poster',
                    'Canvas' => 'Tirage toile',
                    'Dibond' => 'Tirage alu Dibond®',
                    'size0' => 'Dimensions : ',
                    'size1' => 'Dimensions disponibles de ',
                    'size2' => ' à ',
                    'size3' => ' de largeur.',
                    'units' => 'cm',
//                    'Poster_options' => array(
//                        'Rendu' => array('Mat','Brillant'),
//                        ),
//                    'Canvas_options' => array(
//                        'Bords' => array('Etirés','Mirroir','Blancs','Noirs'),
//                        'Cadre' => array('25mm','38mm'),
//                        ),
//                    'Dibond_options' => array(
//                        'Impression' => array('Direct','FineArt mat','FineArt brillant','Alu brossé'),
//                        ),
                    'Poster_images' => array(
                        'local/plugins/PayPalShoppingCart/poster_det.jpg',
                        'local/plugins/PayPalShoppingCart/poster_mat.jpg',
                        'local/plugins/PayPalShoppingCart/poster_bri.jpg',
                        'local/plugins/PayPalShoppingCart/poster_silk.jpg',
                        ),
                    'Canvas_images' => array(
                        'local/plugins/PayPalShoppingCart/canvas_lrg.jpg',
                        'local/plugins/PayPalShoppingCart/edges_stretched.jpg',
                        'local/plugins/PayPalShoppingCart/edges_mirror.jpg',
                        'local/plugins/PayPalShoppingCart/cadre25mm.jpg',
                        'local/plugins/PayPalShoppingCart/cadre38mm.jpg',
                        ),
                    'Dibond_images' => array(
                        'local/plugins/PayPalShoppingCart/dibond_lrg.jpg',
                        'local/plugins/PayPalShoppingCart/dibond_det.jpg',
                        'local/plugins/PayPalShoppingCart/fineart_det.jpg',
                        'local/plugins/PayPalShoppingCart/fineart_mat.jpg',
                        'local/plugins/PayPalShoppingCart/fineart_bri.jpg',
                        'local/plugins/PayPalShoppingCart/brushed_det.jpg',
                        'local/plugins/PayPalShoppingCart/dibond_rail.jpg',
                        ),
                  );
              break;
          case 'US':
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop - United States',
                    'description' => 'Online catalog for Daedalum Photos online shop - United States',
                    'support_poster' => 'Poster print on Fuji Crystal photo paper with a matte, glossy or silky finish. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate, with optional direct print, print on Fuji photo paper or brushed aluminium finish. ',
                    'Poster' => 'Photo print',
                    'Canvas' => 'Canvas print',
                    'Dibond' => 'Alu Dibond® print',
//                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'in',
//                    'Poster_options' => array(
//                        'Finish' => array('Matte','Glossy','Silky'),
//                        ),
//                    'Canvas_options' => array(
//                        'Edges' => array('Stretched','Mirror'),
//                        'Frame' => array('13/16in (20mm)','1in 9/16 (40mm)'),
//                        ),
//                    'Dibond_options' => array(
//                        'Print' => array('Direct print','Photo print mat','Photo print glossy','Brushed Alu'),
//                        ),
                  );
              break;
          case 'GB':
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop - United Kingdom',
                    'description' => 'Online catalog for Daedalum Photos online shop - United Kingdom',
                    'support_poster' => 'Poster print on Fuji Crystal photo paper with a matte, glossy or silky finish. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate, with optional direct print, print on Fuji photo paper or brushed aluminium finish. ',
                    'Poster' => 'Photo print',
                    'Canvas' => 'Canvas print',
                    'Dibond' => 'Alu Dibond® print',
//                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
//                    'Poster_options' => array(
//                        'Finish' => array('Matte','Glossy','Silky'),
//                        ),
//                    'Canvas_options' => array(
//                        'Edges' => array('Stretched','Mirror'),
//                        'Frame' => array('20mm','40mm'),
//                        ),
//                    'Dibond_options' => array(
//                        'Print' => array('Direct print','Photo print mat','Photo print glossy','Brushed Alu'),
//                        ),
                  );
              break;
          case 'CA':
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop - Canada',
                    'description' => 'Online catalog for Daedalum Photos online shop - Canada',
                    'support_poster' => 'Poster print on Fuji Crystal photo paper with a matte, glossy or silky finish. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate, with optional direct print, print on Fuji photo paper or brushed aluminium finish. ',
                    'Poster' => 'Photo print',
                    'Canvas' => 'Canvas print',
                    'Dibond' => 'Alu Dibond® print',
//                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
//                    'Poster_options' => array(
//                        'Finish' => array('Matte','Glossy','Silky'),
//                        ),
//                    'Canvas_options' => array(
//                        'Edges' => array('Stretched','Mirror'),
//                        'Frame' => array('20mm','40mm'),
//                        ),
//                    'Dibond_options' => array(
//                        'Print' => array('Direct print','Photo print mat','Photo print glossy','Brushed Alu'),
//                        ),
                  );
              break;
          default:
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop',
                    'description' => 'Online catalog for Daedalum Photos online shop',
                    'support_poster' => 'Poster print on Fuji photo paper. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate. ',
                    'Poster' => 'Photo print',
                    'Canvas' => 'Canvas print',
                    'Dibond' => 'Alu Dibond® print',
//                    'support' => 'Print on ',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
                  );
              break;
          }
      
 
    $query ='SELECT T1.Price AS price, T1.Shipping AS shipping, T5.Currency AS currency, T10.name AS title, T10.comment AS item, T10.file AS file, MIN(T2.Width_cm) AS minSize_cm, MAX(T2.Width_cm) AS maxSize_cm,'.
            ' MIN(T2.Width_in) AS minSize_in, MAX(T2.Width_in) AS maxSize_in, T10.path, T7.Material AS item_option, T10.Id AS imageId, T5.CountryCode AS countryISOcode, T12.Id AS categoryId, T4.RatioValue as Ratio, '.
            ' T12.name AS categoryName, T12.permalink as categoryPL '.
           'FROM '.PPPPP_PRICE_TABLE.' T1 '.
           'CROSS JOIN '.IMAGES_TABLE.' T10 '.
           'LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id '.
//           'LEFT JOIN '.PPPPP_RATIO_TABLE.' T9 ON T2.Ratio = T9.Id '.
           'LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T6 ON T1.Support = T6.Id '.
           'LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T7 ON T6.SupportMaterial = T7.Id '.
           'LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T6.SupportOption1 = T3.Id '.
           'LEFT JOIN '.PPPPP_OPTION_TABLE.' T8 ON T6.SupportOption2 = T8.Id '.
           'LEFT JOIN '.PPPPP_RATIO_TABLE.' T4 ON T2.Ratio = T4.Id '.
           'LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider '.
           'LEFT JOIN '.IMAGE_CATEGORY_TABLE.' T11 ON T10.Id = T11.image_Id '.
           'LEFT JOIN '.CATEGORIES_TABLE.' T12 ON T11.category_id = T12.Id '.
           'WHERE abs(T4.RatioValue - IF(T10.width>T10.height, ROUND(T10.width/T10.height, 1), ROUND(T10.height/T10.width, 1))) <= 1e-2 '.
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

    if (isset($_POST['catalog_country']))
    {
     //echo('<pre>'.var_export($_POST,true).'</pre>' );

    $filenameBasis = $conf['PayPalShoppingCart']['CatalogFileName'];
    $filename = $filenameBasis.'_'.$_POST['catalog_country'].'.xml';
      
    set_make_full_url();

        $countryLang = $_POST['catalog_country'];
        $ref_cat=false;
        $template->assign('ppppp_catalog_country', $countryLang);
        $template->assign( array(
          'CATALOG' => false,
          'TRANSLATION' => true,
          'FILENAME' => $filename,
          'U_FILENAME' => get_root_url().$filename,
            )
          );

      switch($countryLang){
          case 'it_IT':
              $XMLlang = array(
                    'title' => 'Catalogo online del negozio online Daedalum Photos - Italiano',
                    'description' => 'Catalogo online del negozio online Daedalum Photos - Italiano',
                    'support_poster' => 'Stampa fotografica on carta PH Premium 250gr, opaca o lucida. ',
                    'support_canvas' => 'Stampa su tela, tesa su telaio (25mm o 38mm), con bordo specchiato, ripiegato, bianco o nero. ',
                    'support_dibond' => 'Stampa su alluminio Dibond®, con opzionale stampa diretta, stampa su carta foto PH Premium (opoca o lucida) o stampa alluminio spazzolato ButlerFinish®. ',
                    'Poster' => 'Stampa Poster',
                    'Canvas' => 'Stampa su tela',
                    'Dibond' => 'Stampa alluminio Dibond®',
                    'size0' => 'Dimensioni : ',
                    'size1' => 'Taglie disponibili da ',
                    'size2' => ' a ',
                    'size3' => ' di larghezza.',
                    'units' => 'cm',
                  );
              break;
          case 'es_XX':
              $XMLlang = array(
                    'title' => 'Catálogo en linea de la tienda en linea Daedalum Photos - Español',
                    'description' => 'Catálogo en linea de la tienda en linea Daedalum Photos - Español',
                    'support_poster' => 'Copia fotográfica en PH Premium 250gr foto papel mate o brilllante. ',
                    'support_canvas' => 'Lienzo en bastidor estable de madera maciza (25 o 38mm), opciones : márgenes de espejo, doblodo, blancos o negros ',
                    'support_dibond' => 'Impresión en aluminium Dibond®, con opcional impresión directa, impresión en foto papel PH Premium (mate or brillante) o cepillado aluminium ButlerFinish®. ',
                    'Poster' => 'Copia fotográfica',
                    'Canvas' => 'Lienzo en bastidor',
                    'Dibond' => 'Impresión Alu Dibond®',
                    'size0' => 'Talla: ',
                    'size1' => 'Tallas disponibles de ',
                    'size2' => ' a ',
                    'size3' => ' de ancho.',
                    'units' => 'cm',
                  );
              break;
          case 'de_DE':
              $XMLlang = array(
                    'title' => 'Online-Katalog des Daedalum Photos Online-Shops - Deutsch',
                    'description' => 'Online-Katalog des Daedalum Photos Online-Shops - Deutsch',
                    'support_poster' => 'Foto-Abzug auf PH Premium 250gr in matt oder glänzend. ',
                    'support_canvas' => 'Foto-Leinwand auf einem hochwertigen Trägerrahmen aufgespannt (25mm oder 38mm), optionen : umgeschlagen, gespiegelte, weißer oder schwarzer Rand. ',
                    'support_dibond' => 'Foto-Druck auf Alu-Dibond®, optionen : Direktdruck oder gedruckt Foto-Abzug auf PH Premium 250gr (in matt oder glänzend) oder Foto-Druck  Butlerfinish®. ',
                    'Poster' => 'Foto-abzug',
                    'Canvas' => 'Foto-Leinwand',
                    'Dibond' => 'Foto-Druck Alu-Dibond®',
                    'size0' => 'Größe: ',
                    'size1' => 'Verfügbare Größen von ',
                    'size2' => ' bis ',
                    'size3' => ' Breite.',
                    'units' => 'cm',
                  );
              break;
          case 'nl_XX':
              $XMLlang = array(
                    'title' => 'Online catalogus van de Daedalum Photos online winkel - Nederlands',
                    'description' => 'Online catalogus van de Daedalum Photos online winkel - Nederlands',
                    'support_poster' => 'Posters afdruk op Sterk 250gr HP-posterpapier, mat of glanzend. ',
                    'support_canvas' => 'Foto op canvas, op massief houten frame (25 mm of 38 mm), optioneel: spiegel, gevouwen, witte of zwarte randen. ',
                    'support_dibond' => 'Foto op aluminium Dibond®, optioneel : directdruk, afdruk op Premium HP-fotopapier 250gr (mat of glanzend), of geborsteld aluminium ButlerFinish®. ',
                    'Poster' => 'Posters afdrukken',
                    'Canvas' => 'Foto op canvas',
                    'Dibond' => 'Foto op aluminium',
                    'size0' => 'Dimensies: ',
                    'size1' => 'Afmetingen beschikbaar van ',
                    'size2' => ' tot ',
                    'size3' => ' breed.',
                    'units' => 'cm',
                  );
              break;
          default:
              $XMLlang = array(
                    'title' => 'Online catalog for Daedalum Photos online shop',
                    'description' => 'Online catalog for Daedalum Photos online shop',
                    'support_poster' => 'Poster print on Fuji photo paper. ',
                    'support_canvas' => 'Canvas print stretched on a wooden frame. ',
                    'support_dibond' => 'Print on Dibond® aluminum plate. ',
                    'Poster' => 'Photo print',
                    'Canvas' => 'Canvas print',
                    'Dibond' => 'Alu Dibond® print',
                    'size0' => 'Size: ',
                    'size1' => 'Available sizes from ',
                    'size2' => ' to ',
                    'size3' => ' wide.',
                    'units' => 'cm',
                  );
              break;
          }
      
 
    $query ='SELECT T10.name AS title, T10.comment AS item, T10.file AS file, MIN(T2.Width_cm) AS minSize_cm, MAX(T2.Width_cm) AS maxSize_cm,'.
            ' MIN(T2.Width_in) AS minSize_in, MAX(T2.Width_in) AS maxSize_in, T10.path, T7.Material AS item_option, T10.Id AS imageId, T5.CountryLang AS langISOcode, T5.CountryCode AS countryISOcode,  T1.Shipping AS shipping, T5.Currency AS currency, T12.Id AS categoryId, T4.RatioValue as Ratio, '.
            ' T12.name AS categoryName, T12.permalink as categoryPL '.
           'FROM '.PPPPP_PRICE_TABLE.' T1 '.
           'CROSS JOIN '.IMAGES_TABLE.' T10 '.
           'LEFT JOIN '.PPPPP_SIZES_TABLE.' T2 ON T1.Size = T2.Id '.
  //         'LEFT JOIN '.PPPPP_RATIO_TABLE.' T9 ON T2.Ratio = T9.Id '.
           'LEFT JOIN '.PPPPP_SUPPORT_TABLE.' T6 ON T1.Support = T6.Id '.
           'LEFT JOIN '.PPPPP_MATERIAL_TABLE.' T7 ON T6.SupportMaterial = T7.Id '.
           'LEFT JOIN '.PPPPP_OPTION_TABLE.' T3 ON T6.SupportOption1 = T3.Id '.
           'LEFT JOIN '.PPPPP_OPTION_TABLE.' T8 ON T6.SupportOption2 = T8.Id '.
           'LEFT JOIN '.PPPPP_RATIO_TABLE.' T4 ON T2.Ratio = T4.Id '.
           'LEFT JOIN '.PPPPP_COUNTRY_TABLE.' T5 ON T1.Provider = T5.Provider '.
           'LEFT JOIN '.IMAGE_CATEGORY_TABLE.' T11 ON T10.Id = T11.image_Id '.
           'LEFT JOIN '.CATEGORIES_TABLE.' T12 ON T11.category_id = T12.Id '.
           'WHERE abs(T4.RatioValue - IF(T10.width>T10.height, ROUND(T10.width/T10.height, 1), ROUND(T10.height/T10.width, 1))) <= 1e-2 '.
           'AND T2.Height_in<T10.height*'.$min_res_tolerance.'/T2.MinRes '.
           'AND T5.CountryLang = "'.$countryLang.'" '.
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
        add_item_lang($row, $XMLlang);
      }

    unset_make_full_url();
    end_xml();

    $page['infos'][] = 'Catalog generated. '.$item_count.' items listed in catalog';

    }

 
    break;
}

$template->set_filenames(array('plugin_admin_content' => dirname(__FILE__) . '/admin.tpl')); 
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>
