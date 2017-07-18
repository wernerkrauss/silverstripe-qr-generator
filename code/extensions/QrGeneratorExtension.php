<?php

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;

/**
 * Class QrGeneratorExtension
 *
 * @property SiteTree|QrGeneratorExtension $owner
 */
class QrGeneratorExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        if (!$this->owner->isInDB()) {
            return;
        }

        $fields->addFieldsToTab('Root.QR', [
            HeaderField::create('QRHeading', _t('QrGeneratorExtension.Title', 'QR-Code'), 2),
            LiteralField::create('QRContent',
                '<p><img src="data:image/png;base64,' . $this->getQRCodeBase64() . '" /></p>'),
            LiteralField::create('QRDownload', '<p><a href="' . $this->getQRCodeURL() . '" target="_blank">'
                . _t('QrGeneratorExtension.Download', 'Download')
                . '</a></p>')
        ]);

    }


    /**
     * for inline images
     *
     * <img alt="Scan me" src="data:image/png;base64,$QRCodeBase64" />
     *
     * @return string
     */
    public function getQRCodeBase64()
    {
        return base64_encode($this->generateQRCode());
    }

    /**
     * Very simple proof of concept for now.
     *
     * uses AbsoluteLink() to get the URL...
     *
     * @todo: make output format configurable
     * @todo: make size configurable (by DataObject)
     *
     * @return string
     */
    public function generateQRCode()
    {
        $filename = ASSETS_PATH . $this->getQrCodeName();

        if (file_exists($filename)) {
            return file_get_contents($filename);
        }

        $qr = new QrCode($this->getQrCodeContent());
        $qr->setSize(300)
            ->setWriterByName('png')
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::LOW)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
            ->setValidateResult(false);

        $qr->writeFile(ASSETS_PATH . $this->getQrCodeName());

        return $qr->writeString();
    }

    /**
     * Helper method to generate the filename for the current QR-Code
     *
     * @todo: use classname etc...
     *
     * @return string
     */
    private function getQrCodeName()
    {
        $path = '/';
        $base = implode('-', [
                'qr',
                $this->owner->ClassName,
                $this->owner->Title,
                $this->owner->ID,
            ]) . '.png';

        return $path . $base;
    }

    /**
     * Uses absolute link as default.
     *
     * @todo: check if owner has a method to provide content. This might be useful for other types of codes,
     * e.g. for contact data, calendar data etc...
     *
     * @return mixed
     */
    private function getQrCodeContent()
    {
        return $this->owner->AbsoluteLink();
    }

    /**
     * URL for using in <img alt="Scan me" src="$QRCodeURL" />
     *
     * @return String
     */
    public function getQRCodeURL()
    {
        $this->generateQRCode();
        return Controller::join_links(Director::baseURL(), ASSETS_DIR, $this->getQrCodeName());
    }

}
