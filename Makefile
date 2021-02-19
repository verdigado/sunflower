up:
	cd ../../../../ && docker-compose up -d
	
compile:
	npm run compile:css
	
compile-silent:
	npm run compile:build

watch:
	npm run watch

bundle:
	npm run build && npm run bundle

make-pot:
	composer make-pot

upload:
	rsync ../sunflower.zip sharepic:/var/www/sunflower-theme.de/wp-content/themes/

activate:
	ssh sharepic "cd /var/www/sunflower-theme.de/wp-content/themes/ && wp theme install sunflower.zip --force"

VERSION = $(shell grep Version style.css | cut -d: -f2)
DATE = $(shell date)
announce:
	ssh sharepic "cd /var/www/sunflower-theme.de && wp option update blogdescription 'Demoseite für das WordPress-Theme Sunflower $(VERSION) von $(DATE)' --url=sunflower-theme.de/demo && wp option update blogname 'Sunflower $(VERSION)' && wp post update 1952 -" < announcement.txt

get:
	ssh sharepic "cd /var/www/sunflower-theme.de && wp post get 1952 --field=post_content" | sed -e 's/<version>[^<]*/<version>$(VERSION)/g' | sed -e 's/<date>[^<]*/<date>$(DATE)/g' > announcement.txt

deploy:
	git push && make compile-silent bundle upload activate get announce

publish:
	@echo "Be sure to have edited release_notes.html"
	@echo "Latest tag was: " 
	@git describe --tags --abbrev=0
	@read -p "which version do you want to publish now (start with number, NO v): " newversion; \
	sed -i  "s/Version.*/Version:\ $$newversion/" "sass/style.scss" && \
	git add sass/style.scss && git commit -m "publishing version $$newversion" && \
	git tag "v$$newversion"
	git push && git push --tags
	make deploy


mkdocs-serve:
	cd mkdocs && mkdocs serve

mkdocs-build:
	cd mkdocs && mkdocs build

mkdocs-deploy:
	cd mkdocs && mkdocs build && rsync -r --delete ../documentation/ sharepic:/var/www/sunflower-theme.de/documentation/

js:
	npm run build

js-watch: 
	npm run start

remote-create-dump:
	#echo geht nicht wegen nested command zur tabellenauswahl
	#ssh sharepic "cd /var/www/sunflower-theme.de/dumps && mysqldump wordpress $(mysql wordpress -Bse "show tables like tr_6__%;") > demo.sql"
	#tr_6_commentmeta tr_6_comments tr_6_links tr_6_options tr_6_postmeta tr_6_posts tr_6_term_relationships tr_6_term_taxonomy tr_6_termmeta tr_6_terms
	#ssh sharepic "cd /var/www/sunflower-theme.de/dumps && mysqldump wordpress tr_6_commentmeta tr_6_comments tr_6_links tr_6_options tr_6_postmeta tr_6_posts tr_6_term_relationships tr_6_term_taxonomy tr_6_termmeta tr_6_terms > demo.sql"

remote-demo2test:
	ssh sharepic "cd /var/www/sunflower-theme.de/dumps && mysqldump wordpress tr_6_commentmeta tr_6_comments tr_6_links tr_6_options tr_6_postmeta tr_6_posts tr_6_term_relationships tr_6_term_taxonomy tr_6_termmeta tr_6_terms > demo2test.sql && sed -i 's/tr_6/tr_7/g' demo2test.sql && sed -i 's/\/demo/\/test/g' demo2test.sql && mysql wordpress < demo2test.sql && cd /var/www/sunflower-theme.de/wp-content/uploads/sites && rsync -av 6/ 7"

sync-shared:
	rsync -av sharepic:/var/www/sunflower-theme.de/wp-content/uploads/sites/6/ ../../uploads/sites/2

sync-db:
	ssh sharepic "cd /var/www/sunflower-theme.de/dumps && mysqldump wordpress tr_6_commentmeta tr_6_comments tr_6_links tr_6_options tr_6_postmeta tr_6_posts tr_6_term_relationships tr_6_term_taxonomy tr_6_termmeta tr_6_terms > demo.sql" && \
	cd ../../../../ && \
	rsync sharepic:/var/www/sunflower-theme.de/dumps/demo.sql demo.sql && \
	sed -i 's/tr_6/tr_2/g' demo.sql && \
	sed -i 's/sites\/6/sites\/2/g' demo.sql && \
	sed -i 's/https:\/\/sunflower-theme.de/http:\/\/localhost/g' demo.sql && \
	docker-compose exec -T db mysql wordpress < demo.sql

remote-create-homepage-txt:
	ssh sharepic "cd /var/www/sunflower-theme.de/dumps && wp post get 37 --url=sunflower-theme.de/demo --field=content > homepage.txt"
	
