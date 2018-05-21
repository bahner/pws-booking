#!/usr/bin/make -f

DIRNAME=$(shell basename `pwd`)

release: clean
	cd .. && zip -r pws-booking.zip $(DIRNAME)/*
	mv ../pws-booking.zip .

clean:
	find -name "*pyc" -type f -delete
