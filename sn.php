<?php
@session_start();
if (!defined('_PS_VERSION_'))
	exit ;
class sn extends PaymentModule {

	private $_html = '';
	private $_postErrors = array();

	public function __construct() {

		$this->name = 'sn';
		$this->tab = 'payments_gateways';
		$this->version = '1.1';
		$this->author = 'sn';
		$this->currencies = true;
		$this->currencies_mode = 'radio';

		parent::__construct();

		$this->displayName = $this->l('sn Payment Modlue');
		$this->description = $this->l('Online Payment With sn');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');

		$config = Configuration::getMultiple(array('sn_API'));

		if (!isset($config['sn_API']))
			$this->warning = $this->l('You have to enter your sn merchant key to use sn for your online payments.');

	}

	public function install() {

		if (!parent::install() || !Configuration::updateValue('sn_API', '') || !Configuration::updateValue('sn_LOGO', '') || !Configuration::updateValue('sn_HASH_KEY', $this->hash_key()) || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
			return false;
		else
			return true;
	}

	public function uninstall() {

		if (!Configuration::deleteByName('sn_API') || !Configuration::deleteByName('sn_LOGO') || !Configuration::deleteByName('sn_HASH_KEY') || !parent::uninstall())
			return false;
		else
			return true;
	}

	public function hash_key() {
		$en = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

		$one = rand(1, 26);
		$two = rand(1, 26);
		$three = rand(1, 26);

		return $hash = $en[$one] . rand(0, 9) . rand(0, 9) . $en[$two] . $en[$tree] . rand(0, 9) . rand(10, 99);
	}

	public function getContent() {

		if (Tools::isSubmit('sn_setting')) {

			Configuration::updateValue('sn_API', $_POST['jp_API']);

			$this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
		}

		$this->_generateForm();
		return $this->_html;
	}

	private function _generateForm() {
		$this->_html .= '<div align="center"><form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		$this->_html .= $this->l('Enter your pin :') . '<br/><br/>';
		$this->_html .= '<input type="text" name="jp_API" value="' . Configuration::get('sn_API') . '" ><br/><br/>';
		$this->_html .= '<input type="submit" name="sn_setting"';
		$this->_html .= 'value="' . $this->l('Save it!') . '" class="button" />';
		$this->_html .= '</form><br/></div>';
	}

	public function do_payment($cart) {
		
	// Security
	@session_start();
	$sec = uniqid();
	$md = md5($sec.'vm');
	// Security
			$snPin = Configuration::get('sn_API');
			$amount = floatval(number_format($cart ->getOrderTotal(true, 3), 2, '.', ''));
			$callbackUrl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/sn/sn2.php?do=call_back&id=' . $cart ->id . '&amount=' . $amount. '&sec=' . $sec. '&md=' . $md;
			$orderId = $cart ->id;

	$hash = Configuration::get('sn_HASH');

	$data_string = json_encode(array(
	'pin'=> $snPin,
	'price'=> ceil($amount/10),
	'callback'=> $callbackUrl ,
	'order_id'=> $orderId,
	'ip'=> $_SERVER['REMOTE_ADDR'],
	'callback_type'=>2
	));



	$ch = curl_init('https://developerapi.net/api/v1/request');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Content-Length: ' . strlen($data_string))
	);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result,true);


					$res=$json['result'];

				        switch ($res) {
						    case -1:
						    $msg = "پارامترهای ارسالی برای متد مورد نظر ناقص یا خالی هستند . پارمترهای اجباری باید ارسال گردد";
						    break;
						     case -2:
						    $msg = "دسترسی api برای شما مسدود است";
						    break;
						     case -6:
						    $msg = "عدم توانایی اتصال به گیت وی بانک از سمت وبسرویس";
						    break;

						     case -9:
						    $msg = "خطای ناشناخته";
						    break;

						     case -20:
						    $msg = "پین نامعتبر";
						    break;
						     case -21:
						    $msg = "ip نامعتبر";
						    break;

						     case -22:
						    $msg = "مبلغ وارد شده کمتر از حداقل مجاز میباشد";
						    break;


						    case -23:
						    $msg = "مبلغ وارد شده بیشتر از حداکثر مبلغ مجاز هست";
						    break;
						    
						      case -24:
						    $msg = "مبلغ وارد شده نامعتبر";
						    break;
						    
						      case -26:
						    $msg = "درگاه غیرفعال است";
						    break;
						    
						      case -27:
						    $msg = "آی پی مسدود شده است";
						    break;
						    
						      case -28:
						    $msg = "آدرس کال بک نامعتبر است ، احتمال مغایرت با آدرس ثبت شده";
						    break;
						    
						      case -29:
						    $msg = "آدرس کال بک خالی یا نامعتبر است";
						    break;
						    
						      case -30:
						    $msg = "چنین تراکنشی یافت نشد";
						    break;
						    
						      case -31:
						    $msg = "تراکنش ناموفق است";
						    break;
						    
						      case -32:
						    $msg = "مغایرت مبالغ اعلام شده با مبلغ تراکنش";
						    break;
						 
						    
						      case -35:
						    $msg = "شناسه فاکتور اعلامی order_id نامعتبر است";
						    break;
						    
						      case -36:
						    $msg = "پارامترهای برگشتی بانک bank_return نامعتبر است";
						    break;
						        case -38:
						    $msg = "تراکنش برای چندمین بار وریفای شده است";
						    break;
						    
						      case -39:
						    $msg = "تراکنش در حال انجام است";
						    break;
						    
                            case 1:
						    $msg = "پرداخت با موفقیت انجام گردید.";
						    break;

						    default:
						       $msg = $json['msg'];
						}


					
		
		if(!empty($json['result']) AND $json['result'] == 1)
		{
		$_SESSION[$sec] = [
			'price'=>$amount ,
			'order_id'=>$orderId ,
			'au'=>$json['au'] ,
		];

				    echo $this->success($this->l('Redirecting...'));
					echo ('<div style="display:none">'.$json['form'].'</div>Please wait ... <script language="javascript">document.payment.submit(); </script>');
					} else {
					echo $this->error($this->l('خطایی رخ داده است.') . ' ' .$msg. '');
				    }
				
			}

	public function error($str) {
		return '<div class="alert error">' . $str . '</div>';
	}

	public function success($str) {
		echo '<div class="conf confirm">' . $str . '</div>';
	}

	public function hookPayment($params) {
		global $smarty;
		$smarty ->assign('sn_logo', Configuration::get('sn_LOGO'));
		if ($this->active)
			return $this->display(__FILE__, 'snpayment.tpl');
	}

	public function hookPaymentReturn($params) {
		if ($this->active)
			return $this->display(__FILE__, 'snconfirmation.tpl');
	}

}


?>