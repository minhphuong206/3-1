<?php
class MomoPayment {

    private $partnerCode = "MOMOBKUN20180529";
    private $accessKey   = "klm05TvNBzhg7h7j";
    private $secretKey   = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";

    public function createPayment($orderId, $amount) {

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $baseUrl  = $protocol . $_SERVER['HTTP_HOST'] . "/";

        $redirectUrl = $baseUrl . "index.php?ctrl=cart&act=return_momo";
        $ipnUrl      = $baseUrl . "index.php?ctrl=cart&act=notify_momo";

        $requestId   = $orderId;
        $requestType = "captureWallet";
        $extraData   = "";
        $orderInfo   = "Thanh toan don hang " . $orderId;

        // ❗ RAW HASH KHÔNG ENCODE
        $rawHash =
            "accessKey=".$this->accessKey.
            "&amount=".$amount.
            "&extraData=".$extraData.
            "&ipnUrl=".$ipnUrl.
            "&orderId=".$orderId.
            "&orderInfo=".$orderInfo.
            "&partnerCode=".$this->partnerCode.
            "&redirectUrl=".$redirectUrl.
            "&requestId=".$requestId.
            "&requestType=".$requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'requestId'   => $requestId,
            'amount'      => (int)$amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'requestType' => $requestType,
            'extraData'   => $extraData,
            'signature'   => $signature
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return ['resultCode' => 99, 'message' => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($result, true);
    }
}
