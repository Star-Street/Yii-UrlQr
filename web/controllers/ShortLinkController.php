<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\ShortLink;
use app\models\LinkVisit;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;


class ShortLinkController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $result = [
            'success' => false,
            'qrCode' => '',
            'shortUrl' => '',
            'error' => ''
        ];

        try {
            $originalUrl = Yii::$app->request->post('original_url');
            $originalUrl = trim($originalUrl);

            // validate URL
            if (!$this->isValidUrlFormat($originalUrl)) {
                $result['error'] = 'URL is not valid...';
                return $result;
            }

            // check accessible URL
            if (!$this->isUrlAccessible($originalUrl)) {
                $result['error'] = 'URL is not accessible...';
                return $result;
            }

            // search existing url
            if ($existing = ShortLink::findOne(['original_url' => $originalUrl])) {
                $result['success'] = true;
                $result['shortUrl'] = $this->getShortUrl($existing->short_code);
                $result['qrCode'] = $this->generateQrCode($result['shortUrl']);
                return $result;
            }

            do {
                $code = $this->generateCode(5);
            } while (ShortLink::find()->where(['short_code' => $code])->exists());

            $model = new ShortLink([
                'original_url' => $originalUrl,
                'short_code' => $code,
            ]);

            if ($model->validate()) {
                if ($model->save()) {
                    $shortUrl = $this->getShortUrl($code);
                    $result['success'] = true;
                    $result['shortUrl'] = $shortUrl;
                    $result['qrCode'] = $this->generateQrCode($shortUrl);
                } else {
                    $result['error'] = 'Error saving URL: ' . json_encode($model->errors);
                }
            } else {
                Yii::error($model->errors, __METHOD__);
                throw new \yii\web\HttpException(500, json_encode($model->errors));
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    public function actionRedirect($code): Response
    {
        $model = ShortLink::findOne(['short_code' => $code]);

        if (!$model) {
            throw new NotFoundHttpException('Link not found');
        }

        try {
            $ip = Yii::$app->request->getUserIP() ?? 'unknown';
            //$referrer = Yii::$app->request->getReferrer();
            //$userAgent = Yii::$app->request->getUserAgent();

            $visit = LinkVisit::findOne([
                'short_link_id' => $model->id,
                'ip_source' => $ip,
            ]);

            if ($visit) {
                $visit->qty += 1;
            } else {
                $visit = new LinkVisit([
                    'short_link_id' => $model->id,
                    'qty' => 1,
                    'ip_source' => $ip,
                ]);
            }

            if (!$visit->save()) {
                Yii::error("Failed to save visit: " . json_encode($visit->errors, JSON_UNESCAPED_UNICODE), __METHOD__);
            }
        } catch (\Exception $e) {
            Yii::error("Exception while saving visit: " . $e->getMessage(), __METHOD__);
        }

        return $this->redirect($model->original_url, 302);
    }

    protected function generateQrCode(string $url): string
    {
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->size(300)
            ->margin(10)
            ->build();

        // save qr in temple file
        $qrCodePath = Yii::getAlias('@webroot/images/qr/');
        if (!file_exists($qrCodePath)) {
            mkdir($qrCodePath, 0777, true);
        }

        $filename = md5($url) . '.png';
        if (file_exists($qrCodePath . $filename)) {
            return Yii::getAlias('@web/images/qr/' . $filename);
        }

        $qrCode->saveToFile($qrCodePath . $filename);
        return Yii::getAlias('@web/images/qr/' . $filename);
    }

    protected function generateCode($length = 5): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $max)];
        }

        return $code;
    }

    protected function getShortUrl($code): string
    {
        return Yii::$app->request->hostInfo . '/' . $code;
    }

    /**
     * @throws BadRequestHttpException
     */
    protected function isValidUrlFormat($url): bool
    {
        if (empty($url)) {
            throw new BadRequestHttpException('URL cannot be empty...');
        }

        $parts = parse_url($url);

        if ($parts === false) {
            throw new BadRequestHttpException('Wrong structure URL...');
        }

        if (empty($parts['scheme']) || empty($parts['host'])) {
            throw new BadRequestHttpException('URL must contain structure (http/https) and domain...');
        }

        if (!in_array(strtolower($parts['scheme']), ['http', 'https'])) {
            throw new BadRequestHttpException('Must accept only HTTP and HTTPS protocols...');
        }

        if (!filter_var($parts['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new BadRequestHttpException('Wrong domain name...');
        }

        if (!checkdnsrr($parts['host'], 'A') && !checkdnsrr($parts['host'], 'AAAA')) {
            throw new BadRequestHttpException('Domain does not exist or has no DNS records...');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new BadRequestHttpException('Wrong URL...');
        }

        if (preg_match('/[\s<>]/', $url)) {
            throw new BadRequestHttpException('URL contains invalid characters...');
        }

        return true;
    }

    protected function isUrlAccessible(string $url): bool
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 400;
    }
}
