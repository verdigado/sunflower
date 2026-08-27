up:
	cd ../../../../ && docker-compose up -d

stop:
	cd ../../../../ && docker-compose stop

compile:
	npm run compile:all

compile-silent:
	npm run compile:build

watch:
	npm run watch

bundle:
	npm run build && npm run bundle

make-pot:
	 wp i18n make-pot . languages/sunflower.pot \
	 poedit languages/de_DE.po
	 //composer make-pot

publish:
	@echo "Latest tag was: "
	@git describe --tags --abbrev=0
	@read -p "which version do you want to publish now (start with number, NO v): " newversion; \
	sed -i  "s/Version.*/Version:\ $$newversion/" "sass/style.scss" && \
	php create-changelog.php $$newversion && \
	git checkout -B deploy && \
	git add sass/style.scss changelog.html && git commit -m "publishing version $$newversion" && \
	git push --set-upstream origin deploy
#	git tag "v$$newversion"
#	git push && git push --tags

publishbeta:
	@echo "Publish BETA Release: "
	@echo "Latest tag was: "
	@git describe --tags --abbrev=0
	@read -p "which version do you want to publish now (start with number, NO v) and append -beta-X: " newversion; \
	sed -i  "s/Version.*/Version:\ $$newversion/" "sass/style.scss" && \
	php create-changelog.php $$newversion && \
	git checkout -B deploy-beta && \
	git add sass/style.scss changelog.html && git commit -m "publishing version $$newversion" && \
	git push --set-upstream origin deploy-beta

mkdocs-serve:
	cd mkdocs && mkdocs serve

mkdocs-build:
	cd mkdocs && mkdocs build

js:
	npm run build

js-watch:
	npm run start

changelog:
	php create-changelog.php

change-since-last-tag:
	git log --pretty=format:"%s" HEAD...$(shell git describe --tags --abbrev=0)

test:
	cd ../../../../tests && LOCAL=true MODE=test python3 test.py

test-mobile:
	cd ../../../../tests && LOCAL=true MODE=test MOBILE=true python3 test.py

pattern:
	cd ../../../../tests && LOCAL=true MODE=pattern python3 test.py && LOCAL=true MODE=patterns MOBILE=true python3 test.py
