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


<!--// SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS - SETTINGS //-->

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

<!--// ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS - ALBUMS //-->
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

<!--// PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER - PROVIDER //-->
{elseif $tabsheet_selected=='provider'}
<h3>{'Provider'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append provider'|@translate}</legend>
<br>
<input type=hidden name=ProviderId value="{$ProviderId}">
{'Name'|@translate} <input type=text name=ProviderName value="{$ProviderName}">
{'URL'|@translate} <input type=text name=ProviderUrl value="{$ProviderURL}">
{'Currency'|@translate}
<select name=Currency>
{foreach from=$ppppp_array_currency item=currency_label key=currency_code}
<option value="{$currency_code}"{if $ProviderCurrency==$currency_code} selected{/if}>{$currency_label} ({$currency_code})</option>
{/foreach}
</select>
<br>
<br>
<input type=submit value="{if $ProviderId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th>{'URL'|@translate}</th>
<th>{'Currency'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>
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
<input type=hidden name=IdToEdit value='{$ppppp_row_provider.Id}'>
<input type=hidden name=NameToEdit value='{$ppppp_row_provider.Name}'>
<input type=hidden name=URLToEdit value='{$ppppp_row_provider.URL}'>
<input type=hidden name=CurrencyToEdit value='{$ppppp_row_provider.Currency}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!-- // COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY - COUNTRY //  -->
{elseif $tabsheet_selected=='country'}
<h3>{'Shipping country'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append shipping country'|@translate}</legend>
<br>
<input type=hidden name=CountryId value="{$CountryId}">
{'Name'|@translate} <input type=text name=CountryName value="{$CountryName}">
{'Country code'|@translate} <input type=text name=CountryCode value="{$CountryCode}">
{'Country language'|@translate} <input type=text name=CountryLang value="{$CountryLang}">
{'Currency'|@translate}
<select name=Currency>
{foreach from=$ppppp_array_currency item=currency_label key=currency_code}
<option value="{$currency_code}"{if $CountryCurrency==$currency_code} selected{/if}>{$currency_label} ({$currency_code})</option>
{/foreach}
</select>
{'Provider'|@translate}
<select name=Provider>
{foreach from=$ppppp_array_provider item=ppppp_row_provider}
<option value="{$ppppp_row_provider.Id}"{if $ProviderId==$ppppp_row_provider.Id} selected{/if}>{$ppppp_row_provider.Name}</option>
{/foreach}
</select>
<br>
<br>
<input type=submit value="{if $CountryId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th>{'Country code'|@translate}</th>
<th>{'Country language'|@translate}</th>
<th>{'Currency'|@translate}</th>
<th>{'Provider'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_country item=ppppp_row_country name=ppppp_row_country_loop}
<tr class="{if $smarty.foreach.ppppp_row_country_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_country.Id}</td>
<td align="center">{$ppppp_row_country.CountryName}</td>
<td align="center">{$ppppp_row_country.CountryCode}</td>
<td align="center">{$ppppp_row_country.CountryLang}</td>
<td align="center">{$ppppp_row_country.Currency}</td>
<td align="center">{$ppppp_row_country.ProviderName}</td>
<td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_country.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
<td>
<form method=post>
<input type=hidden name=IdToEdit value='{$ppppp_row_country.Id}'>
<input type=hidden name=CountryNameToEdit value='{$ppppp_row_country.CountryName}'>
<input type=hidden name=CountryCodeToEdit value='{$ppppp_row_country.CountryCode}'>
<input type=hidden name=CountryLangToEdit value='{$ppppp_row_country.CountryLang}'>
<input type=hidden name=CurrencyToEdit value='{$ppppp_row_country.Currency}'>
<input type=hidden name=ProviderIdToEdit value='{$ppppp_row_country.ProviderId}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!--// MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL - MATERIAL //-->
{elseif $tabsheet_selected=='material'}
<h3>{'Materials'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append material'|@translate}</legend>
<br>
<input type=hidden name=MaterialId value="{$MaterialId}">
{'Name'|@translate} <input type=text name=Material value="{$MaterialName}">
<br>
<br>
<input type=submit value="{if $MaterialId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>
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
<td>
<form method=post>
<input type=hidden name=IdToEdit value='{$ppppp_row_materials.Id}'>
<input type=hidden name=MaterialNameToEdit value='{$ppppp_row_materials.Material}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!--// SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION - SUPPORTOPTION //-->
{elseif $tabsheet_selected=='support_option'}
<h3>{'Support options'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append support options'|@translate}</legend>
<br>
<input type=hidden name=OptionId value="{$OptionId}">
{'Name'|@translate} <input type=text name=OptionName value="{$OptionName}">
<br>
<br>
<input type=submit value="{if $OptionId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>
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
<td>
<form method=post>
<input type=hidden name=IdToEdit value='{$ppppp_row_support_options.Id}'>
<input type=hidden name=OptionNameToEdit value='{$ppppp_row_support_options.OptionName}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!-- // SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT - SUPPORT // -->
{elseif $tabsheet_selected=='support'}
<h3>{'Support'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append photo support'|@translate}</legend>
<br>
<input type=hidden name=SupportId value="{$SupportId}">
{'Support'|@translate}
<select name="Material" >
{foreach from=$ppppp_array_materials item=ppppp_row_materials}
<option value="{$ppppp_row_materials.Id}"{if $MaterialId==$ppppp_row_materials.Id} selected{/if}>{$ppppp_row_materials.Material|@translate}</option>
{/foreach}
</select>
{'Option'|@translate} #1 
<select name="Option1" >
{foreach from=$ppppp_array_support_options item=ppppp_row_support_options}
<option value="{$ppppp_row_support_options.Id}"{if $Option1Id==$ppppp_row_support_options.Id} selected{/if}>{$ppppp_row_support_options.OptionName|@translate}</option>
{/foreach}
</select>
{'Option'|@translate} #2
<select name="Option2" >
{foreach from=$ppppp_array_support_options item=ppppp_row_support_options}
<option value="{$ppppp_row_support_options.Id}"{if $Option2Id==$ppppp_row_support_options.Id} selected{/if}>{$ppppp_row_support_options.OptionName|@translate}</option>
{/foreach}
</select>
<br>
<br>
<input type=submit value="{if $SupportId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Support'|@translate}</th>
<th>{'Option'|@translate} #1</th>
<th>{'Option'|@translate} #2</th>
<th colspan="2">{'Action'|@translate}</th>
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
<td>
<form method=post>
<input type=hidden name=IdToEdit value='{$ppppp_row_support.Id}'>
<input type=hidden name=MaterialIdToEdit value='{$ppppp_row_support.MaterialId}'>
<input type=hidden name=Option1IdToEdit value='{$ppppp_row_support.Option1Id}'>
<input type=hidden name=Option2IdToEdit value='{$ppppp_row_support.Option2Id}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!-- // RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO - RATIO // -->
{elseif $tabsheet_selected=='ratio'}
<h3>{'Ratios'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append ratios'|@translate}</legend>
<br>
<input type=hidden name=RatioId value="{$RatioId}">
{'Name'|@translate} <input type=text name=RatioName value="{$RatioName}">
{'Value'|@translate} <input type=text name=RatioValue value="{$RatioValue}">
<br>
<br>
<input type=submit value="{if $RatioId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Name'|@translate}</th>
<th>{'Value'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>

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
<td>
    <form method=post>
    <input type=hidden name=IdToEdit value='{$ppppp_row_ratio.Id}'>
    <input type=hidden name=RatioNameToEdit value='{$ppppp_row_ratio.RatioName}'>
    <input type=hidden name=RatioValueToEdit value='{$ppppp_row_ratio.RatioValue}'>
    <input type=submit value="{'Edit data'|@translate}">
    </form>
</tr>
{/foreach}
</table>
</fieldset>

<!--// SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE - SIZE //-->
{elseif $tabsheet_selected=='size'}
<h3>{'Size'|@translate}</h3>
<form method=post name=SizeEdit>
<fieldset>
<legend>{'Append photo size'|@translate}</legend>
<br>
    <table align="left">
        <tr>
            <td>
                <input type=hidden name=SizeId value="{$SizeId}">
                {'Ratio'|@translate} 
            </td>
            <td colspan="5">
                <select name="RatioId" >
                {foreach from=$ppppp_array_ratio item=ppppp_row_ratio}
                <option value="{$ppppp_row_ratio.Id}"{if $RatioId==$ppppp_row_ratio.Id} selected{/if}>{$ppppp_row_ratio.RatioName|@translate}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>{'Size'|@translate} (cm)</td><td><input type=text name=SizeName value="{$SizeName}"></td>
            <td>{'Width'|@translate} (cm)</td><td><input type=text name=Width_cm size="5" value="{$Width_cm}"></td>
            <td>{'Height'|@translate} (cm)</td><td><input type=text name=Height_cm size="5" value="{$Height_cm}"></td>
        </tr>
        <tr>
            <td>{'Size'|@translate} (in)</td><td><input type=text name=AltSizeName value="{$AltSizeName}"></td>
            <td>{'Width'|@translate} (in)</td><td><input type=text name=Width_in size="5" value="{$Width_in}"></td>
            <td>{'Height'|@translate} (in)</td><td><input type=text name=Height_in size="5" value="{$Height_in}"></td>
        </tr>
        <tr>
            <td>{'Min. Resolution'|@translate}</td>
            <td colspan="5"><input type=text name=MinRes size="5" value="{$MinRes}"></td>
        </tr>
        <tr>
            <td colspan="6">
                <input type=submit value="{if $SizeId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
            </td>
        </tr>
    </table>
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Size'|@translate}<br>(cm)</th>
<th>{'Size'|@translate}<br>(in)</th>
<th>{'Ratio'|@translate}</th>
<th>{'Width'|@translate}<br>(cm)</th>
<th>{'Height'|@translate}<br>(cm)</th>
<th>{'Width'|@translate}<br>(in)</th>
<th>{'Height'|@translate}<br>(in)</th>
<th>{'Min. Resolution'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>
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
<td>
<form method=post>
<input type=hidden name=IdToEdit value='{$ppppp_row_sizes.Id}'>
<input type=hidden name=SizeNameToEdit value='{$ppppp_row_sizes.SizeName}'>
<input type=hidden name=AltSizeNameToEdit value='{$ppppp_row_sizes.AltSizeName}'>
<input type=hidden name=RatioIdToEdit value='{$ppppp_row_sizes.RatioId}'>
<input type=hidden name=WidthcmToEdit value='{$ppppp_row_sizes.Width_cm}'>
<input type=hidden name=HeightcmToEdit value='{$ppppp_row_sizes.Height_cm}'>
<input type=hidden name=WidthinToEdit value='{$ppppp_row_sizes.Width_in}'>
<input type=hidden name=HeightinToEdit value='{$ppppp_row_sizes.Height_in}'>
<input type=hidden name=MinResToEdit value='{$ppppp_row_sizes.MinRes}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!--// PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE - PRICE //-->
{elseif $tabsheet_selected=='price'}
<h3>{'Price'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append prices'|@translate}</legend>
<br>
    <table align="left">
        <tr>
            <td align="center">
                <input type=hidden name=PriceId value="{$PriceId}">
                {'Support'|@translate}<br>
                <select name="SupportId" >
                {foreach from=$ppppp_array_support item=ppppp_row_support}
                <option value="{$ppppp_row_support.Id}"{if $SupportId==$ppppp_row_support.Id} selected{/if}>{$ppppp_row_support.SupportMaterial|@translate}{if $ppppp_row_support.SupportOption1!='None'}  {$ppppp_row_support.SupportOption1|@translate}{/if}{if $ppppp_row_support.SupportOption2!='None'} {$ppppp_row_support.SupportOption2|@translate}{/if}</option>
                {/foreach}
                </select></td>
            <td align="center">{'Size'|@translate}<br>
                 <select name="SizeId" >
                {foreach from=$ppppp_array_sizes item=ppppp_row_sizes}
                <option value="{$ppppp_row_sizes.Id}"{if $SizeId==$ppppp_row_sizes.Id} selected{/if}>{$ppppp_row_sizes.SizeName|@translate} ({$ppppp_row_sizes.Ratio|@translate})</option>
                {/foreach}
                </select></td>
            <td align="center">{'Provider'|@translate}<br>
                 <select name="ProviderId" >
                {foreach from=$ppppp_array_provider item=ppppp_row_provider}
                <option value="{$ppppp_row_provider.Id}"{if $ProviderId==$ppppp_row_provider.Id} selected{/if}>{$ppppp_row_provider.Name} ({$ppppp_row_provider.Currency})</option>
                {/foreach}
                </select></td>
            <td align="center">{'Price'|@translate}<br> <input type=text name=Price size="6" value="{$Price}"></td>
            <td align="center">{'Shipping fees'|@translate}<br> <input type=text name=Shipping size="6" value="{$Shipping}"></td>
        </tr>
        <tr>
            <td colspan="5">
                <input type=submit value="{if $PriceId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
            </td>
        </tr>
    </table>
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Id'|@translate}</th>
<th>{'Material'|@translate}</th>
<th>{'Option #1'|@translate}</th>
<th>{'Option #2'|@translate}</th>
<th>{'Size'|@translate}<br>(cm)</th>
<th>{'Size'|@translate}<br>(in)</th>
<th>{'Ratio'|@translate}</th>
<th>{'Height'|@translate}<br>(cm)</th>
<th>{'Width'|@translate}<br>(cm)</th>
<th>{'Provider'|@translate}</th>
<th>{'Price'|@translate}</th>
<th>{'Shipping fees'|@translate}</th>
<th>{'Currency'|@translate}</th>
<th colspan="2">{'Action'|@translate}</th>
</tr>
<tr>
    <form method=post>
    <fieldset>
    <td><input type=hidden name=filter value='{$ppppp_row_filter_act}'></td>
    <td align="center"><select name="filtMaterial" onchange="submit()">
        <option value="*"{if $ppppp_material_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_material_filt item=ppppp_row_material_filt}
        <option value="{$ppppp_row_material_filt.Id}"{if $ppppp_row_material_filt.Id==$ppppp_material_filt} selected{/if}>{$ppppp_row_material_filt.Material}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtOption1" onchange="submit()">
        <option value="*"{if $ppppp_option1_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_option1_filt item=ppppp_row_option1_filt}
        <option value="{$ppppp_row_option1_filt.Id}"{if $ppppp_row_option1_filt.Id==$ppppp_option1_filt} selected{/if}>{$ppppp_row_option1_filt.OptionName}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtOption2" onchange="submit()">
        <option value="*"{if $ppppp_option2_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_option2_filt item=ppppp_row_option2_filt}
        <option value="{$ppppp_row_option2_filt.Id}"{if $ppppp_row_option2_filt.Id==$ppppp_option2_filt} selected{/if}>{$ppppp_row_option2_filt.OptionName}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtSize" onchange="submit()">
        <option value="*"{if $ppppp_size_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_size_filt item=ppppp_row_size_filt}
        <option value="{$ppppp_row_size_filt.SizeName}"{if $ppppp_row_size_filt.SizeName==$ppppp_size_filt} selected{/if}>{$ppppp_row_size_filt.SizeName}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtAltSize" onchange="submit()">
        <option value="*"{if $ppppp_altsize_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_altsize_filt item=ppppp_row_altsize_filt}
        <option value="{$ppppp_row_altsize_filt.AltSizeName}"{if $ppppp_row_altsize_filt.AltSizeName==$ppppp_altsize_filt} selected{/if}>{$ppppp_row_altsize_filt.AltSizeName}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtRatio" onchange="submit()">
        <option value="*"{if $ppppp_ratio_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_ratio_filt item=ppppp_row_ratio_filt}
        <option value="{$ppppp_row_ratio_filt.Id}"{if $ppppp_row_ratio_filt.Id==$ppppp_ratio_filt} selected{/if}>{$ppppp_row_ratio_filt.RatioName}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtHeight" onchange="submit()">
        <option value="*"{if $ppppp_row_height_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_height_filt item=ppppp_row_height_filt}
        <option value="{$ppppp_row_height_filt.Height_cm}"{if $ppppp_row_height_filt.Height_cm==$ppppp_height_filt} selected{/if}>{$ppppp_row_height_filt.Height_cm}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtWidth" onchange="submit()">
        <option value="*"{if $ppppp_row_width_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_width_filt item=ppppp_row_width_filt}
        <option value="{$ppppp_row_width_filt.Width_cm}"{if $ppppp_row_width_filt.Width_cm==$ppppp_width_filt} selected{/if}>{$ppppp_row_width_filt.Width_cm}</option>
        {/foreach}
    </select></td>
    <td align="center"><select name="filtProvider" onchange="submit()">
        <option value="*"{if $ppppp_row_provider_filt=='*'} selected{/if}>{'All'|@translate}</option>
        {foreach from=$ppppp_array_provider_filt item=ppppp_row_provider_filt}
        <option value="{$ppppp_row_provider_filt.Id}"{if $ppppp_row_provider_filt.Id==$ppppp_provider_filt} selected{/if}>{$ppppp_row_provider_filt.Name}</option>
        {/foreach}
    </select></td>
    <td></td>
    <td></td>
    <td></td>
    <td align="center">
        <input type=submit value="{'Apply filter'|@translate}"></form>

    </td>
    </form>
    <td align="center">{if $ppppp_filter_active}
        <form method=post>
            <input type=hidden name=reset><input type=submit value="{'Reset filter'|@translate}">
        </form>
        {/if}
    </td>    

</tr>

{foreach from=$ppppp_array_price item=ppppp_row_price name=ppppp_row_price_loop}
<tr class="{if $smarty.foreach.ppppp_row_price_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_price.Id}</td>
<td align="center">{$ppppp_row_price.SupportMaterial}</td>
<td align="center">{$ppppp_row_price.SupportOption1}</td>
<td align="center">{$ppppp_row_price.SupportOption2}</td>
<td align="center">{$ppppp_row_price.Size}</td>
<td align="center">{$ppppp_row_price.AltSize}</td>
<td align="center">{$ppppp_row_price.Ratio}</td>
<td align="center">{$ppppp_row_price.Height}</td>
<td align="center">{$ppppp_row_price.Width}</td>
<td align="center">{$ppppp_row_price.Provider}</td>
<td align="center">{$ppppp_row_price.Price}</td>
<td align="center">{$ppppp_row_price.Shipping}</td>
<td align="center">{$ppppp_row_price.Currency}</td>
<td align="center">
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_price.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
<td align="center">
<form method=post>
<input type=hidden name=IdToEdit value='{$ppppp_row_price.Id}'>
<input type=hidden name=ProviderIdToEdit value='{$ppppp_row_price.ProviderId}'>
<input type=hidden name=SizeIdToEdit value='{$ppppp_row_price.SizeId}'>
<input type=hidden name=SupportIdToEdit value='{$ppppp_row_price.SupportId}'>
<input type=hidden name=PriceToEdit value='{$ppppp_row_price.Price}'>
<input type=hidden name=ShippingToEdit value='{$ppppp_row_price.Shipping}'>
<input type=submit value="{'Edit data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!--// PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE - PROMOCODE //-->
{elseif $tabsheet_selected=='code'}
<h3>{'Promo code'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Append promo code'|@translate}</legend>
<input type=hidden name=CodeId value="{$CodeId}">
<table align="left">
    <tr>
        <td>{'Code'|@translate}</td>
        <td><input type=text name=Code value="{$CodeValue}"></td>
    </tr>
    <tr>
        <td>{'Promo relative'|@translate}</td>
        <td><input type=text name=Promo_rel size="3" value="{$Relative}">%</td>
    </tr>
    <tr>
        <td>{'Promo absolute'|@translate}</td>
        <td><input type=text name=Promo_abs size="3" value="{$Absolute}"></td>
    </tr>
    <tr>
        <td>{'Promo shipping'|@translate}</td>
        <td> <input type=text name=Promo_ship size="3" value="{$Shipping}"></td>
    </tr>
    <tr>
        <td colspan="2">
            <input type=submit value="{if $CodeId==0}{'Append data'|@translate}{else}{'Update data'|@translate}{/if}">
        </td>
    </tr>
</table>


<br>
<br>

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
<th colspan="2">{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_promocode item=ppppp_row_promocode name=ppppp_row_promcoode_loop}
<tr class="{if $smarty.foreach.ppppp_row_promocode_loop.index is odd}row1{else}row2{/if}">
<td align="center">{$ppppp_row_promocode.Id}</td>
<td align="center">{$ppppp_row_promocode.Code}</td>
<td align="center">{$ppppp_row_promocode.Promo_rel}</td>
<td align="center">{$ppppp_row_promocode.Promo_abs}</td>
<td align="center">{$ppppp_row_promocode.Promo_ship}</td>
<td>
    <form method=post>
    <input type=hidden name=delete value='{$ppppp_row_promocode.Id}'>
    <input type=submit value="{'Delete data'|@translate}">
    </form>
</td>
<td>
    <form method=post>
    <input type=hidden name=IdToEdit value='{$ppppp_row_promocode.Id}'>
    <input type=hidden name=CodeValueToEdit value='{$ppppp_row_promocode.Code}'>
    <input type=hidden name=RelToEdit value='{$ppppp_row_promocode.Promo_rel}'>
    <input type=hidden name=AbsToEdit value='{$ppppp_row_promocode.Promo_abs}'>
    <input type=hidden name=ShipToEdit value='{$ppppp_row_promocode.Promo_ship}'>
    <input type=submit value="{'Edit data'|@translate}">
    </form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

<!--// CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG - CATALOG //-->
{elseif $tabsheet_selected=='Catalog'}
<h3>{'Catalog'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Configuration'|@translate}</legend>
<br>
    <table align="left">
        <tr>
            <td>{'Brand'|@translate}</td><td> <input type=text name=Brand value ="{$ppppp_cat_brand}"></td>
        </tr>
        <tr>
            <td>{'Google Product Category'|@translate}</td><td> <input type=text name=GoogleId value="{$ppppp_cat_googleId}"></td>
        </tr>
        <tr>
            <td>{'Facebook Product Category'|@translate}</td><td> <input type=text name=FBId value="{$ppppp_cat_fbId}"></td>
        </tr>
        <tr>
            <td>
            {'Reference catalog'|@translate}</td><td>
            <select name=Ref_country>
            {foreach from=$ppppp_array_provider item=ppppp_row_provider}
            <option value="{$ppppp_row_provider.CountryCode}"{if $ppppp_cat_ref_country==$ppppp_row_provider.CountryCode} selected{/if}>{$ppppp_row_provider.CountryName} ({$ppppp_row_provider.SupplierName} / {$ppppp_row_provider.Currency})</option>
            {/foreach}
            </select>
            </td>
        </tr>
        <tr>
            <td>{'Filename'|@translate}</td><td> <input type="input" name="filename" value="{$ppppp_cat_filenamebasis}" /></label>.xml</td>
        </tr>
        <tr>
            <td>
                <input type=submit value="{'Save Settings'|@translate}">
            </td>
        </tr>
    </table>
<br>
<br>
</fieldset>
</form>

<form method=post>
<fieldset>
<legend>{'Select provider for catalog'|@translate}</legend>

<select name=catalog_provider>
{foreach from=$ppppp_array_provider item=ppppp_row_provider}
<option value="{$ppppp_row_provider.CountryCode}"{if $ppppp_catalog_provider==$ppppp_row_provider.CountryCode} selected{/if}>{$ppppp_row_provider.Name} ({$ppppp_row_provider.CountryCode})</option>
{/foreach}
</select>
{if $CATALOG}
<a href="{$U_FILENAME}">{$FILENAME}</a>
{/if}
<br>
<br>

<input type=submit class="submit" value="{'Generate catalog'|@translate}" name="submit">
</fieldset>
</form>

<form method=post>
<fieldset>
<legend>{'Select country for catalog translation'|@translate}</legend>

<select name=catalog_country>
{foreach from=$ppppp_array_country item=ppppp_row_country}
<option value="{$ppppp_row_country.CountryLang}"{if $ppppp_catalog_country==$ppppp_row_country.CountryLang} selected{/if}>{$ppppp_row_country.CountryName} ({$ppppp_row_country.SupplierName})</option>
{/foreach}
</select>
{if $TRANSLATION}
<a href="{$U_FILENAME}">{$FILENAME}</a>
{/if}
<br>
<br>

<input type=submit class="submit" value="{'Generate catalog'|@translate}" name="submit">
</fieldset>
</form>

{/if}
