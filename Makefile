#!/usr/bin/make -f

release: clean pws-booking.zip

pws-booking.zip:
	zip -r pws-booking.zip pws-booking/

clean:
	find -name "*pyc" -type f -delete
	rm -f pws-booking.zip

up:
	cd .docker && docker-compose up -d

down:
	cd .docker && docker-compose down
