## [3.0.0](https://github.com/baumrock/RockForms/compare/v2.1.1...v3.0.0) (2025-02-05)


### ⚠ BREAKING CHANGES

* refactoring & cleanup to use RockDevTools

### Features

* add field to select a form ([8645bf8](https://github.com/baumrock/RockForms/commit/8645bf870ae622d98bab7d0ba4dd4a34c8e7e943))
* refactoring & cleanup to use RockDevTools ([f649098](https://github.com/baumrock/RockForms/commit/f649098b97f3c54a0bf24bba68b3b7a27aa4763a))

## [2.1.1](https://github.com/baumrock/RockForms/compare/v2.1.0...v2.1.1) (2024-12-02)


### Bug Fixes

* make sure to return wiredata in steps getData ([176962a](https://github.com/baumrock/RockForms/commit/176962a14bb067c85b33d890eef74ab532d4e4ea))

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

