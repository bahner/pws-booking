#!/usr/bin/python2.7
# coding: utf-8

"""Constants for mapping NIF data to OPK PWS Booking"""

ATTR_MAP = {
    'address1': 'Adresse',
    'email': 'E-post',
    'firstname': 'Fornavn',
    'lastname': 'Etternavn',
    'phonemobile': 'Tlf. mobil',
    'postaladdress': 'Postadresse',
    'postalcode': 'Postnr',
    'userid': 'Tlf. mobil',
    'id': 'Tlf. mobil',
}

DEFAULT_EMAIL = 'mangler.epost@opk.no'

DEFAULTS = {
    'E-post': DEFAULT_EMAIL,
    'Adresse': 'Konglebakken 1',
    'Postnr': '1111',
    'Postadresse': 'BORTENFOR SOL OG MÅNE',
}
