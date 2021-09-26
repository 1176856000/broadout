<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Linkshare extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        return $this->_getRenderedCode($_section, $_includeon);
    }

    public function getUseCadence()
    {
        return $this->getAdditionalDataValue('use_cadence');
    }

    public function getTrackingKey()
    {
        return $this->getAdditionalDataValue('tracking_key');
    }

    /**
     * {@inheritdoc}
     */
    protected function _getRenderedCode($_section, $_includeon = null)
    {
        switch ($_section) {
            case parent::SECTION_HEAD:
                return $this->_spiAllPages();
            case parent::SECTION_BODYBEGIN:
                $code = '';
                if (isset($_includeon['checkout_success'])) {
                    $order = $this->getLastOrder();
                    if ($order && $order->getId()) {
                        $code .= $this->_spiCheckoutSuccess($order);
                    }
                }
                return $code;
            default:
                return null;
        }
    }

    protected function getEncodedUrl()
    {
        return urlEncode(rtrim($this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB), '/'));
    }

    /**
     * Rakuten Marketing Conversion Tag
     * @return $this
     */
    protected function _spiCheckoutSuccess($order)
    {
        $customerStatus = $this->isNewCustomer($order)? 'New' : 'Existing';
        $discountAmount = $order->getDiscountAmount()? round($order->getDiscountAmount(), 2) : 0;
        $browserStatus  = 'Desktop';

        //Identifying if user is on mobile browser or not
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$this->_request->getServer('HTTP_USER_AGENT'))||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($this->_request->getServer('HTTP_USER_AGENT'),0,4))
        ) {
            $browserStatus = 'Mobile';
        }

        $data = [
            'orderid'           => (string)$order->getIncrementId(), // ORDERID
            'currency'          => (string)$this->getCurrencyCode($order), // CURRENCYCODE
            'customerStatus'    => (string)$customerStatus, // CUSTOMER_STATUS
            'conversionType'    => (string)'Sale',
            'deviceType'        => (string)$browserStatus,
            'customerID'        => (string)$order->getCustomerId(), // CUSTOMER_ID
            'discountCode'      => (string)$order->getCouponCode(), // DISCOUNT_CODE
            'discountAmount'    => (double)$discountAmount, // DISCOUNT_AMOUNT
            'taxAmount'         => (double)$order->getTaxAmount(), // TAX_AMOUNT
            'ranMID'            => (string)$this->getMerchantId(), // 41454
            'siteName'          => (string)$this->getEncodedUrl(), // SITE_NAME
        ];

        foreach ($order->getAllVisibleItems() as $item) {
            $data['lineitems'][] = [
                'quantity'          => (int)$item->getQtyOrdered(),
                'unitPrice'         => round($item->getPriceInclTax(), 2),
                'unitPriceLessTax'  => round($item->getPrice(), 2),
                'SKU'               => (string)$item->getSku(),
                'productName'       => (string)$item->getName(),
            ];
        }

        $data = json_encode($data);

        $js = <<<JAVASCRIPT
<!-- START of Rakuten Marketing Conversion Tag -->
<script type="text/javascript">
var rm_trans = {$data};

/*Do not edit any information beneath this line*/
if(!window.DataLayer){window.DataLayer={Sale:{Basket:rm_trans}}}else{DataLayer.Sale=DataLayer.Sale||{Basket:rm_trans};DataLayer.Sale.Basket=DataLayer.Sale.Basket||rm_trans}DataLayer.Sale.Basket.Ready = true; function sRAN(){var a="p",b=1,c="p",d=1,e=1,f=DataLayer&&DataLayer.Sale&&DataLayer.Sale.Basket?DataLayer.Sale.Basket:{},g=f.affiliateMID||f.ranMID;if(!g)return!1;var h=f.allowCommission;if(h&&"false"==h.toLowerCase())return!1;var i=f.orderid||"OrderNumberNotAvailable",j="",k="",l="",m="",n=f.currency||"",o="o"===c,p=f.taxAmount?Math.abs(Math.round(100*Number(f.taxAmount))):0,q=f.discountAmount?Math.abs(Math.round(100*Number(f.discountAmount))):0,r="p"===a?"ep":"m"===a?"eventnvppixel":"",s=f.customerStatus||"",t="",dc=f.discountCode,sn=f.siteName;t=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge|maemo|midp|mmp|netfront|opera m(ob|in)i|palm(os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows(ce|phone)|xda|xiino/i.test(navigator.userAgent)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|awa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r|s)|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp(i|ip)|hs\-c|ht(c(\-||_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac(|\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt(|\/)|klon|kpt|kwc\-|kyo(c|k)|le(no|xi)|lg(g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-||o|v)|zz)|mt(50|p1|v)|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v)|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-|)|webc|whit|wi(g|nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))?"Mobile":"Desktop";var u=document.location.protocol+"//track.linksynergy.com/"+r+"?",v="";null!=s&&""!=s&&(e&&"EXISTING"==s.toUpperCase()||e&&"RETURNING"==s.toUpperCase())&&(v="R_"),d&&"MOBILE"==t.toUpperCase()&&"R_"==v&&(v="MR_"),d&&"MOBILE"==t.toUpperCase()&&""==v&&(v="M_");for(var w=[],x=0;x<(f.lineitems?f.lineitems.length:0);x++){for(var y=!1,z=window.JSON?JSON.parse(JSON.stringify(f.lineitems[x])):f.lineitems[x],A=0;A<w.length;A++){var B=w[A];B.SKU===z.SKU&&(y=!0,w[A].quantity=Number(w[A].quantity)+Number(z.quantity))}y||w.push(z)}for(var x=0;x<w.length;x++){var z=w[x],C=encodeURIComponent(z.SKU),D=z.unitPriceLessTax||z.unitPrice,E=z.quantity,F=encodeURIComponent(z.productName)||"";j+=v+C+"|",k+=E+"|",l+=Math.round(Number(D)*Number(E)*100)+"|",m+=v+F+"|"}j=j.slice(0,-1),k=k.slice(0,-1),l=l.slice(0,-1),m=m.slice(0,-1),q&&b&&(j+="|"+v+"DISCOUNT",m+="|"+v+"DISCOUNT",k+="|0",l+="|-"+q),o&&p&&(j+="|"+v+"ORDERTAX",k+="|0",l+="|-"+p,m+="|"+v+"ORDERTAX"),u+="mid="+g+"&ord="+i+"&skulist="+j+"&qlist="+k+"&amtlist="+l+"&cur="+n+"&namelist="+m+(dc?"&coupon="+dc:"")+(sn?"&sitename="+sn:"")+"&img=1";var G,H=document.createElement("img");H.setAttribute("src",u),H.setAttribute("height","1px"),H.setAttribute("width","1px"),G=document.getElementsByTagName("script")[0],G.parentNode.insertBefore(H,G)}sRAN();
</script>
<!-- END of Rakuten Marketing Conversion Tag -->
JAVASCRIPT;

        return $js;
    }

    /**
     * Rakuten Marketing Tracking
     * @return $this
     */
    protected function _spiAllPages()
    {
        if (!$this->getUseCadence()) {
            return '';
        }
        $js = <<<JAVASCRIPT
<!-- START Rakuten Marketing Tracking -->
<script type="text/javascript">
    (function (url) {
    /*Tracking Bootstrap
    Set Up DataLayer objects/properties here*/
    if(!window.DataLayer) {
        window.DataLayer = {};
    }
    if(!DataLayer.events) {
        DataLayer.events = {};
    }
    DataLayer.events.SiteSection = "1";
    var loc, ct = document.createElement("script");
    ct.type = "text/javascript";
    ct.async = true;
    ct.src = url;
    loc = document.getElementsByTagName('script')[0];
    loc.parentNode.insertBefore(ct, loc);
    }(document.location.protocol + "//intljs.rmtag.com/{$this->getTrackingKey()}.ct.js"));
</script>
<!-- END Rakuten Marketing Tracking -->
JAVASCRIPT;

        return $js;
    }
}
