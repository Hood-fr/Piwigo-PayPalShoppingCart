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
  Country varchar(3) NOT NULL,
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
  support varchar(40) NOT NULL,
  factor float NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE KEY support (support)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);

        $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_supportoptions (
  Id tinyint(4) NOT NULL AUTO_INCREMENT,
  OptionName varchar(40) NOT NULL,
  PRIMARY KEY (Id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8
;";
    pwg_query($query);
    
    $query = "
CREATE TABLE IF NOT EXISTS ".$prefixeTable."ppppp_size (
  id tinyint(4) NOT NULL AUTO_INCREMENT,
  size varchar(40) NOT NULL,
  price float NOT NULL,
  GF tinyint(4) NOT NULL,
  SQ tinyint(4) NOT NULL,
  Pano52 tinyint(4) NOT NULL,
  Pano31 tinyint(4) NOT NULL,
  Pano41 tinyint(4) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY size (size)
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
  FROM '.$prefixeTable.'ppppp_support
;';
    list($counter_support) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter_support)
    {
      single_insert(
        $prefixeTable."ppppp_support",
        array(
          'support' => 'Poster',
          'factor' => 2,
          )
        );
    }

    $query = '
SELECT COUNT(*)
  FROM '.$prefixeTable.'ppppp_size
;';
    list($counter) = pwg_db_fetch_row(pwg_query($query));

    if (0 == $counter)
    {
      single_insert(
        $prefixeTable."ppppp_size",
        array(
          'size' => 'Classic',
          'price' => 40,
          'GF' => 0,
          'SQ' => 0,
          'Pano52' => 0,
          'Pano31' => 0,
          'Pano41' => 0,
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
 
    $query = "DROP TABLE ".$prefixeTable."ppppp_size;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_support;";
    pwg_query($query);

    $query = "DROP TABLE ".$prefixeTable."ppppp_promocode;";
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
