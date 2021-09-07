<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class PayPalShoppingCart_maintain extends PluginMaintain
{
  private $installed = false;

  function __construct($plugin_id)
  {
    parent::__construct($plugin_id);
  }

  function install($plugin_version, &$errors=array())
  {
    global $conf, $prefixeTable, $template;

    $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_countries (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  CountryName varchar(20) NOT NULL,
  CountryCode varchar(3) NOT NULL,
  Currency varchar(3) NOT NULL,
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);

    $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_prices (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  Provider tinyint(4) NOT NULL,
  Size tinyint(4) NOT NULL,
  Support tinyint(4) NOT NULL,
  MinRes float NOT NULL DEFAULT '180',
  Price float NOT NULL,
  Shipping float NOT NULL,
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);

    $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_ratio (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  RatioValue float NOT NULL,
  RatioName varchar(20) NULL,
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);

    $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_support (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  SupportMaterial tinyint(4) NOT NULL,
  SupportOption1 tinyint(4) NOT NULL DEFAULT '1',
  SupportOption2 tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);

        $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_support_options (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  OptionName varchar(40) NOT NULL,
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);
    
        $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_material (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  Material varchar(40) NOT NULL,
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);
    
        $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_sizes (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  Ratio tinyint(4) NOT NULL,
  SizeName varchar(30) NOT NULL,
  Height float NULL,
  Length float NULL,
  Units ENUM('cm','in','ft', ''),
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);
  
      $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_promocode (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  code varchar(40) NOT NULL,
  reduc_rel float NOT NULL,
  reduc_abs float NOT NULL,
  reduc_ship float NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE KEY code (code)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);
    

    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_countries
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_countries",
        array(
          'CountryName' => 'France',
          'CountryCode' => 'FRA',
          'Currency' => 'EUR',
          'Provider' => 1,
          )
        );
    }

    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_providers
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_providers",
        array(
          'Name' => 'ProviderName',
          'URL' => 'https://www.provider.com',
          'Currency' => 'EUR',
          )
        );
    }    
    
    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_material
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_material",
        array(
          'Material' => 'Poster',
          )
        );
    }

    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_support_options
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_support_options",
        array(
          'OptionName' => 'None',
          )
        );
    }

        $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_support
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_support",
        array(
          'SupportMaterial' => '1',
          'SupportOption1' => '1',
          'SupportOption2' => '1',
          )
        );
    }
    
    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_ratio
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_ratio",
        array(
          'RatioValue' => '1.5',
          'RatioName' => '3:2',
          )
        );
    }
    
    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_promocode
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_promocode",
        array(
          'code' => 'CODE',
          'reduc_rel' => 0,
          'reduc_abs' => 0,
          'reduc_ship' => 0,
          )
        );
    }
      
    // add a new column to existing table
    $result = pwg_query('SHOW COLUMNS FROM `'.CATEGORIES_TABLE.'` LIKE "paypal_active";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `'.CATEGORIES_TABLE.'` ADD `paypal_active` enum(\'true\', \'false\') default \'false\';');
    }

    $ppppp_config = array(
      'fixed_shipping' => 0,
      'currency' => 'EUR',
      'apply_to_albums' => 'all',
      'PayPalAccount' => get_webmaster_mail_address(),
      );
    
    // move the content of table ppppp_config into $conf['PayPalShoppingCart'], serialized
    $result = pwg_query('SHOW TABLES LIKE "'.$prefixeTable.'ppppp_config";');
    if (pwg_db_num_rows($result))
    {
      $query = '
SELECT
    *
  FROM '.$prefixeTable.'ppppp_config
;';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        if (isset($ppppp_config[ $row['param'] ]))
        {
          $ppppp_config[ $row['param'] ] = $row['value'];
        }
      }
      
      pwg_query('DROP TABLE '.$prefixeTable.'ppppp_config;');
    }
  
    // load existing config parameters
    if (!empty($conf['PayPalShoppingCart']))
    {
      $conf['PayPalShoppingCart'] = safe_unserialize($conf['PayPalShoppingCart']);
      
      foreach ($conf['PayPalShoppingCart'] as $key => $value)
      {
        $ppppp_config[$key] = $value;
      }
    }
    
    conf_update_param('PayPalShoppingCart', $ppppp_config, true);
    
    $this->installed = true;
  }

  function activate($plugin_version, &$errors=array())
  {
    global $prefixeTable;
    
    if (!$this->installed)
    {
      $this->install($plugin_version, $errors);
    }
  }

  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }
  
  function deactivate()
  {
  }

  function uninstall()
  {
    global $prefixeTable;
 
    $query = "DROP TABLE ".$prefixeTable."ppppp_sizes;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_support;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_promocode;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_support_options;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_countries;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_material;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_price;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_ratio;";
    pwg_query($query);
      
    $result = pwg_query('SHOW TABLES LIKE "'.$prefixeTable.'ppppp_config";');
    if (pwg_db_num_rows($result))
    {
      $query = "DROP TABLE ".$prefixeTable."ppppp_config;"; 
      pwg_query($query);
    }

    // delete configuration
    pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "PayPalShoppingCart";');
  
    // delete field
    pwg_query('ALTER TABLE `'. CATEGORIES_TABLE .'` DROP COLUMN paypal_active;');
  }
}
?>
