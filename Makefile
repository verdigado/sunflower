compile:
	npm run compile:css

watch:
	npm run watch

bundle:
	npm run bundle

make-pot:
	composer make-pot

upload:
	rsync ../sunflower.zip sharepic:/var/www/wordpress.tom-rose.de/wp-content/themes/

activate:
	ssh sharepic "cd /var/www/wordpress.tom-rose.de/wp-content/themes/ && wp theme install sunflower.zip --force"

VERSION = $(shell grep Version style.css | cut -d: -f2)
DATE = $(shell date)
announce:
	ssh sharepic "cd /var/www/wordpress.tom-rose.de && wp option update blogdescription 'Demoseite für das WordPress-Theme Sunflower $(VERSION) von $(DATE)' --url=wordpress.tom-rose.de/demo && wp option update blogname 'Sunflower $(VERSION)' && wp post update 1952 -" < announcement.txt

get:
	ssh sharepic "cd /var/www/wordpress.tom-rose.de && wp post get 1952 --field=post_content" | sed -e 's/<version>[^<]*/<version>$(VERSION)/g' | sed -e 's/<date>[^<]*/<date>$(DATE)/g' > announcement.txt

deploy:
	make bundle upload activate get announce

mkdocs-serve:
	cd mkdocs && mkdocs serve

mkdocs-build:
	cd mkdocs && mkdocs build

mkdocs-deploy:
	cd mkdocs && mkdocs build && rsync -r --delete ../documentation/ sharepic:/var/www/wordpress.tom-rose.de/documentation/

demo-dump-create:
	mysqldump wordpress $(mysql wordpress -Bse "show tables like 'wp_6_%';") > demo.sql

demo-dump-in:
	mysql wordpress < demo.sql