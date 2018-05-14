#!/usr/bin/make -f

release:
	cd .. && zip pws-booking.zip pws-booking
	mv ../pws-booking.zip .

clean:
	find -name "*pyc" -type f -delete
