## [3.1.1](https://github.com/baumrock/RockForms/compare/v3.1.0...v3.1.1) (2025-07-13)


### Bug Fixes

* ajax post losing querystring if present ([095e6be](https://github.com/baumrock/RockForms/commit/095e6bebdfefa36859a2ddababc17c8ca2a728aa))

## [3.1.0](https://github.com/baumrock/RockForms/compare/v3.0.2...v3.1.0) (2025-07-02)


### Features

* add debug feature + docs ([fdadc3c](https://github.com/baumrock/RockForms/commit/fdadc3ca418a3f52ec97372e96b39d00fa7def87))
* add domready option for CSRF ([89278dc](https://github.com/baumrock/RockForms/commit/89278dc5de5e4c6a6ca9f00764188bc65a67e950))
* add GDPR auto-delete feature ([c9f54a9](https://github.com/baumrock/RockForms/commit/c9f54a936d63f95658eab795f525cab5d6429852))
* add hookField() method for easy wrapper manipulation ([4940a97](https://github.com/baumrock/RockForms/commit/4940a978cfe473f5a0275e0734d8a4ae3c0422b1))
* add ip address to meta-data in saveEntry() ([543cf6e](https://github.com/baumrock/RockForms/commit/543cf6ef286c9b4189eb08bd97c22ecb20badf6e))
* add IP to form submission log ([12109b5](https://github.com/baumrock/RockForms/commit/12109b5fe20f10aec470be5384796ffb804773da))
* add login() method to login a user by mail+password ([6adc624](https://github.com/baumrock/RockForms/commit/6adc624ac414cf4ae963424d74c19c503e49d6ba))
* add successMarkup() / noRedirectPattern option ([dc4a98d](https://github.com/baumrock/RockForms/commit/dc4a98d0d09c40999e42d05e39e730806908d154))
* add successRedirectUrl() method to define the redirect url of the success redirect ([4c9b9dd](https://github.com/baumrock/RockForms/commit/4c9b9dd789fe6b2dcab7aa05ab50ca6ac374c797))
* catch errors in processInput and show error message ([be3855b](https://github.com/baumrock/RockForms/commit/be3855b7ebe1fb9183c0de6791bb6e4684de3ab8))
* improve timeonpage spam protection and flex support ([f05e3d9](https://github.com/baumrock/RockForms/commit/f05e3d90fe3ff227a73504b24fcb103401e227bd))
* mask passwords in logged form submissions ([9778315](https://github.com/baumrock/RockForms/commit/9778315e00e19fb6ba430a10645f34c7e797a1a0))


### Bug Fixes

* add missing options param for renderTable helper ([9e86375](https://github.com/baumrock/RockForms/commit/9e863759efc8176263085402669f07fc0e4ce378))
* add missing uikit classes for datetime and checkboxlist ([1a8075e](https://github.com/baumrock/RockForms/commit/1a8075ee83c5a758df63178dc809bb77eeb5e6ce))
* improve timeonpage spam protection flexbox support ([f00a834](https://github.com/baumrock/RockForms/commit/f00a8349023b03df3a0a093a938a4ac5a27925cb))
* improve timeonpage spam protection to support HTMX loaded forms ([6e79e82](https://github.com/baumrock/RockForms/commit/6e79e8247bb533269d1d2a26bff8fdaa02cf2e8e))
* nullable param ([6faca92](https://github.com/baumrock/RockForms/commit/6faca9239404a4ac1851ca5bc3a59388b522f083))
* prevent alert for timeonpage field ([d99bd5a](https://github.com/baumrock/RockForms/commit/d99bd5a4b51ccb6317b6703c63d4403d0a06b70a))
* remove custom htmx errors ([99abc2d](https://github.com/baumrock/RockForms/commit/99abc2d36a77da5fbe1348a2f8ba8cbb0a0b22d8))
* rename deprecated property names ([c582432](https://github.com/baumrock/RockForms/commit/c582432b00e084c10382c2b2d42b31fbf2a4ab2b))
* typeerror if no form name provided ([404d509](https://github.com/baumrock/RockForms/commit/404d50919fe3fb43775fad34193c8f1e8b1e2648))

## [3.0.2](https://github.com/baumrock/RockForms/compare/v3.0.1...v3.0.2) (2025-03-12)


### Bug Fixes

* improve rockdevtools check ([70d6294](https://github.com/baumrock/RockForms/commit/70d6294b95d396c05d28cb6bf30a5455b443e872))
* new live validation loading concept ([c138168](https://github.com/baumrock/RockForms/commit/c1381687eb512f9ec3e5ec3dead71a83c4b93f05))
* remove obsolete addAssets hook ([f8d5094](https://github.com/baumrock/RockForms/commit/f8d50945bccac3c1bc8acd57eb158c417f1b2690))

## [3.0.1](https://github.com/baumrock/RockForms/compare/v3.0.0...v3.0.1) (2025-02-07)


### Bug Fixes

* add early exit if not <form> element ([d0b0fb2](https://github.com/baumrock/RockForms/commit/d0b0fb2aad78f6a725bcf1ee1ee3f9fae5aa2880))

## [3.0.0](https://github.com/baumrock/RockForms/compare/v2.1.1...v3.0.0) (2025-02-05)


### ⚠ BREAKING CHANGES

* refactoring & cleanup to use RockDevTools

### Features

* add field to select a form ([8645bf8](https://github.com/baumrock/RockForms/commit/8645bf870ae622d98bab7d0ba4dd4a34c8e7e943))
* refactoring & cleanup to use RockDevTools ([f649098](https://github.com/baumrock/RockForms/commit/f649098b97f3c54a0bf24bba68b3b7a27aa4763a))

