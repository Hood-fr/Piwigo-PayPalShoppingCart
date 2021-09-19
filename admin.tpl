{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}
{combine_css path="themes/default/js/plugins/chosen.css"}

{footer_script}{literal}
jQuery(document).ready(function() {
  jQuery(".chzn-select").chosen();

  function checkStatusOptions() {
    if (jQuery("input[name=apply_to_albums]:checked").val() == "list") {
      jQuery("#albumList").show();
    }
    else {
      jQuery("#albumList").hide();
    }
  }

  checkStatusOptions();

  jQuery("input[name=apply_to_albums]").change(function() {
    checkStatusOptions();
  });
});
{/literal}{/footer_script}

<div class="titrePage">
<h2>{'PayPal Shopping Cart Mod'|@translate}</h2>
</div>

{if $tabsheet_selected=='currency'}
<h3>{'Currency'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Currency'|@translate}</legend>
<br>
<select name=currency onchange=submit()>
{foreach from=$ppppp_array_currency item=currency_label key=currency_code}
<option value="{$currency_code}"{if $ppppp_currency==$currency_code} selected{/if}>{$currency_label} ({$currency_code})</option>
{/foreach}
</select>
<br>
<br>
<!--input type=submit value="{'Update data'|@translate}"-->
</fieldset>
</form>



{elseif $tabsheet_selected=='settings'}
<h3>{'Settings'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Settings'|@translate}</legend>
<br>
{'Paypal account email'|@translate} <input type=email name=PayPalAccountEmail size=60 value="{$ppppp_account}">
<br>
<br>
<!--input type=submit value="{'Update data'|@translate}"-->
</fieldset>
<input type=submit value="{'Update data'|@translate}">
</form>

{elseif $tabsheet_selected=='country'}
<h3>{'Shipping country'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append shipping country'|@translate}</legend>
<br>
{'Name'|@translate} <input type=text name=CountryName>
{'Country code'|@translate} <input type=text name=CountryCode>
{'Currency'|@translate}
<select name=Currency>
{foreach from=$ppppp_array_currency item=currency_label key=currency_code}
<option value="{$currency_code}">{$currency_label} ({$currency_code})</option>
{/foreach}
</select>
{'Provider'|@translate}
<select name=Provider>
{foreach from=$ppppp_array_provider item=ppppp_row_provider}
<option value="{$ppppp_row_provider.Id}">{$ppppp_row_provider.Name}</option>
{/foreach}
</select>
<br>
<br>
<input type=submit value="{'Update data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th>{'Country code'|@translate}</th>
<th>{'Currency'|@translate}</th>
<th>{'Provider'|@translate}</th>
</tr>
{foreach from=$ppppp_array_country item=ppppp_row_country name=ppppp_row_country_loop}
<tr class="{if $smarty.foreach.ppppp_row_country_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_country.Id}</td>
<td align="center">{$ppppp_row_country.CountryName}</td>
<td align="center">{$ppppp_row_country.CountryCode}</td>
<td align="center">{$ppppp_row_country.Currency}</td>
<td align="center">{$ppppp_row_country.Name}</td>
<td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_country.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
<td>
<form method=post>
<input type=hidden name=edit value='{$ppppp_row_country.Id}'>
<input type=button value="{'Edit data'|@translate}" onclick="FillFieldForEdit('country',{$ppppp_row_country.Id})">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='provider'}
<h3>{'Provider'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append provider'|@translate}</legend>
<br>
{'Name'|@translate} <input type=text name=ProviderName>
{'URL'|@translate} <input type=text name=ProviderUrl>
{'Currency'|@translate}
<select name=Currency>
{foreach from=$ppppp_array_currency item=currency_label key=currency_code}
<option value="{$currency_code}">{$currency_label} ({$currency_code})</option>
{/foreach}
</select>
<br>
<br>
<input type=submit value="{'Update data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th>{'URL'|@translate}</th>
<th>{'Currency'|@translate}</th>
</tr>
{foreach from=$ppppp_array_provider item=ppppp_row_provider name=ppppp_row_provider_loop}
<tr class="{if $smarty.foreach.ppppp_row_provider_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_provider.Id}</td>
<td align="center">{$ppppp_row_provider.Name}</td>
<td align="center">{$ppppp_row_provider.URL}</td>
<td align="center">{$ppppp_row_provider.Currency}</td>
<td align="center">
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_provider.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
<td>
<form method=post>
<input type=hidden name=edit value='{$ppppp_row_provider.Id}'>
<input type=button value="{'Edit data'|@translate}" onclick="FillFieldForEdit('provider',{$ppppp_row_provider.Id})">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='albums'}
<h3>{'Albums'|@translate}</h3>
<form method=post>
<fieldset>
  <legend>{'Apply to albums'|@translate}</legend>
  <p>
    <label><input type="radio" name="apply_to_albums" value="all"{if $apply_to_albums eq 'all'} checked="checked"{/if}> <strong>{'all albums'|@translate}</strong></label>
    <label><input type="radio" name="apply_to_albums" value="list"{if $apply_to_albums eq 'list'} checked="checked"{/if}> <strong>{'a list of albums'|@translate}</strong></label>
  </p>
  <p id="albumList">
    <select data-placeholder="Select albums..." class="chzn-select" multiple style="width:700px;" name="albums[]">
      {html_options options=$album_options selected=$album_options_selected}
    </select>
  </p>
  <p class="formButtons">
		<input type="submit" name="submit" value="{'Save Settings'|@translate}">
	</p>
</fieldset>
</form>

{elseif $tabsheet_selected=='support'}
<h3>{'Support'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append photo support'|@translate}</legend>
<br>
{'Support'|@translate}
<select name="support" >
{foreach from=$ppppp_array_materials item=ppppp_row_materials}
<option value="{$ppppp_row_materials.Id}">{$ppppp_row_materials.Material|@translate}</option>
{/foreach}
</select>
{'Option'|@translate} #1 
<select name="option1" >
{foreach from=$ppppp_array_support_options item=ppppp_row_support_options}
<option value="{$ppppp_row_support_options.Id}">{$ppppp_row_support_options.OptionName|@translate}</option>
{/foreach}
</select>
{'Option'|@translate} #2
<select name="option2" >
{foreach from=$ppppp_array_support_options item=ppppp_row_support_options}
<option value="{$ppppp_row_support_options.Id}">{$ppppp_row_support_options.OptionName|@translate}</option>
{/foreach}
</select>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Support'|@translate}</th>
<th>{'Option'|@translate} #1</th>
<th>{'Option'|@translate} #2</th>
<th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_support item=ppppp_row_support name=ppppp_row_support_loop}
<tr class="{if $smarty.foreach.ppppp_row_support_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_support.Id}</td>
<td align="center">{$ppppp_row_support.SupportMaterial}</td>
<td align="center">{$ppppp_row_support.SupportOption1}</td>
<td align="center">{$ppppp_row_support.SupportOption2}</td>
<td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_support.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='size'}
<h3>{'Size'|@translate}</h3>
<form method=post name=SizeEdit>
<fieldset>
<legend>{'Append photo size'|@translate}</legend>
<br>
    <table>
        <tr>
            <td align="center" colspan="3">{'Ratio'|@translate} 
                <select name="Ratio" >
                {foreach from=$ppppp_array_ratio item=ppppp_row_ratio}
                <option value="{$ppppp_row_ratio.Id}">{$ppppp_row_ratio.RatioName|@translate}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>{'Size'|@translate} (cm)<input type=text name=SizeName></td>
            <td>{'Width'|@translate} (cm)<input type=text name=Width_cm ></td>
            <td>{'Height'|@translate} (cm)<input type=text name=Height_cm ></td>
        </tr>
        <tr>
            <td>{'Size'|@translate} (in)<input type=text name=AltSizeName></td>
            <td>{'Width'|@translate} (in)<input type=text name=Width_in ></td>
            <td>{'Height'|@translate} (in)<input type=text name=Height_in ></td>
        </tr>
        <tr>
            <td align="center" colspan="3">{'Min. Resolution'|@translate}<br> <input type=text name=MinRes size="5"></td>
        </tr>
    </table>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Size'|@translate}</th>
<th>{'AltSize'|@translate}</th>
<th>{'Ratio'|@translate}</th>
<th>{'Width'|@translate} (cm)</th>
<th>{'Height'|@translate} (cm)</th>
<th>{'Width'|@translate} (in)</th>
<th>{'Height'|@translate} (in)</th>
<th>{'Min. Resolution'|@translate}</th>
<th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_sizes item=ppppp_row_sizes name=ppppp_row_sizes_loop}
<tr class="{if $smarty.foreach.ppppp_row_sizes_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_sizes.Id}</td>
<td align="center">{$ppppp_row_sizes.SizeName}</td>
<td align="center">{$ppppp_row_sizes.AltSizeName}</td>
<td align="center">{$ppppp_row_sizes.Ratio}</td>
<td align="center">{$ppppp_row_sizes.Width_cm}</td>
<td align="center">{$ppppp_row_sizes.Height_cm}</td>
<td align="center">{$ppppp_row_sizes.Width_in}</td>
<td align="center">{$ppppp_row_sizes.Height_in}</td>
<td align="center">{$ppppp_row_sizes.MinRes}</td>
<td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_sizes.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='price'}
<h3>{'Price'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append prices'|@translate}</legend>
<br>
    <table>
        <tr>
            <td align="center">{'Support'|@translate}<br>
                <select name="support" >
                {foreach from=$ppppp_array_support item=ppppp_row_support}
                <option value="{$ppppp_row_support.Id}">{$ppppp_row_support.SupportMaterial|@translate}{if $ppppp_row_support.SupportOption1!='None'}  {$ppppp_row_support.SupportOption1|@translate}{/if}{if $ppppp_row_support.SupportOption2!='None'} {$ppppp_row_support.SupportOption2|@translate}{/if}</option>
                {/foreach}
                </select></td>
            <td align="center">{'Size'|@translate}<br>
                 <select name="size" >
                {foreach from=$ppppp_array_sizes item=ppppp_row_sizes}
                <option value="{$ppppp_row_sizes.Id}">{$ppppp_row_sizes.SizeName|@translate} ({$ppppp_row_sizes.Ratio|@translate})</option>
                {/foreach}
                </select></td>
            <td align="center">{'Provider'|@translate}<br>
                 <select name="provider" >
                {foreach from=$ppppp_array_provider item=ppppp_row_provider}
                <option value="{$ppppp_row_provider.Id}">{$ppppp_row_provider.Name} ({$ppppp_row_provider.Currency})</option>
                {/foreach}
                </select></td>
            <td align="center">{'Price'|@translate}<br> <input type=text name=price size="6"></td>
            <td align="center">{'Shipping fees'|@translate}<br> <input type=text name=shipping size="6"></td>
        </tr>
    </table>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Support'|@translate}</th>
<th>{'Option #1'|@translate}</th>
<th>{'Option #1'|@translate}</th>
<th>{'Size'|@translate}</th>
<th>{'Ratio'|@translate}</th>
<th>{'Height'|@translate}</th>
<th>{'Width'|@translate}</th>
<th>{'Units'|@translate}</th>
<th>{'Provider'|@translate}</th>
<th>{'Price'|@translate}</th>
<th>{'Shipping fees'|@translate}</th>
<th>{'Currency'|@translate}</th>
    <th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_price item=ppppp_row_price name=ppppp_row_price_loop}
<tr class="{if $smarty.foreach.ppppp_row_price_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_price.Id}</td>
<td align="center">{$ppppp_row_price.SupportMaterial}</td>
<td align="center">{$ppppp_row_price.SupportOption1}</td>
<td align="center">{$ppppp_row_price.SupportOption2}</td>
<td align="center">{$ppppp_row_price.Size}</td>
<td align="center">{$ppppp_row_price.Ratio}</td>
<td align="center">{$ppppp_row_price.Height}</td>
<td align="center">{$ppppp_row_price.Width}</td>
<td align="center">{$ppppp_row_price.Units}</td>
<td align="center">{$ppppp_row_price.Provider}</td>
<td align="center">{$ppppp_row_price.Price}</td>
<td align="center">{$ppppp_row_price.Shipping}</td>
<td align="center">{$ppppp_row_price.Currency}</td>
    <td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_price.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='code'}
<h3>{'Promo code'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append promo code'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Code'|@translate} <input type=text name=code></td>
            <td>{'Promo relative'|@translate} <input type=text name=reduc_rel></td>
            <td>{'Promo absolute'|@translate} <input type=text name=reduc_abs></td>
            <td>{'Promo shipping'|@translate} <input type=text name=reduc_ship></td>
        </tr>
    </table>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Code'|@translate}</th>
<th>{'Promo relative'|@translate}</th>
<th>{'Promo absolute'|@translate}</th>
<th>{'Promo shipping'|@translate}</th>
</tr>
{foreach from=$ppppp_array_promocode item=ppppp_row_promocode name=ppppp_row_promcoode_loop}
<tr class="{if $smarty.foreach.ppppp_row_promocode_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_promocode.Id}</td>
<td align="center">{$ppppp_row_promocode.code}</td>
<td align="center">{$ppppp_row_promocode.reduc_rel}</td>
<td align="center">{$ppppp_row_promocode.reduc_abs}</td>
<td align="center">{$ppppp_row_promocode.reduc_ship}</td>
    <td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_promocode.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='ratio'}
<h3>{'Ratios'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append ratios'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Name'|@translate} <input type=text name=RatioName></td>
            <td>{'Value'|@translate} <input type=text name=RatioValue></td>
        </tr>
    </table>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th>{'Value'|@translate}</th>
</tr>
{foreach from=$ppppp_array_ratio item=ppppp_row_ratio name=ppppp_row_ratio_loop}
<tr class="{if $smarty.foreach.ppppp_row_ratio_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_ratio.Id}</td>
<td align="center">{$ppppp_row_ratio.RatioName}</td>
<td align="center">{$ppppp_row_ratio.RatioValue}</td>
    <td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_ratio.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='support_option'}
<h3>{'Support options'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append support options'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Name'|@translate} <input type=text name=OptionName></td>
        </tr>
    </table>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
</tr>
{foreach from=$ppppp_array_support_options item=ppppp_row_support_options name=ppppp_row_support_options_loop}
<tr class="{if $smarty.foreach.ppppp_row_support_options_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_support_options.Id}</td>
<td align="center">{$ppppp_row_support_options.OptionName}</td>
<td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_support_options.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='material'}
<h3>{'Materials'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append material'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Name'|@translate} <input type=text name=Material></td>
        </tr>
    </table>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
</tr>
{foreach from=$ppppp_array_materials item=ppppp_row_materials name=ppppp_row_materials_loop}
<tr class="{if $smarty.foreach.ppppp_row_materials_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_materials.Id}</td>
<td align="center">{$ppppp_row_materials.Material}</td>
    <td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_materials.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='FB_catalog'}
<h3>{'Facebook Catalog'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Configuration'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Brand'|@translate} <input type=text name=Brand value ="{$ppppp_fb_brand}"></td>
        </tr>
        <tr>
            <td>{'Google Product Category'|@translate} <input type=text name=GoogleId value="{$ppppp_fb_googleId}"></td>
        </tr>
        <tr>
            <td>{'Facebook Product Category'|@translate} <input type=text name=FBId value="{$ppppp_fb_fbId}"></td>
        </tr>
        <tr>
            <td>
            {'Reference country'|@translate}
            <select name=Ref_country>
            {foreach from=$ppppp_array_country item=ppppp_row_country}
            <option value="{$ppppp_row_country.CountryCode}"{if $ppppp_fb_ref_country==$ppppp_row_country.CountryCode} selected{/if}>{$ppppp_row_country.CountryName}</option>
            {/foreach}
            </select>
            </td>
        </tr>
        <tr>
            <td>
                <input type=submit value="{'Save settings'|@translate}">
            </td>
        </tr>
    </table>
<br>
<br>
</fieldset>
</form>

<form method=post>
<fieldset>
<legend>{'Select country for catalog'|@translate}</legend>

<select name=catalog_country>
{foreach from=$ppppp_array_country item=ppppp_row_country}
<option value="{$ppppp_row_country.CountryCode}"{if $ppppp_catalog_country==$ppppp_row_country.CountryCode} selected{/if}>{$ppppp_row_country.CountryName} ({$ppppp_row_country.SupplierName})</option>
{/foreach}
</select>
<br>
<br>
<p>
  <label for="filename">{'Filename'|@translate} <input type="input" name="filename" value="{$FILENAMEBASIS}" /></label> <a href="{$U_FILENAME}">{$U_FILENAME}</a>
</p>


<input type=submit class="submit" value="{'Generate catalog'|@translate}" name="submit">
</fieldset>
</form>

{/if}
