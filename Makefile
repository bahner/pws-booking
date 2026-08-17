#!/usr/bin/make -f

.PHONY: release vendor clean up down

release: clean vendor pws-booking.zip

vendor:
	cd pws-booking && composer install --no-dev --optimize-autoloader

pws-booking.zip:
	zip -r pws-booking.zip pws-booking/ -x 'pws-booking/.docker/*'

clean:
	rm -rf pws-booking/vendor
	rm -f pws-booking.zip

up:
	cd .docker && docker-compose up -d

down:
	cd .docker && docker-compose down
