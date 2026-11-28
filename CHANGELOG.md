## [4.0.2](https://github.com/baumrock/RockForms/compare/v4.0.1...v4.0.2) (2026-11-28)


### Bug Fixes

* load RockFormsPage before MagicPage autoload ([f0cf96e](https://github.com/baumrock/RockForms/commit/f0cf96e360161f707d7e6d986f97476e39b3cc9c))
* register namespaces in __construct for early page class boot ([b47fb00](https://github.com/baumrock/RockForms/commit/b47fb0095c26027605b7064c4b3145ef0a4fe72e))
* use getComponentTree instead of deprecated getComponents(true) ([f34af84](https://github.com/baumrock/RockForms/commit/f34af84294975e506a26a09475a5864b8ad49cb5))

## [4.0.1](https://github.com/baumrock/RockForms/compare/v4.0.0...v4.0.1) (2026-11-15)


### Bug Fixes

* avoid null array offset in Nette Container (PHP 8.5) ([d60e455](https://github.com/baumrock/RockForms/commit/d60e455c16098f821fba498a9281ab634024b021))
* guard rockloaders call when helper is missing ([4e0df63](https://github.com/baumrock/RockForms/commit/4e0df6325873cf1779535c70a317f02752ce1f74))
* use hidden csrf field and cleaner honeypot markup ([ed61f7b](https://github.com/baumrock/RockForms/commit/ed61f7b2c04b590f2c0cc6134a09464bade8d600))

## [4.0.0](https://github.com/baumrock/RockForms/compare/v3.2.0...v4.0.0) (2026-01-21)


### ⚠ BREAKING CHANGES

* change license to MIT

### Bug Fixes

* change license to MIT ([8a28d62](https://github.com/baumrock/RockForms/commit/8a28d6252c5c73115465db7cd03efddf187b946a))

## [3.2.0](https://github.com/baumrock/RockForms/compare/v3.1.1...v3.2.0) (2025-08-02)


### Features

* add form-invalid class if nette validation fails ([46e4f87](https://github.com/baumrock/RockForms/commit/46e4f8761c6ab255009a9e4dfce0d02272296967))
* add global debug flag ([56e79bd](https://github.com/baumrock/RockForms/commit/56e79bd1e1c0d65b20e84f3d6b724f9115560c63))
* add RockForms:loading and loaded events to listen to ([8e6eb7f](https://github.com/baumrock/RockForms/commit/8e6eb7fc33372175c53c43bd9dd199216a4eb1fc))
* wrap live validation in settimeout to avoid loading order issues ([20abf8f](https://github.com/baumrock/RockForms/commit/20abf8f58f84da5a51c4fa76c96972b7a203b5f8))

## [3.1.1](https://github.com/baumrock/RockForms/compare/v3.1.0...v3.1.1) (2025-07-13)


### Bug Fixes

* ajax post losing querystring if present ([095e6be](https://github.com/baumrock/RockForms/commit/095e6bebdfefa36859a2ddababc17c8ca2a728aa))

