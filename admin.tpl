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
<th>{'Name'|@translate}</th>
<th>{'Country code'|@translate}</th>
<th>{'Currency'|@translate}</th>
<th>{'Provider'|@translate}</th>
</tr>
{foreach from=$ppppp_array_country item=ppppp_row_country name=ppppp_row_country_loop}
<tr class="{if $smarty.foreach.ppppp_row_country_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_country.CountryName}</td>
<td>{$ppppp_row_country.CountryCode}</td>
<td>{$ppppp_row_country.Currency}</td>
<td>{$ppppp_row_country.Name}</td>
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
{'Support'|@translate} <input type=text name=support>
{'Option'|@translate} #1 
<select name="option1" >
{foreach from=$ppppp_array_supportoption item=ppppp_row_supportoption}
<option value="{$ppppp_row_supportoption.Id}">{$ppppp_row_supportoption.OptionName|@translate}</option>
{/foreach}
</select>
{'Option'|@translate} #2
<select name="option2" >
{foreach from=$ppppp_array_supportoption item=ppppp_row_supportoption}
<option value="{$ppppp_row_supportoption.Id}">{$ppppp_row_supportoption.OptionName|@translate}</option>
{/foreach}
</select>
{'Factor'|@translate} <input type=text name=factor>
<br>
<br>
<input type=submit value="{'Append data'|@translate}">
</fieldset>
</form>
<fieldset>
<table class=table2>
<tr class=throw>
<th>{'Support'|@translate}</th>
<th>{'Option'|@translate} #1</th>
<th>{'Option'|@translate} #2</th>
<th>{'Factor'|@translate}</th>
<th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_support item=ppppp_row_support name=ppppp_row_support_loop}
<tr class="{if $smarty.foreach.ppppp_row_support_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_support.SupportName}</td>
<td>{$ppppp_row_support.SupportOption1}</td>
<td>{$ppppp_row_support.SupportOption2}</td>
<td>{$ppppp_row_support.factor}</td>
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
<form method=post>
<fieldset>
<legend>{'Append photo size'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Size'|@translate} <input type=text name=size></td>
            <td>{'Price factor'|@translate} <input type=text name=price></td>
            <td>{'GF'|@translate} <br>non<input type=radio name=GF value=0 checked="checked"><br>oui
    <input type=radio name=GF value=1></td>
            <td>{'SQ'|@translate} <br>non <input type=radio name=SQ value=0 checked="checked"><br>oui
    <input type=radio name=SQ value=1></td>
           <td>{'Pano52'|@translate} <br>non<input type=radio name=Pano52 value=0 checked="checked"><br>oui
    <input type=radio name=Pano52 value=1></td>
            <td>{'Pano31'|@translate} <br>non <input type=radio name=Pano31 value=0 checked="checked"><br>oui
    <input type=radio name=Pano31 value=1></td>
            <td>{'Pano41'|@translate} <br>non <input type=radio name=Pano41 value=0 checked="checked"><br>oui
    <input type=radio name=Pano41 value=1></td>
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
<th>{'Size'|@translate}</th>
<th>{'Price factor'|@translate}</th>
<th>{'GF'|@translate}</th>
<th>{'SQ'|@translate}</th>
<th>{'Pano52'|@translate}</th>
<th>{'Pano31'|@translate}</th>
<th>{'Pano41'|@translate}</th>
    <th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_size item=ppppp_row_size name=ppppp_row_size_loop}
<tr class="{if $smarty.foreach.ppppp_row_size_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_size.size}</td>
<td>{$ppppp_row_size.price}</td>
<td>{$ppppp_row_size.GF}</td>
<td>{$ppppp_row_size.SQ}</td>
<td>{$ppppp_row_size.Pano52}</td>
<td>{$ppppp_row_size.Pano31}</td>
<td>{$ppppp_row_size.Pano41}</td>
    <td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_size.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{elseif $tabsheet_selected=='sizeNew'}
<h3>{'Sizes'|@translate}</h3>
<form method=post name=NewSizeEdit>
<fieldset>
<legend>{'Append photo size'|@translate}</legend>
<br>
    <table>
        <tr>
            <td>{'Size'|@translate} <input type=text name=SizeName></td>
            <td>{'Ratio'|@translate} 
                <select name="Ratio" >
                {foreach from=$ppppp_array_ratio item=ppppp_row_ratio}
                <option value="{$ppppp_row_ratio.Id}">{$ppppp_row_ratio.RatioName|@translate}</option>
                {/foreach}
                </select>
                </td>
                <td>{'Height'|@translate} <input type=text name=Height onchange="UpdateLength()"></td>
            <td>{'Length'|@translate} <input type=text name=Length onchange="UpdateHeight()"></td>
           <td>{'Units'|@translate}
                <select name="Units" >
                {foreach from=$ppppp_array_units item=unit_label key=unit_code}
                <option value="{$unit_code}">{$unit_label}</option>
                {/foreach}
                </select>
            </td>
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
<th>{'Size'|@translate}</th>
<th>{'Ratio'|@translate}</th>
<th>{'Height'|@translate}</th>
<th>{'Length'|@translate}</th>
<th>{'Units'|@translate}</th>
<th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_sizes item=ppppp_row_sizes name=ppppp_row_sizes_loop}
<tr class="{if $smarty.foreach.ppppp_row_sizes_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_sizes.SizeName}</td>
<td>{$ppppp_row_sizes.Ratio}</td>
<td>{$ppppp_row_sizes.Height}</td>
<td>{$ppppp_row_sizes.Length}</td>
<td>{$ppppp_row_sizes.Units}</td>
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
                <option value="{$ppppp_row_support.Id}">{$ppppp_row_support.SupportName|@translate}{if $ppppp_row_support.SupportOption1!='None'}  {$ppppp_row_support.SupportOption1|@translate}{/if}{if $ppppp_row_support.SupportOption2!='None'} {$ppppp_row_support.SupportOption2|@translate}{/if}</option>
                {/foreach}
                </select></td>
            <td align="center">{'Size'|@translate}<br>
                 <select name="size" >
                {foreach from=$ppppp_array_sizes item=ppppp_row_sizes}
                <option value="{$ppppp_row_sizes.Id}">{$ppppp_row_sizes.SizeName|@translate} ({$ppppp_row_sizes.Ratio|@translate})</option>
                {/foreach}
                </select></td>
            <td align="center">{'Min. Resolution'|@translate}<br> <input type=text name=minres size="5"></td>
            <td align="center">{'Provider'|@translate}<br>
                 <select name="provider" >
                {foreach from=$ppppp_array_provider item=ppppp_row_provider}
                <option value="{$ppppp_row_provider.Id}">{$ppppp_row_provider.Name}</option>
                {/foreach}
                </select></td>
           <td align="center">{'Price'|@translate}<br> <input type=text name=price size="6"></td>
            <td align="center">{'Shipping fees'|@translate}<br> <input type=text name=shipping size="6"></td>
            <td align="center">{'Currency'|@translate}<br> <input type=text name=currency size="3"></td>
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
<th>{'Support'|@translate}</th>
<th>{'Option #1'|@translate}</th>
<th>{'Option #1'|@translate}</th>
<th>{'Size'|@translate}</th>
<th>{'Ratio'|@translate}</th>
<th>{'Height'|@translate}</th>
<th>{'Length'|@translate}</th>
<th>{'Units'|@translate}</th>
<th>{'Min Resolution'|@translate}</th>
<th>{'Provider'|@translate}</th>
<th>{'Price'|@translate}</th>
<th>{'Shipping'|@translate}</th>
<th>{'Currency'|@translate}</th>
    <th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_price item=ppppp_row_price name=ppppp_row_price_loop}
<tr class="{if $smarty.foreach.ppppp_row_price_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_price.Support}</td>
<td>{$ppppp_row_price.SupportOption1}</td>
<td>{$ppppp_row_price.SupportOption2}</td>
<td>{$ppppp_row_price.Size}</td>
<td>{$ppppp_row_price.Ratio}</td>
<td>{$ppppp_row_price.Height}</td>
<td>{$ppppp_row_price.Length}</td>
<td>{$ppppp_row_price.Units}</td>
<td>{$ppppp_row_price.MinRes}</td>
<td>{$ppppp_row_price.Provider}</td>
<td>{$ppppp_row_price.Price}</td>
<td>{$ppppp_row_price.Shipping}</td>
<td>{$ppppp_row_price.Currency}</td>
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
<h3>{'PromoCode'|@translate}</h3>
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
<th>{'Code'|@translate}</th>
<th>{'Promo relative'|@translate}</th>
<th>{'Promo absolute'|@translate}</th>
<th>{'Promo shipping'|@translate}</th>
</tr>
{foreach from=$ppppp_array_promocode item=ppppp_row_promocode name=ppppp_row_promcoode_loop}
<tr class="{if $smarty.foreach.ppppp_row_promocode_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_promocode.code}</td>
<td>{$ppppp_row_promocode.reduc_rel}</td>
<td>{$ppppp_row_promocode.reduc_abs}</td>
<td>{$ppppp_row_promocode.reduc_ship}</td>
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
<th>{'Name'|@translate}</th>
<th>{'Value'|@translate}</th>
</tr>
{foreach from=$ppppp_array_ratio item=ppppp_row_ratio name=ppppp_row_ratio_loop}
<tr class="{if $smarty.foreach.ppppp_row_ratio_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_ratio.RatioName}</td>
<td>{$ppppp_row_ratio.RatioValue}</td>
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

{elseif $tabsheet_selected=='supportoption'}
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
<th>{'Name'|@translate}</th>
</tr>
{foreach from=$ppppp_array_supportoption item=ppppp_row_supportoption name=ppppp_row_supportoption_loop}
<tr class="{if $smarty.foreach.ppppp_row_supportoption_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_supportoption.OptionName}</td>
    <td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_supportoption.Id}'>
<input type=submit value="{'Delete data'|@translate}">
</form>
</td>
</tr>
{/foreach}
</table>
</fieldset>

{else}
<h3>{'Shipping cost'|@translate}</h3>
<form method=post>
<fieldset>
<legend>{'Fixed shipping cost'|@translate}</legend>
<br>
<input type=text name=fixed_shipping value={$ppppp_fixed_shipping}>
<br>
<br>
<input type=submit value="{'Update data'|@translate}">
</fieldset>
</form>
{/if}
