# Decode Ads Website Revamp

[![CircleCI](https://circleci.com/gh/DecodeDigitalMarketing/decode-ads.svg)](https://circleci.com/gh/DecodeDigitalMarketing/decode-ads)
[![Dashboard decode-ads](https://img.shields.io/badge/dashboard-decode_ads-yellow.svg)](https://dashboard.pantheon.io/sites/8ce0c56f-9a2e-4d41-a892-fb17f05cf0ed#dev/code)
[![Dev Site decode-ads](https://img.shields.io/badge/site-decode_ads-blue.svg)](http://dev-decode-ads.pantheonsite.io/)

 ## Local Installation
1. Ask an admin to add you as a collaborator to Pantheon and GitHub, and create a [Pantheon machine token](https://pantheon.io/docs/machine-tokens).
2. Download & install [Composer](https://getcomposer.org/download/) and [Docksal](https://docs.docksal.io/getting-started/setup/), and make sure your Node version is `>=12`.
3. Clone the [GitHub repository](https://github.com/DecodeDigitalMarketing/decode-ads) and `cd` to the root directory.
4. Copy `.docksal/docksal-local.env.example`, rename as `.docksal/docksal-local.env`, and add your Pantheon machine token to this new file as `SECRET_TERMINUS_TOKEN="YOUR_TOKEN"`
5. Build vendor files and front-end assets
	- `composer install` 
	- `cd web/themes/custom/decode && npm i && npm run build-all`
6. Run `fin start`, then `fin pull db`, and `fin pull files` (Note: You might need to enter your Pantheon password after the files command.)