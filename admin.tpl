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
<th>{'Factor'|@translate}</th>
<th>{'Action'|@translate}</th>
</tr>
{foreach from=$ppppp_array_support item=ppppp_row_support name=ppppp_row_support_loop}
<tr class="{if $smarty.foreach.ppppp_row_support_loop.index is odd}row1{else}row2{/if}">
<td>{$ppppp_row_support.support}</td>
<td>{$ppppp_row_support.factor}</td>
<td>
<form method=post>
<input type=hidden name=delete value='{$ppppp_row_support.id}'}>
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
<input type=hidden name=delete value='{$ppppp_row_size.id}'}>
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
<input type=hidden name=delete value='{$ppppp_row_promocode.id}'}>
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