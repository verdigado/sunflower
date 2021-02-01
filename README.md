sunflower WordPress theme
===
 This is sunflower, a WordPress-Theme for the german green party. It is based on the starter theme [_s](https://underscores.me/).
 This repo is for development only, it is not usable out of the box within WordPress

Demo
---------------
A Demo-Website can be found at
https://wordpress.tom-rose.de.


Installation
---------------

### Requirements

`sunflower` requires the following dependencies:

- [Node.js](https://nodejs.org/)
- [Composer](https://getcomposer.org/)

### Setup
 - Clone this repo into *wp-content/themes*
 - install the Node.js and Composer dependencies in `sunflowers` theme folder:
    ```sh
    $ composer install
    $ npm install
    ```
 - activate `sunflower` in WordPress-Backend

### Available CLI commands
- `composer lint:wpcs` : checks all PHP files against [PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- `composer lint:php` : checks all PHP files for syntax errors.
- `composer make-pot` : generates a .pot file in the `languages/` directory.
- `npm run compile:css` : compiles SASS files to css.
- `npm run compile:rtl` : generates an RTL stylesheet.
- `npm run watch` : watches all SASS files and recompiles them to css when they change.
- `npm run lint:scss` : checks all SASS files against [CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/).
- `npm run lint:js` : checks all JavaScript files against [JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/).
- `npm run bundle` : generates a .zip archive for distribution, excluding development and system files.
- `npm run start` : start watcher for js-files
- `npm run build` : build javascript

### Deployment
see Makefile for tasks

#### Publishing
1. edit version in style.scss
2. make deploy
3. publish release on github with same version number. This triggers a github-action
4. increment version in style.scss and add nightly-build


### Contributing
see Documentation in *mkdocs/docs* for more details