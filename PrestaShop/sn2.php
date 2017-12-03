<?php

session_start();
if (isset($_GET['do'])) {
	include (dirname(__FILE__) . '/../../config/config.inc.php');
	include (dirname(__FILE__) . '/../../header.php');
	include_once (dirname(__FILE__) . '/sn.php');
	$sn = new sn;

	if ($_GET['do'] == 'payment') {

			$sn -> do_payment($cart);

	} else {
		if (isset($_GET['sec']) && isset($_GET['md'])) {
		// Security
$sec=$_GET['sec'];
$mdback = md5($sec.'vm');
$mdurl=$_GET['md'];
// Security		
				if($mdback == $mdurl){
			
		
$transData = $_SESSION[$sec];
$au=$transData['au']; //


			$orderId = $_GET['order_id'];
			$amount = $transData['price'];
			$au=$transData['au'];
			if (isset($_GET['order_id'])) {
				
				
					$api = Configuration::get('sn_API');
					
$bank_return = $_POST + $_GET ;
$data_string = json_encode(array (
'pin' => $api,
'price' => ceil($amount/10),
'order_id' => $orderId,
'au' => $au,
'bank_return' =>$bank_return,
));

$ch = curl_init('https://developerapi.net/api/v1/verify');
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


					if (!empty($json['result']) and $json['result'] == 1) {
						error_reporting(E_ALL);
						
						$sn -> validateOrder($orderId, _PS_OS_PAYMENT_, $amount, $sn -> displayName, "سفارش تایید شده / کد رهگیری {$au}", array(), $cookie -> id_currency);
						$orderId = '';
						Tools::redirect('history.php');
					} else {
						echo $sn -> error($sn -> l('خطایی رخ داده است ') . ' (' .$msg. ')<br/>' . $sn -> l('Authority code') . ' : ' . $au);
					}

				
			} else {
				echo $sn -> error($sn -> l('There is a problem.'));
			}
		} else {
			echo $sn -> error($sn -> l('There is a problem.'));
		}
				} else {
			echo $sn -> error($sn -> l('There is a problem.'));
		}
	}
	include_once (dirname(__FILE__) . '/../../footer.php');
} else {
	_403();
}
function _403() {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}