# sunflower WordPress Theme

 This is sunflower, a WordPress-Theme for the german green party. It is based on the starter theme [_s](https://underscores.me/).

 **This repo is for development only, it is not usable out of the box within WordPress.** See [Installation -> Setup](#setup) for the required steps to start developing.

## Demo and Download

 - The project-page can be found at https://sunflower-theme.de
 - The demopage is located at https://sunflower-theme.de/demo
 - Here you can download the installable theme-zip: https://sunflower-theme.de/updateserver/sunflower.zip

## Chat-Channel

There is also a chat-channel (access for green party members only)
https://chatbegruenung.de/channel/sunflower-wordpress


## Installation

### Requirements

`sunflower` requires the following dependencies:

- [Node.js](https://nodejs.org/)
- [Composer](https://getcomposer.org/)

### Setup
 - Clone this repo into *wp-content/themes*
 - install the Node.js and Composer dependencies in `sunflowers` theme folder:
    ```sh
    composer install
    npm install
    ```
 - compile the theme with the following commands:
    ```sh
    npm run compile:css
    npm run composer-lib
    npm run copy-node-modules
    npm run build
    ```
 - activate `sunflower` in WordPress-Backend

### Available CLI commands
- `composer lint:wpcs` : checks all PHP files against [PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- `composer lint:php` : checks all PHP files for syntax errors.
- `composer make-pot` : generates a .pot file in the `languages/` directory.
- `vendor/bin/rector`: run rector with provided `rector.php`.
- `vendor/bin/ecs`: run ecs with provided `ecs.php`.
- `npm run compile:css` : compiles SASS files to css.
- `npm run compile:rtl` : generates an RTL stylesheet.
- `npm run watch` : watches all SASS files and recompiles them to css when they change.
- `npm run lint:scss` : checks all SASS files against [CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/).
- `npm run lint:js` : checks all JavaScript files against [JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/).
- `npm run bundle` : generates a .zip archive for distribution, excluding development and system files.
- `npm run start` : start watcher for js-files
- `npm run build` : build javascript

### Deployment and Publishing

Stable and beta releases are built and deployed automatically via GitHub Actions when a [GitHub Release](https://github.com/verdigado/sunflower/releases) is created.

1. Run `make publish` (or `make publishbeta` for pre-releases) to bump the version in `sass/style.scss` and generate the changelog
2. Create a GitHub Release with a matching `v`-prefixed tag (e.g. `v3.0.10`)
3. The GitHub Action builds CSS/JS, bundles the ZIP, uploads it as a release artifact, and deploys to the update server

See `Makefile` for additional manual deployment targets and *mkdocs/docs/development.md* for the full publishing documentation.

### Contributing
see Documentation in *mkdocs/docs* for more details
