{l s='Your order on %s is complete.' sprintf=$shop_name mod='sn'}
		{if !isset($reference)}
			<br /><br />{l s='Your order number' mod='sn'}: {$id_order}
		{else}
			<br /><br />{l s='Your order number' mod='sn'}: {$id_order}
			<br /><br />{l s='Your order reference' mod='sn'}: {$reference}
		{/if}		<br /><br />{l s='An email has been sent with this information.' mod='sn'}
		<br /><br /> <strong>{l s='Your order will be sent as soon as posible.' mod='sn'}</strong>
		<br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='sn'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='sn'}</a>.
	</p><br />