
<!-- sn Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#sn').submit();" title="{l s='Online payment with sn' mod='sn'}">
        <img src="modules/sn/sn.png" alt="{l s='Online payment with sn' mod='sn'}" />
		{l s=' پرداخت با کارتهای اعتباری / نقدی بانک های عضو شتاب توسط دروازه پرداخت آنلاین ' mod='sn'}
<br>
</a></p>

<form action="modules/sn/sn2.php?do=payment" method="post" id="sn" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of sn Payment Module-->
