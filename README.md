# Silverstripe QR-Generator

## Installation
composer require wernerkrauss/silverstripe-qr-generator

## Requirements
* Silverstripe and Silverstripe CMS > 3.6, should work on 3.1 (untested)

## How it works

At the moment this module only generates QR codes for absolute links to the current Page. 
It utilizes `AbsoluteLink()` to get the content to encode. The codes are cached as png files in _/assets/qr/_

You can either include the code inline or as a source. Both will work out of the box:

### Inline QR Code
`
<img alt="Scan me" src="data:image/png;base64,$QRCodeBase64" />
`

### Linked Image

`
<img alt="Scan me" src="$QRCodeURL" />
`

## Todo
*  More pre-defined formats with wrapper, e.g. calendar item, address...
*  create a Subclass of ViewableData that wraps the generated QR code and can be modified in templates
*  make cache dir configurable
*  make name generation configurable; e.g. check for locale or subsites
