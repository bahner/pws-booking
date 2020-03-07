#!/usr/bin/make -f

PLUGIN_NAME ?= pws-booking

# We want to run clean first. To delete cache-files
# and old builds.
pws-booking.zip: clean
	mkdir $(PLUGIN_NAME)
	cp -a\
		*php\
		*py\
		includes\
		GraphQL\
		config\
		README.md\
		$(PLUGIN_NAME)
	zip -r $(PLUGIN_NAME).zip $(PLUGIN_NAME)/

clean:
	find -name "*pyc" -type f -delete
	rm -rf $(PLUGIN_NAME)
	rm -f $(PLUGIN_NAME).zip
