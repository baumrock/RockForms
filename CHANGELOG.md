## [2.1.0](https://github.com/baumrock/RockForms/compare/v2.0.0...v2.1.0) (2024-11-03)


### Features

* add getData() method for StepsArray ([25af82f](https://github.com/baumrock/RockForms/commit/25af82f310cca863ebe92cd15c576986f291cb5f))
* improve multi step form ([2fcd84b](https://github.com/baumrock/RockForms/commit/2fcd84b85433163bf107b622c7a6bf64e16aea54))


### Bug Fixes

* better exception in renderForm ([8866ecc](https://github.com/baumrock/RockForms/commit/8866ecc524097a501c061c4573c4b07be44f1af1))

## [2.0.0](https://github.com/baumrock/RockForms/compare/v1.6.1...v2.0.0) (2024-10-21)


### ⚠ BREAKING CHANGES

* refactor to use RockLoaders for animations

### Features

* refactor to use RockLoaders for animations ([5c1c335](https://github.com/baumrock/RockForms/commit/5c1c335fc8a7f60684d1098a547b7adee207cfe2))


### Bug Fixes

* add missing return statement for rockloader ([d265e86](https://github.com/baumrock/RockForms/commit/d265e86d72f02a37797c0d5903984940ad4677b0))
* also show loaders on regular form submit ([13f5bf8](https://github.com/baumrock/RockForms/commit/13f5bf84802f968f8d117f28634c70a2740fe985))
* remove RockLoaders requirement ([139c1c9](https://github.com/baumrock/RockForms/commit/139c1c9df1e1e1af6fee16c8233c42023bf098f2))
* throw exception when debug mode ([9bde8ae](https://github.com/baumrock/RockForms/commit/9bde8ae226ad4a00ba0931076447d1fcb6c28418))

## [1.6.1](https://github.com/baumrock/RockForms/compare/v1.6.0...v1.6.1) (2024-10-01)


### Bug Fixes

* revert method getNonSystemValues() that has accidentally been removed ([fc45f71](https://github.com/baumrock/RockForms/commit/fc45f718ca463550962d1d606f038d0204e96fa5))
* set _nss cookie to make sure htmx submissions work properly ([8465c6c](https://github.com/baumrock/RockForms/commit/8465c6c309ff184d9c1ff9f842c2b5f496fe2ecd))

## [1.6.0](https://github.com/baumrock/RockForms/compare/v1.5.0...v1.6.0) (2024-09-17)


### Features

* add form submission logger ([82c6b76](https://github.com/baumrock/RockForms/commit/82c6b7639887aa18e55c76583574a6db8c7c704e))


### Bug Fixes

* add missing docs ([8a3f420](https://github.com/baumrock/RockForms/commit/8a3f420834815a19ce652a6b990f68ff8da78e09))
* forms sometimes not submitting properly with procache ([4f913c2](https://github.com/baumrock/RockForms/commit/4f913c2119ef56dfdec6a24111966c44d3724c08))
* restore missing releases file ([d93b99f](https://github.com/baumrock/RockForms/commit/d93b99fd21e72e1522ee3fdf8c336b4b464f8425))

## [1.5.0](https://github.com/baumrock/RockForms/compare/v1.4.0...v1.5.0) (2024-07-01)


### Features

* add multistep forms ([d5659bb](https://github.com/baumrock/RockForms/commit/d5659bb43804e3ddef3bd2ae9c5f07a62faf8840))
* improve csrf + form submission ([6f51fc0](https://github.com/baumrock/RockForms/commit/6f51fc0b4f7d170c0c5cca0cfcf95d39844c5357))
* improve step navigation ([8d24f8b](https://github.com/baumrock/RockForms/commit/8d24f8b902b752c28d3667f2cf5c5ede568eaa71))


### Bug Fixes

* bug if all steps are done ([813f8fb](https://github.com/baumrock/RockForms/commit/813f8fb51fae623c68f8a908fe562970c4ed9c89))

